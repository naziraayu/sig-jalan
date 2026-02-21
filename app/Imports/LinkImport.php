<?php
// FILE: app/Imports/LinkImport.php (REPLACE file yang lama)

namespace App\Imports;

use App\Models\Link;
use App\Models\LinkMaster;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class LinkImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, SkipsOnError
{
    use SkipsErrors;

    protected $skippedCount = 0;
    protected $errors = [];

    public function model(array $row)
    {
        $linkNo       = $row['link_no'] ?? null;
        $linkName     = $row['link_name'] ?? null;
        $provinceCode = $row['province_code'] ?? null;
        $kabupatenCode = $row['kabupaten_code'] ?? null;
        $year         = $row['year'] ?? null;

        // Validasi field wajib
        if (empty($linkNo) || empty($provinceCode) || empty($kabupatenCode) || empty($year)) {
            $this->skippedCount++;
            $this->errors[] = "Baris dilewati: link_no/province_code/kabupaten_code/year kosong (link_no: {$linkNo})";
            return null;
        }

        // ✅ AUTO LOOKUP: Cari link_master berdasarkan link_no
        // Kalau belum ada → buat baru di link_master
        $linkMaster = LinkMaster::firstOrCreate(
            ['link_no' => $linkNo],
            [
                'link_name'      => $linkName ?? $linkNo,
                'province_code'  => $provinceCode,
                'kabupaten_code' => $kabupatenCode,
            ]
        );

        // Cek apakah kombinasi link_no + year sudah ada
        $exists = Link::where('link_no', $linkNo)->where('year', $year)->exists();
        if ($exists) {
            $this->skippedCount++;
            $this->errors[] = "Dilewati: link_no {$linkNo} tahun {$year} sudah ada di database";
            return null;
        }

        return new Link([
            'year'                 => $year,
            'province_code'        => $provinceCode,
            'kabupaten_code'       => $kabupatenCode,
            'link_no'              => $linkNo,
            'link_master_id'       => $linkMaster->id, // ✅ Otomatis dari lookup
            'link_code'            => $row['link_code'] ?? null,
            'link_name'            => $linkName,
            'status'               => $row['status'] ?? null,
            'function'             => $row['function'] ?? null,
            'class'                => $row['class'] ?? null,
            'project_number'       => $row['project_number'] ?? null,
            'access_status'        => $row['access_status'] ?? null,
            'link_length_official' => $row['link_length_official'] ?? 0,
            'link_length_actual'   => $row['link_length_actual'] ?? 0,
            'WTI'                  => $row['wti'] ?? 0,
            'MCA2'                 => $row['mca2'] ?? 0,
            'MCA3'                 => $row['mca3'] ?? 0,
            'MCA4'                 => $row['mca4'] ?? 0,
            'MCA5'                 => $row['mca5'] ?? 0,
            'CUMESA'               => $row['cumesa'] ?? 0,
            'ESA0'                 => $row['esa0'] ?? 0,
            'AADT'                 => $row['aadt'] ?? 0,
        ]);
    }

    public function chunkSize(): int { return 500; }
    public function batchSize(): int { return 500; }

    public function getSkippedCount() { return $this->skippedCount; }
    public function getErrors() { return $this->errors; }
}