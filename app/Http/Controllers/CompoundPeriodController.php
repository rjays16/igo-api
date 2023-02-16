<?php

namespace App\Http\Controllers;

use App\Models\CompoundPeriod;
use App\Enums\HttpStatusCode;
use App\Enums\SystemMessage;
use Illuminate\Database\QueryException;
use App\Http\Requests\StoreCompoundPeriodRequest;
use App\Http\Requests\UpdateCompoundPeriodRequest;
use App\Http\Requests\ViewCompoundPeriodRequest;
use App\Http\Resources\CompoundPeriodResource;
use App\Exports\CompoundPeriodExport;
class CompoundPeriodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ViewCompoundPeriodRequest $request)
    {
        if($request->validated()){
            //extract page size
            $per_page=$request->input('per_page');

            $dataQuery = CompoundPeriod::select('compound_periods.*');

             // Filter All Column
             if($request->filter_allcolumn!=''){
                $dataQuery->whereRaw('concat(compound_periods.id,compound_periods.compound_period,compound_periods.description,compound_periods.created_at) LIKE ?','%'.$request->filter_allcolumn.'%');
            }

            //Filter ID
            if($request->filter_id!=''){
                $dataQuery->where('compound_periods.compound_period','LIKE','%'.$request->filter_id.'%');
            }

            // Filter Compound Period
            if($request->filter_compound_period!=''){
                $dataQuery->where('compound_periods.compound_period','LIKE','%'.$request->filter_compound_period.'%');
            }

            // Filter Description
            if($request->filter_description!=''){
                $dataQuery->where('compound_periods.description','LIKE','%'.$request->filter_description.'%');
            }

            //Filter Created At
            if($request->filter_createdAt!=''){
                $dataQuery->where('compound_periods.created_at','LIKE','%'.$request->filter_createdAt.'%');
            }

             //Apply Sorting Mechanism Begin ----------------------------------------------//

             //Sort by ID
             if($request->sort_id){
                $dataQuery->orderBy('compound_periods.id',($request->sort_id==1) ? "asc" : "desc");
            }
            //Sort by Compound Period
            if($request->sort_compound_period){
                $dataQuery->orderBy('compound_periods.compound_period',($request->sort_compound_period==1) ? "asc" : "desc");
            }

            //Sort Description
            if($request->sort_description){
                $dataQuery->orderBy('compound_periods.Description',($request->sort_description==1) ? "asc" : "desc");
            }
            
             //Sort by Created_at
             if($request->sort_created_at){
                $dataQuery->orderBy('compound_periods.created_at',($request->sort_created_at==1) ? "asc" : "desc");
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
                    SystemMessage::Messsage=>SystemMessage::CompoundPeriodNoRecordFound,
                    SystemMessage::Success=>false],
                    HttpStatusCode::CompoundPeriodErrorNotFound);
            }

            //Check if this request is for EXPORT. If export_to is NOT '' means YES then export the data to target format
            if($request->export_to!='')
            {
                switch (strtoupper($request->export_to)){
                    case FileName::EXCELFile:
                        return (new CompoundPeriodExport($dataResult))->download(FileName::CompoundPeriodExport.FileName::EXCELFileFormat, \Maatwebsite\Excel\Excel::XLSX,null);
                        break;
                    case FileName::CSVFile:
                        return (new CompoundPeriodExport($dataResult))->download(FileName::CompoundPeriodExport.FileName::CSVFileFormat, \Maatwebsite\Excel\Excel::CSV,null);
                        break;
                    case FileName::PDFFile:
                        return (new CompoundPeriodExport($dataResult))->download(FileName::CompoundPeriodExport.FileName::PDFFileFormat, \Maatwebsite\Excel\Excel::DOMPDF,null);
                        break;

                    default:
                        return response()->json([
                        SystemMessage::Messsage=>SystemMessage::FileFormatNotSupported,
                        SystemMessage::Success=>false],
                HttpStatusCode::CompundPeriodErrorBadRequest);
                }
            }

             //Apply Sorting Mechanism End ------------------------------------------------//
             return CompoundPeriodResource::collection($dataResult)->additional([
                SystemMessage::Messsage=>SystemMessage::CompoundPeriodRecordRetrieved,
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
     * @param  \App\Http\Requests\StoreCompoundPeriodRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCompoundPeriodRequest $request)
    {
         //Validate Request
       if($request->validated()){

        //Pick Up only all those data from the request
        //that are needed to save to compound period Table
        $data=[
            "compound_period" => $request->compound_period,
            "description" => $request->description,
            "created_at" => $request->created_at,
        ];

       //Execute and return the newly created record
        try{
                //Execute create to insert the record to compound period table and put in on compound period object
                //so we can add additional collection element later
                $compound_period=CompoundPeriod::create($data);

                //return the CompoundPeriodResource with additional element
                return CompoundPeriodResource::collection( [$compound_period])->additional([
                    SystemMessage::Messsage=>SystemMessage::CompoundPeriodRecordRetrieved,
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
     * @param  \App\Models\CompoundPeriod  $compoundPeriod
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{

            $compound_period=CompoundPeriod::find($id);

                if(is_null($compound_period)){
                    return response()->json([
                        SystemMessage::Messsage=>SystemMessage::CompoundPeriodID.$id.SystemMessage::NotFound,
                        SystemMessage::Success=>false],
                HttpStatusCode::CompoundPeriodErrorNotFound);
                }

            //return the CompoundPeriodResource with additional element
            return CompoundPeriodResource::collection( [$compound_period])->additional([
                SystemMessage::Messsage=>SystemMessage::CompoundPeriodRecordFound,
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
     * @param  \App\Models\CompoundPeriod  $compoundPeriod
     * @return \Illuminate\Http\Response
     */
    public function edit(CompoundPeriod $compoundPeriod)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCompoundPeriodRequest  $request
     * @param  \App\Models\CompoundPeriod  $compoundPeriod
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCompoundPeriodRequest $request, $id)
    {
          //Validate Request
          if($request->validated()){

            //Pick Up only all those data from the request
            //that are needed to update to compound period Table
            $data=[
            "compound_period" => $request->compound_period,
            "description" => $request->description,
            "created_at" => $request->created_at,
            ];

           //Find and return the found record
            try{
                    $compound_period=CompoundPeriod::find($id);

                    if(is_null($compound_period)){
                        return response()->json([
                            SystemMessage::Messsage=>SystemMessage::CompoundPeriodID.$id.SystemMessage::NotFound,
                            SystemMessage::Success=>false],
                    HttpStatusCode::CompoundPeriodErrorNotFound);
                    }

                //Execute update to update the record on compound period table
                $compound_period->update($data);

                //return the CompoundPeriodResource with additional element
                return CompoundPeriodResource::collection( [$compound_period])->additional([
                    SystemMessage::Messsage=>SystemMessage::CompoundPeriodRecordUpdated,
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
     * @param  \App\Models\CompoundPeriod  $compoundPeriod
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            //is not yet been used as referrence by compound period record
            $compound_period=CompoundPeriod::where('id','=',$id)->first();


            if(!is_null($compound_period)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::CompoundPeriodCanNotDelete.$id.SystemMessage::AlreadyBeenUsed,
                    SystemMessage::Success=>false],
            HttpStatusCode::CompoundPeriodErrorNotFound);
            }

            $compound_period=CompoundPeriod::find($id);

            if(is_null($compound_period)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::CompoundPeriodID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
            HttpStatusCode::CompoundPeriodErrorNotFound);
            }

            //Execute Delete and return the deleted record
            $compound_period->delete();

            //return the CompoundPeriodResource with additional element
            return CompoundPeriodResource::collection( [$compound_period])->additional([
                SystemMessage::Messsage=>SystemMessage::CompoundPeriodRecordDeleted,
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

        $compound_period=CompoundPeriod::withTrashed()->find($id);

            if(is_null($compoundPeriod)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::CompoundPeriodID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
                    HttpStatusCode::CompoundPEriodnErrorNotFound);
            }

            $compound_period=CompoundPeriod::withTrashed()->where('id',$id)->restore();
            $compound_period=CompoundPeriod::find($id);

            //return the CompoundPeriodResource with additional element
            return CompoundPeriodResource::collection( [$compound_period])->additional([
            SystemMessage::Messsage=>SystemMessage::compoundPeriodRecordRestored,
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

            $compound_periodActiveRecords=CompoundPeriod::count();
            $compound_periodSoftDeletedRecords=CompoundPeriod::onlyTrashed()->count();
            $compound_periodTotalRecords= $compound_periodActiveRecords+$compound_periodSoftDeletedRecords;

            return response()->json([
            SystemMessage::Data=>array(SystemMessage::ActiveRecords=>$compound_periodActiveRecords,
                                       SystemMessage::SoftDeletedRecords=>$compound_periodSoftDeletedRecords,
                                       SystemMessage::TotalRecords=>$compound_periodTotalRecords),
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
