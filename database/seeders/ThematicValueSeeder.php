<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThematicValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $values = [
            ['thm_id' => 1, 'typ_id' => 1, 'thm_value' => 'Others', 'thm_active' => 1],
            ['thm_id' => 2, 'typ_id' => 2, 'thm_value' => 'Others', 'thm_active' => 1],
            ['thm_id' => 3, 'typ_id' => 3, 'thm_value' => 'Others', 'thm_active' => 1],
            ['thm_id' => 4, 'typ_id' => 4, 'thm_value' => 'Others', 'thm_active' => 1],
            ['thm_id' => 5, 'typ_id' => 5, 'thm_value' => 'Others', 'thm_active' => 1],
            ['thm_id' => 6, 'typ_id' => 1, 'thm_value' => 'Process', 'thm_active' => 1],
            ['thm_id' => 7, 'typ_id' => 2, 'thm_value' => 'Process', 'thm_active' => 1],
            ['thm_id' => 8, 'typ_id' => 2, 'thm_value' => 'Personnel', 'thm_active' => 1],
            ['thm_id' => 9, 'typ_id' => 2, 'thm_value' => 'Facilities', 'thm_active' => 1],
            ['thm_id' => 10, 'typ_id' => 3, 'thm_value' => 'Process', 'thm_active' => 1],
            ['thm_id' => 11, 'typ_id' => 3, 'thm_value' => 'Facilities', 'thm_active' => 1],
            ['thm_id' => 12, 'typ_id' => 4, 'thm_value' => 'Personnel', 'thm_active' => 1],
            ['thm_id' => 13, 'typ_id' => 4, 'thm_value' => 'Process', 'thm_active' => 1],
            ['thm_id' => 14, 'typ_id' => 4, 'thm_value' => 'Facilities', 'thm_active' => 1],
        ];

        DB::table('thematic_values')->insert($values);
    }
}
