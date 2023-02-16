<?php

namespace App\Exports;

use App\Http\Resources\ClientExportResource;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

class ClientExport implements FromCollection,WithHeadings
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
        return ClientExportResource::collection($this->dataResult);
    }

     public function headings(): array
    {
        return [
            'ID',
            'User ID',
            'Message',
            'Status',
            'Created at'
        ];
    }
}
