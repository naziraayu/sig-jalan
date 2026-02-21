<?php
// ============================================================
// FILE: app/Exports/Templates/ProvinceTemplate.php
// ============================================================
namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProvinceTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        // Baris contoh agar user tau format yang benar
        return [
            ['32', 'JAWA BARAT', '1', '1'],
            ['33', 'JAWA TENGAH', '0', '1'],
        ];
    }

    public function headings(): array
    {
        return [
            'Province_Code*',
            'Province_Name*',
            'Default_Province (1=Ya, 0=Tidak)',
            'Stable (1=Stable, 0=Unstable)',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header row styling - background biru, teks putih, bold
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Baris contoh - background kuning muda
        $sheet->getStyle('A2:D3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF9C3']],
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
        ]);

        // Tambahkan note di baris 1
        $sheet->getComment('A1')->getText()->createTextRun('* = wajib diisi');

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 35,
            'C' => 35,
            'D' => 30,
        ];
    }
}