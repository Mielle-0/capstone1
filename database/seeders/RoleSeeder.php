<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['rol_id' => 1, 'rol_name' => 'Super Admin', 'rol_active' => 1],
            ['rol_id' => 2, 'rol_name' => 'Validator', 'rol_active' => 1],
            ['rol_id' => 3, 'rol_name' => 'Department Head', 'rol_active' => 1],
            ['rol_id' => 4, 'rol_name' => 'Verifier', 'rol_active' => 1],
            ['rol_id' => 5, 'rol_name' => 'Encoder', 'rol_active' => 1],
            ['rol_id' => 6, 'rol_name' => 'Reports Viewing', 'rol_active' => 1],
        ];

        DB::table('roles')->insert($roles);
    }
}
