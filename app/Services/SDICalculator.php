<?php

namespace App\Services;

use App\Models\RoadCondition;
use App\Models\RoadInventory;
use Illuminate\Support\Facades\Log;

/**
 * ✅ SDI Calculator - CORRECTED VERSION
 * Sesuai dengan PKRMS yang SEBENARNYA
 * 
 * PERBAIKAN KRUSIAL:
 * 1. STEP 3 menggunakan JUMLAH LUBANG (pothole_count) per 100m
 * 2. BUKAN menggunakan luas lubang (pothole_area)
 * 3. Threshold: <10, 10-50, >50 per 100 meter
 */
class SDICalculator
{
    /**
     * ✅ Calculate SDI based on PKRMS standard
     * 
     * @param RoadCondition $condition
     * @param bool $detailed
     * @return array
     */
    public static function calculate(RoadCondition $condition, bool $detailed = false): array
    {
        // ========================================
        // CEK TIPE PERKERASAN
        // ========================================
        $pavementType = $condition->pavement ?? 'Asphalt';

        if (in_array($pavementType, ['Block', 'Concrete', 'Unpaved', 'Impassable'])) {
            Log::info('Non-Aspal pavement detected', [
                'link_no' => $condition->link_no,
                'pavement' => $pavementType,
                'chainage' => "{$condition->chainage_from} - {$condition->chainage_to}"
            ]);
            
            return [
                'sdi_final' => 999,
                'category' => 'Rusak Berat',
                'sdi1' => 999,
                'sdi2' => 999,
                'sdi3' => 999,
                'sdi4' => 999,
                'total_crack_area' => 0,
                'crack_percentage' => 0,
            ];
        }

        // ========================================
        // LOAD INVENTORY
        // ========================================
        $inventory = self::getInventory($condition);

        if (!$inventory) {
            Log::warning('No inventory found for SDI calculation', [
                'link_no' => $condition->link_no,
                'chainage' => "{$condition->chainage_from} - {$condition->chainage_to}"
            ]);
            
            return [
                'sdi_final' => 0,
                'category' => 'Data Tidak Lengkap',
                'sdi1' => 0,
                'sdi2' => 0,
                'sdi3' => 0,
                'sdi4' => 0,
                'total_crack_area' => 0,
                'crack_percentage' => 0,
            ];
        }

        $paveWidth = floatval($inventory->pave_width ?? 0);
        
        if ($paveWidth <= 0) {
            Log::warning('Invalid pave width', [
                'link_no' => $condition->link_no,
                'pave_width' => $paveWidth
            ]);
            
            return [
                'sdi_final' => 0,
                'category' => 'Data Tidak Lengkap',
                'sdi1' => 0,
                'sdi2' => 0,
                'sdi3' => 0,
                'sdi4' => 0,
                'total_crack_area' => 0,
                'crack_percentage' => 0,
            ];
        }

        // ========================================
        // SEGMENT LENGTH & AREA
        // ========================================
        $chainageFrom = floatval($condition->chainage_from);
        $chainageTo = floatval($condition->chainage_to);
        
        $segmentLengthMeter = $chainageTo - $chainageFrom;
        
        if ($segmentLengthMeter <= 0) {
            return [
                'sdi_final' => 0,
                'category' => 'Data Tidak Valid',
                'sdi1' => 0,
                'sdi2' => 0,
                'sdi3' => 0,
                'sdi4' => 0,
                'total_crack_area' => 0,
                'crack_percentage' => 0,
            ];
        }

        // Total luas segmen dalam m²
        $totalSegmentArea = $segmentLengthMeter * $paveWidth;

        // ========================================
        // STEP 1: LUAS RETAK (SDI1)
        // ========================================
        $crackDepArea = floatval($condition->crack_dep_area ?? 0);
        $othCrackArea = floatval($condition->oth_crack_area ?? 0);
        $totalCrackArea = $crackDepArea + $othCrackArea;

        // Hitung persentase retak
        $crackPercentage = ($totalSegmentArea > 0) 
            ? ($totalCrackArea / $totalSegmentArea) * 100 
            : 0;

        // Mapping persentase ke SDI1
        $sdi1 = 0;
        $sdi1_explanation = '';
        
        if ($crackPercentage == 0) {
            $sdi1 = 0;
            $sdi1_explanation = 'Tidak ada retak (0%)';
        } elseif ($crackPercentage < 10) {
            $sdi1 = 5;
            $sdi1_explanation = sprintf('Retak < 10%% (%.2f%%) → SDI1 = 5', $crackPercentage);
        } elseif ($crackPercentage >= 10 && $crackPercentage <= 30) {
            $sdi1 = 20;
            $sdi1_explanation = sprintf('Retak 10-30%% (%.2f%%) → SDI1 = 20', $crackPercentage);
        } else {
            $sdi1 = 40;
            $sdi1_explanation = sprintf('Retak > 30%% (%.2f%%) → SDI1 = 40', $crackPercentage);
        }

        // ========================================
        // STEP 2: LEBAR RETAK (SDI2)
        // ========================================
        $crackWidthBobot = intval($condition->crack_width ?? 1);
        $sdi2 = $sdi1;
        $sdi2_explanation = '';

        if ($crackWidthBobot <= 3) {
            $sdi2 = $sdi1;
            $sdi2_explanation = sprintf('Lebar retak ≤ 3mm (bobot %d) → SDI2 = SDI1 = %.2f', $crackWidthBobot, $sdi2);
        } else {
            $sdi2 = $sdi1 * 2;
            $sdi2_explanation = sprintf('Lebar retak > 3mm (bobot 4) → SDI2 = SDI1 × 2 = %.2f', $sdi2);
        }

        // ========================================
        // STEP 3: JUMLAH LUBANG (SDI3)
        // ✅ PERBAIKAN: Berdasarkan JUMLAH per 100m
        // ========================================
        
        // ✅ Ambil jumlah lubang aktual
        $potholeCount = intval($condition->pothole_count ?? 0);
        
        // ✅ Normalisasi ke per 100 meter
        $potholeCountPer100m = ($segmentLengthMeter > 0) 
            ? ($potholeCount / $segmentLengthMeter) * 100 
            : 0;

        $sdi3 = $sdi2;
        $sdi3_addition = 0;
        $sdi3_explanation = '';

        // ✅ Mapping JUMLAH per 100m ke penambahan SDI
        // Sesuai tabel PKRMS yang Anda upload:
        // Bobot 1: Tidak ada → 0
        // Bobot 2: < 10 per 100m → +15
        // Bobot 3: 10-50 per 100m → +75
        // Bobot 4: > 50 per 100m → +225
        
        if ($potholeCount == 0) {
            $sdi3_addition = 0;
            $sdi3_explanation = 'Tidak ada lubang (0) → SDI3 = SDI2';
        } elseif ($potholeCountPer100m < 10) {
            $sdi3_addition = 15;
            $sdi3_explanation = sprintf('Jumlah lubang < 10 per 100m (%d lubang = %.1f/100m) → SDI3 = SDI2 + 15', 
                $potholeCount, $potholeCountPer100m);
        } elseif ($potholeCountPer100m >= 10 && $potholeCountPer100m <= 50) {
            $sdi3_addition = 75;
            $sdi3_explanation = sprintf('Jumlah lubang 10-50 per 100m (%d lubang = %.1f/100m) → SDI3 = SDI2 + 75', 
                $potholeCount, $potholeCountPer100m);
        } else {
            $sdi3_addition = 225;
            $sdi3_explanation = sprintf('Jumlah lubang > 50 per 100m (%d lubang = %.1f/100m) → SDI3 = SDI2 + 225', 
                $potholeCount, $potholeCountPer100m);
        }
        
        $sdi3 = $sdi2 + $sdi3_addition;

        // ========================================
        // STEP 4: KEDALAMAN ALUR RODA (SDI4)
        // ✅ FIXED: Bobot 4 sekarang +20 (bukan +100)
        // ========================================
        $ruttingDepthBobot = intval($condition->rutting_depth ?? 1);
        $sdi4 = $sdi3;
        $sdi4_addition = 0;
        $sdi4_explanation = '';

        if ($ruttingDepthBobot <= 1) {
            $sdi4_explanation = 'Tidak ada alur roda → SDI4 = SDI3';
        } elseif ($ruttingDepthBobot == 2) {
            // Kedalaman < 1cm (X=0.5)
            $sdi4_addition = 5 * 0.5;
            $sdi4 = $sdi3 + $sdi4_addition;
            $sdi4_explanation = sprintf('Alur < 1cm (bobot 2) → SDI4 = SDI3 + (5 × 0.5) = %.2f', $sdi4);
        } elseif ($ruttingDepthBobot == 3) {
            // Kedalaman 1-3cm (X=2)
            $sdi4_addition = 5 * 2;
            $sdi4 = $sdi3 + $sdi4_addition;
            $sdi4_explanation = sprintf('Alur 1-3cm (bobot 3) → SDI4 = SDI3 + (5 × 2) = %.2f', $sdi4);
        } else {
            // ✅ FIXED: Kedalaman > 3cm = +20 (bukan +100!)
            $sdi4_addition = 20;
            $sdi4 = $sdi3 + $sdi4_addition;
            $sdi4_explanation = sprintf('Alur > 3cm (bobot 4) → SDI4 = SDI3 + 20 = %.2f', $sdi4);
        }

        $category = self::getCategory($sdi4);

        // ========================================
        // RETURN RESULT
        // ========================================
        $result = [
            'sdi_final' => round($sdi4, 2),
            'category' => $category,
            'sdi1' => round($sdi1, 2),
            'sdi2' => round($sdi2, 2),
            'sdi3' => round($sdi3, 2),
            'sdi4' => round($sdi4, 2),
            'total_crack_area' => round($totalCrackArea, 2),
            'crack_percentage' => round($crackPercentage, 2),
            'pothole_count' => $potholeCount,
            'pothole_per_100m' => round($potholeCountPer100m, 1),
        ];

        if ($detailed) {
            $result['raw_data'] = [
                'pave_width' => $paveWidth,
                'segment_length_meter' => $segmentLengthMeter,
                'total_segment_area' => round($totalSegmentArea, 2),
                'crack_dep_area' => $crackDepArea,
                'oth_crack_area' => $othCrackArea,
                'total_crack_area' => $totalCrackArea,
                'crack_percentage' => round($crackPercentage, 2),
                'crack_width_bobot' => $crackWidthBobot,
                'pothole_count' => $potholeCount,
                'pothole_per_100m' => round($potholeCountPer100m, 1),
                'rutting_depth_bobot' => $ruttingDepthBobot,
                'pavement_type' => $pavementType,
            ];

            $result['explanations'] = [
                'step1' => [
                    'value' => round($sdi1, 2),
                    'explanation' => $sdi1_explanation,
                    'formula' => '% Retak = (Total Luas Retak / Luas Segmen) × 100'
                ],
                'step2' => [
                    'value' => round($sdi2, 2),
                    'explanation' => $sdi2_explanation,
                    'formula' => 'SDI2 = SDI1 (jika ≤ 3mm) atau SDI1 × 2 (jika > 3mm)'
                ],
                'step3' => [
                    'value' => round($sdi3, 2),
                    'explanation' => $sdi3_explanation,
                    'addition' => $sdi3_addition,
                    'formula' => 'Jumlah per 100m = (Jumlah Lubang / Panjang Segmen) × 100'
                ],
                'step4' => [
                    'value' => round($sdi4, 2),
                    'explanation' => $sdi4_explanation,
                    'addition' => $sdi4_addition,
                    'formula' => 'SDI4 = SDI3 + (nilai sesuai kedalaman alur)'
                ]
            ];
            
            $result['final'] = [
                'sdi_final' => round($sdi4, 2),
                'category' => $category,
            ];
        }

        Log::info('SDI Calculation completed', [
            'sdi_final' => $result['sdi_final'],
            'category' => $result['category'],
            'crack_%' => round($crackPercentage, 2),
            'pothole_count' => $potholeCount,
            'pothole_per_100m' => round($potholeCountPer100m, 1),
        ]);

        return $result;
    }

