<?php

namespace App\Imports;

use App\Models\Alignment;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class AlignmentImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Alignment([
            'province_code'         => $row['province_code'],
            'kabupaten_code'        => $row['kabupaten_code'],
            'link_master_id'        => $row['link_master_id'],
            'link_no'               => $row['link_no'],
            'year'                  => $row['year'],
            'chainage'              => $row['chainage'],
            'chainage_rb'           => $row['chainage_rb'],
            'gpspoint_north_deg'    => $row['gpspoint_north_deg'],
            'gpspoint_north_min'    => $row['gpspoint_north_min'],
            'gpspoint_north_sec'    => $row['gpspoint_north_sec'],
            'gpspoint_east_deg'     => $row['gpspoint_east_deg'],
            'gpspoint_east_min'     => $row['gpspoint_east_min'],
            'gpspoint_east_sec'     => $row['gpspoint_east_sec'],
            'section_wkt_linestring' => $row['section_wkt_linestring'],
            'east'                  => $row['east'],
            'north'                 => $row['north'], 
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
