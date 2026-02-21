<?php
// FILE: app/Exports/Templates/LinkKecamatanTemplate.php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LinkKecamatanTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        // Gunakan link_no, sistem otomatis cari link_id
        return [
            ['32', '3201', '001', 1, 5, 'KEC001'],
            ['32', '3201', '001', 5, 10, 'KEC002'],
        ];
    }

    public function headings(): array
    {
        return [
            'Province_Code*',
            'Kabupaten_Code*',
            'Link_No* (sistem otomatis cari Link_Id)',
            'DRP_From*',
            'DRP_To*',
            'Kecamatan_Code*',
            // TIDAK ADA link_id - dihandle otomatis oleh sistem
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => 'center', 'wrapText' => true],
        ]);
        $sheet->getStyle('A1:F1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DC2626']],
        ]);
        $sheet->getStyle('A2:F3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF9C3']],
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
        ]);
        $sheet->freezePane('A2');
        return [];
    }

    public function columnWidths(): array
    {
        return ['A' => 18, 'B' => 18, 'C' => 38, 'D' => 12, 'E' => 12, 'F' => 20];
    }
}