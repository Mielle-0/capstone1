<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AiSetting;
use Illuminate\Http\Request;

class AiSettingsController extends Controller
{
    public function index()
    {
        // Fetch from database, default to 0.50 if not found
        $threshold = AiSetting::get('prediction_threshold', 0.50);
        
        // Check if AI is enabled (returns 'yes' or 'no')
        $aiEnabled = AiSetting::get('ai_enabled', 'yes') === 'yes';

        return view('admin.ai-settings', compact('threshold', 'aiEnabled'));
    }

    public function update(Request $request)
    {
        // 1. Validate the range input
        $request->validate([
            'prediction_threshold' => 'required|numeric|min:0|max:1',
        ]);

        // 2. Handle the checkbox (if missing, it means "no")
        $aiEnabled = $request->has('ai_enabled') ? 'yes' : 'no';

        // 3. Update or Create
        AiSetting::updateOrCreate(
            ['key' => 'prediction_threshold'],
            ['value' => $request->prediction_threshold]
        );

        AiSetting::updateOrCreate(
            ['key' => 'ai_enabled'],
            ['value' => $aiEnabled]
        );

        return back()->with('success', 'AI Settings updated successfully!');
    }
}
