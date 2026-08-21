<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AiSetting;
use App\Models\FeedbackType;
use Illuminate\Http\Request;

class AiSettingsController extends Controller
{
    public function index()
    {
        // Fetch from database, default to 0.50 if not found
        $threshold = AiSetting::get('ai_threshold', 0.40);
        
        // Check if AI is enabled (returns 'yes' or 'no')
        $aiEnabled = AiSetting::get('ai_enabled', 'yes') === 'yes';
        
        $restrictedJson = AiSetting::where('key', 'restricted_categories')->value('value');
        $restrictedCategories = json_decode($restrictedJson, true) ?? [];

        // Fetch all available feedback types for the checkboxes
        $feedbackTypes = FeedbackType::all();

        return view('admin.ai-settings', compact(
            'threshold', 
            'aiEnabled', 
            'restrictedCategories', 
            'feedbackTypes'
        ));
    }

    public function update(Request $request)
    {
        // 1. Validate the range input
        $request->validate([
            'prediction_threshold' => 'required|numeric|min:0|max:1',
            'restricted_categories' => 'nullable|array', // Ensure it's an array if present
            'restricted_categories.*' => 'integer', // Ensure array items are IDs
        ]);

        // 2. Handle the checkbox (if missing, it means "no")
        $aiEnabled = $request->has('ai_enabled') ? 'yes' : 'no';

        $restrictedArray = array_map('intval', $request->input('restricted_categories', []));

        // 3. Update or Create
        AiSetting::updateOrCreate(
            ['key' => 'ai_threshold'],
            ['value' => $request->prediction_threshold]
        );

        AiSetting::updateOrCreate(
            ['key' => 'ai_enabled'],
            ['value' => $aiEnabled]
        );

        AiSetting::updateOrCreate(
            ['key' => 'restricted_categories'],
            ['value' => json_encode($restrictedArray)]
        );

        return back()->with('success', 'AI Settings updated successfully!');
    }
}
