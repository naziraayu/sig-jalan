<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\RoadCondition;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class RoadConditionImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
         // ubah semua key jadi lowercase
        $row = array_change_key_case($row, CASE_LOWER);

        // bersihkan underscore berlebih (kayak "bleeding_")
        $cleaned = [];
        foreach ($row as $key => $value) {
            $cleaned[rtrim($key, '_')] = $value;
        }
        $row = $cleaned;

        $surveyDate = null;

        // Cek apakah kolom surveydate ada isinya
        if (!empty($row['surveydate'])) {
            // Kalau berupa angka (serial Excel)
            if (is_numeric($row['surveydate'])) {
                $surveyDate = Carbon::instance(ExcelDate::excelToDateTimeObject($row['surveydate']));
            } else {
                // Kalau sudah format date string (yyyy-mm-dd)
                $surveyDate = Carbon::parse($row['surveydate']);
            }
        }

        return new RoadCondition([
            'year'                          => $row['year'] ?? null,
            'province_code'                 => $row['province_code']?? null,
            'kabupaten_code'                => $row['kabupaten_code'] ?? null,
            'link_id'                       => $row['link_id'] ?? null,
            'link_no'                       => $row['link_no'] ?? null,
            'chainage_from'                 => $row['chainagefrom'] ?? null,
            'chainage_to'                   => $row['chainageto']?? null,
            'drp_from'                      => $row['drp_from']?? null,
            'offset_from'                   => $row['offset_from'] ?? null,
            'drp_to'                        => $row['drp_to'] ?? null,
            'offset_to'                     => $row['offset_to'] ?? null,
            'roughness'                     => $row['roughness'] ?? null,
            'bleeding_area'                 => $row['bleeding_area'] ?? null,
            'ravelling_area'                => $row['ravelling_area'] ?? null,
            'desintegration_area'           => $row['desintegration_area']?? null,
            'crack_dep_area'                => $row['crackdep_area'] ?? null,
            'patching_area'                 => $row['patching_area'] ?? null,
            'oth_crack_area'                => $row['othcrack_area'] ?? null,
            'pothole_area'                  => $row['pothole_area'] ?? null,
            'rutting_area'                  => $row['rutting_area'] ?? null,
            'edge_damage_area'              => $row['edgedamage_area'] ?? null,
            'crossfall_area'                => $row['crossfall_area'] ?? null,
            'depressions_area'              => $row['depressions_area'] ?? null,
            'erosion_area'                  => $row['erosion_area'] ?? null,
            'waviness_area'                 => $row['waviness_area'] ?? null,
            'gravel_thickness_area'         => $row['gravelthickness_area'] ?? null,
            'concrete_cracking_area'        => $row['concrete_cracking_area'] ?? null,
            'concrete_spalling_area'        => $row['concrete_spalling_area'] ?? null,
            'concrete_structural_cracking_area' => $row['concrete_structuralcracking_area'] ?? null,
            'concrete_corner_break_no'      => $row['concrete_cornerbreakno'] ?? null,
            'concrete_pumping_no'           => $row['concrete_pumpingno'] ?? null,
            'concrete_blowouts_area'        => $row['concrete_blowouts_area'] ?? null,
            'crack_width'                   => $row['crack_width'] ?? null,
            'pothole_count'                 => $row['pothole_count'] ?? null,
            'rutting_depth'                 => $row['rutting_depth'] ?? null,
            'shoulder_l'                    => $row['shoulder_l'] ?? null,
            'shoulder_r'                    => $row['shoulder_r'] ?? null,
            'drain_l'                       => $row['drain_l'] ?? null,
            'drain_r'                       => $row['drain_r'] ?? null,
            'slope_l'                       => $row['slope_l'] ?? null,
            'slope_r'                       => $row['slope_r'] ?? null,
            'footpath_l'                    => $row['footpath_l'] ?? null,
            'footpath_r'                    => $row['footpath_r'] ?? null,
            'sign_l'                        => $row['sign_l'] ?? null,
            'sign_r'                        => $row['sign_r'] ?? null,
            'guide_post_l'                  => $row['guidepost_l'] ?? null,
            'guide_post_r'                  => $row['guidepost_r'] ?? null,
            'barrier_l'                     => $row['barrier_l'] ?? null,
            'barrier_r'                     => $row['barrier_r'] ?? null,
            'road_marking_l'                => $row['roadmarking_l'] ?? null,
            'road_marking_r'                => $row['roadmarking_r'] ?? null,
            'iri'                           => $row['iri'] ?? null,
            'rci'                           => $row['rci'] ?? null,
            'analysis_base_year'            => $row['analysisbaseyear'] ?? null,
            'segment_tti'                   => $row['segmenttti'] ?? null,
            'survey_by'                     => $row['surveyby'] ?? null,
            'paved'                         => $row['paved'] ?? null,
            'pavement'                      => $row['pavement'] ?? null,
            'check_data'                    => $row['checkdata'] ?? null,
            'composition'                   => $row['composition'] ?? null,
            'crack_type'                    => $row['cracktype'] ?? null,
            'pothole_size'                  => $row['potholesize'] ?? null,
            'should_cond_l'                 => $row['shouldcond_l'] ?? null,
            'should_cond_r'                 => $row['shouldcond_r'] ?? null,
            'crossfall_shape'               => $row['crossfallshape'] ?? null,
            'gravel_size'                   => $row['gravelsize'] ?? null,
            'gravel_thickness'              => $row['gravelthickness'] ?? null,
            'distribution'                  => $row['distribution'] ?? null,
            'edge_damage_area_r'            => $row['edgedamage_area_r'] ?? null,
            'survey_by2'                    => $row['surveyby2'] ?? null,
            'survey_date'                   => $surveyDate,
            'section_status'                => $row['sectionstatus'] ?? null,
        ]);
    }

    public function chunkSize(): int
    {
        return 200;
    }

    public function batchSize(): int
    {
        return 200;
    }
}
