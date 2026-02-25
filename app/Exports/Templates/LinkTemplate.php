<?php
// FILE: app/Exports/Templates/LinkTemplate.php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LinkTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'Template Ruas Jalan';
    }

    public function array(): array
    {
        // Contoh data - 2 baris agar user paham formatnya
        // CATATAN: link_no harus unik, link_name untuk link_master
        // Sistem otomatis lookup/insert ke tabel link_master berdasarkan link_no
        return [
            ['001', 'Ruas Jalan Contoh A', '32', '3201', '2024', 'LNK-001', '1', '1', '1', 'PRJ-001', '1', 10.5, 10.3, 0, 0, 0, 0, 0, 0, 0, 0],
            ['002', 'Ruas Jalan Contoh B', '32', '3273', '2024', 'LNK-002', '2', '2', '2', 'PRJ-002', '1', 25.0, 24.8, 0, 0, 0, 0, 0, 0, 0, 0],
        ];
    }

    public function headings(): array
    {
        return [
            'Link_No*',
            'Link_Name*',
            'Province_Code*',
            'Kabupaten_Code*',
            'Year*',
            'Link_Code',
            'Status',
            'Function',
            'Class',
            'Project_Number',
            'Access_Status',
            'Link_Length_Official',
            'Link_Length_Actual',
            'WTI',
            'MCA2',
            'MCA3',
            'MCA4',
            'MCA5',
            'CUMESA',
            'ESA0',
            'AADT',
            // TIDAK ADA: id, link_master_id (dihandle otomatis oleh sistem)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastCol = 'V'; // kolom terakhir (22 kolom)

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => 'center', 'wrapText' => true],
        ]);

        // Kolom wajib diisi - highlight lebih terang
        $sheet->getStyle('A1:E1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DC2626']],
        ]);

        $sheet->getStyle("A2:{$lastCol}3")->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF9C3']],
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
        ]);

        // Freeze header row
        $sheet->freezePane('A2');

        $sheet->getRowDimension(1)->setRowHeight(45);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, 'B' => 35, 'C' => 18, 'D' => 18, 'E' => 10,
            'F' => 15, 'G' => 35, 'H' => 35, 'I' => 35, 'J' => 20,
            'K' => 18, 'L' => 25, 'M' => 22, 'N' => 10, 'O' => 10,
            'P' => 10, 'Q' => 10, 'R' => 10, 'S' => 15, 'T' => 10,
            'U' => 10,
        ];
    }
}