<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Requests\ViewTransactionRequest;
use Illuminate\Database\QueryException;
use App\Enums\HttpStatusCode;
use App\Enums\SystemMessage;
use App\Http\Resources\TransactionResource;
use App\Enums\FileName;
use App\Exports\TransactionExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TransactionController extends Controller
{

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ViewTransactionRequest $request)
    {
         //Validate first all data being passed
         if($request->validated()){

             //Let's build our queries

             $dataQuery=Transaction::select(['transactions.*','accounts.acct_description','transaction_types.trans_type'])
             ->leftJoin('accounts','transactions.account_id','=','accounts.id')
             ->leftJoin('transaction_types','transactions.trans_type_id','=','transaction_types.id');

            //$dataQuery=Transaction::select('transactions.*');

             $wildCard=($request->filter_activatewildcard==1) ? '%' : '';

             //Check for Filters Begin -------------------------------------------------------//
            //dd($request->filter_allcolumn);
            //Filter All Column
            if($request->filter_allcolumn!=''){
                $dataQuery->whereraw('concat(transactions.id,transactions.account_id,accounts.acct_description,transactions.effective_date,transaction_types.trans_type,transactions.memo,transactions.amount,transactions.entry_date,transactions.created_at) LIKE ?','%'.$request->filter_allcolumn.'%');
            }

            //Filter ID
            if($request->filter_id!=''){
                $dataQuery->where('transactions.id','LIKE',$wildCard.$request->filter_id.'%');
            }

            //Filter account_id
            if($request->filter_account_id!=''){
                $dataQuery->where('transactions.account_id','LIKE',$wildCard.$request->filter_account_id.'%');
            }

            //Filter acct_description
            if($request->filter_acct_description!=''){
                $dataQuery->where('accounts.acct_description','LIKE',$wildCard.$request->filter_acct_description.'%');
            }

            //Filter effective_date
            if($request->filter_effective_date!=''){
                $dataQuery->where('transactions.effective_date','LIKE',$wildCard.$request->filter_effective_date.'%');
            }

            //Filter trans_type
            if($request->filter_trans_type!=''){
                $dataQuery->where('transaction_types.trans_type','LIKE',$wildCard.$request->filter_trans_type.'%');
            }

            //Filter memo
            if($request->filter_memo!=''){
                $dataQuery->where('transactions.memo','LIKE',$wildCard.$request->filter_memo.'%');
            }

            //Filter amount
            if($request->filter_amount!=''){
                $dataQuery->where('transactions.amount','LIKE',$wildCard.$request->filter_amount.'%');
            }

            //Filter entry_date
            if($request->filter_entry_date!=''){
                $dataQuery->where('transactions.entry_date','LIKE',$wildCard.$request->filter_entry_date.'%');
            }

            //Filter created_at
            if($request->filter_created_at!=''){
                $dataQuery->where('transactions.created_at','LIKE',$wildCard.$request->filter_created_at.'%');
            }

             //Check for Filters End -------------------------------------------------------//

             //Apply Sorting Mechanism Begin ----------------------------------------------//

             //Sort by ID
             if($request->sort_id){
                 $dataQuery->orderBy('transactions.id',($request->sort_id==1) ? "asc" : "desc");
             }

             //Sort by account_id
             if($request->sort_account_id){
                 $dataQuery->orderBy('transactions.account_id',($request->sort_account_id==1) ? "asc" : "desc");
             }

             //Sort by acct_description
             if($request->sort_acct_description){
                 $dataQuery->orderBy('accounts.acct_description',($request->sort_acct_description==1) ? "asc" : "desc");
             }

             //Sort by effective_date
             if($request->sort_effective_date){
                 $dataQuery->orderBy('transactions.effective_date',($request->sort_effective_date==1) ? "asc" : "desc");
             }

             //Sort by trans_type
             if($request->sort_trans_type){
                 $dataQuery->orderBy('transaction_types.trans_type',($request->sort_trans_type==1) ? "asc" : "desc");
             }

             //Sort by memo
             if($request->sort_memo){
                 $dataQuery->orderBy('transactions.memo',($request->sort_memo==1) ? "asc" : "desc");
             }

             //Sort by amount
             if($request->sort_amount){
                 $dataQuery->orderBy('transactions.amount',($request->sort_amount==1) ? "asc" : "desc");
             }

             //Sort by entry_date
             if($request->sort_entry_date){
                 $dataQuery->orderBy('transactions.entry_date',($request->sort_entry_date==1) ? "asc" : "desc");
             }

             //Sort by created_at
             if($request->sort_created_at){
                 $dataQuery->orderBy('transactions.created_at',($request->sort_created_at==1) ? "asc" : "desc");
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
                    SystemMessage::Messsage=>SystemMessage::TransactionNoRecordFound,
                    SystemMessage::Success=>false],
            HttpStatusCode::ClientErrorNotFound);
            }

              //Check if this request is for EXPORT. If export_to is NOT '' means YES then export the data to target format
              if($request->export_to!='')
              {
                  switch (strtoupper($request->export_to)){
                      case FileName::EXCELFile:
                          return (new TransactionExport($dataResult))->download(FileName::TransactionExportFile.FileName::EXCELFileFormat, \Maatwebsite\Excel\Excel::XLSX,null);
                          break;
                      case FileName::CSVFile:
                          return (new TransactionExport($dataResult))->download(FileName::TransactionExportFile.FileName::CSVFileFormat, \Maatwebsite\Excel\Excel::CSV,null);
                          break;
                      case FileName::PDFFile:
                          return (new TransactionExport($dataResult))->download(FileName::TransactionExportFile.FileName::PDFFileFormat, \Maatwebsite\Excel\Excel::DOMPDF,null);
                          break;

                      default:
                          return response()->json([
                          SystemMessage::Messsage=>SystemMessage::FileFormatNotSupported,
                          SystemMessage::Success=>false],
                  HttpStatusCode::ClientErrorBadRequest);
                  }
              }

             return TransactionResource::collection($dataResult)->additional([
                SystemMessage::Messsage=>SystemMessage::TransactionRecordRetrieved,
                SystemMessage::Success=>true]);

         }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAccountRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTransactionRequest $request)
    {
        //Validate Request
        if($request->validated()){

            //Pick Up only all those data from the request
            //that are needed to save to account Table
            $data=[
                //"client_id" => $request->client_id,
                "account_id" => $request->account_id,
                "effective_date"=>$request->effective_date,
                "trans_type_id" => $request->trans_type_id,
                "memo" => $request->memo,
                "amount" => $request->amount,
                "entry_date"=>$request->entry_date,
            ];

           //Execute and return the newly created record
            try{
                    //Execute create to insert the record to account table and put in on account object
                    //so we can add additional collection element later
                    $transaction=Transaction::create($data);

                    //return the ClientResource with additional element
                    return TransactionResource::collection( [$transaction])->additional([
                        SystemMessage::Messsage=>SystemMessage::TransactionRecordCreated,
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

            $transaction=Transaction::find($id);

            if(is_null($transaction)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::TransactionID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
            HttpStatusCode::ClientErrorNotFound);
            }

            //show found record as object
            return response()->json([
                SystemMessage::Data=>TransactionResource::make($transaction) ,
                SystemMessage::Messsage=>SystemMessage::TransactionRecordFound,
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
    public function update(UpdateTransactionRequest $request, $id)
    {
         //Validate Request
         if($request->validated()){

            //Pick Up only all those data from the request
            //that are needed to update to Account Table
            $data=[
                "account_id" => $request->account_id,
                "effective_date"=>$request->effective_date,
                "trans_type_id" => $request->trans_type_id,
                "memo" => $request->memo,
                "amount" => $request->amount,
                "entry_date"=>$request->entry_date,
            ];

           //Find and return the found record
            try{
                    $transaction=Transaction::find($id);

                    if(is_null($transaction)){
                        return response()->json([
                            SystemMessage::Messsage=>SystemMessage::TransactionID.$id.SystemMessage::NotFound,
                            SystemMessage::Success=>false],
                    HttpStatusCode::ClientErrorNotFound);
                    }

                //Execute update to update the record on accounts table
                $transaction->update($data);

                //show updated record as object
                return response()->json([
                    SystemMessage::Data=>TransactionResource::make($transaction),
                    SystemMessage::Messsage=>SystemMessage::TransactionRecordUpdated,
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
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAccountRequest  $request
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function nullify($id)
    {
        try{
            //Before delete make sure to check first if this account ID
            //is not yet been used as referrence by transactions record
            $transaction=Transaction::find($id);


            if(is_null($transaction)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::TransactionID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
            HttpStatusCode::ClientErrorNotFound);
            }

            //Nullify and return the nullified record
            $transaction->update(['amount'=>0]);

            //show deleted record as object
            return response()->json([
                SystemMessage::Data=>TransactionResource::make($transaction),
                SystemMessage::Messsage=>SystemMessage::TransactionRecordNullified,
                SystemMessage::Success=>true]);

        } catch(QueryException $e){ // Catch all Query Errors
           return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
        } catch(\Exception $e){     // Catch all General Errors
           return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
        }catch(\Error $e){          // Catch all Php Errors
           return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
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
            $transaction=Transaction::find($id);


            if(is_null($transaction)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::TransactionID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
            HttpStatusCode::ClientErrorNotFound);
            }

            //Execute Delete and return the deleted record
            $transaction->delete();

            //show deleted record as object
            return response()->json([
                SystemMessage::Data=>TransactionResource::make($transaction),
                SystemMessage::Messsage=>SystemMessage::TransactionRecordDeleted,
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


        $transaction=Transaction::withTrashed()->find($id);

            if(is_null($transaction)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::TransactionID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
                    HttpStatusCode::ClientErrorNotFound);
            }

            $transaction=Transaction::withTrashed()->where('id',$id)->restore();
            $transaction=Transaction::find($id);

            //show undeleted record as object
            return response()->json([
                SystemMessage::Data=>TransactionResource::make($transaction),
                SystemMessage::Messsage=>SystemMessage::TransactionRecordRestored,
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

            $accountActiveRecords=Transaction::count();
            $accountSoftDeletedRecords=Transaction::onlyTrashed()->count();
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



    public function previous(ViewTransactionRequest $request,$id)
    {
         //Validate first all data being passed
         if($request->validated()){

             //Let's build our queries
            //get transaction balance of the current month;
            $start = Carbon::now()->startOfMonth()->toDateString();
            $end = Carbon::now()->endOfMonth()->toDateString();

            $balancePerVault=DB::table('transactions')
                                 ->select(DB::raw('sum(transactions.amount) as amount'))
                                 ->where('transactions.entry_date','>=',$start )
                                 ->where('transactions.entry_date','<=',$end )
                                 ->where('transactions.account_id','=',$id)
                                 ->whereNull('transactions.deleted_at')->get();

            //$dataQuery=Transaction::select('transactions.*');

            $dataQuery=Transaction::select(['transactions.*','accounts.acct_description','transaction_types.trans_type'])
            ->leftJoin('accounts','transactions.account_id','=','accounts.id')
            ->leftJoin('transaction_types','transactions.trans_type_id','=','transaction_types.id');

            $dataQuery->where('transactions.account_id','=',$id);

             $wildCard=($request->filter_activatewildcard==1) ? '%' : '';

             //Check for Filters Begin -------------------------------------------------------//
            //dd($request->filter_allcolumn);
            //Filter All Column
            if($request->filter_allcolumn!=''){
                $dataQuery->whereraw('concat(transactions.id,transactions.account_id,accounts.acct_description,transactions.effective_date,transaction_types.trans_type,transactions.memo,transactions.amount,transactions.entry_date,transactions.created_at) LIKE ?','%'.$request->filter_allcolumn.'%');
            }

            //Filter ID
            if($request->filter_id!=''){
                $dataQuery->where('transactions.id','LIKE',$wildCard.$request->filter_id.'%');
            }

            //Filter account_id
            if($request->filter_account_id!=''){
                $dataQuery->where('transactions.account_id','LIKE',$wildCard.$request->filter_account_id.'%');
            }

            //Filter acct_description
            if($request->filter_acct_description!=''){
                $dataQuery->where('accounts.acct_description','LIKE',$wildCard.$request->filter_acct_description.'%');
            }

            //Filter effective_date
            if($request->filter_effective_date!=''){
                $dataQuery->where('transactions.effective_date','LIKE',$wildCard.$request->filter_effective_date.'%');
            }

            //Filter trans_type
            if($request->filter_trans_type!=''){
                $dataQuery->where('transaction_types.trans_type','LIKE',$wildCard.$request->filter_trans_type.'%');
            }

            //Filter memo
            if($request->filter_memo!=''){
                $dataQuery->where('transactions.memo','LIKE',$wildCard.$request->filter_memo.'%');
            }

            //Filter amount
            if($request->filter_amount!=''){
                $dataQuery->where('transactions.amount','LIKE',$wildCard.$request->filter_amount.'%');
            }

            //Filter entry_date
            if($request->filter_entry_date!=''){
                $dataQuery->where('transactions.entry_date','LIKE',$wildCard.$request->filter_entry_date.'%');
            }

            //Filter created_at
            if($request->filter_created_at!=''){
                $dataQuery->where('transactions.created_at','LIKE',$wildCard.$request->filter_created_at.'%');
            }

             //Check for Filters End -------------------------------------------------------//

             //Apply Sorting Mechanism Begin ----------------------------------------------//

             //Sort by ID
             if($request->sort_id){
                 $dataQuery->orderBy('transactions.id',($request->sort_id==1) ? "asc" : "desc");
             }

             //Sort by account_id
             if($request->sort_account_id){
                 $dataQuery->orderBy('transactions.account_id',($request->sort_account_id==1) ? "asc" : "desc");
             }

             //Sort by acct_description
             if($request->sort_acct_description){
                 $dataQuery->orderBy('accounts.acct_description',($request->sort_acct_description==1) ? "asc" : "desc");
             }

             //Sort by effective_date
             if($request->sort_effective_date){
                 $dataQuery->orderBy('transactions.effective_date',($request->sort_effective_date==1) ? "asc" : "desc");
             }

             //Sort by trans_type
             if($request->sort_trans_type){
                 $dataQuery->orderBy('transaction_types.trans_type',($request->sort_trans_type==1) ? "asc" : "desc");
             }

             //Sort by memo
             if($request->sort_memo){
                 $dataQuery->orderBy('transactions.memo',($request->sort_memo==1) ? "asc" : "desc");
             }

             //Sort by amount
             if($request->sort_amount){
                 $dataQuery->orderBy('transactions.amount',($request->sort_amount==1) ? "asc" : "desc");
             }

             //Sort by entry_date
             if($request->sort_entry_date){
                 $dataQuery->orderBy('transactions.entry_date',($request->sort_entry_date==1) ? "asc" : "desc");
             }

             //Sort by created_at
             if($request->sort_created_at){
                 $dataQuery->orderBy('transactions.created_at',($request->sort_created_at==1) ? "asc" : "desc");
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
                    SystemMessage::Messsage=>SystemMessage::TransactionNoRecordFound,
                    SystemMessage::Success=>false],
            HttpStatusCode::ClientErrorNotFound);
            }

             return TransactionResource::collection($dataResult)->additional([
                SystemMessage::BalancePerVault=>$balancePerVault[0]->amount,
                SystemMessage::Messsage=>SystemMessage::TransactionPreviousRecordRetrieved,
                SystemMessage::Success=>true]);

         }
    }


}
