<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static SuccessOK()
 * @method static static SuccessCreated()
 * @method static static SuccessAcceted()
 */
final class HttpStatusCode extends Enum
{
    //Success Statuses
    const SuccessOK = 200;
    const SuccessCreated = 201;
    const SuccessAccepted = 202;

    //Clients Errors Statuses
    const ClientErrorBadRequest=400;
    const ClientErrorUnauthorized=401;
    const ClientErrorForbidden=403;
    const ClientErrorNotFound=404;
    const ClientErrorMethodNotAllowed=405;
    const ClientErrorRequestTimeout=408;
    const ClientErrorConflict=409;
    const ClientErrorGone=410;
    const ClientErrorPayloadTooLarge=413;
    const ClientErrorTooManyRequests=429;

    //Organizations Errors Statuses
    const OrganizationErrorBadRequest=400;
    const OrganizationErrorNotFound=404;

    //Transaction Errors Statuses
    const TransactionErrorNotFound=404;

    //Coumpound Period Errors Statuses
    const CompundPeriodErrorBadRequest=400;
    const CompoundPeriodErrorNotFound=404;
    
    //States Errors Statuses
    const StateErrorBadRequest=400;
    const StateErrorNotFound=404;

    //City Errors Statuses
    const CityErrorBadRequest=400;
    const CityErrorNotFound=404;

    // Client Tye Errors Statuses
    const ClientTypeErrorBadRequest=400;
    const ClientTypeErrorNotFound=404;

    // Transaction Type Errors Statuses
    const TransactionTypeErrorBadRequest=400;
    const TransactionTypeErrorNotFound=404;
    
    // Audit Trail Errors Statuses
    const AuditTrailErrorBadRequest=400;
    const AuditTrailErrorNotFound=404;

    // Role Error Statuses
    const RoleErrorBadRequest=400;
    const RoleErrorNotFound=404;
    //Server Errors Statuses
    const ServerErrorInternalServerError=500;
    const ServerErrorBadGateway=502;

    //Notification Error Statuses
    const NotificationErrorNotFound=404;
    const NotificationErrorBadRequest=400;
 
}
