<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Str;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Feedback;
use App\Models\FeedbackPrediction;

class PredictFeedbackDepartment implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fbk_id;
    protected $details;
    protected $branch;

    public function __construct($fbk_id, $details, $branch)
    {
        $this->fbk_id = $fbk_id;
        $this->details = $details;
        $this->branch = $branch;
    }

    public function handle(): void 
    {

        $aiStatus = DB::table('ai_settings')->where('key', 'ai_enabled')->value('value');
        if ($aiStatus !== 'on') {
            \Log::info("AI Prediction skipped: Feature is disabled in settings.");
            return;
        }

        $timeout = DB::table('ai_settings')->where('key', 'api_timeout')->value('value') ?? 30;
        $url = config('services.ml_api.url');
        

        // 2. Call your Linux-hosted Python API
        $response = Http::connectTimeout(30) // Give DNS/Connection 30 seconds
            ->timeout($timeout)                   // Give the API 2 minutes to process
            ->withHeaders(['X-API-KEY' => config('services.ml_api.key')])
            ->post($url . '/predict', [
                'branch' => $this->branch,
                'details' => $this->details
            ]);


        if ($response->failed()) {
            \Log::error("API Call Failed: " . $response->body());
            return;
        }

        if ($response->successful()) {
            $data = $response->json();
            
            $prediction = FeedbackPrediction::create([
                'fbk_id' => $this->fbk_id,
                'model_version' => Str::afterLast($data['used_model'], '/'),
            ]);

            // 2. Save the Top 3 Candidates (Instead of JSON)
            foreach ($data['top_3'] as $index => $item) {
                $prediction->candidates()->create([
                    'dep_id' => $item['department'],
                    'probability' => $item['probability'],
                    'rank' => $index + 1
                ]);
            }
        }
    }

    
}
