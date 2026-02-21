<?php
// FILE: app/Exports/Templates/RoadInventoryTemplate.php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RoadInventoryTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
            ['32', '3201', '001', '2024', 0, 100, 1, 0, 2, 0, 3.5, 6.0, 'AC', 1.0, 1.0, 'AC', 'AC', 'U', 'U', 'F', 'R', 'R', 0, ''],
            ['32', '3201', '001', '2024', 100, 200, 2, 0, 3, 0, 4.0, 7.0, 'AC', 1.5, 1.5, 'AC', 'AC', 'U', 'U', 'F', 'R', 'R', 0, ''],
        ];
    }

    public function headings(): array
    {
        return [
            'Province_Code*',
            'Kabupaten_Code*',
            'Link_No* (sistem otomatis cari Link_Id)',
            'Year*',
            'Chainage_From*',
            'Chainage_To*',
            'DRP_From',
            'Offset_From',
            'DRP_To',
            'Offset_To',
            'Pave_Width',
            'ROW',
            'Pave_Type (kode pavement)',
            'Should_Width_L',
            'Should_Width_R',
            'Should_Type_L (kode pavement)',
            'Should_Type_R (kode pavement)',
            'Drain_Type_L (kode drain)',
            'Drain_Type_R (kode drain)',
            'Terrain (kode terrain)',
            'Land_Use_L (kode land use)',
            'Land_Use_R (kode land use)',
            'Impassable (1=Ya, 0=Tidak)',
            'Impassable_Reason (kode impassable)',
            // TIDAK ADA: id, link_id - dihandle otomatis
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:X1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => 'center', 'wrapText' => true],
        ]);
        // Kolom wajib merah
        $sheet->getStyle('A1:F1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DC2626']],
        ]);
        $sheet->getStyle('A2:X3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF9C3']],
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
        ]);
        $sheet->freezePane('A2');
        $sheet->getRowDimension(1)->setRowHeight(50);
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18, 'B' => 18, 'C' => 38, 'D' => 10, 'E' => 15,
            'F' => 15, 'G' => 12, 'H' => 14, 'I' => 12, 'J' => 14,
            'K' => 14, 'L' => 12, 'M' => 25, 'N' => 16, 'O' => 16,
            'P' => 25, 'Q' => 25, 'R' => 25, 'S' => 25, 'T' => 20,
            'U' => 22, 'V' => 22, 'W' => 25, 'X' => 30,
        ];
    }
}