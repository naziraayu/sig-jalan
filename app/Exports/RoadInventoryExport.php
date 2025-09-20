<?php

namespace App\Exports;

use App\Models\RoadInventory;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class RoadInventoryExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return RoadInventory::all();
    } 
    public function headings(): array
    {
        return [
            'link_no',
            'province_code', 
            'kabupaten_code',
            'chainagefrom',
            'chainageto',
            'drp_from',
            'offset_from',
            'drp_to',
            'offset_to',
            'pave_width',
            'row',
            'pave_type',
            'should_width_l',
            'should_width_r',
            'should_type_l',
            'should_type_r',
            'drain_type_l',
            'drain_type_r',
            'terrain',
            'land_use_l',
            'land_use_r',
            'impassable',
            'impassablereason',
        ];
    }

        public function map($roadInventory): array
    {
        return [
            $roadInventory->link_no,
            $roadInventory->province_code,
            $roadInventory->kabupaten_code,
            $roadInventory->chainage_from,
            $roadInventory->chainage_to,
            $roadInventory->drp_from,
            $roadInventory->offset_from,
            $roadInventory->drp_to,
            $roadInventory->offset_to,
            $roadInventory->pave_width,
            $roadInventory->row,
            $roadInventory->pave_type,
            $roadInventory->should_width_L,
            $roadInventory->should_width_R,
            $roadInventory->should_type_L,
            $roadInventory->should_type_R,
            $roadInventory->drain_type_L,
            $roadInventory->drain_type_R,
            $roadInventory->terrain,
            $roadInventory->land_use_L,
            $roadInventory->land_use_R,
            $roadInventory->impassable,
            $roadInventory->impassable_reason,
        ];
    }
}
