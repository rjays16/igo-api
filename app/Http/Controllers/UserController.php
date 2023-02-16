<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Models\User;
use App\Exports\UserExport;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Requests\ViewUserRequest;
use Illuminate\Database\QueryException;
use App\Enums\SystemMessage;
use App\Enums\HttpStatusCode;
use App\Enums\FileName;
use App\Models\AuditTrail;

class UserController extends Controller
{
    public function index(ViewUserRequest $request)
    {

        //Validate first all data being passed
        if($request->validated()){

           //extract page size
           $per_page=$request->input('per_page');

            $wildCard=($request->filter_activatewildcard==1) ? '%' : '';
            //dd($wildCard);

            //Let's build our queries

            $dataQuery=User::select(['users.*','cities.city','roles.role'])
            ->leftjoin('cities','users.city_id','=','cities.id')
            ->leftjoin('roles','users.role_id','=','roles.id');

            //$dataQuery=User::select('users.*');

            //Check for Filters Begin -------------------------------------------------------//

            //Filter All COlumn
            if($request->filter_allcolumn!=''){
                $dataQuery->whereraw('concat(users.id,users.first_name,users.last_name,users.email,users.phone,users.address1,users.address2,cities.city,users.state,users.zip,roles.role,users.created_at) LIKE ?','%'.$request->filter_allcolumn.'%');
           }

            //Filter ID
           if($request->filter_id!=''){
                $dataQuery->where('users.id','LIKE',$wildCard.$request->filter_id.'%');
           }

            //Filter First Name
            if($request->filter_first_name!=''){
                $dataQuery->where('users.first_name','LIKE',$wildCard.$request->filter_first_name.'%');
            }

            //Filter Last Name
            if($request->filter_last_name!=''){
                $dataQuery->where('users.last_name','LIKE',$wildCard.$request->filter_last_name.'%');
            }


            //Filter Email
            if($request->filter_email!=''){
                $dataQuery->where('users.email','LIKE',$wildCard.$request->filter_email.'%');
            }

            //Filter Phone
            if($request->filter_phone!=''){
                $dataQuery->where('users.phone','LIKE',$wildCard.$request->filter_phone.'%');
            }

            //Filter Address1
            if($request->filter_address1!=''){
                $dataQuery->where('users.address1','LIKE',$wildCard.$request->filter_address1.'%');
            }

            //Filter Address2
            if($request->filter_address2!=''){
                $dataQuery->where('users.address2','LIKE',$wildCard.$request->filter_address2.'%');
            }


            //Filter City
            if($request->filter_city!=''){
                $dataQuery->where('cities.city','LIKE',$wildCard.$request->filter_city.'%');
            }

            //Filter State
            if($request->filter_state!=''){
                $dataQuery->where('users.state','LIKE',$wildCard.$request->filter_state.'%');
            }

            //Filter Zip
            if($request->filter_zip!=''){
                $dataQuery->where('users.zip','LIKE',$wildCard.$request->filter_zip.'%');
            }

            //Filter role
            if($request->filter_role!=''){
                $dataQuery->where('roles.role','LIKE',$wildCard.$request->filter_role.'%');
            }

            //Filter Created_at
            if($request->filter_created_at!=''){
                $dataQuery->where('users.created_at','LIKE',$wildCard.$request->filter_created_at.'%');
            }

            //Check for Filters End -------------------------------------------------------//

            //Apply Sorting Mechanism Begin ----------------------------------------------//

            //Sort by ID
            if($request->sort_id){
                $dataQuery->orderBy('users.id',($request->sort_id==1) ? "asc" : "desc");
            }

            //Sort by First Name
            if($request->sort_first_name){
                $dataQuery->orderBy('users.first_name',($request->sort_first_name==1) ? "asc" : "desc");
            }

            //Sort by Last Name
            if($request->sort_last_name){
                $dataQuery->orderBy('users.last_name',($request->sort_last_name==1) ? "asc" : "desc");
            }

            //Sort by Email
            if($request->sort_email){
                $dataQuery->orderBy('users.email',($request->sort_email==1) ? "asc" : "desc");
            }

            //Sort by Phone
            if($request->sort_phone){
                $dataQuery->orderBy('users.phone',($request->sort_phone==1) ? "asc" : "desc");
            }

            //Sort by Address1
            if($request->sort_address1){
                $dataQuery->orderBy('users.address1',($request->sort_address1==1) ? "asc" : "desc");
            }

            //Sort by Address2
            if($request->sort_address2){
                $dataQuery->orderBy('users.address2',($request->sort_address2==1) ? "asc" : "desc");
            }

             //Sort by City
             if($request->sort_city){
                $dataQuery->orderBy('cities.city',($request->sort_city==1) ? "asc" : "desc");
            }

            //Sort by State
            if($request->sort_state){
                $dataQuery->orderBy('users.state',($request->sort_state==1) ? "asc" : "desc");
            }

            //Sort by Zip
            if($request->sort_zip){
                $dataQuery->orderBy('users.zip',($request->sort_zip==1) ? "asc" : "desc");
            }

            //Sort by roles
            if($request->sort_role){
                $dataQuery->orderBy('roles.role',($request->sort_role==1) ? "asc" : "desc");
            }

            //Sort by Created_at
            if($request->sort_created_at){
                $dataQuery->orderBy('users.created_at',($request->sort_created_at==1) ? "asc" : "desc");
            }

            //Apply Sorting Mechanism End -----------------------------------------==-----//

            try{

            //Lastly Execute Query and Paginate the result
            $dataResult=$dataQuery->Simplepaginate($per_page);

            //dd($dataResult);
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
                    SystemMessage::Messsage=>SystemMessage::UserNoRecordFound,
                    SystemMessage::Success=>false],
                    HttpStatusCode::ClientErrorNotFound);
            }

