<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Faker\Factory as Faker;

class ActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();

        // Get reference data
        $users = DB::table('users')->pluck('usr_id')->toArray();
        $tickets = DB::table('tickets')
            ->select('tck_id', 'tck_date_created', 'tck_date_action', 'tck_date_verified', 'tck_verified_by')
            ->get();

        if ($tickets->isEmpty()) {
            $this->command->error('No tickets found. Please run TicketSeeder first.');
            return;
        }

        $actionsCreated = 0;

        foreach ($tickets as $ticket) {
            // Determine how many actions this ticket should have (1 to 4)
            // Even tickets without a 'tck_date_action' might have initial internal notes/actions
            $numActions = rand(1, 4);
            
            $lastActionDate = Carbon::parse($ticket->tck_date_created);

            for ($i = 0; $i < $numActions; $i++) {
                // Progress the date for each subsequent action
                $actDateCreated = $lastActionDate->copy()->addHours(rand(1, 24));
                
                // Ensure we don't exceed the ticket's final action/verification date if they exist
                if ($ticket->tck_date_verified && $actDateCreated->gt(Carbon::parse($ticket->tck_date_verified))) {
                    break; 
                }

                $isLastAction = ($i === $numActions - 1);
                
                // Logic for verification: usually only the final action is the one that gets verified
                $isVerified = $isLastAction && $ticket->tck_date_verified !== null;
                
                // 10% chance an action was rejected (if not the verified one)
                $isRejected = !$isVerified && $faker->boolean(10);

                $action = [
                    'act_uuid' => $faker->uuid(),
                    'tck_id' => $ticket->tck_id,
                    'act_details' => $faker->paragraph(rand(2, 5)),
                    'act_date_created' => $actDateCreated,
                    'act_created_by' => !empty($users) ? $faker->randomElement($users) : null,
                    'act_status' => $isVerified ? 1 : ($isRejected ? 2 : 0),
                    'act_reject_details' => $isRejected ? $faker->sentence() : null,
                    'act_date_verified' => $isVerified ? $ticket->tck_date_verified : null,
                    'act_verified_by' => $isVerified ? $ticket->tck_verified_by : null,
                    'act_active' => 1,
                    'act_file' => $faker->boolean(30) ? 'uploads/actions/' . $faker->word() . '.pdf' : null,
                    'act_auto_closed' => 0,
                    'act_auto_closed_date' => null,
                ];

                DB::table('actions')->insert($action);
                $actionsCreated++;
                
                $lastActionDate = $actDateCreated;
            }
        }

        $this->command->info('Total actions created: ' . $actionsCreated);
    }
}