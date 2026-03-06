<?php

namespace App\Exports;

use App\Models\Link;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LinkExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Link::with('linkMaster')
            ->get()
            ->map(fn($link) => [
                'link_no'              => $link->link_no,
                'link_name'            => $link->linkMaster?->link_name,
                'province_code'        => $link->province_code,
                'kabupaten_code'       => $link->kabupaten_code,
                'year'                 => $link->year,
                'link_code'            => $link->link_code,
                'status'               => $link->status,
                'function'             => $link->function,
                'class'                => $link->class,
                'project_number'       => $link->project_number,
                'access_status'        => $link->access_status,
                'link_length_official' => $link->link_length_official,
                'link_length_actual'   => $link->link_length_actual,
                'WTI'                  => $link->WTI,
                'MCA2'                 => $link->MCA2,
                'MCA3'                 => $link->MCA3,
                'MCA4'                 => $link->MCA4,
                'MCA5'                 => $link->MCA5,
                'CUMESA'               => $link->CUMESA,
                'ESA0'                 => $link->ESA0,
                'AADT'                 => $link->AADT,
            ]);
    }

    public function headings(): array
    {
        return [
            'Link_No',
            'Link_Name',
            'Province_Code',
            'Kabupaten_Code',
            'Year',
            'Link_Code',
            'Status',
            'Function',
            'Class',
            'Project_Number',
            'Access_Status',
            'Link_Length_Official',
            'Link_Length_Actual',
            'WTI',
            'MCA2',
            'MCA3',
            'MCA4',
            'MCA5',
            'CUMESA',
            'ESA0',
            'AADT',
        ];
    }
}