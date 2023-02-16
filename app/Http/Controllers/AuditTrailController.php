<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Http\Requests\StoreAuditTrailRequest;
use App\Http\Requests\UpdateAuditTrailRequest;
use App\Http\Requests\ViewAuditTrailRequest;
use App\Http\Resources\AuditTrailResource;
use App\Enums\HttpStatusCode;
use App\Enums\SystemMessage;
use Illuminate\Database\QueryException;
use App\Exports\AuditTrailExport;
use App\Enums\FileName;

class AuditTrailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ViewAuditTrailRequest $request)
    {
        if($request->validated()){
            $wildCard=($request->filter_activatewildcard==1) ? '%' : '';

            //extract page size
            $per_page=$request->input('per_page');

            $dataQuery = AuditTrail::select('audit_trails.*');


             // Filter All Column
             if($request->filter_allcolumn!=''){
                $dataQuery->whereRaw('concat(audit_trails.id,audit_trails.user_id,audit_trails.pages,audit_trails.activity,audit_trails.created_at) LIKE ?','%'.$request->filter_allcolumn.'%');
            }

             // Filter User ID
             if($request->filter_id!=''){
                $dataQuery->where('audit_trails.id','LIKE',$wildCard.$request->filter_id.'%');
            }

            // Filter User ID
            if($request->filter_user_id!=''){
                $dataQuery->where('audit_trails.user_id','LIKE',$wildCard.$request->filter_user_id.'%');
            }

            // Filter Pages
            if($request->filter_pages!=''){
                $dataQuery->where('audit_trails.pages','LIKE',$wildCard.$request->filter_pages.'%');
            }

            //Filter Activity
            if($request->filter_activity!=''){
                $dataQuery->where('audit_trails.activity','LIKE',$wildCard.$request->filter_activity.'%');
            }

             //Filter Created At
             if($request->filter_created_at!=''){
                $dataQuery->where('audit_trails.created_at','LIKE',$wildCard.$request->filter_created_at.'%');
            }

            //Filter only_this_user_id
             if($request->filter_only_this_user_id!=''){
                $dataQuery->where('audit_trails.user_id','=',$request->filter_only_this_user_id);
            }

             //Filter Created At (from date)
             if($request->filter_from_date!=''){
                $dataQuery->where('audit_trails.created_at','>=',$request->filter_from_date." 00:00:00");
            }

            //Filter Created At (to date)
             if($request->filter_to_date!=''){
                $dataQuery->where('audit_trails.created_at','<=',$request->filter_to_date." 23:59:59");
            }

             //Apply Sorting Mechanism Begin ----------------------------------------------//

             //Sort by ID
             if($request->sort_id){
                $dataQuery->orderBy('audit_trails.id',($request->sort_id==1) ? "asc" : "desc");
            }
            //Sort by User Id
            if($request->sort_user_id){
                $dataQuery->orderBy('audit_trails.user_id',($request->sort_user_id==1) ? "asc" : "desc");
            }

            if($request->sort_pages){
                $dataQuery->orderBy('audit_trails.pages',($request->sort_pages==1) ? "asc" : "desc");
            }

            //Sort Activity
            if($request->sort_activity){
                $dataQuery->orderBy('audit_trails.activity',($request->sort_activity==1) ? "asc" : "desc");
            }

             //Sort by Created_at
             if($request->sort_created_at){
                $dataQuery->orderBy('audit_trails.created_at',($request->sort_created_at==1) ? "asc" : "desc");
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
                    SystemMessage::Messsage=>SystemMessage::AuditTrailNoRecordFound,
                    SystemMessage::Success=>false],
                    HttpStatusCode::AuditTrailErrorNotFound);
            }

            //Check if this request is for EXPORT. If export_to is NOT '' means YES then export the data to target format
            if($request->export_to!='')
            {
                switch (strtoupper($request->export_to)){
                    case FileName::EXCELFile:
                        return (new AuditTrailExport($dataResult))->download(FileName::AuditTrailExport.FileName::EXCELFileFormat, \Maatwebsite\Excel\Excel::XLSX,null);
                        break;
                    case FileName::CSVFile:
                        return (new AuditTrailExport($dataResult))->download(FileName::AuditTrailExport.FileName::CSVFileFormat, \Maatwebsite\Excel\Excel::CSV,null);
                        break;
                    case FileName::PDFFile:
                        return (new AuditTrailExport($dataResult))->download(FileName::AuditTrailExport.FileName::PDFFileFormat, \Maatwebsite\Excel\Excel::DOMPDF,null);
                        break;

                    default:
                        return response()->json([
                        SystemMessage::Messsage=>SystemMessage::FileFormatNotSupported,
                        SystemMessage::Success=>false],
                HttpStatusCode::AuditTrailErrorBadRequest);
                }
            }

             //Apply Sorting Mechanism End ------------------------------------------------//
             return AuditTrailResource::collection($dataResult)->additional([
                SystemMessage::Messsage=>SystemMessage::AuditTrailRecordRetrieved,
                SystemMessage::Success=>true]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAuditTrailRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAuditTrailRequest $request)
    {
        //Validate Request
       if($request->validated()){

        //Pick Up only all those data from the request
        //that are needed to save to Audit Trail Table
        $data=[
           "user_id" => $request->user_id,
           "pages" => $request->pages,
           "activity" => $request->activity,
        ];

       //Execute and return the newly created record
        try{
                //Execute create to insert the record to Audit Trail table and put in on Audit Trail object
                //so we can add additional collection element later
                $audit_trail=AuditTrail::create($data);

                //return the AuditTrailResource with additional element
                return AuditTrailResource::collection( [$audit_trail])->additional([
                    SystemMessage::Messsage=>SystemMessage::AuditTrailRecordCreated,
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
     * @param  \App\Models\AuditTrail  $auditTrail
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{

            $audit_trail=AuditTrail::find($id);

                if(is_null($audit_trail)){
                    return response()->json([
                        SystemMessage::Messsage=>SystemMessage::AuditTrailID.$id.SystemMessage::NotFound,
                        SystemMessage::Success=>false],
                HttpStatusCode::AuditTrailErrorNotFound);
                }

            //show found record as object
            return response()->json([
                SystemMessage::Data=>AuditTrailResource::make($audit_trail) ,
                SystemMessage::Messsage=>SystemMessage::AuditTrailRecordFound,
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
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAuditTrailRequest  $request
     * @param  \App\Models\AuditTrail  $auditTrail
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAuditTrailRequest $request, $id)
    {
       //Validate Request
       if($request->validated()){

        //Pick Up only all those data from the request
        //that are needed to update to Audit Trail Table
        $data=[
            "user_id" => $request->user_id,
            "pages" => $request->pages,
            "activity" => $request->activity,
        ];

       //Find and return the found record
        try{
                $audit_trail=AuditTrail::find($id);

                if(is_null($audit_trail)){
                    return response()->json([
                        SystemMessage::Messsage=>SystemMessage::AuditTrailID.$id.SystemMessage::NotFound,
                        SystemMessage::Success=>false],
                HttpStatusCode::AuditTrailErrorNotFound);
                }

            //Execute update to update the record on Audit Trail table
            $audit_trail->update($data);

            //return the AuditTrailResource with additional element
            return AuditTrailResource::collection( [$audit_trail])->additional([
                SystemMessage::Messsage=>SystemMessage::AuditTrailRecordUpdated,
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
     * @param  \App\Models\AuditTrail  $auditTrail
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            //is not yet been used as referrence by Audit Trail record
            $audit_trail=AuditTrail::where('id','=',$id)->first();


            if(!is_null($audit_trail)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::AuditTrailCanNotDelete.$id.SystemMessage::AlreadyBeenUsed,
                    SystemMessage::Success=>false],
            HttpStatusCode::AuditTrailErrorNotFound);
            }

            $audit_trail=AuditTrail::find($id);

            if(is_null($audit_trail)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::AuditTrailID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
            HttpStatusCode::AuditTrailErrorNotFound);
            }

            //Execute Delete and return the deleted record
            $audit_trail->delete();

            //return the AuditTrailResource with additional element
            return AuditTrailResource::collection( [$audit_trail])->additional([
                SystemMessage::Messsage=>SystemMessage::AuditTrailRecordDeleted,
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

        $audit_trail=AuditTrail::withTrashed()->find($id);

            if(is_null($audit_trail)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::AuditTrailID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
                    HttpStatusCode::AuditTrailErrorNotFound);
            }

            $audit_trail=AuditTrail::withTrashed()->where('id',$id)->restore();
            $audit_trail=AuditTrail::find($id);

            //return the AuditTrailResource with additional element
            return AuditTrailResource::collection( [$audit_trail])->additional([
            SystemMessage::Messsage=>SystemMessage::AuditTrailRecordRestored,
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

            $audit_trailActiveRecords=AuditTrail::count();
            $audit_trailSoftDeletedRecords=AuditTrail::onlyTrashed()->count();
            $audit_trailTotalRecords= $audit_trailActiveRecords+$audit_trailSoftDeletedRecords;

            return response()->json([
            SystemMessage::Data=>array(SystemMessage::ActiveRecords=>$audit_trailActiveRecords,
                                       SystemMessage::SoftDeletedRecords=>$audit_trailSoftDeletedRecords,
                                       SystemMessage::TotalRecords=>$audit_trailTotalRecords),
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
