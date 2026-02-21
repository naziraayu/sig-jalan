<?php

namespace App\Imports;

use App\Models\Province;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // ✅ FIXED: Tambahkan WithHeadingRow

class ProvinceImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // ✅ FIXED: Dengan WithHeadingRow, baris header otomatis di-skip
        // dan $row berisi key berdasarkan nama kolom (lowercase snake_case)
        // Contoh key: 'province_code', 'province_name', 'defaultprovince', 'stable'

        // Skip baris kosong
        if (empty($row['province_code'])) {
            return null;
        }

        // ✅ FIXED: ProvinceExport mengexport nilai 'Ya'/'Tidak' dan 'Stable'/'Unstable'
        // Sebelumnya: (int)'Ya' = 0, (int)'Stable' = 0 --> semua tersimpan 0
        // Sekarang: parse string ke boolean dengan benar
        $defaultProvince = $this->parseBool($row['defaultprovince'] ?? null);
        $stable          = $this->parseStable($row['stable'] ?? null);

        return new Province([
            'province_code'    => $row['province_code'],
            'province_name'    => $row['province_name'] ?? null,
            'default_province' => $defaultProvince,
            'stable'           => $stable,
        ]);
    }

    /**
     * Parse nilai 'Ya'/'Tidak'/1/0/true/false -> integer (0 atau 1)
     */
    private function parseBool($value): int
    {
        if (is_null($value) || $value === '') return 0;
        if (is_bool($value)) return (int) $value;
        if (is_numeric($value)) return (int) $value;

        $lower = strtolower(trim($value));
        return in_array($lower, ['ya', 'yes', 'true', '1']) ? 1 : 0;
    }

    /**
     * Parse nilai 'Stable'/'Unstable'/1/0 -> integer (0 atau 1)
     */
    private function parseStable($value): int
    {
        if (is_null($value) || $value === '') return 0;
        if (is_bool($value)) return (int) $value;
        if (is_numeric($value)) return (int) $value;

        $lower = strtolower(trim($value));
        return in_array($lower, ['stable', 'ya', 'yes', 'true', '1']) ? 1 : 0;
    }
}