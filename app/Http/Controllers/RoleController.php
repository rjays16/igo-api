<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Enums\FileName;
use App\Enums\SystemMessage;
use App\Enums\HttpStatusCode;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Requests\ViewRoleRequest;
use App\Exports\RoleExport;
use App\Http\Resources\RoleResource;
class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ViewRoleRequest $request)
    {
        if($request->validated()){

            //extract page size
            $per_page=$request->input('per_page');
            
            $dataQuery = Role::select('roles.*');

             // Filter All Column
             if($request->filter_allcolumn!=''){
                $dataQuery->whereRaw('concat(roles.id,roles.role,roles.description,roles.permission) LIKE ?','%'.$request->filter_allcolumn.'%');
            }

            // Filter User ID
            if($request->filter_id!=''){
                $dataQuery->where('roles.id','LIKE','%'.$request->filter_id.'%');
            }

            // Filter Role
            if($request->filter_role!=''){
                $dataQuery->where('roles.roles','LIKE','%'.$request->filter_role.'%');
            }

            // Filter Description
            if($request->filter_description!=''){
                $dataQuery->where('roles.description','LIKE','%'.$request->filter_description.'%');
            }

             // Filter Description
             if($request->filter_permission!=''){
                $dataQuery->where('roles.permission','LIKE','%'.$request->filter_permission.'%');
            }

            //Filter Created At
            if($request->filter_created_at!=''){
                $dataQuery->where('roles.created_at','LIKE','%'.$request->filter_created_at.'%');
            }
             //Apply Sorting Mechanism Begin ----------------------------------------------//

             //Sort by ID
             if($request->sort_id){
                $dataQuery->orderBy('roles.id',($request->sort_id==1) ? "asc" : "desc");
            }
            //Sort by Role
            if($request->sort_role){
                $dataQuery->orderBy('roles.role',($request->sort_role==1) ? "asc" : "desc");
            }

            //Sort Description
            if($request->sort_description){
                $dataQuery->orderBy('roles.description',($request->sort_description==1) ? "asc" : "desc");
            }

            //Sort Permission
            if($request->sort_permission){
                $dataQuery->orderBy('roles.permission',($request->sort_permission==1) ? "asc" : "desc");
            }

             //Sort by Created_at
             if($request->sort_created_at){
                $dataQuery->orderBy('roles.created_at',($request->sort_created_at==1) ? "asc" : "desc");
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
                    SystemMessage::Messsage=>SystemMessage::RoleNoRecordFound,
                    SystemMessage::Success=>false],
                    HttpStatusCode::RoleErrorNotFound);
            }

            //Check if this request is for EXPORT. If export_to is NOT '' means YES then export the data to target format
            if($request->export_to!='')
            {
                switch (strtoupper($request->export_to)){
                    case FileName::EXCELFile:
                        return (new RoleExport($dataResult))->download(FileName::RoleExport.FileName::EXCELFileFormat, \Maatwebsite\Excel\Excel::XLSX,null);
                        break;
                    case FileName::CSVFile:
                        return (new RoleExport($dataResult))->download(FileName::RoleExport.FileName::CSVFileFormat, \Maatwebsite\Excel\Excel::CSV,null);
                        break;
                    case FileName::PDFFile:
                        return (new RoleExport($dataResult))->download(FileName::RoleExport.FileName::PDFFileFormat, \Maatwebsite\Excel\Excel::DOMPDF,null);
                        break;

                    default:
                        return response()->json([
                        SystemMessage::Messsage=>SystemMessage::FileFormatNotSupported,
                        SystemMessage::Success=>false],
                HttpStatusCode::RoleErrorBadRequest);
                }
            }

             //Apply Sorting Mechanism End ------------------------------------------------//
             return RoleResource::collection($dataResult)->additional([
                SystemMessage::Messsage=>SystemMessage::RoleRecordRetrieved,
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
     * @param  \App\Http\Requests\StoreRoleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRoleRequest $request)
    {
         //Validate Request
       if($request->validated()){

        //Pick Up only all those data from the request
        //that are needed to save to role Table
        $data=[
            "role" => $request->role,
            "description" => $request->description,
            "permission" => $request->permission,
            "created_at" => $request->created_at,
        ];

       //Execute and return the newly created record
        try{
                //Execute create to insert the record to role table and put in on role object
                //so we can add additional collection element later
                $role=Role::create($data);

                //return the RoleResource with additional element
                return RoleResource::collection( [$role])->additional([
                    SystemMessage::Messsage=>SystemMessage::RoleRecordCreated,
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
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{

            $role=Role::find($id);

                if(is_null($role)){
                    return response()->json([
                        SystemMessage::Messsage=>SystemMessage::RoleID.$id.SystemMessage::NotFound,
                        SystemMessage::Success=>false],
                HttpStatusCode::RoleErrorNotFound);
                }

            //show found record as object
            return response()->json([
                SystemMessage::Data=>RoleResource::make($role) ,
                SystemMessage::Messsage=>SystemMessage::RoleNoRecordFound,
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
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRoleRequest  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRoleRequest $request, $id)
    {
        //Validate Request
        if($request->validated()){

            //Pick Up only all those data from the request
            //that are needed to update to Role Table
            $data=[
                "role" => $request->role,
                "permission" => $request->permission,
                "description" => $request->description,
            ];

           //Find and return the found record
            try{
                    $role=Role::find($id);

                    if(is_null($role)){
                        return response()->json([
                            SystemMessage::Messsage=>SystemMessage::RoleID.$id.SystemMessage::NotFound,
                            SystemMessage::Success=>false],
                    HttpStatusCode::RoleErrorNotFound);
                    }

                //Execute update to update the record on role table
                $role->update($data);

                //return the RoleResource with additional element
                return RoleResource::collection( [$role])->additional([
                    SystemMessage::Messsage=>SystemMessage::RoleRecordUpdated,
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
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            //is not yet been used as referrence by role record
            $role=Role::where('id','=',$id)->first();


            if(!is_null($role)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::RoleCanNotDelete.$id.SystemMessage::AlreadyBeenUsed,
                    SystemMessage::Success=>false],
            HttpStatusCode::RoleErrorNotFound);
            }

            $role=Role::find($id);

            if(is_null($role)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::RoleID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
            HttpStatusCode::RoleErrorNotFound);
            }

            //Execute Delete and return the deleted record
            $role->delete();

            //return the RoleResource with additional element
            return RoleResource::collection( [$role])->additional([
                SystemMessage::Messsage=>SystemMessage::RoleRecordDeleted,
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

        $role=Role::withTrashed()->find($id);

            if(is_null($role)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::RoleID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
                    HttpStatusCode::RoleErrorNotFound);
            }

            $role=Role::withTrashed()->where('id',$id)->restore();
            $role=Role::find($id);

            //return the RoleResource with additional element
            return RoleResource::collection( [$role])->additional([
            SystemMessage::Messsage=>SystemMessage::RoleRecordRestored,
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

            $roleActiveRecords=Role::count();
            $roleSoftDeletedRecords=Role::onlyTrashed()->count();
            $roleTotalRecords= $roleActiveRecords+$roleSoftDeletedRecords;


            return response()->json([
            SystemMessage::Data=>array(SystemMessage::ActiveRecords=>$roleActiveRecords,
                                       SystemMessage::SoftDeletedRecords=>$roleSoftDeletedRecords,
                                       SystemMessage::TotalRecords=>$roleTotalRecords),
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
