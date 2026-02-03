<?php

namespace App\Console\Commands;

use App\Models\RoadCondition;
use App\Services\SDICalculator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RecalculateSDI extends Command
{
    protected $signature = 'sdi:recalculate 
                            {--link_no= : Specific link_no to recalculate}
                            {--year= : Specific year to recalculate}
                            {--all : Recalculate all conditions}
                            {--force : Force recalculate even if SDI exists}';

    protected $description = 'Recalculate SDI for road conditions';

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

        // Skip yang sudah ada SDI (jika tidak force)
        if (!$force) {
            $beforeCount = $query->count();
            $query->whereNull('sdi_value');
            $afterCount = $query->count();
            
            if ($beforeCount > $afterCount) {
                $skipped = $beforeCount - $afterCount;
                $this->warn("Skipping {$skipped} records with existing SDI (use --force to override)");
            }
        }

        $totalCount = $query->count();

        if ($totalCount === 0) {
            $this->warn('No conditions found to recalculate.');
            return 0;
        }

        $this->info("Found {$totalCount} conditions to recalculate...");

        if ($totalCount > 1000 && !$this->confirm("Are you sure you want to recalculate {$totalCount} records?")) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $bar = $this->output->createProgressBar($totalCount);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
        $bar->start();

        $successCount = 0;
        $failedCount = 0;
        $errors = [];
        $startTime = microtime(true);

        // ✅ MANUAL PAGINATION - bypass Laravel's ORDER BY issue
        $chunkSize = 100;
        $offset = 0;

        while (true) {
            // Clone query dan ambil data per batch
            $conditions = (clone $query)
                ->offset($offset)
                ->limit($chunkSize)
                ->get();

            // Break jika tidak ada data lagi
            if ($conditions->isEmpty()) {
                break;
            }

            foreach ($conditions as $condition) {
                try {
                    $sdi = SDICalculator::calculate($condition);
                    
                    DB::table('road_condition')
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

                $bar->advance();
            }

            // Free memory dan increment offset
            unset($conditions);
            $offset += $chunkSize;
        }

        $bar->finish();
        $this->newLine(2);

        $duration = round(microtime(true) - $startTime, 2);

        $this->info("✅ Recalculation completed in {$duration} seconds!");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Success', $successCount],
                ['Failed', $failedCount],
                ['Total', $totalCount],
                ['Success Rate', $totalCount > 0 ? round(($successCount / $totalCount) * 100, 2) . '%' : '0%']
            ]
        );

        if (!empty($errors)) {
            $this->newLine();
            $this->error("⚠️  {$failedCount} errors encountered:");
            
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