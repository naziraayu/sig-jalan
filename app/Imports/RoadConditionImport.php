<?php
// FILE: app/Imports/RoadConditionImport.php (REPLACE file yang lama)

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Link;
use App\Models\RoadCondition;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class RoadConditionImport implements
    ToModel,
    WithHeadingRow,
    WithChunkReading,
    WithBatchInserts,
    WithEvents,
    SkipsOnError
{
    use SkipsErrors;

    protected $importedCount = 0;
    protected $skippedCount  = 0;
    protected $errors        = [];
    protected $linkIdCache   = []; // ✅ Cache untuk performa

    public function model(array $row)
    {
        $row = array_change_key_case($row, CASE_LOWER);

        // Bersihkan underscore berlebih di akhir key
        $cleaned = [];
        foreach ($row as $key => $value) {
            $cleaned[rtrim($key, '_')] = $value;
        }
        $row = $cleaned;

        // Validasi field wajib
        if (empty($row['link_no'])) {
            $this->skippedCount++;
            return null;
        }
        if (!isset($row['chainagefrom']) || $row['chainagefrom'] === '') {
            $this->skippedCount++;
            $this->errors[] = "Dilewati: chainagefrom kosong untuk link_no {$row['link_no']}";
            return null;
        }
        if (!isset($row['chainageto']) || $row['chainageto'] === '') {
            $this->skippedCount++;
            $this->errors[] = "Dilewati: chainageto kosong untuk link_no {$row['link_no']}";
            return null;
        }
        if ($row['chainageto'] <= $row['chainagefrom']) {
            $this->skippedCount++;
            $this->errors[] = "Dilewati: chainage_to <= chainage_from untuk link_no {$row['link_no']}";
            return null;
        }
        if (empty($row['year'])) {
            $this->skippedCount++;
            $this->errors[] = "Dilewati: year kosong untuk link_no {$row['link_no']}";
            return null;
        }

        // ✅ AUTO LOOKUP link_id dari link_no + province + kabupaten
        $provinceCode  = $row['province_code'] ?? null;
        $kabupatenCode = $row['kabupaten_code'] ?? null;
        $year = $row['reference_year'] ?? null; // ✅ Pakai reference_year untuk lookup link

        $linkId = $this->getLinkId(
            $row['link_no'], 
            $provinceCode, 
            $kabupatenCode,
            $year // ✅ Kirim year
        );
        if (!$linkId) {
            $this->skippedCount++;
            $this->errors[] = "Dilewati: link_no '{$row['link_no']}' tidak ditemukan. Import Ruas Jalan terlebih dahulu.";
            return null;
        }

        // Parse survey date
        $surveyDate = null;
        if (!empty($row['surveydate'])) {
            try {
                $surveyDate = is_numeric($row['surveydate'])
                    ? Carbon::instance(ExcelDate::excelToDateTimeObject($row['surveydate']))
                    : Carbon::parse($row['surveydate']);
            } catch (\Exception $e) {
                Log::warning("Gagal parse survey_date untuk link_no {$row['link_no']}: {$e->getMessage()}");
            }
        }

        $this->importedCount++;

        return new RoadCondition([
            'year'                              => $row['year'],
            'province_code'                     => $provinceCode,
            'kabupaten_code'                    => $kabupatenCode,
            'reference_year'                    => $row['reference_year'] ?? null,
            'link_id'                           => $linkId, // ✅ Dari lookup otomatis
            'link_no'                           => $row['link_no'],
            'chainage_from'                     => $row['chainagefrom'],
            'chainage_to'                       => $row['chainageto'],
            'drp_from'                          => $row['drp_from'] ?? null,
            'offset_from'                       => $row['offset_from'] ?? null,
            'drp_to'                            => $row['drp_to'] ?? null,
            'offset_to'                         => $row['offset_to'] ?? null,
            'roughness'                         => $row['roughness'] ?? null,
            'bleeding_area'                     => $row['bleeding_area'] ?? null,
            'ravelling_area'                    => $row['ravelling_area'] ?? null,
            'desintegration_area'               => $row['desintegration_area'] ?? null,
            'crack_dep_area'                    => $row['crackdep_area'] ?? null,
            'patching_area'                     => $row['patching_area'] ?? null,
            'oth_crack_area'                    => $row['othcrack_area'] ?? null,
            'pothole_area'                      => $row['pothole_area'] ?? null,
            'rutting_area'                      => $row['rutting_area'] ?? null,
            'edge_damage_area'                  => $row['edgedamage_area'] ?? null,
            'crossfall_area'                    => $row['crossfall_area'] ?? null,
            'depressions_area'                  => $row['depressions_area'] ?? null,
            'erosion_area'                      => $row['erosion_area'] ?? null,
            'waviness_area'                     => $row['waviness_area'] ?? null,
            'gravel_thickness_area'             => $row['gravelthickness_area'] ?? null,
            'concrete_cracking_area'            => $row['concrete_cracking_area'] ?? null,
            'concrete_spalling_area'            => $row['concrete_spalling_area'] ?? null,
            'concrete_structural_cracking_area' => $row['concrete_structuralcracking_area'] ?? null,
            'concrete_corner_break_no'          => $row['concrete_cornerbreakno'] ?? null,
            'concrete_pumping_no'               => $row['concrete_pumpingno'] ?? null,
            'concrete_blowouts_area'            => $row['concrete_blowouts_area'] ?? null,
            'crack_width'                       => $row['crack_width'] ?? null,
            'pothole_count'                     => $row['pothole_count'] ?? null,
            'rutting_depth'                     => $row['rutting_depth'] ?? null,
            'shoulder_l'                        => $row['shoulder_l'] ?? null,
            'shoulder_r'                        => $row['shoulder_r'] ?? null,
            'drain_l'                           => $row['drain_l'] ?? null,
            'drain_r'                           => $row['drain_r'] ?? null,
            'slope_l'                           => $row['slope_l'] ?? null,
            'slope_r'                           => $row['slope_r'] ?? null,
            'footpath_l'                        => $row['footpath_l'] ?? null,
            'footpath_r'                        => $row['footpath_r'] ?? null,
            'sign_l'                            => $row['sign_l'] ?? null,
            'sign_r'                            => $row['sign_r'] ?? null,
            'guide_post_l'                      => $row['guidepost_l'] ?? null,
            'guide_post_r'                      => $row['guidepost_r'] ?? null,
            'barrier_l'                         => $row['barrier_l'] ?? null,
            'barrier_r'                         => $row['barrier_r'] ?? null,
            'road_marking_l'                    => $row['roadmarking_l'] ?? null,
            'road_marking_r'                    => $row['roadmarking_r'] ?? null,
            'iri'                               => $row['iri'] ?? null,
            'rci'                               => $row['rci'] ?? null,
            'analysis_base_year'                => $row['analysisbaseyear'] ?? null,
            'segment_tti'                       => $row['segmenttti'] ?? null,
            'survey_by'                         => $row['surveyby'] ?? null,
            'paved'                             => $row['paved'] ?? null,
            'pavement'                          => $row['pavement'] ?? null,
            'check_data'                        => $row['checkdata'] ?? null,
            'composition'                       => $row['composition'] ?? null,
            'crack_type'                        => $row['cracktype'] ?? null,
            'pothole_size'                      => $row['potholesize'] ?? null,
            'should_cond_l'                     => $row['shouldcond_l'] ?? null,
            'should_cond_r'                     => $row['shouldcond_r'] ?? null,
            'crossfall_shape'                   => $row['crossfallshape'] ?? null,
            'gravel_size'                       => $row['gravelsize'] ?? null,
            'gravel_thickness'                  => $row['gravelthickness'] ?? null,
            'distribution'                      => $row['distribution'] ?? null,
            'edge_damage_area_r'                => $row['edgedamage_area_r'] ?? null,
            'survey_by2'                        => $row['surveyby2'] ?? null,
            'survey_date'                       => $surveyDate,
            'section_status'                    => $row['sectionstatus'] ?? null,
        ]);
    }

    /**
     * ✅ Lookup link_id dengan caching agar tidak query berulang
     */
    protected function getLinkId(?string $linkNo, ?string $provinceCode, ?string $kabupatenCode, ?int $year = null): ?int
    {
        if (!$linkNo) return null;

        $cacheKey = "{$linkNo}_{$provinceCode}_{$kabupatenCode}_{$year}";

        if (!isset($this->linkIdCache[$cacheKey])) {
            $query = Link::where('link_no', $linkNo);
            if ($provinceCode)  $query->where('province_code', $provinceCode);
            if ($kabupatenCode) $query->where('kabupaten_code', $kabupatenCode);
            if ($year)          $query->where('year', $year); // ✅ Tambah ini

            $link = $query->select('id')->first();
            $this->linkIdCache[$cacheKey] = $link ? $link->id : null;
        }

        return $this->linkIdCache[$cacheKey];
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                Log::info('Mulai import kondisi jalan');
                $this->importedCount = 0;
                $this->skippedCount  = 0;
                $this->errors        = [];
                $this->linkIdCache   = [];
            },
            AfterImport::class => function (AfterImport $event) {
                Log::info('Import kondisi jalan selesai', [
                    'imported' => $this->importedCount,
                    'skipped'  => $this->skippedCount,
                ]);
                if (count($this->errors) > 0) {
                    Log::warning('Import errors', ['errors' => array_slice($this->errors, 0, 20)]);
                }
                Cache::flush();
            },
        ];
    }

    public function chunkSize(): int { return 200; }
    public function batchSize(): int { return 200; }
    public function getImportedCount() { return $this->importedCount; }
    public function getSkippedCount() { return $this->skippedCount; }
    public function getErrors() { return $this->errors; }
}