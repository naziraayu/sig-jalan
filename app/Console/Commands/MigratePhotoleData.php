<?php

namespace App\Console\Commands;

use App\Models\RoadCondition;
use App\Services\SDICalculator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigratePotholeData extends Command
{
    protected $signature = 'sdi:migrate-pothole-data
    {--dry-run : Preview changes without saving}
    {--year= : Only migrate specific year}
    {--link-no= : Only migrate specific road}';
    
    protected $description = 'Migrate old pothole_area data (m²) to new bobot system (1-4)';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $year = $this->option('year');
        $linkNo = $this->option('link-no');
        
        $this->info('🔄 Starting pothole data migration...');
        $this->info('Mode: ' . ($dryRun ? 'DRY RUN (preview only)' : 'LIVE (will update database)'));
        $this->newLine();
        
        // ========================================
        // QUERY DATA YANG PERLU DI-MIGRATE
        // ========================================
        $query = RoadCondition::query();
        
        // Filter by year if specified
        if ($year) {
            $query->where('year', $year);
            $this->info("Filtering by year: {$year}");
        }
        
        // Filter by road if specified
        if ($linkNo) {
            $query->where('link_no', $linkNo);
            $this->info("Filtering by link_no: {$linkNo}");
        }
        
        // Ambil semua data yang pothole_area > 4 (kemungkinan data lama dalam m²)
        // Karena bobot hanya 1-4, jika > 4 berarti itu luas m²
        $conditions = $query->where('pothole_area', '>', 4)
                           ->orWhereNull('pothole_area')
                           ->get();
        
        if ($conditions->isEmpty()) {
            $this->info('✅ No data needs migration!');
            return 0;
        }
        
        $this->info("Found {$conditions->count()} records to migrate");
        $this->newLine();
        
        // ========================================
        // PREVIEW TABLE
        // ========================================
        if ($dryRun) {
            $this->table(
                ['Link No', 'Chainage', 'Old Area', 'Count', 'New Bobot', 'Old SDI', 'New SDI'],
                $conditions->take(10)->map(function($c) {
                    $segmentLength = $c->chainage_to - $c->chainage_from;
                    $potholeCount = $c->pothole_count ?? 0;
                    
                    $newBobot = SDICalculator::calculatePotholeBobot($potholeCount, $segmentLength);
                    
                    // Calculate new SDI
                    $oldSdi = $c->sdi_value;
                    
                    // Temporarily update untuk calculate
                    $tempCondition = clone $c;
                    $tempCondition->pothole_area = $newBobot;
                    $sdiResult = SDICalculator::calculate($tempCondition);
                    $newSdi = $sdiResult['sdi_final'];
                    
                    return [
                        $c->link_no,
                        "{$c->chainage_from}-{$c->chainage_to}",
                        $c->pothole_area ?? 'NULL',
                        $potholeCount,
                        $newBobot,
                        $oldSdi,
                        $newSdi
                    ];
                })
            );
            
            $this->newLine();
            $this->warn('⚠️ This is a DRY RUN. No changes were made.');
            $this->info('To apply changes, run without --dry-run flag');
            return 0;
        }
        
        // ========================================
        // CONFIRM BEFORE PROCEEDING
        // ========================================
        if (!$this->confirm("Do you want to migrate {$conditions->count()} records?")) {
            $this->info('Migration cancelled.');
            return 0;
        }
        
        // ========================================
        // START MIGRATION
        // ========================================
        $this->info('🚀 Starting migration...');
        $progressBar = $this->output->createProgressBar($conditions->count());
        
        $success = 0;
        $failed = 0;
        $errors = [];
        
        DB::beginTransaction();
        
        try {
            foreach ($conditions as $condition) {
                try {
                    // Calculate segment length
                    $segmentLength = $condition->chainage_to - $condition->chainage_from;
                    $potholeCount = $condition->pothole_count ?? 0;
                    
                    // Calculate new bobot
                    $newBobot = SDICalculator::calculatePotholeBobot($potholeCount, $segmentLength);
                    
                    // Update pothole_area
                    $condition->pothole_area = $newBobot;
                    $condition->save();
                    
                    // Recalculate SDI
                    $sdiResult = SDICalculator::calculate($condition);
                    $condition->update([
                        'sdi_value' => $sdiResult['sdi_final'],
                        'sdi_category' => $sdiResult['category'],
                    ]);
                    
                    $success++;
                    $progressBar->advance();
                    
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = [
                        'link_no' => $condition->link_no,
                        'chainage' => "{$condition->chainage_from}-{$condition->chainage_to}",
                        'error' => $e->getMessage()
                    ];
                }
            }
            
            DB::commit();
            
            $progressBar->finish();
            $this->newLine(2);
            
            // ========================================
            // SUMMARY
            // ========================================
            $this->info('✅ Migration completed!');
            $this->newLine();
            
            $this->table(
                ['Status', 'Count'],
                [
                    ['Success', $success],
                    ['Failed', $failed],
                    ['Total', $conditions->count()]
                ]
            );
            
            if (!empty($errors)) {
                $this->newLine();
                $this->error('❌ Errors encountered:');
                $this->table(
                    ['Link No', 'Chainage', 'Error'],
                    collect($errors)->take(10)->map(fn($e) => [
                        $e['link_no'],
                        $e['chainage'],
                        $e['error']
                    ])
                );
                
                if (count($errors) > 10) {
                    $this->warn('... and ' . (count($errors) - 10) . ' more errors');
                }
            }
            
            return 0;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->error('❌ Migration failed!');
            $this->error($e->getMessage());
            
            return 1;
        }
    }
}