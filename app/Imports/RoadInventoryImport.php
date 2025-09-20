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
            'link_no'             => $row['link_no'] ?? null,
            'province_code'       => $row['province_code'] ?? null,
            'kabupaten_code'      => $row['kabupaten_code'] ?? null,
            'chainage_from'       => $row['chainagefrom'] ?? 0,
            'chainage_to'         => $row['chainageto'] ?? 0,
            'drp_from'            => $row['drp_from'] ?? null,
            'offset_from'         => $row['offset_from'] ?? null,
            'drp_to'              => $row['drp_to'] ?? null,
            'offset_to'           => $row['offset_to'] ?? null,
            'pave_width'          => $row['pave_width'] ?? null,
            'row'                 => $row['row'] ?? null,
            'pave_type'           => $row['pave_type'] ?? null,
            'should_width_L'      => $row['should_width_l'] ?? null,
            'should_width_R'      => $row['should_width_r'] ?? null,
            'should_type_L'       => $row['should_type_l'] ?? null,
            'should_type_R'       => $row['should_type_r'] ?? null,
            'drain_type_L'        => $row['drain_type_l'] ?? null,
            'drain_type_R'        => $row['drain_type_r'] ?? null,
            'terrain'             => $row['terrain'] ?? null,
            'land_use_L'          => $row['land_use_l'] ?? null,
            'land_use_R'          => $row['land_use_r'] ?? null,
            'impassable'          => $row['impassable'] ?? 0,
            'impassable_reason'   => $row['impassablereason'] ?? null,
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