            //Check if this request is for EXPORT. If export_to is NOT '' means YES then export the data to target format
            if($request->export_to!='')
            {
                switch (strtoupper($request->export_to)){
                    case FileName::EXCELFile:
                        return (new UserExport($dataResult))->download(FileName::UserExportFile.FileName::EXCELFileFormat, \Maatwebsite\Excel\Excel::XLSX,null);
                        break;
                    case FileName::CSVFile:
                        return (new UserExport($dataResult))->download(FileName::UserExportFile.FileName::CSVFileFormat, \Maatwebsite\Excel\Excel::CSV,null);
                        break;
                    case FileName::PDFFile:
                        return (new UserExport($dataResult))->download(FileName::UserExportFile.FileName::PDFFileFormat, \Maatwebsite\Excel\Excel::DOMPDF,null);
                        break;

                    default:
                        return response()->json([
                        SystemMessage::Messsage=>SystemMessage::FileFormatNotSupported,
                        SystemMessage::Success=>false],
                HttpStatusCode::ClientErrorBadRequest);
                }
            }

            return UserResource::collection($dataResult)->additional([
                SystemMessage::Messsage=>SystemMessage::UserRecordRetrieved,
                SystemMessage::Success=>true]);

        }
    }

    public function all()
    {
        //Show all records including those mark deleted
        $dataResult=User::withTrashed()->simplepaginate(100);
        return UserResource::collection($dataResult)->additional([
            SystemMessage::Messsage=>SystemMessage::ClientRecordRetrieved,
            SystemMessage::Success=>true]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreClientRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {

     //Validate Request
     if($request->validated()){

        //Pick Up only all those data from the request
        //that are needed to save to Client Table
        $data=[
            "first_name" => $request->first_name,
            "last_name" => $request->last_name,
            "name" => $request->first_name.' '.$request->last_name,
            "email" => $request->email,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // default password is password
            "address1"=>$request->address1,
            "address2"=>$request->address2,
            "city_id" => $request->city_id,
            "state" => $request->state,
            "zip" => $request->zip,
            "phone" => $request->phone,
            "role_id" => $request->role_id,
            "client_id" => $request->client_id
        ];

       //Execute and return the newly created record
        try{
                //Execute create to insert the record to clients table and put in on client object
                //so we can add additional collection element later
                $user=User::create($data);

                //return the ClientResource with additional element
                return UserResource::collection( [$user])->additional([
                    SystemMessage::Messsage=>SystemMessage::UserRecordCreated,
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
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{

            $user=User::find($id);

                if(is_null($user)){
                    return response()->json([
                        SystemMessage::Messsage=>SystemMessage::UserID.$id.SystemMessage::NotFound,
                        SystemMessage::Success=>false],
                HttpStatusCode::ClientErrorNotFound);
                }

            //show found record as object
            return response()->json([
                SystemMessage::Data=>UserResource::make($user),
                SystemMessage::Messsage=>SystemMessage::UserRecordFound,
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
     * @param  \App\Http\Requests\UpdateClientRequest  $request
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
         //Validate Request
         if($request->validated()){

            //Pick Up only all those data from the request
            //that are needed to update to Client Table
            $data=[
                "first_name" => $request->first_name,
                "last_name" => $request->last_name,
                "name" => $request->first_name.' '.$request->last_name,
                "email" => $request->email,
                "address1"=>$request->address1,
                "address2"=>$request->address2,
                "city_id" => $request->city_id,
                "state" => $request->state,
                "zip" => $request->zip,
                "phone" => $request->phone,
                "role_id" => $request->role_id,
                "client_id" => $request->client_id
            ];

           //Find and return the found record
            try{
                    $user=User::find($id);

                    if(is_null($user)){
                        return response()->json([
                            SystemMessage::Messsage=>SystemMessage::UserID.$id.SystemMessage::NotFound,
                            SystemMessage::Success=>false],
                    HttpStatusCode::ClientErrorNotFound);
                    }

                //Execute update to update the record on clients table
                $user->update($data);

                //show found record as object
                return response()->json([
                    SystemMessage::Data=>UserResource::make($user),
                    SystemMessage::Messsage=>SystemMessage::UserRecordUpdated,
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
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            //Before delete make sure to check first if this client ID
            //is not yet been used as referrence by accounts record
            $auditTrail=AuditTrail::where('user_id','=',$id)->first();


            if(!is_null($auditTrail)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::UserCanNotDelete.$id.SystemMessage::AlreadyBeenUsed,
                    SystemMessage::Success=>false],
            HttpStatusCode::ClientErrorNotFound);
            }

            $user=User::find($id);

            if(is_null($user)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::UserID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
            HttpStatusCode::ClientErrorNotFound);
            }

            //Execute Delete and return the deleted record
            $user->delete();


            //show deleted record as object
            return response()->json([
                SystemMessage::Data=>UserResource::make($user),
                SystemMessage::Messsage=>SystemMessage::UserRecordDeleted,
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

        $user=User::withTrashed()->find($id);

            if(is_null($user)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::UserID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
                    HttpStatusCode::ClientErrorNotFound);
            }

            $user=User::withTrashed()->where('id',$id)->restore();
            $user=User::find($id);

           //show undeleted record as object
            return response()->json([
                SystemMessage::Data=>UserResource::make($user),
                SystemMessage::Messsage=>SystemMessage::UserRecordRestored,
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

            $userActiveRecords=User::count();
            $userSoftDeletedRecords=User::onlyTrashed()->count();
            $userTotalRecords= $userActiveRecords+$userSoftDeletedRecords;


            //return the ClientResource with additional element
            return response()->json([
            SystemMessage::Data=>array(SystemMessage::ActiveRecords=>$userActiveRecords,
                                       SystemMessage::SoftDeletedRecords=>$userSoftDeletedRecords,
                                       SystemMessage::TotalRecords=>$userTotalRecords),
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
