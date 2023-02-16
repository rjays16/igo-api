<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class SystemMessage extends Enum
{

    //Loan Message
    const Loan = " Loan ";

    const InvalidCredentials = "Authentication Fail. Invalid Credentials";
    const ApplicatonError = "Applicaton Error";
    const GeneralError = "General Error";
    const QueryError = "Query Error";
    const ValidationError="Validation Errors";

    //File Messages
    const FileFormatNotSupported = "File format not supported";


     //Common Messages
    const ActiveRecords = "active_records";
    const SoftDeletedRecords ="soft_deleted_records";
    const TotalRecords = "total_records";
    const Success = "success";
    const Error = "error";
    const Messsage = "message";
    const Data = "data";
    const NotFound=" Not Found";
    const AlreadyBeenUsed = " as it is already been used as referrence by other records";
    const StatsRetrieved = "Stats has been retrieved";

    //Clients Messages
    const ClientRecordCreated="New client record has been created";
    const ClientRecordUpdated="Client record has been updated";
    const ClientRecordDeleted="Client record has been deleted";
    const ClientRecordRestored="Client record has been restored";
    const ClientRecordFound="Client record found";
    const ClientRecordRetrieved="Client record has been retrieved";
    const ClientNoRecordFound="No client record found";
    const ClientID="Client id ";
    const ClientCanNotDelete="Can not delete client id ";


    //Accounts Messages
    const AccountRecordCreated="New account record has been created";
    const AccountRecordUpdated="Account record has been updated";
    const AccountRecordDeleted="Account record has been deleted";
    const AccountRecordRestored="Account record has been restored";
    const AccountRecordFound="Account record found";
    const AccountRecordRetrieved="Account record has been retrieved";
    const AccountNoRecordFound="No account record found";
    const AccountID="Account id ";
    const AccountCanNotDelete="Can not delete account id ";

    //States Messages
    const StateRecordRestored="State record has been restored";
    const StateRecordUpdated="State record has been updated";
    const StateRecordCreated="New State record has been created";
    const StateRecordRetrieved="State record has been retrieved";
    const StateRecordDeleted="State record has been deleted";
    const StateNoRecordFound="No State record found";
    const StateCanNotDelete="Can not delete State ";
    const StateRecordFound="State record found";
    const State="State";

    //Cities Messages
    const CityRecordUpdated="City record has been updated";
    const CityRecordCreated="New City record has been created";
    const CityRecordRetrieved="City record has been retrieved";
    const CityRecordDeleted="City record has been deleted";
    const CityCanNotDelete="Can not delete City id ";
    const CityRecordFound="City record found";
    const CityID="City id ";
    const CityRecordRestored="City record has beeen restored";

    //Client Type Messages
    const ClientTypeRecordRetrieved="Client Type record has been retrieved";
    const ClientTypeRecordCreated="New Client Type record has been created";
    const ClientTypeRecordRestored="Client Type record has been restored";
    const ClientTypeRecordUpdated="Client Type record has been updated";
    const ClientTypeRecordDeleted="Client Type record has been deleted";
    const ClientTypeCanNotDelete="Can not delete Client type id ";
    const ClientTypeNoRecordFound="No Client Type record found";
    const ClientTypeRecordFound="Client Type record found";
    const ClientTypeID="Client Type id ";

    //Compound Period Messages
    const CompoundPeriodRecordRetrieved="Compound Period record has been retrieved";
    const CompoundPeriodRecordCreated="New Compound Period record has been created";
    const CompoundPeriodRecordRestored="Compound Period record has been restored";
    const CompoundPeriodRecordDeleted="Compound Period record has been deleted";
    const CompoundPeriodRecordUpdated="Compound Period record has been updated";
    const CompoundPeriodCanNotDelete="Can not delete Compound Period id";
    const CompoundPeriodNoRecordFound="No Compound Period record found";
    const CompoundPeriodRecordFound="Compound Period record found";
    const CompoundPeriodID="Compound Period id ";

     //Organization Messages
     const OrganizationRecordCreated="New Organization record has been created";
     const OrganizationRecordRetrieved="Organization record has been retrieved";
     const OrganizationRecordRestored="Organization record has been restored";
     const OrganizationRecordDeleted="Organization record has been deleted";
     const OrganizationRecordUpdated="Organization record has been updated";
     const OrganizationCanNotDelete="Can not delete Organization id ";
     const OrganizationNoRecordFound="No Organization record found";
     const OrganizationRecordFound="Organization record found";
     const OrganizationID="Organization id ";

     //Status Messages
     const StatusRecordRetrieved="Status record has been retrieved";

     // Transaction Type Messages
     const TransactionTypeRecordCreated="New Transaction Type record has been created";
     const TransactionTypeRecordFound="Transaction Type record found";
     const TransactionTypeRecordUpdated="Transaction Type record has been updated";
     const TransactionTypeRecordRestored="Transaction Type record has been restored";
     const TransactionTypeRetrieved="Transaction Type Period record has been retrieved";
     const TransactionTypeRecordDeleted="Transaction Type record has been deleted";
     const TransactionTypeCanNotDelete="Can not delete Transaction Type id ";
     const TransactionTypeTypeID="Transaction Type id ";

    //Term Messages
    const TermRecordCreated="New term record has been created";
    const TermRecordUpdated="Term record has been updated";
    const TermRecordDeleted="Term record has been deleted";
    const TermRecordRestored="Term record has been restored";
    const TermRecordFound="Term record found";
    const TermRecordRetrieved="Term record has been retrieved";
    const TermNoRecordFound="No term record found";
    const TermID="Term id ";
    const TermCanNotDelete="Can not delete term id ";

    //Transaction Messages
    const TransactionRecordCreated="New transaction record has been created";
    const TransactionRecordUpdated="Transaction record has been updated";
    const TransactionRecordDeleted="Transaction record has been deleted";
    const TransactionRecordRestored="Transaction record has been restored";
    const TransactionRecordFound="Transaction record found";
    const TransactionRecordRetrieved="Transaction record has been retrieved";
    const TransactionNoRecordFound="No transaction record found";
    const TransactionID="Transaction id ";
    const TransactionCanNotDelete="Can not delete term id ";
    const TransactionRecordNullified="Transaction record has been nullified";
    const BalancePerVault="balance_per_vault";
    const TransactionPreviousRecordRetrieved="Previous transaction has been retrieved.";

    //AuditTrail Messages
    const AuditTrailRecordCreated="New Audit Trail record has been created";
    const AuditTrailRecordUpdated="Audit Trail record has been updated";
    const AuditTrailRecordDeleted="Audit Trail record has been deleted";
    const AuditTrailRecordRestored="Audit Trail record has been restored";
    const AuditTrailRecordFound="Audit Trail record found";
    const AuditTrailRecordRetrieved="Audit Trail record has been retrieved";
    const AuditTrailNoRecordFound="No Audit Trail record found";
    const AuditTrailID="Audit Trail id ";
    const AuditTrailCanNotDelete="Can not delete Audit Trail id ";

    //Roles Messages
    const RoleRecordCreated="New Role record has been created";
    const RoleRecordRetrieved="Role record has been retrieved";
    const RoleRecordRestored="Role record has been restored";
    const RoleRecordDeleted="Role record has been deleted";
    const RoleRecordUpdated="Role record has been updated";
    const RoleCanNotDelete="Can not delete Role id ";
    const RoleNoRecordFound="No Role record found";
    const RoleRecordFound="Role record found";
    const RoleID="Role id ";

    //Pages Messages
    const PageAdminDashboard="/admin/dashboard";
    const PageAdminClients="/admin/clients";
    const PageAdminAccounts= "/admin/accounts";
    const PageAdminTerms="/admin/terms";
    const PageAdminTransaction="admin/transactions";
    const PageAdminSummary="/admin/summary";
    const PageAdminIADashboard="admin/iadashboard";
    const PageAdminStatements="admin/statements";
    const PageAdminInerestReport="admin/interestreport";
    const PageAdminAuditTrail="/admin/audittrail";
    const PageAdminUsers="/admin/users";
    const PageRoles="/admin/roles";
    const PageTransactionType="admin/miscellaneous/transaction_type";
    const PageAdminClientType="admin/miscellaneous/client_type";
    const PageAdminOrganizations="admin/miscellaneous/organization";
    const PageStates="admin/miscellaneous/state";
    const PageCities="admin/miscellaneous/cities";
    const PageProfile="admin/profile/profile";
    const PageProfilePassword="admin/profile/changepassword";

    //User Messages
    const UserRecordCreated="New user record has been created";
    const UserRecordUpdated="User record has been updated";
    const UserRecordDeleted="User record has been deleted";
    const UserRecordRestored="User record has been restored";
    const UserRecordFound="User record found";
    const UserRecordRetrieved="User record has been retrieved";
    const UserNoRecordFound="No user record found";
    const UserID="User id ";
    const UserCanNotDelete="Can not delete user id ";
    const UserProfilePicUpdated="User Profile Pic has been updated.";

   //Notification
   const NotificationRecordRetrieved="Notification record has been retrieved";
   const NotificationRecordCreated="New Notification record has been created";
   const NotificationRecordRestored="Notification record has been restored";
   const NotificationRecordDeleted="Notification record has been deleted";
   const NotificationRecordUpdated="Notification record has been updated";
   const NotificationCanNotDelete="Can not delete notification id ";
   const NotificationNoRecordFound="No Notification record found";
   const NotificationRecordFound="Notification record found";
   const NotificationID="Notification id ";

    //Authentication Messages
    const AuthLoginFailed="Authentication Failed. Invalid credentials";
    const AuthLoginSuccess="Authentication Success. Welcome.";
    const AuthLogout="User has been logout. Goodbye.";
    const AccessToken="access_token";
    const Permission="permission";
    const TokenID="token_id";
    const UnauthorizedAccess="Unauthorized Access. You do not have permission to access this route.";
    const ChangePasswordSuccess= "Change password success.";
    const IncorrectOldPasssword="Incorrect current password.";

}
