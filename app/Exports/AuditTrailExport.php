<?php

namespace App\Exports;

use App\Http\Resources\AuditTrailExportResource;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;

class AuditTrailExport implements FromCollection
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($dataResult)
    {
        $this->dataResult = $dataResult;
    }
    public function collection()
    {
        return AuditTrailExportResource::collection($this->dataResult);
    }
}
