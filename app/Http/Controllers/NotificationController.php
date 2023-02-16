<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Http\Requests\ViewNotificationRequest;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Exports\NotificationExport;
use App\Enums\FileName;
use App\Enums\HttpStatusCode;
use App\Enums\SystemMessage;
class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ViewNotificationRequest $request)
    {
        if($request->validated()){

            //extract page size
            $per_page=$request->input('per_page');
 
             $wildCard=($request->filter_activatewildcard==1) ? '%' : '';
             //dd($wildCard);
 
             //Let's build our queries
 
             $dataQuery=Notification::select(['notifications.*'])
             ->join('users','notifications.user_id','=','users.id');
 
 
             //Check for Filters Begin -------------------------------------------------------//
 
             //Filter All COlumn
             if($request->filter_allcolumn!=''){
                 $dataQuery->whereraw('concat(notifications.user_id,notifications.message,notifications.created_at) LIKE ?','%'.$request->filter_allcolumn.'%');
            }
 
             //Filter ID
            if($request->filter_id!=''){
                 $dataQuery->where('notifications.id','LIKE',$wildCard.$request->filter_id.'%');
            }
 
             //Filter User ID
             if($request->filter_user_id!=''){
                 $dataQuery->where('notifications.first_name','LIKE',$request->filter_user_id.'%');
             }
 
             //Filter Message
             if($request->filter_message!=''){
                 $dataQuery->where('notifications.last_name','LIKE',$wildCard.$request->filter_message.'%');
             }

             //Filter status
             if($request->filter_status!=''){
                $dataQuery->where('notifications.last_name','LIKE',$wildCard.$request->filter_status.'%');
            }

 
             //Check for Filters End -------------------------------------------------------//
 
             //Apply Sorting Mechanism Begin ----------------------------------------------//
 
             //Sort by ID
             if($request->sort_id){
                 $dataQuery->orderBy('notifications.id',($request->sort_id==1) ? "asc" : "desc");
             }
 
             //Sort by User Id
             if($request->sort_user_id){
                 $dataQuery->orderBy('notifications.user_id',($request->sort_user_id==1) ? "asc" : "desc");
             }
 
             //Sort by Messages
             if($request->sort_message){
                 $dataQuery->orderBy('notifications.message',($request->sort_message==1) ? "asc" : "desc");
             }

             //Sort by status
             if($request->sort_status){
                $dataQuery->orderBy('notifications.message',($request->sort_status==1) ? "asc" : "desc");
            }
 
 
             //Sort by Created_at
             if($request->sort_created_at){
                 $dataQuery->orderBy('notifications.created_at',($request->sort_created_at==1) ? "asc" : "desc");
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
                     SystemMessage::Messsage=>SystemMessage::NotificationNoRecordFound,
                     SystemMessage::Success=>false],
                     HttpStatusCode::NotificationErrorNotFound);
             }
 
             //Check if this request is for EXPORT. If export_to is NOT '' means YES then export the data to target format
             if($request->export_to!='')
             {
                 switch (strtoupper($request->export_to)){
                     case FileName::EXCELFile:
                         return (new NotificationExport($dataResult))->download(FileName::NotificationExportFile.FileName::EXCELFileFormat, \Maatwebsite\Excel\Excel::XLSX,null);
                         break;
                     case FileName::CSVFile:
                         return (new NotificationExport($dataResult))->download(FileName::NotificationExportFile.FileName::CSVFileFormat, \Maatwebsite\Excel\Excel::CSV,null);
                         break;
                     case FileName::PDFFile:
                         return (new NotificationExport($dataResult))->download(FileName::NotificationExportFile.FileName::PDFFileFormat, \Maatwebsite\Excel\Excel::DOMPDF,null);
                         break;
 
                     default:
                         return response()->json([
                         SystemMessage::Messsage=>SystemMessage::FileFormatNotSupported,
                         SystemMessage::Success=>false],
                 HttpStatusCode::NotificationErrorBadRequest);
                 }
             }
 
             return NotificationResource::collection($dataResult)->additional([
                 SystemMessage::Messsage=>SystemMessage::NotificationRecordRetrieved,
                 SystemMessage::Success=>true]);
 
         }
     }
 
     public function all()
     {
         //Show all records including those mark deleted
         $dataResult=Notification::withTrashed()->simplepaginate(100);
         return NotificationResource::collection($dataResult)->additional([
             SystemMessage::Messsage=>SystemMessage::NotificationRecordRetrieved,
             SystemMessage::Success=>true]);
 
     }
 
     /**
      * Store a newly created resource in storage.
      *
      * @param  \App\Http\Requests\StoreNotificationRequest  $request
      * @return \Illuminate\Http\Response
      */

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
     * @param  \App\Http\Requests\StoreNotificationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreNotificationRequest $request)
    {
         //Validate Request
       if($request->validated()){

        //Pick Up only all those data from the request
        //that are needed to save to notification Table
        $data=[
            "user_id" => $request->user_id,
            "message" => $request->message,
            "status" => $request->status,
            "created_at" => $request->created_at,
        ];

       //Execute and return the newly created record
        try{
                //Execute create to insert the record to notification table and put in on notification object
                //so we can add additional collection element later
                $notification=Notification::create($data);

                //return the NotificationResource with additional element
                return NotificationResource::collection( [$notification])->additional([
                    SystemMessage::Messsage=>SystemMessage::NotificationRecordCreated,
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
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{

            $notification=Notification::find($id);

                if(is_null($notification)){
                    return response()->json([
                        SystemMessage::Messsage=>SystemMessage::NotificationID.$id.SystemMessage::NotFound,
                        SystemMessage::Success=>false],
                HttpStatusCode::NotificationErrorNotFound);
                }

            //show found record as object
            return response()->json([
                SystemMessage::Data=>NotificationResource::make($notification),
                SystemMessage::Messsage=>SystemMessage::NotificationRecordFound,
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
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function edit(Notification $notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateNotificationRequest  $request
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateNotificationRequest $request, $id)
    {
          //Validate Request
          if($request->validated()){

            //Pick Up only all those data from the request
            //that are needed to update to Notification Table
            $data=[
                "user_id" => $request->user_id,
                "message" => $request->message,
                "status" => $request->status,
            ];

           //Find and return the found record
            try{
                    $notification=Notification::find($id);

                    if(is_null($notification)){
                        return response()->json([
                            SystemMessage::Messsage=>SystemMessage::NotificationID.$id.SystemMessage::NotFound,
                            SystemMessage::Success=>false],
                    HttpStatusCode::NotificationErrorNotFound);
                    }

                //Execute update to update the record on notification table
                $notification->update($data);

                //return the NotificationResource with additional element
                return NotificationResource::collection( [$notification])->additional([
                    SystemMessage::Messsage=>SystemMessage::NotificationRecordUpdated,
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
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         try{
            //Before delete make sure to check first if this Notification ID
            //is not yet been used as referrence by notification record
            $notification=Notification::where('id','=',$id)->first();


            if(!is_null($notification)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::NotificationCanNotDelete.$id.SystemMessage::AlreadyBeenUsed,
                    SystemMessage::Success=>false],
            HttpStatusCode::NotificationErrorNotFound);
            }

            $notification=Notification::find($id);

            if(is_null($notification)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::NotificationID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
            HttpStatusCode::NotificationErrorNotFound);
            }

            //Execute Delete and return the deleted record
            $notification->delete();


            //show deleted record as object
            return response()->json([
                SystemMessage::Data=>NotificationResource::make($notification),
                SystemMessage::Messsage=>SystemMessage::NotificationRecordDeleted,
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

        $notification=Notification::withTrashed()->find($id);

            if(is_null($notification)){
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::NotificationID.$id.SystemMessage::NotFound,
                    SystemMessage::Success=>false],
                    HttpStatusCode::NotificationErrorNotFound);
            }

            $notification=Notification::withTrashed()->where('id',$id)->restore();
            $notification=Notification::find($id);

            //return the NotificationResource with additional element
            return NotificationResource::collection( [$notification])->additional([
            SystemMessage::Messsage=>SystemMessage::NotificationRecordRestored,
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

            $notificationActiveRecords=Notification::count();
            $notificationSoftDeletedRecords=Notification::onlyTrashed()->count();
            $notificationTotalRecords= $notificationActiveRecords+$notificationSoftDeletedRecords;

            return response()->json([
            SystemMessage::Data=>array(SystemMessage::ActiveRecords=>$notificationActiveRecords,
                                       SystemMessage::SoftDeletedRecords=>$notificationSoftDeletedRecords,
                                       SystemMessage::TotalRecords=>$notificationTotalRecords),
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
