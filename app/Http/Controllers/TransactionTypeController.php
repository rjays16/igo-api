<?php

namespace App\Http\Controllers;

use App\Models\TransactionType;
use App\Http\Requests\StoreTransactionTypeRequest;
use App\Http\Requests\UpdateTransactionTypeRequest;
use App\Http\Requests\ViewTransactionTypeRequest;
use App\Http\Resources\TransactionTypeResource;
use App\Enums\HttpStatusCode;
use App\Enums\SystemMessage;
use Illuminate\Database\QueryException;

class TransactionTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ViewTransactionTypeRequest $request)
    {
        if($request->validated()){

            //extract page size
            $per_page=$request->input('per_page');

            $dataQuery = TransactionType::select('transaction_types.*');

             // Filter All Column
             if($request->filter_allcolumn!=''){
                $dataQuery->whereRaw('concat(transaction_types.id,transaction_types.trans_type,transaction_types.description,transaction_types.created_at) LIKE ?','%'.$request->filter_allcolumn.'%');
            }

            // Filter Transaction Type
            if($request->filter_trans_type!=''){
                $dataQuery->where('transaction_types.client_type','LIKE','%'.$request->filter_trans_type.'%');
            }

            // Filter Description
            if($request->filter_description!=''){
                $dataQuery->where('transaction_types.description','LIKE','%'.$request->filter_description.'%');
            }

            //Filter Created At
            if($request->filter_created_att!=''){
                $dataQuery->where('transaction_types.created_at','LIKE','%'.$request->filter_created_at.'%');
            }

             //Apply Sorting Mechanism Begin ----------------------------------------------//

             //Sort by ID
             if($request->sort_id){
                $dataQuery->orderBy('transaction_types.id',($request->sort_id) ? "asc" : "desc");
            }
            //Sort by Trans Type
            if($request->sort_trans_type){
                $dataQuery->orderBy('transaction_types.trans_type',($request->sort_trans_type==1) ? "asc" : "desc");
            }

            //Sort Description
            if($request->sort_description){
                $dataQuery->orderBy('transaction_types.Description',($request->sort_description==1) ? "asc" : "desc");
            }

             //Sort by Created_at
             if($request->sort_created_at){
                $dataQuery->orderBy('transaction_types.created_at',($request->sort_created_at==1) ? "asc" : "desc");
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

             //Apply Sorting Mechanism End ------------------------------------------------//
             return TransactionTypeResource::collection($dataResult)->additional([
                SystemMessage::Messsage=>SystemMessage::TransactionTypeRetrieved,
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

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTransactionTypeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTransactionTypeRequest $request)
    {
        //Validate Request
        if($request->validated()){

            //Pick Up only all those data from the request
            //that are needed to save to transaction type Table
            $data=[
                "trans_type" => $request->trans_type,
                "description" => $request->description,
                "created_at" => $request->created_at,
            ];
    
           //Execute and return the newly created record
            try{
                    //Execute create to insert the record to transaction type table and put in on transaction type object
                    //so we can add additional collection element later
                    $transaction_type=TransactionType::create($data);
    
                    //return the TransactionTypeResource with additional element
                    return TransactionTypeResource::collection( [$transaction_type])->additional([
                        SystemMessage::Messsage=>SystemMessage::TransactionTypeRecordCreated,
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
     * @param  \App\Models\TransactionType  $transactionType
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{

            $transaction_type=TransactionType::find($id);

                if(is_null($transaction_type)){
                    return response()->json([
                        SystemMessage::Messsage=>SystemMessage::TransactionTypeTypeID.$id.SystemMessage::NotFound,
                        SystemMessage::Success=>false],
                HttpStatusCode::TransactionErrorNotFound);
                }

            //show found record as object
            return response()->json([
                SystemMessage::Data=>TransactionTypeResource::make($transaction_type) ,
                SystemMessage::Messsage=>SystemMessage::TransactionTypeRecordFound,
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
     * @param  \App\Models\TransactionType  $transactionType
     * @return \Illuminate\Http\Response
     */
    public function edit(TransactionType $transactionType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTransactionTypeRequest  $request
     * @param  \App\Models\TransactionType  $transactionType
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTransactionTypeRequest $request, $id)
    {
        //Validate Request
        if($request->validated()){

            //Pick Up only all those data from the request
            //that are needed to update to transaction type Table
            $data=[
                "trans_type" => $request->trans_type,
                "description" => $request->description,
            ];

           //Find and return the found record
            try{
                    $transaction_type=TransactionType::find($id);

                    if(is_null($transaction_type)){
                        return response()->json([
                            SystemMessage::Messsage=>SystemMessage::TransactionTypeTypeID.$id.SystemMessage::NotFound,
                            SystemMessage::Success=>false],
                    HttpStatusCode::TransactionTypeErrorNotFound);
                    }

                //Execute update to update the record on transaction type table
                $transaction_type->update($data);

                //return the TransactionTypeResource with additional element
                return TransactionTypeResource::collection( [$transaction_type])->additional([
                    SystemMessage::Messsage=>SystemMessage::TransactionTypeRecordUpdated,
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
     * @param  \App\Models\TransactionType  $transactionType
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            //is not yet been used as referrence by transaction type record
            $transaction_type=TransactionType::where('id','=',$id)->first();


            if(!is_null($transaction_type)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::TransactionTypeCanNotDelete.$id.SystemMessage::AlreadyBeenUsed,
                    SystemMessage::Success=>false],
            HttpStatusCode::TransactionTypeErrorNotFound);
            }

            $transaction_type=TransactionType::find($id);

            if(is_null($transaction_type)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::TransactionTypeTypeID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
            HttpStatusCode::TransactionTypeErrorNotFound);
            }

            //Execute Delete and return the deleted record
            $transaction_type->delete();

            //return the TransactionTypeResource with additional element
            return TransactionTypeResource::collection( [$transaction_type])->additional([
                SystemMessage::Messsage=>SystemMessage::TransactionTypeRecordDeleted,
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

        $transaction_type=TransactionType::withTrashed()->find($id);

            if(is_null($transaction_type)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::TransactionTypeTypeID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
                    HttpStatusCode::TransactionTypeErrorNotFound);
            }

            $transaction_type=TransactionType::withTrashed()->where('id',$id)->restore();
            $transaction_type=TransactionType::find($id);

            //return the TransactionTypeResource with additional element
            return TransactionTypeResource::collection( [$transaction_type])->additional([
            SystemMessage::Messsage=>SystemMessage::TransactionTypeRecordRestored,
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

            $transaction_typeActiveRecords=TransactionType::count();
            $transaction_typeSoftDeletedRecords=TransactionType::onlyTrashed()->count();
            $transaction_typeTotalRecords= $transaction_typeActiveRecords+$transaction_typeSoftDeletedRecords;


            //return the TransactionTypeResource with additional element
            return response()->json([
            SystemMessage::Data=>array(SystemMessage::ActiveRecords=>$transaction_typeActiveRecords,
                                       SystemMessage::SoftDeletedRecords=>$transaction_typeSoftDeletedRecords,
                                       SystemMessage::TotalRecords=>$transaction_typeTotalRecords),
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
