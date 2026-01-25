<?php

namespace App\Services;

use App\Models\RoadCondition;
use App\Models\RoadInventory;
use Illuminate\Support\Facades\Log;

class SDICalculator
{
    /**
     * ✅ SATU-SATUNYA tempat logic SDI calculation
     * Dipakai oleh Observer dan Controller (jika perlu detail)
     * 
     * @param RoadCondition $condition
     * @param bool $detailed - Set true untuk return detail perhitungan
     * @return array
     */
    public static function calculate(RoadCondition $condition, bool $detailed = false): array
    {
        // ========================================
        // CEK TIPE PERKERASAN
        // ========================================
        $pavementType = $condition->pavement ?? 'AS';
        
        // Jika BUKAN ASPAL → Auto RUSAK BERAT
        if (in_array($pavementType, ['BT', 'BL', 'NA', 'TD'])) {
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
        // SEGMENT LENGTH (dalam METER)
        // ========================================
        $chainageFrom = floatval($condition->chainage_from);
        $chainageTo = floatval($condition->chainage_to);
        
        // ✅ Chainage dalam database = METER
        // Contoh: chainage 0-100 = 100 meter
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

        // ========================================
        // STEP 1: PERSENTASE RETAK
        // ========================================
        $crackDepArea = floatval($condition->crack_dep_area ?? 0);
        $othCrackArea = floatval($condition->oth_crack_area ?? 0);
        $totalCrackArea = $crackDepArea + $othCrackArea;

        // ✅ Luas segmen = panjang (meter) × lebar (meter) = m²
        // Contoh: 100 m × 4 m = 400 m²
        $totalSegmentArea = $segmentLengthMeter * $paveWidth;
        $crackPercentage = ($totalSegmentArea > 0) 
            ? ($totalCrackArea / $totalSegmentArea) * 100 
            : 0;

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
        // STEP 2: LEBAR RETAK
        // ========================================
        // ✅ crack_width di database adalah BOBOT/KATEGORI, bukan nilai mm!
        // Bobot 1 = Tidak ada
        // Bobot 2 = Halus < 1mm
        // Bobot 3 = Sedang 1-3mm
        // Bobot 4 = Lebar > 3mm
        $crackWidthBobot = intval($condition->crack_width ?? 0);
        $sdi2 = $sdi1;
        $sdi2_explanation = '';

        if ($crackWidthBobot == 0 || $crackWidthBobot == 1) {
            $sdi2_explanation = 'Tidak ada retak → SDI2 = SDI1';
        } elseif ($crackWidthBobot == 2 || $crackWidthBobot == 3) {
            // Halus atau Sedang (≤ 3mm)
            $sdi2 = $sdi1;
            $kategori = $crackWidthBobot == 2 ? 'Halus < 1mm' : 'Sedang 1-3mm';
            $sdi2_explanation = sprintf('Lebar retak %s (bobot %d) → SDI2 = SDI1 = %.2f', $kategori, $crackWidthBobot, $sdi2);
        } else {
            // Lebar > 3mm (bobot 4)
            $sdi2 = $sdi1 * 2;
            $sdi2_explanation = sprintf('Lebar retak > 3mm (bobot 4) → SDI2 = SDI1 × 2 = %.2f', $sdi2);
        }

        // ========================================
        // STEP 3: JUMLAH LUBANG (per 100m)
        // ========================================
        $potholeCount = intval($condition->pothole_count ?? 0);
        
        // ✅ PERBAIKAN: Normalized per 100 meter
        // Gunakan segment length dalam METER, bukan km
        $normalizedPotholes = ($segmentLengthMeter > 0) 
            ? ($potholeCount / $segmentLengthMeter) * 100 
            : 0;

        $sdi3 = $sdi2;
        $sdi3_addition = 0;
        $sdi3_explanation = '';

        if ($normalizedPotholes == 0) {
            $sdi3_explanation = 'Tidak ada lubang → SDI3 = SDI2';
        } elseif ($normalizedPotholes < 10) {
            $sdi3_addition = 15;
            $sdi3 = $sdi2 + $sdi3_addition;
            $sdi3_explanation = sprintf('Lubang < 10/100m (%.2f) → SDI3 = SDI2 + 15 = %.2f', $normalizedPotholes, $sdi3);
        } elseif ($normalizedPotholes >= 10 && $normalizedPotholes <= 50) {
            $sdi3_addition = 75;
            $sdi3 = $sdi2 + $sdi3_addition;
            $sdi3_explanation = sprintf('Lubang 10-50/100m (%.2f) → SDI3 = SDI2 + 75 = %.2f', $normalizedPotholes, $sdi3);
        } else {
            $sdi3_addition = 225;
            $sdi3 = $sdi2 + $sdi3_addition;
            $sdi3_explanation = sprintf('Lubang > 50/100m (%.2f) → SDI3 = SDI2 + 225 = %.2f', $normalizedPotholes, $sdi3);
        }

        // ========================================
        // STEP 4: KEDALAMAN ALUR RODA
        // ========================================
        // ✅ rutting_depth di database adalah BOBOT/KATEGORI, bukan nilai cm!
        // Bobot 1 = Tidak ada
        // Bobot 2 = Kedalaman < 1cm (X=0.5)
        // Bobot 3 = Kedalaman 1-3cm (X=2)
        // Bobot 4 = Kedalaman > 3cm (X=5)
        $ruttingDepthBobot = intval($condition->rutting_depth ?? 0);
        $sdi4 = $sdi3;
        $sdi4_addition = 0;
        $sdi4_explanation = '';

        if ($ruttingDepthBobot == 0 || $ruttingDepthBobot == 1) {
            $sdi4_explanation = 'Tidak ada alur roda → SDI4 = SDI3';
        } elseif ($ruttingDepthBobot == 2) {
            // Kedalaman < 1cm (X=0.5)
            $X = 0.5;
            $sdi4_addition = 5 * $X;
            $sdi4 = $sdi3 + $sdi4_addition;
            $sdi4_explanation = sprintf('Alur < 1cm (bobot 2, X=0.5) → SDI4 = SDI3 + (5 × 0.5) = %.2f', $sdi4);
        } elseif ($ruttingDepthBobot == 3) {
            // Kedalaman 1-3cm (X=2)
            $X = 2;
            $sdi4_addition = 5 * $X;
            $sdi4 = $sdi3 + $sdi4_addition;
            $sdi4_explanation = sprintf('Alur 1-3cm (bobot 3, X=2) → SDI4 = SDI3 + (5 × 2) = %.2f', $sdi4);
        } else {
            // Kedalaman > 3cm (X=5)
            // ✅ PERBAIKAN: SDI4 = SDI3 + 20 * 5 (bukan 5 * 4)
            $X = 5;
            $sdi4_addition = 20 * $X;
            $sdi4 = $sdi3 + $sdi4_addition;
            $sdi4_explanation = sprintf('Alur > 3cm (bobot 4, X=5) → SDI4 = SDI3 + (20 × 5) = %.2f', $sdi4);
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
        ];

        // Jika detailed = true, return semua detail perhitungan
        if ($detailed) {
            $result['raw_data'] = [
                'pave_width' => $paveWidth,
                'segment_length_meter' => $segmentLengthMeter,
                'total_segment_area' => round($totalSegmentArea, 2),
                'crack_dep_area' => $crackDepArea,
                'oth_crack_area' => $othCrackArea,
                'crack_width_bobot' => $crackWidthBobot,
                'pothole_count' => $potholeCount,
                'normalized_potholes' => round($normalizedPotholes, 2),
                'rutting_depth_bobot' => $ruttingDepthBobot,
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
                    'formula' => 'SDI3 = SDI2 + (nilai sesuai jumlah lubang per 100m)'
                ],
                'step4' => [
                    'value' => round($sdi4, 2),
                    'explanation' => $sdi4_explanation,
                    'addition' => $sdi4_addition,
                    'formula' => 'SDI4 = SDI3 + (nilai sesuai kedalaman alur)'
                ]
            ];
        }

        return $result;
    }

    /**
     * Get inventory for a road condition segment
     */
    private static function getInventory(RoadCondition $condition): ?RoadInventory
    {
        return RoadInventory::where('link_no', $condition->link_no)
            ->where(function($query) use ($condition) {
                // Cari inventory yang overlap dengan segmen condition
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
     * Useful for manual recalculation or artisan commands
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