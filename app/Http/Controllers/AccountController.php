<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Requests\ViewAccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Transaction;
use Illuminate\Database\QueryException;
use App\Enums\SystemMessage;
use App\Enums\HttpStatusCode;
use App\Enums\FileName;
use App\Exports\AccountExport;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ViewAccountRequest $request)
    {
         //Validate first all data being passed
         if($request->validated()){

             //Let's build our queries

             $dataQuery=Account::select(['accounts.*','statuses.status'])
             ->leftJoin('statuses','accounts.status_id','=','statuses.id');
             //->join('terms','accounts.term_id','=','terms.id');


            //$dataQuery=Account::select('accounts.*');

             $wildCard=($request->filter_activatewildcard==1) ? '%' : '';

             //Check for Filters Begin -------------------------------------------------------//
            //dd($request->filter_allcolumn);
            //Filter All Column
            if($request->filter_allcolumn!=''){
                $dataQuery->whereraw('concat(accounts.id,accounts.client_id,statuses.status,accounts.creditor_id,accounts.acct_description,accounts.acct_number,accounts.debtor_id,accounts.current_rate,accounts.note,accounts.origin_date,accounts.tag,accounts.created_at) LIKE ?','%'.$request->filter_allcolumn.'%');
            }

            //Filter ID
            if($request->filter_id!=''){
                $dataQuery->where('accounts.id','LIKE',$wildCard.$request->filter_id.'%');
            }

            //Filter Client ID
            if($request->filter_client_id!=''){
                $dataQuery->where('accounts.client_id','LIKE',$wildCard.$request->filter_client_id.'%');
            }

            //Filter status
            if($request->filter_status!=''){
                $dataQuery->where('statuses.status','LIKE',$wildCard.$request->filter_status.'%');
            }

            //Filter creditor ID
            if($request->filter_creditor_id!=''){
                $dataQuery->where('accounts.creditor_id','LIKE',$wildCard.$request->filter_creditor_id.'%');
            }

            //Filter acct_description
            if($request->filter_acct_description!=''){
                $dataQuery->where('accounts.acct_description','LIKE',$wildCard.$request->filter_acct_description.'%');
            }

            //Filter acct_number
            if($request->filter_acct_number!=''){
                $dataQuery->where('accounts.acct_number','LIKE',$wildCard.$request->filter_acct_number.'%');
            }

            //Filter debtor_id
            if($request->filter_debtor_id!=''){
                $dataQuery->where('accounts.debtor_id','LIKE',$wildCard.$request->filter_debtor_id.'%');
            }

            //Filter term rate
            if($request->filter_rate!=''){
                $dataQuery->where('accounts.current_rate','LIKE',$wildCard.$request->filter_rate.'%');
            }

            //Filter note
            if($request->filter_note!=''){
                $dataQuery->where('accounts.note','LIKE',$wildCard.$request->filter_note.'%');
            }

            //Filter origin_date
            if($request->filter_origin_date!=''){
                $dataQuery->where('accounts.origin_date','LIKE',$wildCard.$request->filter_origin_date.'%');
            }

            //Filter tag
            if($request->filter_tag!=''){
                $dataQuery->where('accounts.tag','LIKE',$wildCard.$request->filter_tag.'%');
            }

            //Filter created_at
            if($request->filter_created_at!=''){
                $dataQuery->where('accounts.created_at','LIKE',$wildCard.$request->filter_created_at.'%');
            }


             //Check for Filters End -------------------------------------------------------//

             //Apply Sorting Mechanism Begin ----------------------------------------------//

             //Sort by ID
             if($request->sort_id){
                 $dataQuery->orderBy('accounts.id',($request->sort_id==1) ? "asc" : "desc");
             }

             //Sort by client_id
             if($request->sort_client_id){
                 $dataQuery->orderBy('accounts.client_id',($request->sort_client_id==1) ? "asc" : "desc");
             }

             //Sort by status
             if($request->sort_status){
                 $dataQuery->orderBy('statuses.status',($request->sort_status==1) ? "asc" : "desc");
             }

             //Sort by creditor_id
             if($request->sort_creditor_id){
                 $dataQuery->orderBy('accounts.creditor_id',($request->sort_creditor_id==1) ? "asc" : "desc");
             }

             //Sort by acct_description
             if($request->sort_acct_description){
                 $dataQuery->orderBy('accounts.acct_description',($request->sort_acct_description==1) ? "asc" : "desc");
             }

             //Sort by acct_number
             if($request->sort_acct_number){
                 $dataQuery->orderBy('accounts.acct_number',($request->sort_acct_number==1) ? "asc" : "desc");
             }

             //Sort by debtor_id
             if($request->sort_debtor_id){
                 $dataQuery->orderBy('accounts.debtor_id',($request->sort_debtor_id==1) ? "asc" : "desc");
             }

             //Sort by term rate
             if($request->sort_rate){
                 $dataQuery->orderBy('accounts.current_rate',($request->sort_rate==1) ? "asc" : "desc");
             }

             //Sort by note
             if($request->sort_note){
                 $dataQuery->orderBy('accounts.note',($request->sort_note==1) ? "asc" : "desc");
             }

             //Sort by origin_date
             if($request->sort_origin_date){
                 $dataQuery->orderBy('accounts.origin_date',($request->sort_origin_date==1) ? "asc" : "desc");
             }

              //Sort by tag
              if($request->sort_tag){
                 $dataQuery->orderBy('accounts.tag',($request->sort_tag==1) ? "asc" : "desc");
             }

             //Sort by created_at
             if($request->sort_created_at){
                 $dataQuery->orderBy('accounts.created_at',($request->sort_created_at==1) ? "asc" : "desc");
             }


             //Apply Sorting Mechanism End -----------------------------------------==-----//


             try{

             //Lastly Execute Query and Paginate the result
             $dataResult=$dataQuery->Simplepaginate($request->per_page);

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
                    SystemMessage::Messsage=>SystemMessage::AccountNoRecordFound,
                    SystemMessage::Success=>false],
            HttpStatusCode::ClientErrorNotFound);
            }

              //Check if this request is for EXPORT. If export_to is NOT '' means YES then export the data to target format
              if($request->export_to!='')
              {
                  switch (strtoupper($request->export_to)){
                      case FileName::EXCELFile:
                          return (new AccountExport($dataResult))->download(FileName::AccountExportFile.FileName::EXCELFileFormat, \Maatwebsite\Excel\Excel::XLSX,null);
                          break;
                      case FileName::CSVFile:
                          return (new AccountExport($dataResult))->download(FileName::AccountExportFile.FileName::CSVFileFormat, \Maatwebsite\Excel\Excel::CSV,null);
                          break;
                      case FileName::PDFFile:
                          return (new AccountExport($dataResult))->download(FileName::AccountExportFile.FileName::PDFFileFormat, \Maatwebsite\Excel\Excel::DOMPDF,null);
                          break;

                      default:
                          return response()->json([
                          SystemMessage::Messsage=>SystemMessage::FileFormatNotSupported,
                          SystemMessage::Success=>false],
                  HttpStatusCode::ClientErrorBadRequest);
                  }
              }

             return AccountResource::collection($dataResult)->additional([
                SystemMessage::Messsage=>SystemMessage::AccountRecordRetrieved,
                SystemMessage::Success=>true]);

         }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAccountRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAccountRequest $request)
    {
        //Validate Request
        if($request->validated()){

            $loanNumber=Account::withTrashed(true)->where('creditor_id','=',$request->creditor_id)->count()+1;
            //dd($loanNumber);
            //Pick Up only all those data from the request
            //that are needed to save to account Table
            $data=[
                //"client_id" => $request->client_id,
                "status_id" => $request->status_id,
                //"status" => $request->status,
                "creditor_id"=>$request->creditor_id,
                "acct_description" => $request->acct_description.SystemMessage::Loan.$loanNumber,
                "acct_number" => $loanNumber,     // the value of this field is determine by the system
                "debtor_id" => $request->debtor_id,
                "term_id" => 1,               // the value of this field is determine by the system
                "current_rate" => $request->rate,
                "note"=>$request->note,
                "origin_date"=>$request->origin_date,
                "tag" =>$request->tag,
            ];

           //Execute and return the newly created record
            try{
                    //Execute create to insert the record to account table and put in on account object
                    //so we can add additional collection element later
                    $account=Account::create($data);

                    //return the ClientResource with additional element
                    return AccountResource::collection( [$account])->additional([
                        SystemMessage::Messsage=>SystemMessage::AccountRecordCreated,
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
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{


             $dataQuery=Account::select(['accounts.*','clients.first_name','clients.last_name'])
             ->leftJoin('clients','accounts.creditor_id','=','clients.id');
             $account=$dataQuery->where('accounts.id','=',$id)->first();
           // dd($account);

                if(is_null($account)){
                    return response()->json([
                        SystemMessage::Messsage=>SystemMessage::AccountID.$id.SystemMessage::NotFound,
                        SystemMessage::Success=>false],
                HttpStatusCode::ClientErrorNotFound);
                }
                //dd('pasar');

            //show found record as object
            return response()->json([
                SystemMessage::Data=>AccountResource::make($account) ,
                SystemMessage::Messsage=>SystemMessage::AccountRecordFound,
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
     * @param  \App\Http\Requests\UpdateAccountRequest  $request
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAccountRequest $request, $id)
    {
         //Validate Request
         if($request->validated()){

            //Pick Up only all those data from the request
            //that are needed to update to Account Table
            $data=[

                "status_id" => $request->status_id,
                //"status" => $request->status,
                //"creditor_id" => $request->creditor_id,
                "acct_description" => $request->acct_description,
                //"acct_number" => $request->acct_number,       //do not allow this to be updated as it is the system the determines this
                "debtor_id" => $request->debtor_id,
                //"term_id" => $request->term_id,               //term_id and current_rate will be updated whenever there is an
                //"current_rate" => $request->current_rate,     // update made on the terms table for this account.
                "note"=>$request->note,
                "origin_date"=>$request->origin_date,
                "tag" =>$request->tag,
            ];

           //Find and return the found record
            try{
                    $account=Account::find($id);

                    if(is_null($account)){
                        return response()->json([
                            SystemMessage::Messsage=>SystemMessage::AccountID.$id.SystemMessage::NotFound,
                            SystemMessage::Success=>false],
                    HttpStatusCode::ClientErrorNotFound);
                    }

                //Execute update to update the record on accounts table
                $account->update($data);

                //show updated record as object
                return response()->json([
                    SystemMessage::Data=>AccountResource::make($account),
                    SystemMessage::Messsage=>SystemMessage::AccountRecordUpdated,
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
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            //Before delete make sure to check first if this account ID
            //is not yet been used as referrence by transactions record
            $transaction=Transaction::where('account_id','=',$id)->first();


            if(!is_null($transaction)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::AccountCanNotDelete.$id.SystemMessage::AlreadyBeenUsed,
                    SystemMessage::Success=>false],
            HttpStatusCode::ClientErrorNotFound);
            }

            $account=Account::find($id);

            if(is_null($account)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::AccountID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
            HttpStatusCode::ClientErrorNotFound);
            }

            //Execute Delete and return the deleted record
            $account->delete();

            //show deleted record as object
            return response()->json([
                SystemMessage::Data=>AccountResource::make($account),
                SystemMessage::Messsage=>SystemMessage::AccountRecordDeleted,
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

        $account=Account::withTrashed()->find($id);

            if(is_null($account)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::AccountID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
                    HttpStatusCode::ClientErrorNotFound);
            }

            $client=Account::withTrashed()->where('id',$id)->restore();
            $client=Account::find($id);

            //show undeleted record as object
            return response()->json([
                SystemMessage::Data=>AccountResource::make($account),
                SystemMessage::Messsage=>SystemMessage::AccountRecordRestored,
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

            $accountActiveRecords=Account::count();
            $accountSoftDeletedRecords=Account::onlyTrashed()->count();
            $accountTotalRecords=$accountActiveRecords+$accountSoftDeletedRecords;


            //return the AccountResource with additional element
            return response()->json([
            SystemMessage::Data=>array(SystemMessage::ActiveRecords=>$accountActiveRecords,
                                       SystemMessage::SoftDeletedRecords=>$accountSoftDeletedRecords,
                                       SystemMessage::TotalRecords=>$accountTotalRecords),
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
