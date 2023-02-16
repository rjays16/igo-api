<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Http\Resources\UserExportResource;

class UserExport implements FromCollection
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
        return UserExportResource::collection($this->dataResult);
    }
}
