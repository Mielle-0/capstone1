<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();

        // Get reference data
        $users = DB::table('users')->pluck('usr_id')->toArray();
        $departments = DB::table('departments')->pluck('dep_id')->toArray();

        // Get all feedbacks
        $feedbacks = DB::table('feedbacks')
            ->select('fbk_id', 'fbk_details', 'fbk_date_created')
            ->get();

        if ($feedbacks->isEmpty()) {
            $this->command->error('No feedbacks found. Please run FeedbackSeeder first.');
            return;
        }

        // Group feedbacks by similar content to identify duplicates
        $feedbackGroups = $this->groupSimilarFeedbacks($feedbacks);

        $this->command->info('Found ' . count($feedbackGroups) . ' unique feedback groups');

        $ticketsCreated = 0;

        foreach ($feedbackGroups as $group) {
            // Create one ticket per group of similar feedbacks
            // The ticket will reference the first feedback in the group
            $firstFeedback = $group[0];
            
            $dateCreated = Carbon::parse($firstFeedback->fbk_date_created)
                ->addDays(rand(0, 2)); // Ticket created 0-2 days after feedback

            // 70% of tickets have been actioned
            $hasAction = $faker->boolean(70);
            $dateAction = $hasAction 
                ? $dateCreated->copy()->addDays(rand(1, 5)) 
                : null;
            $actionBy = $hasAction && !empty($users) 
                ? $faker->randomElement($users) 
                : null;

            // 50% of actioned tickets have been verified
            $hasVerification = $hasAction && $faker->boolean(50);
            $dateVerified = $hasVerification 
                ? $dateAction->copy()->addDays(rand(1, 3)) 
                : null;
            $verifiedBy = $hasVerification && !empty($users) 
                ? $faker->randomElement($users) 
                : null;

            // 5% chance of disapproval details (only if verified)
            $disapproveDetails = $hasVerification && $faker->boolean(5)
                ? $faker->sentence(rand(10, 20))
                : null;

            // 95% of tickets are active
            $isActive = $faker->boolean(95) ? 1 : 0;

            $ticket = [
                'tck_uuid' => $faker->uuid(),
                'fbk_id' => $firstFeedback->fbk_id,
                'dep_id' => !empty($departments) ? $faker->randomElement($departments) : null,
                'tck_date_created' => $dateCreated,
                'tck_created_by' => !empty($users) ? $faker->randomElement($users) : null,
                'tck_date_action' => $dateAction,
                'tck_action_by' => $actionBy,
                'tck_date_verified' => $dateVerified,
                'tck_verified_by' => $verifiedBy,
                'tck_disapprove_details' => $disapproveDetails,
                'tck_active' => $isActive,
            ];

            DB::table('tickets')->insert($ticket);
            $ticketsCreated++;

            // Log that this ticket was created from multiple feedbacks
            if (count($group) > 1) {
                $this->command->info(
                    "Created ticket #{$ticketsCreated} from " . count($group) . " duplicate feedbacks"
                );
            }
        }

        $this->command->info('Total tickets created: ' . $ticketsCreated);
        $this->command->info('Total feedbacks processed: ' . $feedbacks->count());
    }

    /**
     * Group similar feedbacks together (identifying duplicates)
     */
    private function groupSimilarFeedbacks($feedbacks)
    {
        $groups = [];
        $processed = [];

        foreach ($feedbacks as $feedback) {
            // Skip if already processed
            if (in_array($feedback->fbk_id, $processed)) {
                continue;
            }

            // Start a new group
            $currentGroup = [$feedback];
            $processed[] = $feedback->fbk_id;

            // Find similar feedbacks
            foreach ($feedbacks as $otherFeedback) {
                if ($feedback->fbk_id === $otherFeedback->fbk_id) {
                    continue;
                }

                if (in_array($otherFeedback->fbk_id, $processed)) {
                    continue;
                }

                // Check similarity (simple approach - you can make this more sophisticated)
                if ($this->areFeedbacksSimilar($feedback->fbk_details, $otherFeedback->fbk_details)) {
                    $currentGroup[] = $otherFeedback;
                    $processed[] = $otherFeedback->fbk_id;
                }
            }

            $groups[] = $currentGroup;
        }

        return $groups;
    }

    /**
     * Check if two feedback messages are similar
     */
    private function areFeedbacksSimilar($message1, $message2)
    {
        // Normalize messages
        $msg1 = strtolower(trim($message1));
        $msg2 = strtolower(trim($message2));

        // Exact match
        if ($msg1 === $msg2) {
            return true;
        }

        // Calculate similarity percentage
        similar_text($msg1, $msg2, $percent);

        // Consider similar if 85% or more match
        return $percent >= 85;
    }
}