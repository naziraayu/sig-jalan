<?php
// FILE: app/Exports/Templates/KabupatenTemplate.php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class KabupatenTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
            ['32', '3201', 'KABUPATEN BOGOR', 'B.32', 'ISL001', '0', '1'],
            ['32', '3273', 'KOTA BANDUNG',    'B.32', 'ISL001', '1', '1'],
        ];
    }

    public function headings(): array
    {
        return [
            'Province_Code*',
            'Kabupaten_Code*',
            'Kabupaten_Name*',
            'Balai_Code*',
            'Island_Code*',
            'Default_Kabupaten',
            'Stable',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => 'center'],
        ]);
        $sheet->getStyle('A2:G3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF9C3']],
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
        ]);
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, 'B' => 20, 'C' => 35,
            'D' => 20, 'E' => 20, 'F' => 35, 'G' => 30,
        ];
    }
}