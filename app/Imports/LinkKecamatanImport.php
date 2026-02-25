<?php

namespace App\Imports;

use App\Models\LinkMaster; // ✅ Ganti dari Link ke LinkMaster
use App\Models\LinkKecamatan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class LinkKecamatanImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, SkipsOnError
{
    use SkipsErrors;

    protected $skippedCount = 0;
    protected $errors = [];
    protected $linkMasterIdCache = []; // ✅ Rename cache

    public function model(array $row)
    {
        $linkNo        = $row['link_no'] ?? null;
        $provinceCode  = $row['province_code'] ?? null;
        $kabupatenCode = $row['kabupaten_code'] ?? null;
        $kecamatanCode = $row['kecamatan_code'] ?? null;

        if (empty($linkNo) || empty($provinceCode) || empty($kabupatenCode) || empty($kecamatanCode)) {
            $this->skippedCount++;
            $this->errors[] = "Baris dilewati: field wajib kosong (link_no: {$linkNo})";
            return null;
        }

        // ✅ Lookup link_master_id
        $linkMasterId = $this->getLinkMasterId($linkNo, $provinceCode, $kabupatenCode);

        if (!$linkMasterId) {
            $this->skippedCount++;
            $this->errors[] = "Dilewati: link_no '{$linkNo}' tidak ditemukan di link_master. Pastikan Ruas Jalan sudah diimport terlebih dahulu.";
            return null;
        }

        return new LinkKecamatan([
            'province_code'   => $provinceCode,
            'kabupaten_code'  => $kabupatenCode,
            'link_master_id'  => $linkMasterId, // ✅ Ganti dari link_id
            'drp_from'        => $row['drp_from'] ?? null,
            'drp_to'          => $row['drp_to'] ?? null,
            'kecamatan_code'  => $kecamatanCode,
        ]);
    }

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