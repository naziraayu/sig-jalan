<?php

namespace App\Exports;

use App\Models\DRP;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class DRPExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return DRP::select(
            'province_code',
            'kabupaten_code',
            'link_no',
            'drp_num',
            'chainage',
            'drp_order',
            'drp_length',
            'dpr_north_deg',
            'dpr_north_min',
            'dpr_north_sec',
            'dpr_east_deg',
            'dpr_east_min',
            'dpr_east_sec',
            'drp_type',
            'drp_desc',
            'drp_comment'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Province_Code',
            'Kabupaten_Code',
            'Link_No',
            'DRP_Num',
            'Chainage',
            'DRP_Order',
            'DRP_Length',
            'DRP_North_Deg',
            'DRP_North_Min',
            'DRP_North_Sec',
            'DRP_East_Deg',
            'DRP_East_Min',
            'DRP_East_Sec',
            'DRP_Type',
            'DRP_Desc',
            'DRP_Comment',
        ];
    }
}
