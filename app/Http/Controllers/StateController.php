<?php

namespace App\Http\Controllers;


use App\Models\State;
use App\Enums\HttpStatusCode;
use App\Enums\SystemMessage;
use App\Http\Requests\StoreStateRequest;
use App\Http\Requests\UpdateStateRequest;
use Illuminate\Database\QueryException;
use App\Http\Requests\ViewStateRequest;
use App\Http\Resources\StateResource;
use App\Exports\StateExport;
class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ViewStateRequest $request)
    {
        if($request->validated()){


            $dataQuery = State::select('states.*');

            // Filter All Column
            if($request->filter_allcolumn!=''){
                $dataQuery->whereRaw('concat(states.state,states.name,states.description,states.created_at) LIKE ?','%'.$request->filter_allcolumn.'%');
            }

            // Filter State
            if($request->filter_state!=''){
                $dataQuery->where('states.state','LIKE','%'.$request->filter_state.'%');
            }

            // Filter Name
            if($request->filter_name!=''){
                $dataQuery->where('states.name','LIKE','%'.$request->filter_name.'%');
            }

            //Filter Description
            if($request->filter_description!=''){
                $dataQuery->where('states.description','LIKE','%'.$request->filter_description.'%');
            }

            //Filter Created At
            if($request->filter_created_at!=''){
                $dataQuery->where('states.created_at','LIKE','%'.$request->filter_created_at.'%');
            }

            //Apply Sorting Mechanism Begin ----------------------------------------------//

            //Sort by State
            if($request->sort_state){
                $dataQuery->orderBy('states.state',($request->sort_state==1) ? "asc" : "desc");
            }

            //Sort by Name
            if($request->sort_name){
                $dataQuery->orderBy('states.name',($request->sort_name==1) ? "asc" : "desc");
            }

            //Sort Description
            if($request->sort_description){
                $dataQuery->orderBy('states.description',($request->sort_description==1) ? "asc" : "desc");
            }

             //Sort by Created_at
             if($request->sort_created_at){
                $dataQuery->orderBy('states.created_at',($request->sort_created_at==1) ? "asc" : "desc");
            }

             //Apply Sorting Mechanism End ------------------------------------------------//
            try{
             //Lastly Execute Query and Paginate the result
             $dataResult=$dataQuery->Simplepaginate($request->per_page);

            } catch(QueryException $e){ // Catch all Query Errors
                return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
            } catch(\Exception $e){     // Catch all General Erroers
                return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
            }catch(\Error $e){          // Catch all Php Errors
                return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
            }

            //Check if dataResult has record, if not throw message
            if($dataResult->count()<=0){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::StateNoRecordFound,
                    SystemMessage::Success=>false],
                    HttpStatusCode::StateErrorNotFound);
            }

            //Check if this request is for EXPORT. If export_to is NOT '' means YES then export the data to target format
            if($request->export_to!='')
            {
                switch (strtoupper($request->export_to)){
                    case FileName::EXCELFile:
                        return (new StateExport($dataResult))->download(FileName::StateExport.FileName::EXCELFileFormat, \Maatwebsite\Excel\Excel::XLSX,null);
                        break;
                    case FileName::CSVFile:
                        return (new StateExport($dataResult))->download(FileName::StateExport.FileName::CSVFileFormat, \Maatwebsite\Excel\Excel::CSV,null);
                        break;
                    case FileName::PDFFile:
                        return (new StateExport($dataResult))->download(FileName::StateExport.FileName::PDFFileFormat, \Maatwebsite\Excel\Excel::DOMPDF,null);
                        break;

                    default:
                        return response()->json([
                        SystemMessage::Messsage=>SystemMessage::FileFormatNotSupported,
                        SystemMessage::Success=>false],
                HttpStatusCode::StateErrorBadRequest);
                }
            }
            
                //return  response()->json( $dataResult,HttpStatusCode::SuccessOK);
                return StateResource::collection($dataResult)->additional([
                    SystemMessage::Messsage=>SystemMessage::StateRecordRetrieved,
                    SystemMessage::Success=>true]);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreStateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStateRequest $request)
    {
         //Validate Request
       if($request->validated()){

        //Pick Up only all those data from the request
        //that are needed to save to state Table
        $data=[
            "state" => $request->state,
            "name" => $request->name,
            "description" => $request->description,
        ];

       //Execute and return the newly created record
        try{
                //Execute create to insert the record to state table and put in on state object
                //so we can add additional collection element later
                $state=State::create($data);

                //return the StateResource with additional element
                return StateResource::collection([$state])->additional([
                    SystemMessage::Messsage=>SystemMessage::StateRecordCreated,
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
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function show($state)
    {
        try{

            $states=State::find($state);

                if(is_null($states)){
                    return response()->json([
                        SystemMessage::Messsage=>SystemMessage::State.$state.SystemMessage::NotFound,
                        SystemMessage::Success=>false],
                HttpStatusCode::StateErrorNotFound);
                }

            //show found record as object
            return response()->json([
            SystemMessage::Data=>StateResource::make($states) ,
            SystemMessage::Messsage=>SystemMessage::StateRecordFound,
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
     * @param  \App\Http\Requests\UpdateStateRequest  $request
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStateRequest $request, $state)
    {
          //Validate Request
          if($request->validated()){

            //Pick Up only all those data from the request
            //that are needed to update to State Table
            $data=[
                "state" => $request->state,
                "name" => $request->name,
                "description" => $request->description,
                "created_at" => $request->created_at,
            ];

           //Find and return the found record
            try{
                    $states=State::find($state);

                    if(is_null($states)){
                        return response()->json([
                            SystemMessage::Messsage=>SystemMessage::State.$state.SystemMessage::NotFound,
                            SystemMessage::Success=>false],
                    HttpStatusCode::StateErrorNotFound);
                    }

                //Execute update to update the record on State table
                $states->update($data);

                //return the StateResource with additional element
                return StateResource::collection([$states])->additional([
                    SystemMessage::Messsage=>SystemMessage::StateRecordUpdated,
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
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function destroy($state)
    {
        try{
            //is not yet been used as referrence by State record
            $states=State::where('state','=',$state)->first();


            if(!is_null($states)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::StateCanNotDelete.$state.SystemMessage::AlreadyBeenUsed,
                    SystemMessage::Success=>false],
            HttpStatusCode::StateErrorNotFound);
            }

            $states=State::find($state);

            if(is_null($states)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::State.$states.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
            HttpStatusCode::StateErrorNotFound);
            }

            //Execute Delete and return the deleted record
            $states->delete();

            //return the StateResource with additional element
            return StateResource::collection( [$states])->additional([
                SystemMessage::Messsage=>SystemMessage::StateRecordDeleted,
                SystemMessage::Success=>true]);

        } catch(QueryException $e){ // Catch all Query Errors
           return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
        } catch(\Exception $e){     // Catch all General Errors
           return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
        }catch(\Error $e){          // Catch all Php Errors
           return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
        }
    }

    public function undestroy($state)
    {
        try{

        $states=State::withTrashed()->find($state);

            if(is_null($states)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::State.$state.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
                    HttpStatusCode::StateErrorNotFound);
            }

            $states=State::withTrashed()->where('state',$state)->restore();
            $states=State::find($state);

            //return the StateResource with additional element
            return StateResource::collection([$states])->additional([
            SystemMessage::Messsage=>SystemMessage::StateRecordRestored,
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

            $stateActiveRecords=State::count();
            $stateSoftDeletedRecords=State::onlyTrashed()->count();
            $stateTotalRecords= $stateActiveRecords+$stateSoftDeletedRecords;

            return response()->json([
            SystemMessage::Data=>array(SystemMessage::ActiveRecords=>$stateActiveRecords,
                                       SystemMessage::SoftDeletedRecords=>$stateSoftDeletedRecords,
                                       SystemMessage::TotalRecords=>$stateTotalRecords),
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
