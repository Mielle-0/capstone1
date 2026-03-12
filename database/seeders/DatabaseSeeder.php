<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {

        
        $this->call([
            BranchSeeder::class,
            DepartmentSeeder::class,
            FeedbackTypeSeeder::class,
            RoleSeeder::class,
            ThematicValueSeeder::class,

            UserSeeder::class,
            
            AiSettingSeeder::class,
            // FeedbackSeeder::class,
            // TicketSeeder::class,
            // ActiontSeeder::class,
        ]);
    }
}