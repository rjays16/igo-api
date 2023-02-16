<?php

namespace App\Exports;

use App\Http\Resources\CompoundPeriodExportResource;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;

class CompoundPeriodExport implements FromCollection
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
        return CompoundPeriodExportResource::collection($this->dataResult);
    }
}
