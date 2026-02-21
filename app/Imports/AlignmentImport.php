<?php
// FILE: app/Imports/AlignmentImport.php (REPLACE file yang lama)

namespace App\Imports;

use App\Models\Alignment;
use App\Models\LinkMaster;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class AlignmentImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts, SkipsOnError
{
    use SkipsErrors;

    protected $skippedCount = 0;
    protected $errors       = [];
    protected $linkMasterIdCache = []; // ✅ Cache lookup

    public function model(array $row)
    {
        $linkNo        = $row['link_no'] ?? null;
        $provinceCode  = $row['province_code'] ?? null;
        $kabupatenCode = $row['kabupaten_code'] ?? null;

        if (empty($linkNo) || empty($provinceCode) || empty($kabupatenCode)) {
            $this->skippedCount++;
            $this->errors[] = "Baris dilewati: link_no/province_code/kabupaten_code kosong";
            return null;
        }

        // ✅ AUTO LOOKUP link_master_id dari link_no
        $linkMasterId = $this->getLinkMasterId($linkNo, $provinceCode, $kabupatenCode);

        if (!$linkMasterId) {
            $this->skippedCount++;
            $this->errors[] = "Dilewati: link_no '{$linkNo}' tidak ditemukan di link_master. Import Ruas Jalan terlebih dahulu.";
            return null;
        }

        return new Alignment([
            'province_code'          => $provinceCode,
            'kabupaten_code'         => $kabupatenCode,
            'link_master_id'         => $linkMasterId, // ✅ Dari lookup otomatis
            'link_no'                => $linkNo,
            'year'                   => $row['year'] ?? null,
            'chainage'               => $row['chainage'] ?? null,
            'chainage_rb'            => $row['chainage_rb'] ?? null,
            'gpspoint_north_deg'     => $row['gpspoint_north_deg'] ?? null,
            'gpspoint_north_min'     => $row['gpspoint_north_min'] ?? null,
            'gpspoint_north_sec'     => $row['gpspoint_north_sec'] ?? null,
            'gpspoint_east_deg'      => $row['gpspoint_east_deg'] ?? null,
            'gpspoint_east_min'      => $row['gpspoint_east_min'] ?? null,
            'gpspoint_east_sec'      => $row['gpspoint_east_sec'] ?? null,
            'section_wkt_linestring' => $row['section_wkt_linestring'] ?? null,
            'east'                   => $row['east'] ?? null,
            'north'                  => $row['north'] ?? null,
        ]);
    }

    /**
     * ✅ Lookup link_master_id dengan caching
     */
    protected function getLinkMasterId(string $linkNo, string $provinceCode, string $kabupatenCode): ?int
    {
        $cacheKey = "{$linkNo}_{$provinceCode}_{$kabupatenCode}";

        if (!isset($this->linkMasterIdCache[$cacheKey])) {
            $linkMaster = LinkMaster::where('link_no', $linkNo)
                ->where('province_code', $provinceCode)
                ->where('kabupaten_code', $kabupatenCode)
                ->select('id')
                ->first();

            $this->linkMasterIdCache[$cacheKey] = $linkMaster ? $linkMaster->id : null;
        }

        return $this->linkMasterIdCache[$cacheKey];
    }

    public function chunkSize(): int { return 1000; }
    public function batchSize(): int { return 1000; }
    public function getSkippedCount() { return $this->skippedCount; }
    public function getErrors() { return $this->errors; }
}