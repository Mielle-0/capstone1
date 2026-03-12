<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $faker = Faker::create();

        // 1. CLEANUP: Disable constraints and truncate
        Schema::disableForeignKeyConstraints();
        DB::table('user_departments')->truncate();
        DB::table('user_roles')->truncate();
        DB::table('users')->truncate();
        Schema::enableForeignKeyConstraints();

        $users = [];
        $userNumber = 20;
        
        for ($i = 0; $i < $userNumber; $i++) {
            $usrCode = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            $usrName = $faker->name();
            $usrPassword = Hash::make('password' . $usrCode);
            
            $userId = DB::table('users')->insertGetId([
                'usr_code' => $usrCode,
                'usr_name' => $usrName,
                'usr_password' => $usrPassword,
                'usr_active' => 1,
            ]);
            
            $users[] = $userId;
        }
        
        // Assign roles to users (randomize role assignment)
        $roles = [1, 2, 3, 4, 5, 6]; // Super Admin, Validator, Dept Head, Verifier, Encoder, Reports Viewing
        
        foreach ($users as $userId) {
            // Assign 1-2 random roles per user
            $numRoles = rand(1, 2);
            $assignedRoles = $faker->randomElements($roles, $numRoles);
            
            foreach ($assignedRoles as $roleId) {
                DB::table('user_roles')->insert([
                    'usr_id' => $userId,
                    'rol_id' => $roleId,
                ]);
            }
        }
        
        // Assign departments to users based on specified distribution
        $userIndex = 0;
        
        // 4 users with 4 departments each
        for ($i = 0; $i < 4; $i++) {
            $departments = $faker->randomElements(range(1, 25), 4);
            foreach ($departments as $depId) {
                DB::table('user_departments')->insert([
                    'usr_id' => $users[$userIndex],
                    'dep_id' => $depId,
                ]);
            }
            $userIndex++;
        }
        
        // 3 users with 3 departments each
        for ($i = 0; $i < 3; $i++) {
            $departments = $faker->randomElements(range(1, 25), 3);
            foreach ($departments as $depId) {
                DB::table('user_departments')->insert([
                    'usr_id' => $users[$userIndex],
                    'dep_id' => $depId,
                ]);
            }
            $userIndex++;
        }
        
        // 5 users with 2 departments each
        for ($i = 0; $i < 5; $i++) {
            $departments = $faker->randomElements(range(1, 25), 2);
            foreach ($departments as $depId) {
                DB::table('user_departments')->insert([
                    'usr_id' => $users[$userIndex],
                    'dep_id' => $depId,
                ]);
            }
            $userIndex++;
        }
        
        // 5 users with 1 department each
        for ($i = 0; $i < 5; $i++) {
            $depId = rand(1, 25);
            DB::table('user_departments')->insert([
                'usr_id' => $users[$userIndex],
                'dep_id' => $depId,
            ]);
            $userIndex++;
        }
        
        $this->command->info('Database seeded successfully!');
        $this->command->info('Total users created: ' . count($users));
    }
}
