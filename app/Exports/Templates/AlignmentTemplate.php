<?php
// FILE: app/Exports/Templates/AlignmentTemplate.php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AlignmentTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
            ['32', '3201', '001', '2024', 0, 0, -6, 30, 0.00, 106, 45, 0.00, '', 106.75, -6.50],
            ['32', '3201', '001', '2024', 100, 100, -6, 30, 5.00, 106, 45, 5.00, '', 106.76, -6.51],
        ];
    }

    public function headings(): array
    {
        return [
            'Province_Code*',
            'Kabupaten_Code*',
            'Link_No*',
            'Year*',
            'Chainage*',
            'Chainage_RB',
            'GPSPoint_North_Deg',
            'GPSPoint_North_Min',
            'GPSPoint_North_Sec',
            'GPSPoint_East_Deg',
            'GPSPoint_East_Min',
            'GPSPoint_East_Sec',
            'Section_WKT_Linestring',
            'East',
            'North',
            // TIDAK ADA: id, link_master_id - dihandle otomatis
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => 'center', 'wrapText' => true],
        ]);
        $sheet->getStyle('A1:E1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DC2626']],
        ]);
        $sheet->getStyle('A2:O3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF9C3']],
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
        ]);
        $sheet->freezePane('A2');
        $sheet->getRowDimension(1)->setRowHeight(45);
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18, 'B' => 18, 'C' => 38, 'D' => 10, 'E' => 12,
            'F' => 14, 'G' => 20, 'H' => 20, 'I' => 20,
            'K' => 20, 'L' => 20, 'M' => 20, 'N' => 30, 'O' => 15, 'P' => 15,
        ];
    }
}