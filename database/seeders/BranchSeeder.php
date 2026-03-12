<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['branch_id' => 'UM-BANSALAN'],
            ['branch_id' => 'UM-DIGOS'],
            ['branch_id' => 'UM-MAIN'],
            ['branch_id' => 'UM-PANABO'],
            ['branch_id' => 'UM-PENAPLATA'],
            ['branch_id' => 'UM-TAGUM'],
        ];

        DB::table('branches')->insert($roles);
    }
}
