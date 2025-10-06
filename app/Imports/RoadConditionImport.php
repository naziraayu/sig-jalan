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

class RoadConditionImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts, ShouldQueue
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
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
            'year'                          => $row['year'],
            'province_code'                 => $row['province_code'],
            'kabupaten_code'                => $row['kabupaten_code'],
            'link_no'                       => $row['link_no'],
            'chainage_from'                 => $row['chainagefrom'],
            'chainage_to'                   => $row['chainageto'],
            'drp_from'                      => $row['drp_from'],
            'offset_from'                   => $row['offset_from'],
            'drp_to'                        => $row['drp_to'],
            'offset_to'                     => $row['offset_to'],
            'roughness'                     => $row['roughness'],
            'bleeding_area'                 => $row['bleeding_area'],
            'ravelling_area'                => $row['ravelling_area'],
            'desintegration_area'           => $row['desintegration_area'],
            'crack_dep_area'                => $row['crackdep_area'],
            'patching_area'                 => $row['patching_area'],
            'oth_crack_area'                => $row['othcrack_area'],
            'pothole_area'                  => $row['pothole_area'],
            'rutting_area'                  => $row['rutting_area'],
            'edge_damage_area'              => $row['edgedamage_area'],
            'crossfall_area'                => $row['crossfall_area'],
            'depressions_area'              => $row['depressions_area'],
            'erosion_area'                  => $row['erosion_area'],
            'waviness_area'                 => $row['waviness_area'],
            'gravel_thickness_area'         => $row['gravelthickness_area'],
            'concrete_cracking_area'        => $row['concrete_cracking_area'],
            'concrete_spalling_area'        => $row['concrete_spalling_area'],
            'concrete_structural_cracking_area' => $row['concrete_structuralcracking_area'],
            'concrete_corner_break_no'      => $row['concrete_cornerbreakno'],
            'concrete_pumping_no'           => $row['concrete_pumpingno'],
            'concrete_blowouts_area'        => $row['concrete_blowouts_area'],
            'crack_width'                   => $row['crack_width'],
            'pothole_count'                 => $row['pothole_count'],
            'rutting_depth'                 => $row['rutting_depth'],
            'shoulder_l'                    => $row['shoulder_l'],
            'shoulder_r'                    => $row['shoulder_r'],
            'drain_l'                       => $row['drain_l'],
            'drain_r'                       => $row['drain_r'],
            'slope_l'                       => $row['slope_l'],
            'slope_r'                       => $row['slope_r'],
            'footpath_l'                    => $row['footpath_l'],
            'footpath_r'                    => $row['footpath_r'],
            'sign_l'                        => $row['sign_l'],
            'sign_r'                        => $row['sign_r'],
            'guide_post_l'                  => $row['guidepost_l'],
            'guide_post_r'                  => $row['guidepost_r'],
            'barrier_l'                     => $row['barrier_l'],
            'barrier_r'                     => $row['barrier_r'],
            'road_marking_l'                => $row['roadmarking_l'],
            'road_marking_r'                => $row['roadmarking_r'],
            'iri'                           => $row['iri'],
            'rci'                           => $row['rci'],
            'analysis_base_year'            => $row['analysisbaseyear'],
            'segment_tti'                   => $row['segmenttti'],
            'survey_by'                     => $row['surveyby'],
            'paved'                         => $row['paved'],
            'pavement'                      => $row['pavement'],
            'check_data'                    => $row['checkdata'],
            'composition'                   => $row['composition'],
            'crack_type'                    => $row['cracktype'],
            'pothole_size'                  => $row['potholesize'],
            'should_cond_l'                 => $row['shouldcond_l'],
            'should_cond_r'                 => $row['shouldcond_r'],
            'crossfall_shape'               => $row['crossfallshape'],
            'gravel_size'                   => $row['gravelsize'],
            'gravel_thickness'              => $row['gravelthickness'],
            'distribution'                  => $row['distribution'],
            'edge_damage_area_r'            => $row['edgedamage_area_r'],
            'survey_by2'                    => $row['surveyby2'],
            'survey_date'                   => $surveyDate,
            'section_status'                => $row['sectionstatus'],
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
