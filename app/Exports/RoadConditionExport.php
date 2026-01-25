<?php

namespace App\Exports;

use App\Models\RoadCondition;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RoadConditionExport implements 
    FromQuery, 
    WithHeadings, 
    WithMapping, 
    WithColumnFormatting,
    WithChunkReading,
    ShouldAutoSize,
    WithStrictNullComparison
{
    protected $year;
    protected $provinceCode;

    public function __construct($year = null, $provinceCode = null)
    {
        $this->year = $year;
        $this->provinceCode = $provinceCode;
    }

    public function query()
    {
        // ✅ OPTIMIZED: Gunakan select() yang lebih efisien
        $query = RoadCondition::query()
            ->select([ 
                'year',
                'sdi_value',
                'sdi_category',
                'reference_year',
                'province_code',
                'kabupaten_code',
                'link_id',
                'link_no',
                'chainage_from',
                'chainage_to',
                'drp_from',
                'offset_from',
                'drp_to',
                'offset_to',
                'roughness',
                'bleeding_area',
                'ravelling_area',
                'desintegration_area',
                'crack_dep_area',
                'patching_area',
                'oth_crack_area',
                'pothole_area',
                'rutting_area',
                'edge_damage_area',
                'crossfall_area',
                'depressions_area',
                'erosion_area',
                'waviness_area',
                'gravel_thickness_area',
                'concrete_cracking_area',
                'concrete_spalling_area',
                'concrete_structural_cracking_area',
                'concrete_corner_break_no',
                'concrete_pumping_no',
                'concrete_blowouts_area',
                'crack_width',
                'pothole_count',
                'rutting_depth',
                'shoulder_l',
                'shoulder_r',
                'drain_l',
                'drain_r',
                'slope_l',
                'slope_r',
                'footpath_l',
                'footpath_r',
                'sign_l',
                'sign_r',
                'guide_post_l',
                'guide_post_r',
                'barrier_l',
                'barrier_r',
                'road_marking_l',
                'road_marking_r',
                'iri',
                'rci',
                'analysis_base_year',
                'segment_tti',
                'survey_by',
                'paved',
                'pavement',
                'check_data',
                'composition',
                'crack_type',
                'pothole_size',
                'should_cond_l',
                'should_cond_r',
                'crossfall_shape',
                'gravel_size',
                'gravel_thickness',
                'distribution',
                'edge_damage_area_r',
                'survey_by2',
                'survey_date',
                'section_status',
            ]);

        // ✅ FIXED: Terapkan filter tahun jika diberikan
        if ($this->year) {
            $query->where('year', $this->year);
        }

        // ✅ FIXED: Terapkan filter provinsi jika diberikan
        if ($this->provinceCode) {
            $query->where('province_code', $this->provinceCode);
        }

        return $query->orderBy('year', 'desc')
                    ->orderBy('link_no', 'asc')
                    ->orderBy('chainage_from', 'asc');
    }

    public function chunkSize(): int
    {
        // ✅ OPTIMIZED: Naikkan chunk size untuk performa lebih baik
        // 500 terlalu kecil, 1000-2000 lebih optimal untuk data besar
        return 1000;
    }

    public function map($row): array
    {
        return [
            $row->year,
            $row->sdi_value,
            $row->sdi_category,
            $row->reference_year,
            $row->province_code,
            $row->kabupaten_code,
            $row->link_id,
            $row->link_no,
            $row->chainage_from,
            $row->chainage_to,
            $row->drp_from,
            $row->offset_from,
            $row->drp_to,
            $row->offset_to,
            $row->roughness ? 'Ya' : 'Tidak',
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
            $row->paved ? 'Ya' : 'Tidak',
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

    public function headings(): array
    {
        return [
            'Year',
            'SDI_Value',
            'SDI_Category',
            'Reference_Year',
            'Province_Code',
            'Kabupaten_Code',
            'Link_Id',
            'Link_No',
            'Chainage_From',
            'Chainage_To',
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
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_TEXT, // Province_Code
            'F' => NumberFormat::FORMAT_TEXT, // Kabupaten_Code
            'G' => NumberFormat::FORMAT_TEXT, // Link_Id
            'H' => NumberFormat::FORMAT_TEXT, // Link_No
            'I' => NumberFormat::FORMAT_NUMBER_00, // Chainage_From
            'J' => NumberFormat::FORMAT_NUMBER_00, // Chainage_To
        ];
    }
}