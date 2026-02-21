<?php
// FILE: app/Exports/Templates/DRPTemplate.php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DRPTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
            ['32', '3201', '001', 1, 0.000, 1, 0.100, -6, 30, 0, 106, 45, 0, 'S', 'Titik awal', ''],
            ['32', '3201', '001', 2, 0.100, 2, 0.100, -6, 30, 5, 106, 45, 5, 'S', 'Titik kedua', ''],
        ];
    }

    public function headings(): array
    {
        return [
            'Province_Code*',
            'Kabupaten_Code*',
            'Link_No* (bukan Link_Id)',
            'DRP_Num*',
            'Chainage*',
            'DRP_Order',
            'DRP_Length',
            'DRP_North_Deg',
            'DRP_North_Min',
            'DRP_North_Sec',
            'DRP_East_Deg',
            'DRP_East_Min',
            'DRP_East_Sec',
            'DRP_Type (kode dari tabel code_drptype)',
            'DRP_Desc',
            'DRP_Comment',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:P1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => 'center', 'wrapText' => true],
        ]);
        $sheet->getStyle('A1:E1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DC2626']],
        ]);
        $sheet->getStyle('A2:P3')->applyFromArray([
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
            'A' => 18, 'B' => 18, 'C' => 30, 'D' => 12, 'E' => 12,
            'F' => 12, 'G' => 12, 'H' => 15, 'I' => 15, 'J' => 15,
            'K' => 15, 'L' => 15, 'M' => 15, 'N' => 35, 'O' => 20, 'P' => 20,
        ];
    }
}