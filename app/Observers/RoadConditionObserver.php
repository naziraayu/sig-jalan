<?php

namespace App\Observers;

use App\Models\RoadCondition;
use App\Services\SDICalculator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoadConditionObserver
{
    /**
     * Handle the RoadCondition "created" event.
     */
    public function created(RoadCondition $roadCondition): void
    {
        $this->calculateAndStoreSdi($roadCondition);
    }

    /**
     * Handle the RoadCondition "updated" event.
     */
    public function updated(RoadCondition $roadCondition): void
    {
        // Hanya recalculate jika ada perubahan data yang mempengaruhi SDI
        if ($this->needsRecalculation($roadCondition)) {
            $this->calculateAndStoreSdi($roadCondition);
        }
    }

    /**
     * Check if SDI needs to be recalculated
     */
    private function needsRecalculation(RoadCondition $roadCondition): bool
    {
        $dirtyFields = $roadCondition->getDirty();
        
        // Fields yang mempengaruhi SDI calculation
        $sdiAffectingFields = [
            'pavement',              // Tipe perkerasan
            'crack_dep_area',        // Luas retak
            'oth_crack_area',        // Luas retak lainnya
            'crack_width',           // Lebar retak
            'pothole_count',         // Jumlah lubang
            'rutting_depth',         // Kedalaman alur
            'chainage_from',         // Panjang segmen berubah
            'chainage_to',           // Panjang segmen berubah
        ];
        
        foreach ($sdiAffectingFields as $field) {
            if (array_key_exists($field, $dirtyFields)) {
                Log::info('SDI recalculation triggered', [
                    'link_no' => $roadCondition->link_no,
                    'changed_field' => $field,
                    'old_value' => $roadCondition->getOriginal($field),
                    'new_value' => $dirtyFields[$field]
                ]);
                return true;
            }
        }
        
        return false;
    }

    /**
     * Calculate and store SDI for the road condition
     */
    private function calculateAndStoreSdi(RoadCondition $roadCondition): void
    {
        try {
            // ✅ PAKAI SERVICE - Single Source of Truth
            $sdi = SDICalculator::calculate($roadCondition);
            
            // ✅ Update langsung ke database (bukan via Eloquent untuk avoid trigger observer lagi)
            DB::table('road_condition')
                ->where('link_no', $roadCondition->link_no)
                ->where('chainage_from', $roadCondition->chainage_from)
                ->where('chainage_to', $roadCondition->chainage_to)
                ->where('year', $roadCondition->year)
                ->update([
                    'sdi_value' => $sdi['sdi_final'],
                    'sdi_category' => $sdi['category'],
                    'updated_at' => now()
                ]);
            
            Log::info('✅ Observer: SDI calculated and saved', [
                'link_no' => $roadCondition->link_no,
                'chainage' => "{$roadCondition->chainage_from} - {$roadCondition->chainage_to}",
                'year' => $roadCondition->year,
                'sdi_value' => $sdi['sdi_final'],
                'sdi_category' => $sdi['category'],
                'pavement' => $roadCondition->pavement ?? 'AS'
            ]);
                
        } catch (\Exception $e) {
            Log::error("❌ Observer: Failed to calculate SDI", [
                'link_no' => $roadCondition->link_no ?? null,
                'chainage_from' => $roadCondition->chainage_from ?? null,
                'chainage_to' => $roadCondition->chainage_to ?? null,
                'year' => $roadCondition->year ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}