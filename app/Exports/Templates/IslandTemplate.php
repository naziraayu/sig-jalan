<?php
// ============================================================
// FILE: app/Exports/Templates/IslandTemplate.php
// ============================================================
namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class IslandTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
            ['32', 'ISL001', 'Pulau Jawa'],
            ['61', 'ISL002', 'Pulau Kalimantan'],
        ];
    }

    public function headings(): array
    {
        return ['Province_Code*', 'Island_Code*', 'Island_Name*'];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => 'center'],
        ]);
        $sheet->getStyle('A2:C3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF9C3']],
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
        ]);
        return [];
    }

    public function columnWidths(): array
    {
        return ['A' => 20, 'B' => 20, 'C' => 35];
    }
}
