<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Feedback;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Faker\Factory as Faker; 

class SeedRandomFeedback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedback:seed-random {--count=1 : Number of feedbacks to insert}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Picks random unused rows from a CSV and inserts them as Feedback';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $csvPath = storage_path('app/feedbacks.csv');
        $trackerPath = storage_path('app/used_feedback_rows.json');

        if (!file_exists($csvPath)) {
            $this->error("CSV file not found at: {$csvPath}");
            return Command::FAILURE;
        }

        $rows = file($csvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $usedRows = file_exists($trackerPath) ? json_decode(file_get_contents($trackerPath), true) : [];

        $allIndices = array_keys($rows);
        $availableIndices = array_diff($allIndices, $usedRows, [0]); // Exclude header row

        $count = (int) $this->option('count');

        if (empty($availableIndices)) {
            $this->warn("All rows from the CSV have already been used!");
            return Command::SUCCESS;
        }

        if (count($availableIndices) < $count) {
            $this->warn("Only " . count($availableIndices) . " unused rows left. Seeding those.");
            $count = count($availableIndices);
        }

        // 2. Initialize Faker
        $faker = Faker::create();

        for ($i = 0; $i < $count; $i++) {
            $randomKey = array_rand($availableIndices);
            $rowIndex = $availableIndices[$randomKey];
            
            $line = str_getcsv($rows[$rowIndex]);
            
            $branchId = $line[0] ?? null;
            $details = $line[1] ?? 'No details provided';

            // 3. Insert into the database with Faker data
            $feedback = Feedback::create([
                'fbk_uuid'         => (string) Str::uuid(),
                'branch_id'        => $branchId,
                'fbk_details'      => $details,
                
                // Add the generated fake data
                'std_name'         => $faker->name(),
                'std_email'        => $faker->safeEmail(),
                'std_phone'        => $faker->numerify('###########'), // Forces exactly 11 random digits
                
                'fbk_status'       => 0, // Pending
                'fbk_date_created' => now(),
            ]);

            $usedRows[] = $rowIndex;
            unset($availableIndices[$randomKey]);

            $logMsg = "Seeded Feedback ID: {$feedback->fbk_id} using CSV row #{$rowIndex}. Branch: {$branchId}";
            $this->info($logMsg);
            Log::info($logMsg);
        }

        file_put_contents($trackerPath, json_encode(array_values($usedRows)));
        
        return Command::SUCCESS;
    }
}
