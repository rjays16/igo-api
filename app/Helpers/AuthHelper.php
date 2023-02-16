<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

if (!function_exists('burtTest')) {
    function burtTest(){
        echo "Hello this is just a helper test created by burt";
    }

}

if (!function_exists('getTokenId')) {

    /**
     * This functions return the token ID from Authorization token bearer
     *
     * @param
     * @return
     */
    function getTokenId(Request $request)
    {
         //Extract First the TokenID from the $request
         $access_token = $request->header('Authorization');
         $auth_header = explode(' ', $access_token);
         $token = $auth_header[1];
         $token_parts = explode('.', $token);
         $token_header = $token_parts[1];
         $token_header_json = base64_decode($token_header);
         $token_header_array = json_decode($token_header_json, true);

         //Get the tokenID
         $tokenId = $token_header_array['jti'];

         return $tokenId;
    }

}

if (!function_exists('getUserIdBaseFromTokenId')) {

    /**
     * This functions return the token ID from Authorization token bearer
     *
     * @param
     * @return
     */
    function getUserIdBaseFromTokenId($tokenId)
    {
        $query=DB::table('oauth_access_tokens')->select('user_id')->where('id','=',$tokenId)->first();
         return $query->user_id;
    }
}

if (!function_exists('getClientIdBaseFromUserId')) {

    /**
     * This functions return the token ID from Authorization token bearer
     *
     * @param
     * @return
     */
    function getClientIdBaseFromUserId($userId)
    {
        $query=DB::table('users')->select('client_id')->where('id','=',$userId)->first();
         return $query->client_id;
    }
}

if (!function_exists('verifyOldPassword')) {

    /**
     * This functions return the token ID from Authorization token bearer
     *
     * @param
     * @return
     */
    function verifyOldPassword($userId,$currentPassword)
    {
        //get the current password from users table
        $query=DB::table('users')->select('password')->where('id','=',$userId)->first();
        $Password=$query->password;

        //check if current password matches the password
        if(Hash::check($currentPassword, $Password))
        {
            return true;
        }else{
            return false;
        }
    }
}




