<?php

namespace App\Http\Controllers;

use App\Enums\FileName;
use App\Enums\HttpStatusCode;
use App\Enums\SystemMessage;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\TokenRepository;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Requests\UpdateUserProfilePicRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\Client;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;


class AuthController extends Controller
{

    public function login(LoginUserRequest $request)
    {

        if($request->validated()){

            $loginCredentials=[
                'email' =>$request->email,
                'password' =>$request->password,
            ];

            try{

                if(!Auth::attempt($loginCredentials) ){
                    return response()->json([
                        SystemMessage::Messsage=>SystemMessage::AuthLoginFailed,
                        SystemMessage::Success=>false],
                        HttpStatusCode::ClientErrorNotFound);
                    }

                    $user=User::find(Auth::id());
                    $accessToken  =   $user->createToken('authToken')->accessToken;
                    $permission= Role::find($user->role_id);

                    //show found record as object
                return response()->json([
                    SystemMessage::Data=>UserResource::make($user),
                    SystemMessage::AccessToken=> $accessToken,
                    SystemMessage::Permission=> $permission->permission,
                    SystemMessage::Messsage=>SystemMessage::AuthLoginSuccess,
                    SystemMessage::Success=>true]);


                }catch(QueryException $e){ // Catch all Query Errors
                    return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
                }catch(\Exception $e){     // Catch all General Errors
                return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
                }catch(\Error $e){          // Catch all Php Errors
                return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
            }
        }
    }



    public function logout(Request $request){

        //Get the tokenId use getTokenId function from AuthHelper.php in Helpers folder.
        $tokenId = getTokenId($request);

        try{

            //Use the Token Repository to Revoke the Access Token
            $tokenRepository = app(TokenRepository::class);
            $tokenRepository->revokeAccessToken($tokenId);

            return response()->json([
            SystemMessage::Messsage=>SystemMessage::AuthLogout,
            SystemMessage::Success=>true]);
        }catch(QueryException $e){ // Catch all Query Errors
            return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
        }catch(\Exception $e){     // Catch all General Errors
            return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
        }catch(\Error $e){          // Catch all Php Errors
            return response()->json([ SystemMessage::Messsage =>$e->getMessage(),SystemMessage::Success=>false],HttpStatusCode::ServerErrorInternalServerError);
        }
    }

    //Update User Profile
    public function update_user_profile(UpdateUserProfileRequest $request)
    {
        $tokenId=getTokenId($request);
        $userId=getUserIdBaseFromTokenId($tokenId);

         //Validate Request
         if($request->validated()){

            //Pick Up only all those data from the request
            //that are needed to update to users Table and (Client table if user is a client)
            $data=[
                "first_name" => $request->first_name,
                "last_name" => $request->last_name,
                "email" => $request->email,
                "phone" => $request->phone,
                "address1"=>$request->address1,
                "address2"=>$request->address2,
                "city_id" => $request->city_id,
                "state" => $request->state,
                "zip" => $request->zip
            ];

           //Find and return the found record
            try{
                    $user=User::find($userId);

                    if(is_null($user)){
                        return response()->json([
                            SystemMessage::Messsage=>SystemMessage::UserID.$userId.SystemMessage::NotFound,
                            SystemMessage::Success=>false],
                    HttpStatusCode::ClientErrorNotFound);
                    }

                //Execute update to update the record on clients table
                $user->update($data);

                //dd(getClientIdBaseFromUserId($userId));
                //Update also the client table with the new values of those data fields
                $client=Client::find(getClientIdBaseFromUserId($userId));
                if(!is_null($client)){
                    $client->update($data);
                }

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


    public function update_user_profile_pic(UpdateUserProfilePicRequest $request)
    {
        $tokenId=getTokenId($request);
        $userId=getUserIdBaseFromTokenId($tokenId);

         //Validate Request
         if($request->validated()){

            //get the uploaded file and store to Destination path with unique random filename
            $filename = basename($request->file('picture')->store(FileName::UploadFileDestinationPath));

            //Pick Up only all those data from the request
            $data=[
                "picture" => $filename,
            ];

           //Find and return the found record
            try{
                    $user=User::find($userId);

                    if(is_null($user)){
                        return response()->json([
                            SystemMessage::Messsage=>SystemMessage::UserID.$userId.SystemMessage::NotFound,
                            SystemMessage::Success=>false],
                    HttpStatusCode::ClientErrorNotFound);
                    }

                //Execute update to update the record on clients table
                $user->update($data);


                //show found record as object
                return response()->json([
                    SystemMessage::Data=>UserResource::make($user),
                    SystemMessage::Messsage=>SystemMessage::UserProfilePicUpdated,
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

     //Change Password
     public function change_password(ChangePasswordRequest $request)
     {

          //Validate Request
          if($request->validated()){

            $tokenId=getTokenId($request);
            $userId=getUserIdBaseFromTokenId($tokenId);

            //verify if old password is correct
            if (!verifyOldPassword($userId,$request->current_password))
            {
                return response()->json([
                    SystemMessage::Messsage=>SystemMessage::IncorrectOldPasssword,
                    SystemMessage::Success=>false],
                    HttpStatusCode::ClientErrorNotFound);
            }

             //Pick Up only all those data from the request
             //that are needed to update to User Table

             $data=[
                 "password" => Hash::make($request->new_password),
             ];

            //Find and return the found record
             try{
                     $user=user::find($userId);

                     if(is_null($user)){
                         return response()->json([
                             SystemMessage::Messsage=>SystemMessage::UserID.$userId.SystemMessage::NotFound,
                             SystemMessage::Success=>false],
                     HttpStatusCode::ClientErrorNotFound);
                     }

                 //Execute update to update the record on clients table
                 $user->update($data);

                 //show change password success
                 return response()->json([
                     SystemMessage::TokenID=>$tokenId,
                     SystemMessage::Messsage=>SystemMessage::ChangePasswordSuccess,
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
}
