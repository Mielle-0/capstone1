<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;
use Carbon\Carbon;

class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('feedback_predictions')->truncate();
        DB::table('prediction_candidates')->truncate();
        DB::table('feedbacks')->truncate();
        Schema::enableForeignKeyConstraints();
        
        $faker = Faker::create();
        
        // Get reference data
        $branches = ['UM-BANSALAN', 'UM-DIGOS', 'UM-MAIN', 'UM-PANABO', 'UM-TAGUM'];
        $users = DB::table('users')->pluck('usr_id')->toArray();
        $themes = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14];
        
        $programs = [
            'BS Computer Science',
            'BS Information Technology',
            'BS Business Administration',
            'BS Accountancy',
            'BS Education',
            'BS Nursing',
            'BS Engineering',
            'AB Communication'
        ];

        // Classification to type_id mapping
        $classificationMap = [
            'Inquiry' => 1,
            'Concern' => 2,
            'Suggestion' => 3,
            'Complaint' => 4,
            'Commendation' => 5,
        ];

        // Department to theme mapping (you can adjust these)
        $departmentThemeMap = [
            'Registrar' => [6, 7, 13], // Process themes
            'Library' => [9, 11, 14], // Facilities themes
            'Facilities' => [9, 11, 14],
            'Security' => [8, 9],
            'IT' => [6, 7, 10],
            'default' => [1, 2, 3, 4, 5] // Others
        ];

        // Load JSON file
        $jsonPath = resource_path('sample_feedback.json');
        if (!File::exists($jsonPath)) {
            $this->command->error('JSON file not found at: ' . $jsonPath);
            return;
        }

        $jsonData = json_decode(File::get($jsonPath), true);
        
        if (!$jsonData) {
            $this->command->error('Invalid JSON format');
            return;
        }

        $this->command->info('Loading ' . count($jsonData) . ' feedbacks from JSON...');

        // Collect feedbacks for duplication (30% will be duplicated)
        $feedbacksToStore = [];
        $feedbacksForDuplication = [];

        foreach ($jsonData as $item) {
            $typeId = $classificationMap[$item['classification']] ?? 2; // Default to concerns
            
            // Get appropriate theme based on department
            $themes = $departmentThemeMap[$item['department']] ?? $departmentThemeMap['default'];
            $themeId = $faker->randomElement($themes);
            
            $dateSubmitted = isset($item['date_submitted']) 
                ? Carbon::parse($item['date_submitted']) 
                : Carbon::now()->subDays(rand(1, 90));
            
            $dateValidated = isset($item['date_validated']) && $item['date_validated']
                ? Carbon::parse($item['date_validated'])
                : null;
            
            $isValidated = !is_null($dateValidated);

            $feedback = [
                'fbk_uuid' => $faker->uuid(),
                'typ_id' => $typeId,
                'thm_id' => $themeId,
                'branch_id' => $faker->randomElement($branches),
                'std_id_no' => $faker->numberBetween(2020000, 2024999),
                'std_name' => $item['name'] ?? 'Anonymous',
                'std_email' => $item['email'] ?? null,
                'std_mobile' => isset($item['phone_num']) ? str_replace('+63', '0', $item['phone_num']) : null,
                'std_program' => $faker->randomElement($programs),
                'fbk_details' => $item['message'],
                'fbk_date_created' => $dateSubmitted,
                'fbk_date_validated' => $dateValidated,
                'fbk_validated_by' => $isValidated && !empty($users) ? $faker->randomElement($users) : null,
                'fbk_status' => $isValidated ? 1 : 0,
                'fbk_disapprove_details' => null,
                'fbk_created_by' => !empty($users) && $faker->boolean(60) ? $faker->randomElement($users) : null,
            ];

            $feedbacksToStore[] = $feedback;
            
            // 30% chance this feedback will be selected for duplication
            if ($faker->boolean(30)) {
                $feedbacksForDuplication[] = $feedback;
            }
        }

        foreach ($feedbacksToStore as $feedbackData) {
            Feedback::create($feedbackData);
        }

        $this->command->info('Total feedbacks in database: ' . DB::table('feedbacks')->count());
    }
}
