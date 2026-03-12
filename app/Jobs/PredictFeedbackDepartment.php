<?php

namespace App\Jobs;

use App\Models\Feedback;
use App\Models\FeedbackPrediction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class PredictFeedbackDepartment implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Feedback $feedback) {}

    public function handle(): void {
        \Log::info("Job started for Feedback: " . $this->feedback->fbk_id);
    
        $payload = [
            'branch' => $this->feedback->branch->branch_name ?? 'Unknown',
            'details' => $this->feedback->fbk_details,
            'target_model' => 'multinomial_nb_count', 
            'target_vectorizer' => 'count_vectorizer'
        ];

        // 2. Call your Linux-hosted Python API
        $response = Http::timeout(10)->post('http://127.0.0.1:5000/predict', $payload);

        if ($response->failed()) {
            \Log::error("API Call Failed: " . $response->body());
            return;
        }

        if ($response->successful()) {
            $data = $response->json();
            
            $prediction = FeedbackPrediction::create([
                'fbk_id' => $this->feedback->fbk_id,
                'model_version' => $data['used_model'],
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
