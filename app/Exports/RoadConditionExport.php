<?php

namespace App\Exports;

use App\Models\RoadCondition;
use Maatwebsite\Excel\Concerns\FromQuery;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class RoadConditionExport implements FromQuery, WithHeadings, WithMapping, WithColumnFormatting
{
    /**
     * Ambil data pakai query, bukan ::all()
     */
    public function query()
    {
        // TAMBAHKAN orderBy yang jelas
        return RoadCondition::query()
            ->orderBy('year', 'desc')
            ->orderBy('link_no', 'asc')
            ->orderBy('chainage_from', 'asc');
    }

    /**
     * Mapping tiap row supaya urutan kolom sesuai
     */
    public function map($row): array
    {
        return [
            $row->year,
            $row->province_code,
            $row->kabupaten_code,
            "\t" .$row->link_no,
            $row->chainage_from,
            $row->chainage_to,
            $row->drp_from,
            $row->offset_from,
            $row->drp_to,
            $row->offset_to,
            $row->roughness,
            $row->bleeding_area,
            $row->ravelling_area,
            $row->desintegration_area,
            $row->crack_dep_area,
            $row->patching_area,
            $row->oth_crack_area,
            $row->pothole_area,
            $row->rutting_area,
            $row->edge_damage_area,
            $row->crossfall_area,
            $row->depressions_area,
            $row->erosion_area,
            $row->waviness_area,
            $row->gravel_thickness_area,
            $row->concrete_cracking_area,
            $row->concrete_spalling_area,
            $row->concrete_structural_cracking_area,
            $row->concrete_corner_break_no,
            $row->concrete_pumping_no,
            $row->concrete_blowouts_area,
            $row->crack_width,
            $row->pothole_count,
            $row->rutting_depth,
            $row->shoulder_l,
            $row->shoulder_r,
            $row->drain_l,
            $row->drain_r,
            $row->slope_l,
            $row->slope_r,
            $row->footpath_l,
            $row->footpath_r,
            $row->sign_l,
            $row->sign_r,
            $row->guide_post_l,
            $row->guide_post_r,
            $row->barrier_l,
            $row->barrier_r,
            $row->road_marking_l,
            $row->road_marking_r,
            $row->iri,
            $row->rci,
            $row->analysis_base_year,
            $row->segment_tti,
            $row->survey_by,
            $row->paved,
            $row->pavement,
            $row->check_data,
            $row->composition,
            $row->crack_type,
            $row->pothole_size,
            $row->should_cond_l,
            $row->should_cond_r,
            $row->crossfall_shape,
            $row->gravel_size,
            $row->gravel_thickness,
            $row->distribution,
            $row->edge_damage_area_r,
            $row->survey_by2,
            $row->survey_date,
            $row->section_status,
        ];
    }

    /**
     * Heading untuk kolom Excel
     */
    public function headings(): array
    {
        return [
            'Year',
            'Province_Code',
            'Kabupaten_Code',
            'Link_No',
            'ChainageFrom',
            'ChainageTo',
            'DRP_From',
            'Offset_From',
            'DRP_To',
            'Offset_To',
            'Roughness',
            'Bleeding_area',
            'Ravelling_area',
            'Desintegration_area',
            'CrackDep_area',
            'Patching_area',
            'OtherCrack_area',
            'Pothole_area',
            'Rutting_area',
            'EdgeDamage_area',
            'Crossfall_area',
            'Depressions_area',
            'Erosion_area',
            'Waviness_area',
            'GravelThickness_area',
            'Concrete_Cracking_area',
            'Concrete_Spalling_area',
            'Concrete_StructuralCracking_area',
            'Concrete_CornerBreakNo',
            'Concrete_PumpingNo',
            'Concrete_Blowouts_area',
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
            'GuidePost_L',
            'GuidePost_R',
            'Barrier_L',
            'Barrier_R',
            'RoadMarking_L',
            'RoadMarking_R',
            'IRI',
            'RCI',
            'AnalysisBaseYear',
            'SegmentTTI',
            'SurveyBy',
            'Paved',
            'Pavement',
            'CheckData',
            'Composition',
            'CrackType',
            'PotholeSize',
            'ShoulderCond_L',
            'ShoulderCond_R',
            'CrossfallShape',
            'GravelSize',
            'GravelThickness',
            'Distribution',
            'EdgeDamage_areaR',
            'SurveyBy2',
            'SurveyDate',
            'SectionStatus',
        ];
    }

     /**
     * Baca per chunk (misal 500 rows sekali proses)
     */
    // public function chunkSize(): int
    // {
    //     return 1000;
    // }

    /**
     * Format kolom tertentu sebagai text
     */
    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_TEXT,  // Kolom D = Link_No
            'B' => NumberFormat::FORMAT_TEXT,  // Kolom B = Province_Code
            'C' => NumberFormat::FORMAT_TEXT,  // Kolom C = Kabupaten_Code
        ];
    }
}
