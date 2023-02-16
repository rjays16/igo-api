<?php

namespace App\Http\Controllers;

use App\Models\Term;
use App\Http\Requests\StoreTermRequest;
use App\Http\Requests\UpdateTermRequest;
use App\Http\Requests\ViewTermRequest;
use Illuminate\Database\QueryException;
use App\Enums\HttpStatusCode;
use App\Enums\SystemMessage;
use App\Http\Resources\TermResource;
use App\Models\Account;
use App\Enums\FileName;
use App\Exports\TermExport;

class TermController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ViewTermRequest $request)
    {
         //Validate first all data being passed
         if($request->validated()){

             //Let's build our queries
             //$dataQuery=Term::select('terms.*');

             $dataQuery=Term::select(['terms.*','accounts.acct_description','compound_periods.compound_period'])
             ->leftJoin('accounts','terms.account_id','=','accounts.id')
             ->leftJoin('compound_periods','terms.compound_period_id','=','compound_periods.id');

             $wildCard=($request->filter_activatewildcard==1) ? '%' : '';

             //Check for Filters Begin -------------------------------------------------------//
            //dd($request->filter_allcolumn);
            //Filter All Column
            if($request->filter_allcolumn!=''){
                $dataQuery->whereraw('concat(terms.id,accounts.acct_description,terms.effective_date,terms.rate,compound_periods.compound_period,terms.note,terms.created_at) LIKE ?',$wildCard.$request->filter_allcolumn.'%');
            }

            //Filter ID
            if($request->filter_id!=''){
                $dataQuery->where('terms.id','LIKE',$wildCard.$request->filter_id.'%');
            }

            //Filter acct_description
            if($request->filter_acct_description!=''){
                $dataQuery->where('accounts.acct_description','LIKE',$wildCard.$request->filter_acct_description.'%');
            }

            //Filter effective_date
            if($request->filter_effective_date!=''){
                $dataQuery->where('terms.effective_date','LIKE',$wildCard.$request->filter_effective_date.'%');
            }

            //Filter rate
            if($request->filter_rate!=''){
                $dataQuery->where('terms.rate','LIKE',$wildCard.$request->filter_rate.'%');
            }

            //Filter compound_period
            if($request->filter_compound_period!=''){
                $dataQuery->where('compound_periods.compound_period','LIKE',$wildCard.$request->filter_compound_period.'%');
            }

            //Filter note
            if($request->filter_note!=''){
                $dataQuery->where('terms.note','LIKE',$wildCard.$request->filter_note.'%');
            }

            //Filter created_at
            if($request->filter_created_at!=''){
                $dataQuery->where('terms.created_at','LIKE',$wildCard.$request->filter_created_at.'%');
            }


             //Check for Filters End -------------------------------------------------------//

             //Apply Sorting Mechanism Begin ----------------------------------------------//

             //Sort by ID
             if($request->sort_id){
                 $dataQuery->orderBy('terms.id',($request->sort_id==1) ? "asc" : "desc");
             }

             //Sort by acct_description
             if($request->sort_acct_description){
                 $dataQuery->orderBy('accounts.acct_description',($request->sort_acct_description==1) ? "asc" : "desc");
             }

             //Sort by effective_date
             if($request->sort_effective_date){
                 $dataQuery->orderBy('terms.sort_effective_date',($request->sort_effective_date==1) ? "asc" : "desc");
             }

             //Sort by rate
             if($request->sort_rate){
                 $dataQuery->orderBy('terms.rate',($request->sort_rate==1) ? "asc" : "desc");
             }

             //Sort by compound_period
             if($request->sort_compound_period){
                 $dataQuery->orderBy('compound_periods.compound_period',($request->sort_compound_period==1) ? "asc" : "desc");
             }

             //Sort by note
             if($request->sort_note){
                 $dataQuery->orderBy('terms.note',($request->sort_note==1) ? "asc" : "desc");
             }

            //Sort by created_at
             if($request->sort_created_at){
                 $dataQuery->orderBy('terms.created_at',($request->sort_created_at==1) ? "asc" : "desc");
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
                    SystemMessage::Messsage=>SystemMessage::TermNoRecordFound,
                    SystemMessage::Success=>false],
            HttpStatusCode::ClientErrorNotFound);
            }

            //Check if this request is for EXPORT. If export_to is NOT '' means YES then export the data to target format
            if($request->export_to!='')
            {
                switch (strtoupper($request->export_to)){
                    case FileName::EXCELFile:
                        return (new TermExport($dataResult))->download(FileName::TermExportFile.FileName::EXCELFileFormat, \Maatwebsite\Excel\Excel::XLSX,null);
                        break;
                    case FileName::CSVFile:
                        return (new TermExport($dataResult))->download(FileName::TermExportFile.FileName::CSVFileFormat, \Maatwebsite\Excel\Excel::CSV,null);
                        break;
                    case FileName::PDFFile:
                        return (new TermExport($dataResult))->download(FileName::TermExportFile.FileName::PDFFileFormat, \Maatwebsite\Excel\Excel::DOMPDF,null);
                        break;

                    default:
                        return response()->json([
                        SystemMessage::Messsage=>SystemMessage::FileFormatNotSupported,
                        SystemMessage::Success=>false],
                HttpStatusCode::ClientErrorBadRequest);
                }
            }

             return TermResource::collection($dataResult)->additional([
                SystemMessage::Messsage=>SystemMessage::TermRecordRetrieved,
                SystemMessage::Success=>true]);

         }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTermRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTermRequest $request)
    {

     //Validate Request
     if($request->validated()){

        //Pick Up only all those data from the request
        //that are needed to save to Client Table
        $data=[
            "account_id" => $request->account_id,
            "effective_date" => $request->effective_date,
            "rate" => $request->rate,
            "compound_period_id" => $request->compound_period_id,
            "note" => $request->note,
        ];

       //Execute and return the newly created record
        try{
                //Execute create to insert the record to clients table and put in on client object
                //so we can add additional collection element later
                $term=Term::create($data);

                //return the ClientResource with additional element
                return TermResource::collection( [$term])->additional([
                    SystemMessage::Messsage=>SystemMessage::TermRecordCreated,
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
     * @param  \App\Models\Term  $term
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        try{

            $term=Term::find($id);

                if(is_null($term)){
                    return response()->json([
                        SystemMessage::Messsage=>SystemMessage::TermID.$id.SystemMessage::NotFound,
                        SystemMessage::Success=>false],
                HttpStatusCode::ClientErrorNotFound);
                }

            //show found record as object
            return response()->json([
                SystemMessage::Data=>TermResource::make($term),
                SystemMessage::Messsage=>SystemMessage::TermRecordFound,
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
    public function update(UpdateTermRequest $request, $id)
    {
         //Validate Request
         if($request->validated()){

            //Pick Up only all those data from the request
            //that are needed to update to Client Table
            $data=[
                "account_id" => $request->account_id,
                "effective_date" => $request->effective_date,
                "rate" => $request->rate,
                "compound_period_id" => $request->compound_period_id,
                "note" => $request->note,
            ];

           //Find and return the found record
            try{
                    $term=Term::find($id);

                    if(is_null($term)){
                        return response()->json([
                            SystemMessage::Messsage=>SystemMessage::TermID.$id.SystemMessage::NotFound,
                            SystemMessage::Success=>false],
                    HttpStatusCode::ClientErrorNotFound);
                    }

                //Execute update to update the record on clients table
                $term->update($data);

                //show found record as object
                return response()->json([
                    SystemMessage::Data=>TermResource::make($term),
                    SystemMessage::Messsage=>SystemMessage::TermRecordUpdated,
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
            $account=Account::where('term_id','=',$id)->first();


            if(!is_null($account)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::TermCanNotDelete.$id.SystemMessage::AlreadyBeenUsed,
                    SystemMessage::Success=>false],
            HttpStatusCode::ClientErrorNotFound);
            }

            $term=Term::find($id);

            if(is_null($term)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::TermID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
            HttpStatusCode::ClientErrorNotFound);
            }

            //Execute Delete and return the deleted record
            $term->delete();


            //show deleted record as object
            return response()->json([
                SystemMessage::Data=>TermResource::make($term),
                SystemMessage::Messsage=>SystemMessage::TermRecordDeleted,
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

        $term=Term::withTrashed()->find($id);

            if(is_null($term)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::TermID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
                    HttpStatusCode::ClientErrorNotFound);
            }

            $term=Term::withTrashed()->where('id',$id)->restore();
            $term=Term::find($id);

           //show undeleted record as object
            return response()->json([
                SystemMessage::Data=>TermResource::make($term),
                SystemMessage::Messsage=>SystemMessage::TermRecordRestored,
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

            $clientActiveRecords=Term::count();
            $clientSoftDeletedRecords=Term::onlyTrashed()->count();
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
