<?php

namespace Database\Seeders;

use App\Models\AiSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class AiSettingSeeder extends Seeder
{
    public function run(): void
    {

        Schema::disableForeignKeyConstraints();
        AiSetting::truncate();
        Schema::enableForeignKeyConstraints();
        
        $settings = [
            [
                'key' => 'ai_threshold',
                'value' => '0.50',
                'description' => 'Minimum probability (0.0 - 1.0) required to show an AI suggestion.'
            ],
            [
                'key' => 'ai_enabled',
                'value' => 'yes',
                'description' => 'Global toggle to show or hide AI suggestions (yes/no).'
            ],
            [
                'key' => 'restricted_categories',
                'value' => json_encode([4]), // 4 = Complaint ID (Add more IDs to this array as needed, e.g. [3, 5])
                'description' => 'JSON array of Feedback Type IDs that require human intervention and are blocked from auto-routing.'
            ],
        ];

        foreach ($settings as $setting) {
            AiSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}