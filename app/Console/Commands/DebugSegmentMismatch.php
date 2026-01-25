<?php

namespace App\Console\Commands;

use App\Models\RoadCondition;
use App\Models\RoadInventory;
use Illuminate\Console\Command;

class DebugSegmentMismatch extends Command
{
    protected $signature = 'debug:segment-mismatch {link_no} {year}';
    protected $description = 'Debug segmen yang tidak match antara Inventory dan Condition';

    public function handle()
    {
        $linkNo = $this->argument('link_no');
        $year = $this->argument('year');

        $this->info("=== DEBUGGING SEGMENT MISMATCH ===");
        $this->info("Link No: {$linkNo}");
        $this->info("Year: {$year}");
        $this->newLine();

        // 1. CEK INVENTORY
        $this->info("1️⃣ CHECKING ROAD INVENTORY...");
        $inventories = RoadInventory::where('link_no', $linkNo)
            ->orderByRaw('CAST(chainage_from AS DECIMAL(10,3)) ASC')
            ->get();

        if ($inventories->isEmpty()) {
            $this->error("❌ Tidak ada inventory untuk link_no: {$linkNo}");
            
            // Cek inventory tanpa filter year
            $invAnyYear = RoadInventory::where('link_no', $linkNo)->get();
            if ($invAnyYear->isNotEmpty()) {
                $this->warn("⚠️  Tapi ada inventory di tahun lain:");
                foreach ($invAnyYear->groupBy('year') as $yr => $items) {
                    $this->line("   - Tahun {$yr}: {$items->count()} segmen");
                }
            }
        } else {
            $this->info("✅ Found {$inventories->count()} inventory segments");
            $this->table(
                ['No', 'Year', 'Chainage From', 'Chainage To', 'Pave Width', 'Length'],
                $inventories->map(function($inv, $idx) {
                    return [
                        $idx + 1,
                        $inv->year,
                        $inv->chainage_from,
                        $inv->chainage_to,
                        $inv->pave_width ?? 'NULL',
                        ($inv->chainage_to - $inv->chainage_from) * 1000 . ' m'
                    ];
                })
            );
        }

        $this->newLine();

        // 2. CEK CONDITION
        $this->info("2️⃣ CHECKING ROAD CONDITION...");
        $conditions = RoadCondition::where('link_no', $linkNo)
            ->where('year', $year)
            ->orderByRaw('CAST(chainage_from AS DECIMAL(10,3)) ASC')
            ->get();

        if ($conditions->isEmpty()) {
            $this->error("❌ Tidak ada condition untuk link_no: {$linkNo}, year: {$year}");
            return;
        }

        $this->info("✅ Found {$conditions->count()} condition segments");
        $this->newLine();

        // 3. CEK MATCHING
        $this->info("3️⃣ CHECKING MATCHING BETWEEN INVENTORY AND CONDITION...");
        $this->newLine();

        $matchedCount = 0;
        $unmatchedCount = 0;
        $unmatchedSegments = [];

        foreach ($conditions as $condition) {
            $matched = $inventories->first(function($inv) use ($condition) {
                // Overlap logic
                return ($inv->chainage_from <= $condition->chainage_from && $inv->chainage_to >= $condition->chainage_from) ||
                       ($inv->chainage_from <= $condition->chainage_to && $inv->chainage_to >= $condition->chainage_to) ||
                       ($condition->chainage_from <= $inv->chainage_from && $condition->chainage_to >= $inv->chainage_to);
            });

            if ($matched) {
                $matchedCount++;
                $this->line("✅ Condition [{$condition->chainage_from} - {$condition->chainage_to}] matched with Inventory [{$matched->chainage_from} - {$matched->chainage_to}]");
            } else {
                $unmatchedCount++;
                $unmatchedSegments[] = [
                    'chainage_from' => $condition->chainage_from,
                    'chainage_to' => $condition->chainage_to,
                    'year' => $condition->year,
                ];
                $this->error("❌ Condition [{$condition->chainage_from} - {$condition->chainage_to}] NOT MATCHED");
            }
        }

        $this->newLine();
        $this->info("=== SUMMARY ===");
        $this->info("Total Condition Segments: {$conditions->count()}");
        $this->info("Matched: {$matchedCount}");
        $this->error("Unmatched: {$unmatchedCount}");

        if ($unmatchedCount > 0) {
            $this->newLine();
            $this->warn("⚠️  UNMATCHED SEGMENTS:");
            $this->table(
                ['Chainage From', 'Chainage To', 'Year', 'Possible Reason'],
                collect($unmatchedSegments)->map(function($seg) use ($inventories) {
                    $reason = 'No overlapping inventory found';
                    
                    // Cek apakah ada inventory yang dekat
                    $closest = $inventories->sortBy(function($inv) use ($seg) {
                        return abs($inv->chainage_from - $seg['chainage_from']);
                    })->first();
                    
                    if ($closest) {
                        $distance = abs($closest->chainage_from - $seg['chainage_from']);
                        $reason = "Closest inventory: [{$closest->chainage_from} - {$closest->chainage_to}] (distance: " . round($distance * 1000, 2) . "m)";
                    }
                    
                    return [
                        $seg['chainage_from'],
                        $seg['chainage_to'],
                        $seg['year'],
                        $reason
                    ];
                })
            );

            $this->newLine();
            $this->warn("💡 POSSIBLE SOLUTIONS:");
            $this->line("1. Tambahkan inventory untuk segmen yang missing");
            $this->line("2. Atau, sistem akan menggunakan default pave_width = 7.0");
            $this->line("3. Periksa apakah ada typo di chainage (misal: 367.00 vs 367)");
        }

        $this->newLine();
        
        // 4. CEK APAKAH ADA INVENTORY DI TAHUN BERBEDA
        $this->info("4️⃣ CHECKING INVENTORY IN OTHER YEARS...");
        $invOtherYears = RoadInventory::where('link_no', $linkNo)
            ->where('year', '!=', $year)
            ->get()
            ->groupBy('year');

        if ($invOtherYears->isNotEmpty()) {
            $this->warn("⚠️  Found inventory in other years:");
            foreach ($invOtherYears as $yr => $items) {
                $this->line("   - Tahun {$yr}: {$items->count()} segmen");
            }
            $this->newLine();
            $this->info("💡 TIP: Mungkin inventory tahun {$year} belum dibuat. Pertimbangkan untuk:");
            $this->line("   1. Copy inventory dari tahun sebelumnya");
            $this->line("   2. Atau buat inventory baru untuk tahun {$year}");
        } else {
            $this->info("✅ No inventory found in other years");
        }
    }
}