    /**
     * Get inventory for a road condition segment
     */
    private static function getInventory(RoadCondition $condition): ?RoadInventory
    {
        return RoadInventory::where('link_no', $condition->link_no)
            ->where(function($query) use ($condition) {
                $query->whereBetween('chainage_from', [$condition->chainage_from, $condition->chainage_to])
                      ->orWhereBetween('chainage_to', [$condition->chainage_from, $condition->chainage_to])
                      ->orWhere(function($q) use ($condition) {
                          $q->where('chainage_from', '<=', $condition->chainage_from)
                            ->where('chainage_to', '>=', $condition->chainage_to);
                      });
            })
            ->first();
    }

    /**
     * Get SDI category based on value
     */
    private static function getCategory(float $sdi): string
    {
        if ($sdi < 50) {
            return 'Baik';
        } elseif ($sdi >= 50 && $sdi < 100) {
            return 'Sedang';
        } elseif ($sdi >= 100 && $sdi < 150) {
            return 'Rusak Ringan';
        } else {
            return 'Rusak Berat';
        }
    }

    /**
     * Recalculate SDI for specific conditions
     */
    public static function recalculateForConditions(array $conditions): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        foreach ($conditions as $condition) {
            try {
                $sdi = self::calculate($condition);
                
                $condition->update([
                    'sdi_value' => $sdi['sdi_final'],
                    'sdi_category' => $sdi['category'],
                ]);

                $results['success']++;

                Log::info('✅ SDI recalculated', [
                    'link_no' => $condition->link_no,
                    'chainage' => "{$condition->chainage_from} - {$condition->chainage_to}",
                    'sdi' => $sdi['sdi_final']
                ]);

            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'link_no' => $condition->link_no,
                    'chainage' => "{$condition->chainage_from} - {$condition->chainage_to}",
                    'error' => $e->getMessage()
                ];

                Log::error('❌ Failed to recalculate SDI', [
                    'link_no' => $condition->link_no,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $results;
    }
}