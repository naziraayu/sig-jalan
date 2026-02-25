<?php
// FILE: app/Imports/RoadInventoryImport.php (REPLACE file yang lama)

namespace App\Imports;

use App\Models\Link;
use App\Models\RoadInventory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class RoadInventoryImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts, SkipsOnError
{
    use SkipsErrors;

    protected $skippedCount = 0;
    protected $errors = [];
    protected $linkIdCache = [];

    public function model(array $row)
    {
        $linkNo        = $row['link_no'] ?? null;
        $provinceCode  = $row['province_code'] ?? null;
        $kabupatenCode = $row['kabupaten_code'] ?? null;
        $year          = $row['year'] ?? null;

        if (empty($linkNo) || empty($provinceCode) || empty($kabupatenCode)) {
            $this->skippedCount++;
            $this->errors[] = "Baris dilewati: link_no/province_code/kabupaten_code kosong";
            return null;
        }

        // ✅ AUTO LOOKUP link_id dari link_no + province + kabupaten
        $linkId = $this->getLinkId($linkNo, $provinceCode, $kabupatenCode, $year);

        if (!$linkId) {
            $this->skippedCount++;
            $this->errors[] = "Dilewati: link_no '{$linkNo}' tidak ditemukan. Import Ruas Jalan terlebih dahulu.";
            return null;
        }

        return new RoadInventory([
            'province_code'    => $provinceCode,
            'kabupaten_code'   => $kabupatenCode,
            'link_id'          => $linkId, // ✅ Dari lookup otomatis
            'link_no'          => $linkNo,
            'year'             => $year,
            'chainage_from'    => $row['chainagefrom'] ?? 0,
            'chainage_to'      => $row['chainageto'] ?? 0,
            'drp_from'         => $row['drp_from'] ?? null,
            'offset_from'      => $row['offset_from'] ?? null,
            'drp_to'           => $row['drp_to'] ?? null,
            'offset_to'        => $row['offset_to'] ?? null,
            'pave_width'       => $row['pave_width'] ?? null,
            'row'              => $row['row'] ?? null,
            'pave_type'        => $row['pave_type'] ?? null,
            'should_width_L'   => $row['should_width_l'] ?? null,
            'should_width_R'   => $row['should_width_r'] ?? null,
            'should_type_L'    => $row['should_type_l'] ?? null,
            'should_type_R'    => $row['should_type_r'] ?? null,
            'drain_type_L'     => $row['drain_type_l'] ?? null,
            'drain_type_R'     => $row['drain_type_r'] ?? null,
            'terrain'          => $row['terrain'] ?? null,
            'land_use_L'       => $row['land_use_l'] ?? null,
            'land_use_R'       => $row['land_use_r'] ?? null,
            'impassable'       => $row['impassable'] ?? 0,
            'impassable_reason' => $row['impassable_reason'] ?? null,
        ]);
    }

    /**
     * ✅ Lookup link_id dengan caching
     */
    protected function getLinkId(string $linkNo, string $provinceCode, string $kabupatenCode, ?int $year = null): ?int
    {
        $cacheKey = "{$linkNo}_{$provinceCode}_{$kabupatenCode}_{$year}";

        if (!isset($this->linkIdCache[$cacheKey])) {
            $link = Link::where('link_no', $linkNo)
                ->where('province_code', $provinceCode)
                ->where('kabupaten_code', $kabupatenCode)
                ->when($year, fn($q) => $q->where('year', $year))
                ->select('id')
                ->first();

            $this->linkIdCache[$cacheKey] = $link ? $link->id : null;
        }

        return $this->linkIdCache[$cacheKey];
    }

    public function chunkSize(): int { return 1000; }
    public function batchSize(): int { return 1000; }
    public function getSkippedCount() { return $this->skippedCount; }
    public function getErrors() { return $this->errors; }
}