<?php
// FILE: app/Exports/Templates/RoadConditionTemplate.php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RoadConditionTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        // Contoh 1 baris saja karena kolom sangat banyak
        return [
            [
                '2026',     // year
                '',         // sdi_value (dihitung otomatis)
                '',         // sdi_category (dihitung otomatis)
                '2025',     // reference_year
                '32',       // province_code
                '3201',     // kabupaten_code
                '001',      // link_no (bukan link_id!)
                0,          // chainage_from
                100,        // chainage_to
                1,          // drp_from
                0,          // offset_from
                2,          // drp_to
                0,          // offset_to
                0,          // roughness (1=Ya, 0=Tidak)
                0,          // bleeding_area
                0,          // ravelling_area
                0,          // desintegration_area
                0,          // crack_dep_area
                0,          // patching_area
                0,          // oth_crack_area
                0,          // pothole_area
                0,          // rutting_area
                0,          // edge_damage_area
                0,          // crossfall_area
                0,          // depressions_area
                0,          // erosion_area
                0,          // waviness_area
                0,          // gravel_thickness_area
                0,          // concrete_cracking_area
                0,          // concrete_spalling_area
                0,          // concrete_structural_cracking_area
                0,          // concrete_corner_break_no
                0,          // concrete_pumping_no
                0,          // concrete_blowouts_area
                0,          // crack_width
                0,          // pothole_count
                0,          // rutting_depth
                'G',        // shoulder_l
                'G',        // shoulder_r
                'G',        // drain_l
                'G',        // drain_r
                'G',        // slope_l
                'G',        // slope_r
                'N',        // footpath_l
                'N',        // footpath_r
                0,          // sign_l
                0,          // sign_r
                0,          // guide_post_l
                0,          // guide_post_r
                0,          // barrier_l
                0,          // barrier_r
                0,          // road_marking_l
                0,          // road_marking_r
                0,          // iri
                0,          // rci
                '2024',     // analysis_base_year
                0,          // segment_tti
                'Surveyor A', // survey_by
                1,          // paved (1=Ya, 0=Tidak)
                'AC',       // pavement
                '',         // check_data
                '',         // composition
                '',         // crack_type
                '',         // pothole_size
                '',         // should_cond_l
                '',         // should_cond_r
                '',         // crossfall_shape
                '',         // gravel_size
                0,          // gravel_thickness
                '',         // distribution
                0,          // edge_damage_area_r
                '',         // survey_by2
                '2024-01-15', // survey_date
                '',         // section_status
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Year*',
            'SDI_Value (kosongkan, dihitung otomatis)',
            'SDI_Category (kosongkan, dihitung otomatis)',
            'Reference_Year',
            'Province_Code*',
            'Kabupaten_Code*',
            'Link_No*',
            'Chainage_From*',
            'Chainage_To*',
            'DRP_From',
            'Offset_From',
            'DRP_To',
            'Offset_To',
            'Roughness',
            'Bleeding_Area',
            'Ravelling_Area',
            'Desintegration_Area',
            'Crack_Dep_Area',
            'Patching_Area',
            'Oth_Crack_Area',
            'Pothole_Area',
            'Rutting_Area',
            'Edge_Damage_Area',
            'Crossfall_Area',
            'Depressions_Area',
            'Erosion_Area',
            'Waviness_Area',
            'Gravel_Thickness_Area',
            'Concrete_Cracking_Area',
            'Concrete_Spalling_Area',
            'Concrete_Structural_Cracking_Area',
            'Concrete_Corner_Break_No',
            'Concrete_Pumping_No',
            'Concrete_Blowouts_Area',
            'Crack_Width',
            'Pothole_Count',
            'Rutting_Depth',
            'Shoulder_L',
            'Shoulder_R',
            'Drain_L',
            'Drain_R',
            'Slope_L',
            'Slope_R',
            'Footpath_L',
            'Footpath_R',
            'Sign_L',
            'Sign_R',
            'Guide_Post_L',
            'Guide_Post_R',
            'Barrier_L',
            'Barrier_R',
            'Road_Marking_L',
            'Road_Marking_R',
            'IRI',
            'RCI',
            'Analysis_Base_Year',
            'Segment_TTI',
            'Survey_By',
            'Paved',
            'Pavement',
            'Check_Data',
            'Composition',
            'Crack_Type',
            'Pothole_Size',
            'Should_Cond_L',
            'Should_Cond_R',
            'Crossfall_Shape',
            'Gravel_Size',
            'Gravel_Thickness',
            'Distribution',
            'Edge_Damage_Area_R',
            'Survey_By2',
            'Survey_Date',
            'Section_Status',
            // TIDAK ADA: id, link_id - dihandle otomatis
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header row
        $sheet->getStyle('A1:BQ1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => 'center', 'wrapText' => true],
        ]);
        // Kolom wajib (Year, Province, Kabupaten, Link_No, Chainage) - merah
        $sheet->getStyle('A1:A1')->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DC2626']]]);
        $sheet->getStyle('E1:I1')->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DC2626']]]);
        // SDI kolom - abu abu (auto-generated)
        $sheet->getStyle('B1:C1')->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '6B7280']]]);

        // Contoh row
        $sheet->getStyle('A2:BQ2')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF9C3']],
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
        ]);

        $sheet->freezePane('A2');
        $sheet->getRowDimension(1)->setRowHeight(60);
        return [];
    }

    public function columnWidths(): array
    {
        $widths = [];
        // Set semua kolom ke lebar 18 secara default
        foreach (range('A', 'Z') as $col) {
            $widths[$col] = 18;
        }
        // Kolom dengan keterangan panjang - lebih lebar
        $widths['B'] = 35; // SDI Value note
        $widths['C'] = 35; // SDI Category note
        $widths['G'] = 38; // Link_No note
        $widths['BQ'] = 30; // Survey Date

        return $widths;
    }
}