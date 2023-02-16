<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ClientTypeController;
use App\Http\Controllers\CompoundPeriodController;
use App\Http\Controllers\TransactionTypeController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AuditTrailController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::prefix('v1')->group(function () {

    //Authentication Route /*******************************************************************/
    //Authenticate User. Keep login route open
    Route::post('auth/login',[AuthController::class,'login']);


    //Authentication Routes /*******************************************************************/
    Route::middleware('auth:api')->prefix('auth')->group(function () {
    Route::middleware('auth:api')->post('/logout',[AuthController::class,'logout']);
    Route::middleware('auth:api')->post('/update-user-profile',[AuthController::class,'update_user_profile']);
    Route::middleware('auth:api')->post('/update-user-profile-pic',[AuthController::class,'update_user_profile_pic']);
    Route::middleware('auth:api')->post('/change-password',[AuthController::class,'change_password']);
    });

    //Protected Route
    //Route::middleware('auth:api')->get('/clients',[ClientController::class,'index']);

    //Clients Route /*******************************************************************/
    Route::middleware('auth:api')->prefix('client')->group(function () {
        Route::match(['get', 'post'], '', [ClientController::class,'index']);
        Route::match(['get', 'post'],'/show/{id}',[ClientController::class,'show']);
        Route::match(['get', 'post'], '/store', [ClientController::class,'store']);
        Route::match(['get', 'post', 'put'], '/update/{id}', [ClientController::class,'update']);
        Route::match(['get', 'delete'], '/delete/{id}', [ClientController::class,'destroy']);
        Route::match(['get', 'post'], '/restore/{id}', [ClientController::class,'undestroy']);
        Route::match(['get', 'post'], '/stats', [ClientController::class,'stats']);
    });

    //Account Routes /*******************************************************************/
    Route::middleware('auth:api')->prefix('account')->group(function () {
        Route::match(['get', 'post'], '', [AccountController::class,'index']);
        Route::match(['get', 'post'],'/show/{id}',[AccountController::class,'show']);
        Route::match(['get', 'post'], '/store', [AccountController::class,'store']);
        Route::match(['get', 'post', 'put'], '/update/{id}', [AccountController::class,'update']);
        Route::match(['get', 'delete'], '/delete/{id}', [AccountController::class,'destroy']);
        Route::match(['get', 'post'], '/restore/{id}', [AccountController::class,'undestroy']);
        Route::match(['get', 'post'], '/stats', [AccountController::class,'stats']);
    });

    //Organization Routes /*******************************************************************/
    Route::middleware('auth:api')->prefix('organization')->group(function () {
        Route::match(['get', 'post'], '', [OrganizationController::class,'index']);
        Route::match(['get', 'post'],'/show/{id}',[OrganizationController::class,'show']);
        Route::match(['get', 'post'], '/store', [OrganizationController::class,'store']);
        Route::match(['get', 'post', 'put'], '/update/{id}', [OrganizationController::class,'update']);
        Route::match(['get', 'delete'], '/delete/{id}', [OrganizationController::class,'destroy']);
        Route::match(['get', 'post'], '/restore/{id}', [OrganizationController::class,'undestroy']);
        Route::match(['get', 'post'], '/stats', [OrganizationController::class,'stats']);
    });

    //State Routes /*******************************************************************/
    Route::middleware('auth:api')->prefix('state')->group(function () {
        Route::match(['get', 'post'], '', [StateController::class,'index']);
        Route::match(['get', 'post'],'/show/{state}',[StateController::class,'show']);
        Route::match(['get', 'post'], '/store', [StateController::class,'store']);
        Route::match(['get', 'post', 'put'], '/update/{state}', [StateController::class,'update']);
        Route::match(['get', 'delete'], '/delete/{state}', [StateController::class,'destroy']);
        Route::match(['get', 'post'], '/restore/{state}', [StateController::class,'undestroy']);
        Route::match(['get', 'post'], '/stats', [StateController::class,'stats']);
    });

    //City Routes /*******************************************************************/
    Route::middleware('auth:api')->prefix('city')->group(function () {
        Route::match(['get', 'post'], '', [CityController::class,'index']);
        Route::match(['get', 'post'],'/show/{id}',[CityController::class,'show']);
        Route::match(['get', 'post'], '/store', [CityController::class,'store']);
        Route::match(['get', 'post', 'put'], '/update/{id}', [CityController::class,'update']);
        Route::match(['get', 'delete'], '/delete/{id}', [CityController::class,'destroy']);
        Route::match(['get', 'post'], '/restore/{state}', [CityController::class,'undestroy']);
        Route::match(['get', 'post'], '/stats', [CityController::class,'stats']);
    });

    //Client Type Routes /*******************************************************************/
    Route::middleware('auth:api')->prefix('client-type')->group(function () {
        Route::match(['get', 'post'], '', [ClientTypeController::class,'index']);
        Route::match(['get', 'post'],'/show/{id}',[ClientTypeController::class,'show']);
        Route::match(['get', 'post'], '/store', [ClientTypeController::class,'store']);
        Route::match(['get', 'post', 'put'], '/update/{id}', [ClientTypeController::class,'update']);
        Route::match(['get', 'delete'], '/delete/{id}', [ClientTypeController::class,'destroy']);
        Route::match(['get', 'post'], '/restore/{id}', [ClientTypeController::class,'undestroy']);
        Route::match(['get', 'post'], '/stats', [ClientTypeController::class,'stats']);
    });

    //Compound Period Routes /*******************************************************************/
    Route::middleware('auth:api')->prefix('compound-period')->group(function () {
        Route::match(['get', 'post'], '', [CompoundPeriodController::class,'index']);
        Route::match(['get', 'post'],'/show/{id}',[CompoundPeriodController::class,'show']);
        Route::match(['get', 'post'], '/store', [CompoundPeriodController::class,'store']);
        Route::match(['get', 'post', 'put'], '/update/{id}', [CompoundPeriodController::class,'update']);
        Route::match(['get', 'delete'], '/delete/{id}', [CompoundPeriodController::class,'destroy']);
        Route::match(['get', 'post'], '/restore/{id}', [CompoundPeriodController::class,'undestroy']);
        Route::match(['get', 'post'], '/stats', [CompoundPeriodController::class,'stats']);
    });

    //Transaction-Type Routes /*******************************************************************/
    Route::middleware('auth:api')->prefix('transaction-type')->group(function () {
        Route::match(['get', 'post'], '', [TransactionTypeController::class,'index']);
        Route::match(['get', 'post'],'/show/{id}',[TransactionTypeController::class,'show']);
        Route::match(['get', 'post'], '/store', [TransactionTypeController::class,'store']);
        Route::match(['get', 'post', 'put'], '/update/{id}', [TransactionTypeController::class,'update']);
        Route::match(['get', 'delete'], '/delete/{id}', [TransactionTypeController::class,'destroy']);
        Route::match(['get', 'post'], '/restore/{id}', [TransactionTypeController::class,'undestroy']);
        Route::match(['get', 'post'], '/stats', [TransactionTypeController::class,'stats']);
    });

    // Audit Trail Routes /*******************************************************************/
    Route::middleware('auth:api')->prefix('audit-trail')->group(function () {
        Route::match(['get', 'post'], '', [AuditTrailController::class,'index']);
        Route::match(['get', 'post'],'/show/{id}',[AuditTrailController::class,'show']);
        Route::match(['get', 'post'], '/store', [AuditTrailController::class,'store']);
        Route::match(['get', 'post', 'put'], '/update/{id}', [AuditTrailController::class,'update']);
        Route::match(['get', 'delete'], '/delete/{id}', [AuditTrailController::class,'destroy']);
        Route::match(['get', 'post'], '/restore/{id}', [AuditTrailController::class,'undestroy']);
        Route::match(['get', 'post'], '/stats', [AuditTrailController::class,'stats']);
    });

    // Role Routes /*******************************************************************/
    Route::middleware('auth:api')->prefix('role')->group(function () {
        Route::match(['get', 'post'], '', [RoleController::class,'index']);
        Route::match(['get', 'post'],'/show/{id}',[RoleController::class,'show']);
        Route::match(['get', 'post'], '/store', [RoleController::class,'store']);
        Route::match(['get', 'post', 'put'], '/update/{id}', [RoleController::class,'update']);
        Route::match(['get', 'delete'], '/delete/{id}', [RoleController::class,'destroy']);
        Route::match(['get', 'post'], '/restore/{id}', [RoleController::class,'undestroy']);
        Route::match(['get', 'post'], '/stats', [RoleController::class,'stats']);
    });

    //Notification Routes /*******************************************************************/
    Route::middleware('auth:api')->prefix('notification')->group(function () {
        Route::match(['get', 'post'], '', [NotificationController::class,'index']);
        Route::match(['get', 'post'],'/show/{id}',[NotificationController::class,'show']);
        Route::match(['get', 'post'], '/store', [NotificationController::class,'store']);
        Route::match(['get', 'post', 'put'], '/update/{id}', [NotificationController::class,'update']);
        Route::match(['get', 'delete'], '/delete/{id}', [NotificationController::class,'destroy']);
        Route::match(['get', 'post'], '/restore/{id}', [NotificationController::class,'undestroy']);
        Route::match(['get', 'post'], '/stats', [NotificationController::class,'stats']);
    });

    //Transaction Routes /*******************************************************************/
    Route::middleware('auth:api')->prefix('transaction')->group(function () {
        Route::match(['get', 'post'], '', [TransactionController::class,'index']);
        Route::match(['get', 'post'],'/show/{id}',[TransactionController::class,'show']);
        Route::match(['get', 'post'], '/store', [TransactionController::class,'store']);
        Route::match(['get', 'post', 'put'], '/update/{id}', [TransactionController::class,'update']);
        Route::match(['get', 'delete'], '/delete/{id}', [TransactionController::class,'destroy']);
        Route::match(['get', 'nullify'], '/nullify/{id}', [TransactionController::class,'nullify']);
        Route::match(['get', 'post'], '/restore/{id}', [TransactionController::class,'undestroy']);
        Route::match(['get', 'post'], '/stats', [TransactionController::class,'stats']);
        Route::match(['get', 'post'], '/previous/{id}', [TransactionController::class,'previous']);
        });

    //Status Routes /*******************************************************************/
    Route::middleware('auth:api')->prefix('status')->group(function () {
        Route::match(['get', 'post'], '', [StatusController::class,'index']);
    });

    //Terms Routes /*******************************************************************/
    Route::middleware('auth:api')->prefix('term')->group(function () {
        Route::match(['get', 'post'], '', [TermController::class,'index']);
        Route::match(['get', 'post'],'/show/{id}',[TermController::class,'show']);
        Route::match(['get', 'post'], '/store', [TermController::class,'store']);
        Route::match(['get', 'post', 'put'], '/update/{id}', [TermController::class,'update']);
        Route::match(['get', 'delete'], '/delete/{id}', [TermController::class,'destroy']);
        Route::match(['get', 'post'], '/restore/{id}', [TermController::class,'undestroy']);
        Route::match(['get', 'post'], '/stats', [TermController::class,'stats']);
        });

    //User Routes /*******************************************************************/
    Route::middleware('auth:api')->prefix('user')->group(function () {
        Route::match(['get', 'post'], '', [UserController::class,'index']);
        Route::match(['get', 'post'],'/show/{id}',[UserController::class,'show']);
        Route::match(['get', 'post'], '/store', [UserController::class,'store']);
        Route::match(['get', 'post', 'put'], '/update/{id}', [UserController::class,'update']);
        Route::match(['get', 'delete'], '/delete/{id}', [UserController::class,'destroy']);
        Route::match(['get', 'post'], '/restore/{id}', [UserController::class,'undestroy']);
        Route::match(['get', 'post'], '/stats', [UserController::class,'stats']);
        });
});
