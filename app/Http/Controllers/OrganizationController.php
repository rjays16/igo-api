<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Http\Requests\ViewOrganizationRequest;
use App\Http\Resources\OrganizationResource;
use Illuminate\Database\QueryException;
use App\Enums\HttpStatusCode;
use App\Enums\SystemMessage;
use App\Exports\OrganizationExport;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ViewOrganizationRequest $request)
    {
        if($request->validated()){

            //extract page size
            $per_page=$request->input('per_page');
            
            $dataQuery = Organization::select('organizations.*');

             // Filter All Column
             if($request->filter_allcolumn!=''){
                $dataQuery->whereRaw('concat(organizations.id,organizations.organization,organizations.description) LIKE ?','%'.$request->filter_allcolumn.'%');
            }

            // Filter User ID
            if($request->filter_id!=''){
                $dataQuery->where('organizations.id','LIKE','%'.$request->filter_id.'%');
            }

            // Filter Organization
            if($request->filter_organization!=''){
                $dataQuery->where('organizations.organization','LIKE','%'.$request->filter_organization.'%');
            }

            // Filter Description
            if($request->filter_description!=''){
                $dataQuery->where('organizations.description','LIKE','%'.$request->filter_description.'%');
            }

              //Filter Created At
              if($request->filter_created_at!=''){
                $dataQuery->where('organizations.created_at','LIKE','%'.$request->filter_created_at.'%');
            }
             //Apply Sorting Mechanism Begin ----------------------------------------------//

             //Sort by ID
             if($request->sort_id){
                $dataQuery->orderBy('organizations.id',($request->sort_id==1) ? "asc" : "desc");
            }
            //Sort by Organization
            if($request->sort_organization){
                $dataQuery->orderBy('organizations.organization',($request->sort_organization==1) ? "asc" : "desc");
            }

            //Sort Description
            if($request->sort_description){
                $dataQuery->orderBy('organizations.description',($request->sort_description==1) ? "asc" : "desc");
            }

             //Sort by Created_at
             if($request->sort_created_at){
                $dataQuery->orderBy('organizations.created_at',($request->sort_created_at==1) ? "asc" : "desc");
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
                    SystemMessage::Messsage=>SystemMessage::OrganizationNoRecordFound,
                    SystemMessage::Success=>false],
                    HttpStatusCode::OrganizationErrorNotFound);
            }

            //Check if this request is for EXPORT. If export_to is NOT '' means YES then export the data to target format
            if($request->export_to!='')
            {
                switch (strtoupper($request->export_to)){
                    case FileName::EXCELFile:
                        return (new OrganizationExport($dataResult))->download(FileName::OrganizationExport.FileName::EXCELFileFormat, \Maatwebsite\Excel\Excel::XLSX,null);
                        break;
                    case FileName::CSVFile:
                        return (new OrganizationExport($dataResult))->download(FileName::OrganizationExport.FileName::CSVFileFormat, \Maatwebsite\Excel\Excel::CSV,null);
                        break;
                    case FileName::PDFFile:
                        return (new OrganizationExport($dataResult))->download(FileName::OrganizationExport.FileName::PDFFileFormat, \Maatwebsite\Excel\Excel::DOMPDF,null);
                        break;

                    default:
                        return response()->json([
                        SystemMessage::Messsage=>SystemMessage::FileFormatNotSupported,
                        SystemMessage::Success=>false],
                HttpStatusCode::OrganizationErrorBadRequest);
                }
            }

             //Apply Sorting Mechanism End ------------------------------------------------//
             return OrganizationResource::collection($dataResult)->additional([
                SystemMessage::Messsage=>SystemMessage::OrganizationRecordRetrieved,
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
     * @param  \App\Http\Requests\StoreOrganizationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrganizationRequest $request)
    {
       //Validate Request
       if($request->validated()){

        //Pick Up only all those data from the request
        //that are needed to save to organization Table
        $data=[
            "organization" => $request->organization,
            "description" => $request->description,
            "created_at" => $request->created_at,
        ];

       //Execute and return the newly created record
        try{
                //Execute create to insert the record to organization table and put in on organization object
                //so we can add additional collection element later
                $organization=Organization::create($data);

                //return the OrganizationResource with additional element
                return OrganizationResource::collection( [$organization])->additional([
                    SystemMessage::Messsage=>SystemMessage::OrganizationRecordCreated,
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
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{

            $organizations=Organization::find($id);

                if(is_null($organizations)){
                    return response()->json([
                        SystemMessage::Messsage=>SystemMessage::OrganizationID.$id.SystemMessage::NotFound,
                        SystemMessage::Success=>false],
                HttpStatusCode::OrganizationErrorNotFound);
                }

            //show found record as object
            return response()->json([
                SystemMessage::Data=>OrganizationResource::make($organizations) ,
                SystemMessage::Messsage=>SystemMessage::OrganizationRecordFound,
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
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function edit(Organization $organization)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrganizationRequest  $request
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrganizationRequest $request, $id)
    {
        //Validate Request
        if($request->validated()){

            //Pick Up only all those data from the request
            //that are needed to update to Organization Table
            $data=[
                "organization" => $request->organization,
                "description" => $request->description,
            ];

           //Find and return the found record
            try{
                    $organization=Organization::find($id);

                    if(is_null($organization)){
                        return response()->json([
                            SystemMessage::Messsage=>SystemMessage::OrganizationID.$id.SystemMessage::NotFound,
                            SystemMessage::Success=>false],
                    HttpStatusCode::OrganizationErrorNotFound);
                    }

                //Execute update to update the record on organization table
                $organization->update($data);

                //return the OrganizationResource with additional element
                return OrganizationResource::collection( [$organization])->additional([
                    SystemMessage::Messsage=>SystemMessage::OrganizationRecordUpdated,
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
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            //is not yet been used as referrence by organization record
            $organization=Organization::where('id','=',$id)->first();


            if(!is_null($organization)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::OrganizationCanNotDelete.$id.SystemMessage::AlreadyBeenUsed,
                    SystemMessage::Success=>false],
            HttpStatusCode::OrganizationErrorNotFound);
            }

            $organization=Organization::find($id);

            if(is_null($organization)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::OrganizationID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
            HttpStatusCode::OrganizationErrorNotFound);
            }

            //Execute Delete and return the deleted record
            $organization->delete();

            //return the OrganizationResource with additional element
            return OrganizationResource::collection( [$organization])->additional([
                SystemMessage::Messsage=>SystemMessage::OrganizationRecordDeleted,
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

        $organization=Organization::withTrashed()->find($id);

            if(is_null($organization)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::OrganizationID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
                    HttpStatusCode::OrganizationErrorNotFound);
            }

            $organization=Organization::withTrashed()->where('id',$id)->restore();
            $organization=Organization::find($id);

            //return the OrganizationResource with additional element
            return OrganizationResource::collection( [$organization])->additional([
            SystemMessage::Messsage=>SystemMessage::OrganizationRecordRestored,
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

            $organizationActiveRecords=Organization::count();
            $organizationSoftDeletedRecords=Organization::onlyTrashed()->count();
            $organizationTotalRecords= $organizationActiveRecords+$organizationSoftDeletedRecords;


            //return the OrganizationResource with additional element
            return response()->json([
            SystemMessage::Data=>array(SystemMessage::ActiveRecords=>$organizationActiveRecords,
                                       SystemMessage::SoftDeletedRecords=>$organizationSoftDeletedRecords,
                                       SystemMessage::TotalRecords=>$organizationTotalRecords),
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
