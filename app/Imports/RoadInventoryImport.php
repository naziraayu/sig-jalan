<?php

namespace App\Imports;

use App\Models\RoadInventory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class RoadInventoryImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new RoadInventory([
            'link_no'             => $row['link_no'],
            'province_code'       => $row['province_code'],
            'kabupaten_code'      => $row['kabupaten_code'],
            'chainage_from'       => $row['chainagefrom'],
            'chainage_to'         => $row['chainageto'],
            'drp_from'            => $row['drp_from'],
            'offset_from'         => $row['offset_from'],
            'drp_to'              => $row['drp_to'],
            'offset_to'           => $row['offset_to'],
            'pave_width'          => $row['pave_width'],
            'row'                 => $row['row'],   // hati², nama field "row" bisa bentrok dengan reserved word di Excel / PHP
            'pave_type'           => $row['pave_type'],
            'should_width_L'       => $row['should_width_l'],
            'should_width_R'       => $row['should_width_r'],
            'should_type_L'       => $row['should_type_l'],
            'should_type_R'       => $row['should_type_r'],
            'drain_type_L'        => $row['drain_type_l'],
            'drain_type_R'        => $row['drain_type_r'],
            'terrain'             => $row['terrain'],
            'land_use_L'          => $row['land_use_l'],
            'land_use_R'          => $row['land_use_r'],
            'impassable'          => $row['impassable'],
            'impassable_reason'   => $row['impassablereason'],
        ]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }
}
