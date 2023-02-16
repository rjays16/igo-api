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
            'First Name',
            'Last Name',
            'Gender',
            'Date of Birth',
            'Email',
            'Phone',
            'Organization',
            'Address1',
            'Address2',
            'City',
            'State',
            'Zip',
            'Client Type',
            'CA_Date',
            'Note',
            'Tag',
            'Created at'
        ];
    }
}
