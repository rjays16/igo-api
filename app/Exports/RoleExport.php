<?php

namespace App\Exports;

use App\Http\Resources\RoleExportResource;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;

class RoleExport implements FromCollection
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
        return RoleExportResource::collection($this->dataResult);
    }
}
