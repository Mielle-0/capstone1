<?php

namespace Database\Seeders;

use App\Models\AiSetting;
use Illuminate\Database\Seeder;

class AiSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'prediction_threshold',
                'value' => '0.50',
                'description' => 'Minimum probability (0.0 - 1.0) required to show an AI suggestion.'
            ],
            [
                'key' => 'ai_enabled',
                'value' => 'yes',
                'description' => 'Global toggle to show or hide AI suggestions (yes/no).'
            ],
        ];

        foreach ($settings as $setting) {
            AiSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}