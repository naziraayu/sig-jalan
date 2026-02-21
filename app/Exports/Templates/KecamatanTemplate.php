<?php
// FILE: app/Exports/Templates/KecamatanTemplate.php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class KecamatanTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
            ['32', '3201', 'KEC001', 'KECAMATAN CIBINONG'],
            ['32', '3201', 'KEC002', 'KECAMATAN GUNUNG PUTRI'],
        ];
    }

    public function headings(): array
    {
        return [
            'Province_Code*',
            'Kabupaten_Code*',
            'Kecamatan_Code*',
            'Kecamatan_Name*',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => 'center'],
        ]);
        $sheet->getStyle('A2:D3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF9C3']],
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
        ]);
        return [];
    }

    public function columnWidths(): array
    {
        return ['A' => 20, 'B' => 20, 'C' => 20, 'D' => 35];
    }
}