<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeedbackTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['typ_id' => 1, 'typ_value' => 'Inquiry', 'typ_active' => 1],
            ['typ_id' => 2, 'typ_value' => 'Concerns', 'typ_active' => 1],
            ['typ_id' => 3, 'typ_value' => 'Suggestions', 'typ_active' => 1],
            ['typ_id' => 4, 'typ_value' => 'Complaints', 'typ_active' => 1],
            ['typ_id' => 5, 'typ_value' => 'Commendation', 'typ_active' => 1],
        ];

        DB::table('feedback_types')->insert($types);
    }
}
