<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Enums\HttpStatusCode;
use App\Enums\SystemMessage;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Http\Resources\CityResource;
use App\Http\Requests\ViewCityRequest;
use Illuminate\Database\QueryException;
use App\Exports\CityExport;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ViewCityRequest $request)
    {
        if($request->validated()){
            $wildCard=($request->filter_activatewildcard==1) ? '%' : '';
            
            $dataQuery = City::select('cities.*');

             // Filter All Column
             if($request->filter_allcolumn!=''){
                $dataQuery->whereRaw('concat(cities.id,cities.state,cities.city,cities.description,cities.created_at) LIKE ?',$request->filter_allcolumn.'%');
            }

            // Filter State
            if($request->filter_state!=''){
                $dataQuery->where('cities.state','LIKE',$wildCard.$request->filter_state.'%');
            }

             // Filter ID
             if($request->filter_id!=''){
                $dataQuery->where('cities.id','LIKE',$wildCard.$request->filter_id.'%');
            }

            // Filter City
            if($request->filter_city!=''){
                $dataQuery->where('cities.city','LIKE',$wildCard.$request->filter_city.'%');
            }

            // Filter Description
            if($request->filter_description!=''){
                $dataQuery->where('cities.description','LIKE',$wildCard.$request->filter_description.'%');
            }

            //Filter Created At
            if($request->filter_created_at!=''){
                $dataQuery->where('cities.created_at','LIKE',$wildCard.$request->filter_created_at.'%');
            }

             //Apply Sorting Mechanism Begin ----------------------------------------------//

             //Sort by ID
             if($request->sort_id){
                $dataQuery->orderBy('cities.id',($request->sort_id==1) ? "asc" : "desc");
            }
            //Sort by State
            if($request->sort_state){
                $dataQuery->orderBy('cities.state',($request->sort_state==1) ? "asc" : "desc");
            }

              //Sort by City
              if($request->sort_city){
                $dataQuery->orderBy('cities.state',($request->sort_city==1) ? "asc" : "desc");
            }

            //Sort Description
            if($request->sort_description){
                $dataQuery->orderBy('cities.Description',($request->sort_description==1) ? "asc" : "desc");
            }

             //Sort by Created_at
             if($request->sort_created_at){
                $dataQuery->orderBy('cities.created_at',($request->sort_created_at==1) ? "asc" : "desc");
            }

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

                //Check if this request is for EXPORT. If export_to is NOT '' means YES then export the data to target format
            if($request->export_to!='')
            {
                switch (strtoupper($request->export_to)){
                    case FileName::EXCELFile:
                        return (new CityExport($dataResult))->download(FileName::CityExport.FileName::EXCELFileFormat, \Maatwebsite\Excel\Excel::XLSX,null);
                        break;
                    case FileName::CSVFile:
                        return (new CityExport($dataResult))->download(FileName::CityExport.FileName::CSVFileFormat, \Maatwebsite\Excel\Excel::CSV,null);
                        break;
                    case FileName::PDFFile:
                        return (new CityExport($dataResult))->download(FileName::CityExport.FileName::PDFFileFormat, \Maatwebsite\Excel\Excel::DOMPDF,null);
                        break;

                    default:
                        return response()->json([
                        SystemMessage::Messsage=>SystemMessage::FileFormatNotSupported,
                        SystemMessage::Success=>false],
                HttpStatusCode::CityErrorBadRequest);
                }
            }

             //Apply Sorting Mechanism End ------------------------------------------------//
             return CityResource::collection($dataResult)->additional([
                SystemMessage::Messsage=>SystemMessage::CityRecordRetrieved,
                SystemMessage::Success=>true]);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCityRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCityRequest $request)
    {
    //Validate Request
    if($request->validated()){

        //Pick Up only all those data from the request
        //that are needed to save to City Table
        $data=[
            "state" => $request->state,
            "city" => $request->city,
            "description" => $request->description,
            "created_at" => $request->created_at,
        ];

       //Execute and return the newly created record
        try{
                //Execute create to insert the record to city table and put in on city object
                //so we can add additional collection element later
                $city=City::create($data);

                //return the City with additional element
                return CityResource::collection( [$city])->additional([
                    SystemMessage::Messsage=>SystemMessage::CityRecordCreated,
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
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{

            $city=City::find($id);

                if(is_null($city)){
                    return response()->json([
                        SystemMessage::Messsage=>SystemMessage::CityID.$id.SystemMessage::NotFound,
                        SystemMessage::Success=>false],
                HttpStatusCode::CityErrorNotFound);
                }

        //show found record as object
        return response()->json([
            SystemMessage::Data=>CityResource::make($city) ,
            SystemMessage::Messsage=>SystemMessage::CityRecordFound,
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
     * @param  \App\Http\Requests\UpdateCityRequest  $request
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCityRequest $request, $id)
    {
         //Validate Request
         if($request->validated()){

            //Pick Up only all those data from the request
            //that are needed to update to City Table
            $data=[
                "state" => $request->state,
                "city" => $request->city,                      
                "description" => $request->description,
            ];

           //Find and return the found record
            try{
                    $city=City::find($id);

                    if(is_null($city)){
                        return response()->json([
                            SystemMessage::Messsage=>SystemMessage::CityID.$id.SystemMessage::NotFound,
                            SystemMessage::Success=>false],
                    HttpStatusCode::CityErrorNotFound);
                    }

                //Execute update to update the record on city table
                $city->update($data);

                //show found record as object
                return response()->json([
                    SystemMessage::Data=>CityResource::make($city),
                    SystemMessage::Messsage=>SystemMessage::CityRecordUpdated,
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
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            //is not yet been used as referrence by city record
            $city=City::where('id','=',$id)->first();


            if(!is_null($city)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::CityCanNotDelete.$id.SystemMessage::AlreadyBeenUsed,
                    SystemMessage::Success=>false],
            HttpStatusCode::CityErrorNotFound);
            }

            $city=City::find($id);

            if(is_null($city)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::CityID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
            HttpStatusCode::CityErrorNotFound);
            }

            //Execute Delete and return the deleted record
            $city->delete();

            //return the CityResource with additional element
            return CityResource::collection( [$city])->additional([
                SystemMessage::Messsage=>SystemMessage::CityRecordRetrieved,
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

        $city=City::withTrashed()->find($id);

            if(is_null($city)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::CityID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
                    HttpStatusCode::CityErrorNotFound);
            }

            $city=City::withTrashed()->where('id',$id)->restore();
            $city=City::find($id);

            //return the CityResource with additional element
            return CityResource::collection( [$city])->additional([
            SystemMessage::Messsage=>SystemMessage::CityRecordRestored,
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
            $cityActiveRecords=City::count();
            $citySoftDeletedRecords=City::onlyTrashed()->count();
            $cityTotalRecords= $cityActiveRecords+$citySoftDeletedRecords;

            return response()->json([
            SystemMessage::Data=>array(SystemMessage::ActiveRecords=>$cityActiveRecords,
                                       SystemMessage::SoftDeletedRecords=>$citySoftDeletedRecords,
                                       SystemMessage::TotalRecords=>$cityTotalRecords),
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
