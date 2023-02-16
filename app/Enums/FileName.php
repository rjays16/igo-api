<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class FileName extends Enum
{
    const CSVFileFormat = ".csv";
    const EXCELFileFormat = ".xlsx";
    const PDFFileFormat = ".pdf";

    const EXCELFile ="XLSX";
    const CSVFile ="CSV";
    const PDFFile = "PDF";

    const ClientExportFile = "client_export";
    const AccountExportFile = "account_export";
    const TermExportFile = "term_export";
    const TransactionExportFile = "transaction_export";
    const AuditTrailExport ="audit_trail_export";
    const UserExportFile ="user_trail_export";
    const RoleExport ="role_trail_export";
    const NotificationExportFile = "notification_export";
    const UploadFileDestinationPath="public/profile_pics";
    const ProfilePicPath="/storage/profile_pics";

}
