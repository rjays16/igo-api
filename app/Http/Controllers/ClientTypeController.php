<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientType;
use App\Enums\HttpStatusCode;
use App\Enums\SystemMessage;
use App\Http\Requests\StoreClientType;
use App\Http\Requests\UpdateClientTypeRequest;
use Illuminate\Database\QueryException;
use App\Http\Requests\ClientTypeRequest;
use App\Http\Resources\ClientTypeResource;
use App\Http\Requests\ViewClientTypeRequest;
use App\Exports\ClientTypeExport;
class ClientTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ViewClientTypeRequest $request)
    {
        if($request->validated()){

            //extract page size
            $per_page=$request->input('per_page');

            //extract all sorting parameters if available
            $sortID=$request->input('sort_id');
            $sortClientType=$request->input('sort_client_type');
            $sortDescription=$request->input('sort_description');
            $sortCreated_at=$request->input('sort_created_at');

            $dataQuery = ClientType::select('client_types.*');

             // Filter All Column
             if($request->filter_allcolumn!=''){
                $dataQuery->whereRaw('concat(client_types.id,client_types.client_type,client_types.description,client_types.created_at) LIKE ?','%'.$request->filter_allcolumn.'%');
            }

            // Filter ID
            if($request->filter_id!=''){
                $dataQuery->where('client_types.client_type','LIKE','%'.$request->filter_id.'%');
            }

            // Filter Client Type
            if($request->filter_client_type!=''){
                $dataQuery->where('client_types.client_type','LIKE','%'.$request->filter_client_type.'%');
            }

            // Filter Description
            if($request->filter_description!=''){
                $dataQuery->where('client_types.description','LIKE','%'.$request->filter_description.'%');
            }

            //Filter Created At
            if($request->filter_created_at!=''){
                $dataQuery->where('client_types.created_at','LIKE','%'.$request->filter_created_at.'%');
            }

             //Apply Sorting Mechanism Begin ----------------------------------------------//

             //Sort by ID
             if($request->sort_id){
                $dataQuery->orderBy('client_types.id',($request->sort_id==1) ? "asc" : "desc");
            }
            //Sort by Client Type
            if($request->sort_client_type){
                $dataQuery->orderBy('client_types.client_type',($request->sort_client_type==1) ? "asc" : "desc");
            }

            //Sort Description
            if($request->sort_description){
                $dataQuery->orderBy('client_types.Description',($request->sort_description==1) ? "asc" : "desc");
            }
            
             //Sort by Created_at
             if($request->sort_created_at){
                $dataQuery->orderBy('clients.created_at',($request->sort_created_at==1) ? "asc" : "desc");
            }

            try{
                //Lastly Execute Query and Paginate the result
               $dataResult=$dataQuery->Simplepaginate($per_page);
                  
               } catch(QueryException $e){ // Catch all Query Errors
                   return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
               } catch(\Exception $e){     // Catch all General Errors
                   return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
               }catch(\Error $e){          // Catch all Php Errors
                   return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
               }

               //Check if dataResult has record, if not throw message
             if($dataResult->count()<=0){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::ClientTypeNoRecordFound,
                    SystemMessage::Success=>false],
                    HttpStatusCode::ClientTypeErrorNotFound);
            }

            //Check if this request is for EXPORT. If export_to is NOT '' means YES then export the data to target format
            if($request->export_to!='')
            {
                switch (strtoupper($request->export_to)){
                    case FileName::EXCELFile:
                        return (new ClientTypeExport($dataResult))->download(FileName::ClientTypeExport.FileName::EXCELFileFormat, \Maatwebsite\Excel\Excel::XLSX,null);
                        break;
                    case FileName::CSVFile:
                        return (new ClientTypeExport($dataResult))->download(FileName::ClientTypeExport.FileName::CSVFileFormat, \Maatwebsite\Excel\Excel::CSV,null);
                        break;
                    case FileName::PDFFile:
                        return (new ClientTypeExport($dataResult))->download(FileName::ClientTypeExport.FileName::PDFFileFormat, \Maatwebsite\Excel\Excel::DOMPDF,null);
                        break;

                    default:
                        return response()->json([
                        SystemMessage::Messsage=>SystemMessage::FileFormatNotSupported,
                        SystemMessage::Success=>false],
                HttpStatusCode::ClientTypeErrorBadRequest);
                }
            }

             //Apply Sorting Mechanism End ------------------------------------------------//
             return ClientTypeResource::collection($dataResult)->additional([
                SystemMessage::Messsage=>SystemMessage::ClientTypeRecordRetrieved,
                SystemMessage::Success=>true]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreClientType $request)
    {
        //Validate Request
       if($request->validated()){

        //Pick Up only all those data from the request
        //that are needed to save to client type Table
        $data=[
            "client_type" => $request->client_type,
            "description" => $request->description,
        ];

       //Execute and return the newly created record
        try{
                //Execute create to insert the record to client type table and put in on client type object
                //so we can add additional collection element later
                $client_type=ClientType::create($data);

                //return the ClientTypeResource with additional element
                return ClientTypeResource::collection( [$client_type])->additional([
                    SystemMessage::Messsage=>SystemMessage::ClientTypeRecordCreated,
                    SystemMessage::Success=>true]);

            } catch(QueryException $e){ // Catch all Query Errors
            return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
            } catch(\Exception $e){     // Catch all General Errors
            return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
            }catch(\Error $e){          // Catch all Php Errors
            return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
        }
    }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{

            $client_type=ClientType::find($id);

                if(is_null($client_type)){
                    return response()->json([
                        SystemMessage::Messsage=>SystemMessage::ClientTypeID.$id.SystemMessage::NotFound,
                        SystemMessage::Success=>false],
                HttpStatusCode::ClientTypeErrorNotFound);
                }

                //show found record as object
                return response()->json([
                SystemMessage::Data=>ClientTypeResource::make($client_type) ,
                SystemMessage::Messsage=>SystemMessage::ClientTypeNoRecordFound,
                SystemMessage::Success=>true]);

            }catch(QueryException $e){ // Catch all Query Errors
                return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
            }catch(\Exception $e){     // Catch all General Errors
               return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
            }catch(\Error $e){          // Catch all Php Errors
               return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
            }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClientTypeRequest $request, $id)
    {
        //Validate Request
        if($request->validated()){

            //Pick Up only all those data from the request
            //that are needed to update to Client type Table
            $data=[
                "client_type" => $request->client_type,
                "description" => $request->description,
            ];

           //Find and return the found record
            try{
                    $client_type=ClientType::find($id);

                    if(is_null($client_type)){
                        return response()->json([
                            SystemMessage::Messsage=>SystemMessage::ClientTypeID.$id.SystemMessage::NotFound,
                            SystemMessage::Success=>false],
                    HttpStatusCode::ClientTypeErrorNotFound);
                    }

                //Execute update to update the record on client type table
                $client_type->update($data);

                //return the ClientTypeResource with additional element
                return ClientTypeResource::collection( [$client_type])->additional([
                    SystemMessage::Messsage=>SystemMessage::ClientTypeRecordUpdated,
                    SystemMessage::Success=>true]);

                } catch(QueryException $e){ // Catch all Query Errors
                   return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
                } catch(\Exception $e){     // Catch all General Errors
                   return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
                }catch(\Error $e){          // Catch all Php Errors
                   return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
                }
            }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            //is not yet been used as referrence by client_type record
            $client_type=ClientType::where('id','=',$id)->first();


            if(!is_null($client_type)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::ClientTypeCanNotDelete.$id.SystemMessage::AlreadyBeenUsed,
                    SystemMessage::Success=>false],
            HttpStatusCode::ClientTypeErrorNotFound);
            }

            $client_type=ClientType::find($id);

            if(is_null($client_type)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::ClientTypeID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
            HttpStatusCode::ClientTypeErrorNotFound);
            }

            //Execute Delete and return the deleted record
            $client_type->delete();

            //return the ClientTypeResource with additional element
            return ClientTypeResource::collection( [$client_type])->additional([
                SystemMessage::Messsage=>SystemMessage::ClientTypeRecordDeleted,
                SystemMessage::Success=>true]);

        } catch(QueryException $e){ // Catch all Query Errors
           return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
        } catch(\Exception $e){     // Catch all General Errors
           return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
        }catch(\Error $e){          // Catch all Php Errors
           return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
        }
    }

    public function undestroy($id)
    {
        try{

        $client_type=ClientType::withTrashed()->find($id);

            if(is_null($client_type)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::ClientTypeID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
                    HttpStatusCode::ClientTypeErrorNotFound);
            }

            $client_type=ClientType::withTrashed()->where('id',$id)->restore();
            $client_type=ClientType::find($id);

            //return the ClientTypeResource with additional element
            return ClientTypeResource::collection( [$client_type])->additional([
            SystemMessage::Messsage=>SystemMessage::ClientTypeRecordRestored,
            SystemMessage::Success=>true]);

        }catch(QueryException $e){ // Catch all Query Errors
            return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
        }catch(\Exception $e){     // Catch all General Errors
           return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
        }catch(\Error $e){          // Catch all Php Errors
           return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
        }
    }

    public function stats()
    {
        try{

            $client_typeActiveRecords=ClientType::count();
            $client_typeSoftDeletedRecords=ClientType::onlyTrashed()->count();
            $client_typeTotalRecords= $client_typeActiveRecords+$client_typeSoftDeletedRecords;

            return response()->json([
            SystemMessage::Data=>array(SystemMessage::ActiveRecords=>$client_typeActiveRecords,
                                       SystemMessage::SoftDeletedRecords=>$client_typeSoftDeletedRecords,
                                       SystemMessage::TotalRecords=>$client_typeTotalRecords),
            SystemMessage::Messsage=>SystemMessage::StatsRetrieved,
            SystemMessage::Success=>true],
    HttpStatusCode::SuccessOK);

            }catch(QueryException $e){ // Catch all Query Errors
                return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
            }catch(\Exception $e){     // Catch all General Errors
               return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
            }catch(\Error $e){          // Catch all Php Errors
               return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
            }
    }

}
