<?php

namespace App\Console\Commands;

use App\Models\RoadCondition;
use App\Services\SDICalculator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RecalculateSDI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sdi:recalculate 
                            {--link_no= : Specific link_no to recalculate}
                            {--year= : Specific year to recalculate}
                            {--all : Recalculate all conditions}
                            {--force : Force recalculate even if SDI exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate SDI for road conditions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $linkNo = $this->option('link_no');
        $year = $this->option('year');
        $all = $this->option('all');
        $force = $this->option('force');

        if (!$all && !$linkNo && !$year) {
            $this->error('Please specify --link_no, --year, or --all');
            return 1;
        }

        // Build query
        $query = RoadCondition::query();

        if ($linkNo) {
            $query->where('link_no', $linkNo);
            $this->info("Filtering by link_no: {$linkNo}");
        }

        if ($year) {
            $query->where('year', $year);
            $this->info("Filtering by year: {$year}");
        }

        // ✅ OPTIONAL: Skip yang sudah ada SDI (jika tidak force)
        if (!$force) {
            $beforeCount = $query->count();
            $query->whereNull('sdi_value');
            $afterCount = $query->count();
            
            if ($beforeCount > $afterCount) {
                $skipped = $beforeCount - $afterCount;
                $this->warn("Skipping {$skipped} records with existing SDI (use --force to override)");
            }
        }

        $conditions = $query->get();

        if ($conditions->isEmpty()) {
            $this->warn('No conditions found to recalculate.');
            return 0;
        }

        $this->info("Found {$conditions->count()} conditions to recalculate...");
        
        // ✅ KONFIRMASI untuk --all
        if ($all && !$this->confirm("Are you sure you want to recalculate ALL {$conditions->count()} records?")) {
            $this->info('Operation cancelled.');
            return 0;
        }
        
        // ✅ Progress bar dengan format yang jelas
        $bar = $this->output->createProgressBar($conditions->count());
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
        $bar->start();

        // ✅ Manual loop untuk update progress bar per item
        $successCount = 0;
        $failedCount = 0;
        $errors = [];
        $startTime = microtime(true);

        foreach ($conditions as $condition) {
            try {
                // Calculate SDI using Service
                $sdi = SDICalculator::calculate($condition);
                
                // ✅ PERBAIKAN: Update via DB::table (bypass Observer untuk avoid double calculation)
                \Illuminate\Support\Facades\DB::table('road_condition')
                    ->where('link_no', $condition->link_no)
                    ->where('chainage_from', $condition->chainage_from)
                    ->where('chainage_to', $condition->chainage_to)
                    ->where('year', $condition->year)
                    ->update([
                        'sdi_value' => $sdi['sdi_final'],
                        'sdi_category' => $sdi['category'],
                        'updated_at' => now()
                    ]);

                $successCount++;

                // Log success untuk verification
                Log::info('✅ SDI recalculated', [
                    'link_no' => $condition->link_no,
                    'chainage' => "{$condition->chainage_from} - {$condition->chainage_to}",
                    'sdi_value' => $sdi['sdi_final'],
                    'sdi_category' => $sdi['category']
                ]);

            } catch (\Exception $e) {
                $failedCount++;
                $errors[] = [
                    'link_no' => $condition->link_no,
                    'chainage' => "{$condition->chainage_from} - {$condition->chainage_to}",
                    'error' => $e->getMessage()
                ];

                Log::error('❌ Failed to recalculate SDI', [
                    'link_no' => $condition->link_no,
                    'chainage' => "{$condition->chainage_from} - {$condition->chainage_to}",
                    'error' => $e->getMessage()
                ]);
            }

            // ✅ ADVANCE progress bar setiap item
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Calculate duration
        $duration = round(microtime(true) - $startTime, 2);

        // Display results
        $this->info("✅ Recalculation completed in {$duration} seconds!");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Success', $successCount],
                ['Failed', $failedCount],
                ['Total', $conditions->count()],
                ['Success Rate', $conditions->count() > 0 ? round(($successCount / $conditions->count()) * 100, 2) . '%' : '0%']
            ]
        );

        if (!empty($errors)) {
            $this->newLine();
            $this->error("⚠️  {$failedCount} errors encountered:");
            
            // Show first 10 errors
            $displayErrors = array_slice($errors, 0, 10);
            foreach ($displayErrors as $error) {
                $this->line("  - {$error['link_no']} ({$error['chainage']}): {$error['error']}");
            }
            
            if (count($errors) > 10) {
                $remaining = count($errors) - 10;
                $this->line("  ... and {$remaining} more errors");
            }
            
            $this->newLine();
            $this->warn("Check logs for full error details: storage/logs/laravel.log");
        }

        $this->newLine();
        $this->info("💡 Don't forget to clear dashboard cache:");
        $this->comment("   php artisan cache:clear");

        return 0;
    }
}