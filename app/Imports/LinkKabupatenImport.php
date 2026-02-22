<?php

namespace App\Imports;

use App\Models\Link;
use App\Models\LinkKabupaten;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class LinkKabupatenImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, SkipsOnError
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

        if (empty($linkNo) || empty($provinceCode) || empty($kabupatenCode)) {
            $this->skippedCount++;
            $this->errors[] = "Baris dilewati: link_no/province_code/kabupaten_code kosong";
            return null;
        }

        // ✅ AUTO LOOKUP link_id dari link_no
        $linkId = $this->getLinkId($linkNo, $provinceCode, $kabupatenCode);

        if (!$linkId) {
            $this->skippedCount++;
            $this->errors[] = "Dilewati: link_no '{$linkNo}' tidak ditemukan. Import Ruas Jalan terlebih dahulu.";
            return null;
        }

        return new LinkKabupaten([
            'province_code'  => $provinceCode,
            'kabupaten_code' => $kabupatenCode,
            'link_id'        => $linkId, // ✅ Dari lookup otomatis
            'drp_from'       => $row['drp_from'] ?? null,
            'drp_to'         => $row['drp_to'] ?? null,
            'kabupaten'      => $row['kabupaten'] ?? null,
        ]);
    }

    protected function getLinkId(string $linkNo, string $provinceCode, string $kabupatenCode): ?int
    {
        $cacheKey = "{$linkNo}_{$provinceCode}_{$kabupatenCode}";

        if (!isset($this->linkIdCache[$cacheKey])) {
            $link = Link::where('link_no', $linkNo)
                ->where('province_code', $provinceCode)
                ->where('kabupaten_code', $kabupatenCode)
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