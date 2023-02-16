<?php

namespace App\Http\Controllers;

use App\Enums\FileName;
use App\Enums\HttpStatusCode;
use App\Enums\SystemMessage;
use App\Models\Client;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Requests\ViewClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Account;
use Illuminate\Database\QueryException;
use App\Exports\ClientExport;


class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ViewClientRequest $request)
    {

        //Validate first all data being passed
        if($request->validated()){

           //extract page size
           $per_page=$request->input('per_page');

            $wildCard=($request->filter_activatewildcard==1) ? '%' : '';
            //dd($wildCard);

            //Let's build our queries

            $dataQuery=Client::select(['clients.*','organizations.organization','cities.city','client_types.client_type'])
            ->leftJoin('organizations','clients.organization_id','=','organizations.id')
            ->leftJoin('cities','clients.city_id','=','cities.id')
            ->leftJoin('client_types','clients.client_type_id','=','client_types.id');

            //$dataQuery=Client::select('clients.*');

            //Check for Filters Begin -------------------------------------------------------//

            //Filter All COlumn
            if($request->filter_allcolumn!=''){
                $dataQuery->whereraw('concat(clients.id,clients.first_name,clients.last_name,clients.gender,clients.date_of_birth,clients.email,clients.phone,organizations.organization,clients.address1,clients.address2,cities.city,clients.state,clients.zip,client_types.client_type,clients.ca_date,clients.tag,clients.note,clients.created_at) LIKE ?','%'.$request->filter_allcolumn.'%');
           }

            //Filter ID
           if($request->filter_id!=''){
                $dataQuery->where('clients.id','LIKE',$wildCard.$request->filter_id.'%');
           }

            //Filter First Name
            if($request->filter_first_name!=''){
                $dataQuery->where('clients.first_name','LIKE',$wildCard.$request->filter_first_name.'%');
            }

            //Filter Last Name
            if($request->filter_last_name!=''){
                $dataQuery->where('clients.last_name','LIKE',$wildCard.$request->filter_last_name.'%');
            }

            //Filter Gender
            if($request->filter_gender!=''){
                $dataQuery->where('clients.gender','LIKE',$wildCard.$request->filter_gender.'%');
            }

            //Filter Date of Birth
            if($request->filter_date_of_birth!=''){
                $dataQuery->where('clients.date_of_birth','LIKE',$wildCard.$request->filter_date_of_birth.'%');
            }

            //Filter Email
            if($request->filter_email!=''){
                $dataQuery->where('clients.email','LIKE',$wildCard.$request->filter_email.'%');
            }

            //Filter Phone
            if($request->filter_phone!=''){
                $dataQuery->where('clients.phone','LIKE',$wildCard.$request->filter_phone.'%');
            }

            //Filter Organization
            if($request->filter_organization!=''){
                //dd($filterEmail);
                $dataQuery->where('organizations.organization','LIKE',$wildCard.$request->filter_organization.'%');
            }


            //Filter Address1
            if($request->filter_address1!=''){
                $dataQuery->where('clients.address1','LIKE',$wildCard.$request->filter_address1.'%');
            }

            //Filter Address2
            if($request->filter_address2!=''){
                $dataQuery->where('clients.address2','LIKE',$wildCard.$request->filter_address2.'%');
            }


            //Filter City
            if($request->filter_city!=''){
                $dataQuery->where('cities.city','LIKE',$wildCard.$request->filter_city.'%');
            }

            //Filter State
            if($request->filter_state!=''){
                $dataQuery->where('clients.state','LIKE',$wildCard.$request->filter_state.'%');
            }

            //Filter Zip
            if($request->filter_zip!=''){
                $dataQuery->where('clients.zip','LIKE',$wildCard.$request->filter_zip.'%');
            }

            //Filter Client Type
            if($request->filter_client_type!=''){
                $dataQuery->where('client_types.client_type','LIKE',$wildCard.$request->filter_client_type.'%');
            }

            //Filter ca_date
            if($request->filter_ca_date!=''){
                $dataQuery->where('clients.ca_date','LIKE',$wildCard.$request->filter_ca_date.'%');
            }

            //Filter tag
            if($request->filter_tag!=''){
                $dataQuery->where('clients.tag','LIKE',$wildCard.$request->filter_tag.'%');
            }

            //Filter Note
            if($request->filter_note!=''){
                $dataQuery->where('clients.note','LIKE',$wildCard.$request->filter_note.'%');
            }

            //Filter Created_at
            if($request->filter_created_at!=''){
                $dataQuery->where('clients.created_at','LIKE',$wildCard.$request->filter_created_at.'%');
            }

            //Check for Filters End -------------------------------------------------------//

            //Apply Sorting Mechanism Begin ----------------------------------------------//

            //Sort by ID
            if($request->sort_id){
                $dataQuery->orderBy('clients.id',($request->sort_id==1) ? "asc" : "desc");
            }

            //Sort by First Name
            if($request->sort_first_name){
                $dataQuery->orderBy('clients.first_name',($request->sort_first_name==1) ? "asc" : "desc");
            }

            //Sort by Last Name
            if($request->sort_last_name){
                $dataQuery->orderBy('clients.last_name',($request->sort_last_name==1) ? "asc" : "desc");
            }

            //Sort by Gender
            if($request->sort_gender){
                $dataQuery->orderBy('clients.gender',($request->sort_gender==1) ? "asc" : "desc");
            }

            //Sort by Date of Birth
            if($request->sort_date_of_birth){
                $dataQuery->orderBy('clients.date_of_birth',($request->sort_date_of_birth==1) ? "asc" : "desc");
            }

            //Sort by Email
            if($request->sort_email){
                $dataQuery->orderBy('clients.email',($request->sort_email==1) ? "asc" : "desc");
            }

            //Sort by Phone
            if($request->sort_phone){
                $dataQuery->orderBy('clients.phone',($request->sort_phone==1) ? "asc" : "desc");
            }

            //Sort by Organization
            if($request->sort_organization){
                $dataQuery->orderBy('organizations.organization',($request->sort_organization==1) ? "asc" : "desc");
            }

            //Sort by Address1
            if($request->sort_address1){
                $dataQuery->orderBy('clients.address1',($request->sort_address1==1) ? "asc" : "desc");
            }

            //Sort by Address2
            if($request->sort_address2){
                $dataQuery->orderBy('clients.address2',($request->sort_address2==1) ? "asc" : "desc");
            }

             //Sort by City
             if($request->sort_city){
                $dataQuery->orderBy('cities.city',($request->sort_city==1) ? "asc" : "desc");
            }

            //Sort by State
            if($request->sort_state){
                $dataQuery->orderBy('clients.state',($request->sort_state==1) ? "asc" : "desc");
            }

            //Sort by Zip
            if($request->sort_zip){
                $dataQuery->orderBy('clients.zip',($request->sort_zip==1) ? "asc" : "desc");
            }

            //Sort by Client Type
            if($request->sort_client_type){
                $dataQuery->orderBy('client_types.client_type',($request->sort_client_type==1) ? "asc" : "desc");
            }

            //Sort by Ca Date
            if($request->sort_ca_date){
                $dataQuery->orderBy('clients.ca_date',($request->sort_ca_date==1) ? "asc" : "desc");
            }

            //Sort by tag
            if($request->sort_tag){
                $dataQuery->orderBy('clients.tag',($request->sort_tag==1) ? "asc" : "desc");
            }

            //Sort by Note
            if($request->sort_note){
                $dataQuery->orderBy('clients.note',($request->sort_note==1) ? "asc" : "desc");
            }

            //Sort by Created_at
            if($request->sort_created_at){
                $dataQuery->orderBy('clients.created_at',($request->sort_created_at==1) ? "asc" : "desc");
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
                    SystemMessage::Messsage=>SystemMessage::ClientNoRecordFound,
                    SystemMessage::Success=>false],
                    HttpStatusCode::ClientErrorNotFound);
            }

            //Check if this request is for EXPORT. If export_to is NOT '' means YES then export the data to target format
            if($request->export_to!='')
            {
                switch (strtoupper($request->export_to)){
                    case FileName::EXCELFile:
                        return (new ClientExport($dataResult))->download(FileName::ClientExportFile.FileName::EXCELFileFormat, \Maatwebsite\Excel\Excel::XLSX,null);
                        break;
                    case FileName::CSVFile:
                        return (new ClientExport($dataResult))->download(FileName::ClientExportFile.FileName::CSVFileFormat, \Maatwebsite\Excel\Excel::CSV,null);
                        break;
                    case FileName::PDFFile:
                        return (new ClientExport($dataResult))->download(FileName::ClientExportFile.FileName::PDFFileFormat, \Maatwebsite\Excel\Excel::DOMPDF,null);
                        break;

                    default:
                        return response()->json([
                        SystemMessage::Messsage=>SystemMessage::FileFormatNotSupported,
                        SystemMessage::Success=>false],
                HttpStatusCode::ClientErrorBadRequest);
                }
            }

            return ClientResource::collection($dataResult)->additional([
                SystemMessage::Messsage=>SystemMessage::ClientRecordRetrieved,
                SystemMessage::Success=>true]);

        }
    }

    public function all()
    {
        //Show all records including those mark deleted
        $dataResult=Client::withTrashed()->simplepaginate(100);
        return ClientResource::collection($dataResult)->additional([
            SystemMessage::Messsage=>SystemMessage::ClientRecordRetrieved,
            SystemMessage::Success=>true]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreClientRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreClientRequest $request)
    {

     //Validate Request
     if($request->validated()){

        //Pick Up only all those data from the request
        //that are needed to save to Client Table
        $data=[
            "first_name" => $request->first_name,
            "last_name" => $request->last_name,
            "gender" => $request->gender,
            "date_of_birth" => $request->date_of_birth,
            "email" => $request->email,
            "phone" => $request->phone,
            "organization_id" => $request->organization_id,
            "address1"=>$request->address1,
            "address2"=>$request->address2,
            "city_id" => $request->city_id,
            "state" => $request->state,
            "zip" => $request->zip,
            "client_type_id" => $request->client_type_id,
            "ca_date" => $request->ca_date,
            "tag" => $request->tag,
            "note" => $request->note,
        ];

       //Execute and return the newly created record
        try{
                //Execute create to insert the record to clients table and put in on client object
                //so we can add additional collection element later
                $client=Client::create($data);

                //return the ClientResource with additional element
                return ClientResource::collection( [$client])->additional([
                    SystemMessage::Messsage=>SystemMessage::ClientRecordCreated,
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

            $client=Client::find($id);

                if(is_null($client)){
                    return response()->json([
                        SystemMessage::Messsage=>SystemMessage::ClientID.$id.SystemMessage::NotFound,
                        SystemMessage::Success=>false],
                HttpStatusCode::ClientErrorNotFound);
                }

            //show found record as object
            return response()->json([
                SystemMessage::Data=>ClientResource::make($client),
                SystemMessage::Messsage=>SystemMessage::ClientRecordFound,
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
    public function update(UpdateClientRequest $request, $id)
    {
         //Validate Request
         if($request->validated()){

            //Pick Up only all those data from the request
            //that are needed to update to Client Table
            $data=[
                "first_name" => $request->first_name,
                "last_name" => $request->last_name,
                "gender" => $request->gender,
                "date_of_birth" => $request->date_of_birth,
                "email" => $request->email,
                "phone" => $request->phone,
                "organization_id" => $request->organization_id,
                "address1"=>$request->address1,
                "address2"=>$request->address2,
                "city_id" => $request->city_id,
                "state" => $request->state,
                "zip" => $request->zip,
                "client_type_id" => $request->client_type_id,
                "ca_date" => $request->ca_date,
                "tag" => $request->tag,
                "note" => $request->note,
            ];

           //Find and return the found record
            try{
                    $client=Client::find($id);

                    if(is_null($client)){
                        return response()->json([
                            SystemMessage::Messsage=>SystemMessage::ClientID.$id.SystemMessage::NotFound,
                            SystemMessage::Success=>false],
                    HttpStatusCode::ClientErrorNotFound);
                    }

                //Execute update to update the record on clients table
                $client->update($data);

                //show found record as object
                return response()->json([
                    SystemMessage::Data=>ClientResource::make($client),
                    SystemMessage::Messsage=>SystemMessage::ClientRecordUpdated,
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
            $account=Account::where('creditor_id','=',$id)->first();


            if(!is_null($account)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::ClientCanNotDelete.$id.SystemMessage::AlreadyBeenUsed,
                    SystemMessage::Success=>false],
            HttpStatusCode::ClientErrorNotFound);
            }

            $client=Client::find($id);

            if(is_null($client)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::ClientID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
            HttpStatusCode::ClientErrorNotFound);
            }

            //Execute Delete and return the deleted record
            $client->delete();


            //show deleted record as object
            return response()->json([
                SystemMessage::Data=>ClientResource::make($client),
                SystemMessage::Messsage=>SystemMessage::ClientRecordDeleted,
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

        $client=Client::withTrashed()->find($id);

            if(is_null($client)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::ClientID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
                    HttpStatusCode::ClientErrorNotFound);
            }

            $client=Client::withTrashed()->where('id',$id)->restore();
            $client=Client::find($id);

           //show undeleted record as object
            return response()->json([
                SystemMessage::Data=>ClientResource::make($client),
                SystemMessage::Messsage=>SystemMessage::ClientRecordRestored,
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

            $clientActiveRecords=Client::count();
            $clientSoftDeletedRecords=Client::onlyTrashed()->count();
            $clientTotalRecords= $clientActiveRecords+$clientSoftDeletedRecords;


            //return the ClientResource with additional element
            return response()->json([
            SystemMessage::Data=>array(SystemMessage::ActiveRecords=>$clientActiveRecords,
                                       SystemMessage::SoftDeletedRecords=>$clientSoftDeletedRecords,
                                       SystemMessage::TotalRecords=>$clientTotalRecords),
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



