<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\FeedbackPrediction;
use App\Models\Feedback;
use App\Models\Ticket;
use App\Models\AiSetting;

class PredictFeedbackDepartment implements ShouldQueue 
{
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
        // 1. Start timer & measure data quality
        $startTime = microtime(true);
        $wordCount = str_word_count($this->details);

        // 2. Fetch AI Settings
        $aiStatus = DB::table('ai_settings')->where('key', 'ai_enabled')->value('value');
        if (!in_array($aiStatus, ['yes', 'on'])) {
            Log::info("AI Prediction skipped: Feature is disabled in settings.");
            return;
        }

        $timeout = (int) (DB::table('ai_settings')->where('key', 'api_timeout')->value('value') ?? 30);
        
        // Handle threshold scale (e.g., UI might save 70 for 70%, but we need 0.70)
        $rawThreshold = DB::table('ai_settings')->where('key', 'ai_threshold')->value('value') ?? 70;
        $currentThreshold = (float) ($rawThreshold > 1 ? $rawThreshold / 100 : $rawThreshold);

        $url = config('services.ml_api.url');
        $apiKey = config('services.ml_api.key');
        
        try 
        {
            
            // ==========================================
            // API CALL 1: Predict the Category
            // ==========================================
            $catResponse = Http::connectTimeout(30)->timeout($timeout)
                ->withHeaders(['X-API-KEY' => $apiKey])
                ->post($url . '/predict-category', [
                    'details' => $this->details
                ]);

            // Fallbacks in case category prediction fails but we still want to try department
            $predictedCategory = 'unknown';
            $categoryConfidence = null;
            $categoryModelVersion = null;
            $detectedLanguage = 'unknown';

            if ($catResponse->successful()) {
                $catData = $catResponse->json();
                $predictedCategory = $catData['category'] ?? 'unknown';
                $categoryConfidence = $catData['confidence'] ?? $catData['probability'] ?? null;
                $categoryModelVersion = Str::afterLast($catData['used_model'] ?? '', '/');
                $detectedLanguage = $catData['detected_language'] ?? 'unknown'; // Extract detected language
            } else {
                Log::warning("Category API Call Failed. Using 'unknown' fallback for fbk_id: {$this->fbk_id}");
            }

            // ==========================================
            // API CALL 2: Predict the Department
            // ==========================================
            $depResponse = Http::connectTimeout(30)->timeout($timeout)
                ->withHeaders(['X-API-KEY' => $apiKey])
                ->post($url . '/predict', [
                    'branch' => $this->branch,
                    'details' => $this->details,
                    'category' => (int) $predictedCategory 
                ]);

            if ($depResponse->failed()) {
                Log::error("Department API Call Failed: " . $depResponse->body());
                throw new \Exception("Department API failed: " . $depResponse->status());
            }

            $depData = $depResponse->json();

            if (empty($depData['top_3'])) {
                // FIX: Same here, throw an exception if the ML payload is corrupted/empty
                throw new \Exception("Empty candidates returned from ML API for fbk_id: {$this->fbk_id}");
            }

            // ==========================================
            // INTERVENTION LOGIC
            // ==========================================
            $qualifyingDepartments = [];
            foreach ($depData['top_3'] as $item) {
                if ((float) $item['probability'] >= $currentThreshold) {
                    $qualifyingDepartments[] = $item['department'];
                }
            }

            // Fetch restricted categories array from database (defaults to [3] if setting is missing/invalid)
            $rawRestricted = AiSetting::get('restricted_categories', '[4]');
            $restrictedCategories = json_decode($rawRestricted, true) ?? [4];

            // Normalize array values to integers for loose-type safety
            $restrictedCategoryIds = array_map('intval', $restrictedCategories);

            // Check if the predicted category is inside the restricted list
            $isRestrictedCategory = in_array((int) $predictedCategory, $restrictedCategoryIds, true);



            // Check if the category prediction crossed the threshold
            // $categoryPassed = !is_null($categoryConfidence) && ((float) $categoryConfidence >= $currentThreshold);

            // Intervention is required if no departments qualified OR if category is restricted
            $requiresIntervention = empty($qualifyingDepartments) || $isRestrictedCategory;
            // $requiresIntervention = empty($qualifyingDepartments) || !$categoryPassed;

            if ($isRestrictedCategory && !empty($qualifyingDepartments)) {
                Log::info("Auto-route blocked for Feedback ID: {$this->fbk_id}. Reason: Category ID [{$predictedCategory}] is restricted from auto-routing.");
            }

            $endTime = microtime(true);
            $processingTimeMs = round(($endTime - $startTime) * 1000);

            // ==========================================
            // SAVE TO DATABASE & ROUTE TICKET
            // ==========================================
            DB::transaction(function () use (
                $predictedCategory, $categoryConfidence, $categoryModelVersion, 
                $depData, $currentThreshold, $requiresIntervention, $detectedLanguage,
                $wordCount, $processingTimeMs, $qualifyingDepartments
            ) {
                
                $actionTaken = $requiresIntervention ? null : 'auto_routed';
                $actionTakenAt = $requiresIntervention ? null : now();

                $verifiedDeptIds = $requiresIntervention ? null : $qualifyingDepartments;

                // 1. Create or Update the Parent Table
                $prediction = FeedbackPrediction::updateOrCreate(
                    ['fbk_id' => $this->fbk_id],
                    [
                        // Category Data
                        'category_model_version' => $categoryModelVersion,
                        'predicted_category' => $predictedCategory,
                        'category_confidence' => $categoryConfidence,
                        'detected_language'      => $detectedLanguage, 
                        
                        // Department Data
                        'model_version' => Str::afterLast($depData['used_model'] ?? 'unknown', '/'),
                        
                        // Performance Data
                        'input_word_count' => $wordCount,
                        'processing_time_ms' => $processingTimeMs,
                        
                        // Intervention Data
                        'requires_intervention' => $requiresIntervention,
                        'threshold_applied' => $currentThreshold,
                        'action_taken' => $actionTaken,
                        'action_taken_at' => $actionTakenAt,
                        'verified_dept_ids' => $verifiedDeptIds,
                    ]
                );

                // 2. Clear old candidates & save the Top 3
                $prediction->candidates()->delete();
                foreach ($depData['top_3'] as $index => $item) {
                    $prediction->candidates()->create([
                        'dep_id' => $item['department'],
                        'probability' => $item['probability'],
                        'rank' => $index + 1
                    ]);
                }

                // 3. Auto-Route Logic (Objective 1.3.2.2 / Title alignment)
                if (!$requiresIntervention) {
                    
                    // Loop through EVERY department that passed the threshold and create a ticket
                    foreach ($qualifyingDepartments as $depId) {
                        Ticket::create([
                            'tck_uuid' => (string) Str::uuid(),
                            'fbk_id' => $this->fbk_id,
                            'dep_id' => $depId,
                            'tck_date_created' => now(),
                            'tck_active' => 1,
                        ]);
                    }

                    Feedback::where('fbk_id', $this->fbk_id)
                        ->update(['fbk_status' => 1]);

                    $deptString = implode(', ', $qualifyingDepartments);
                    Log::info("Tickets auto-routed for Feedback ID: {$this->fbk_id} to departments: [{$deptString}]");
                }

            });

        } catch (\Exception $e) {
            Log::error("AI Prediction Pipeline Failed: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return; 
        }
    }  

    public function failed(\Throwable $exception)
    {
        // Log the exact error for debugging
        Log::critical("AI Prediction API failed for Feedback ID: {$this->fbk_id}. Error: {$exception->getMessage()}");

        // Optional: Send an email to the Super Admin
        // Mail::to('admin@um.edu.ph')->send(new AiApiDownAlert($exception->getMessage()));
        
        // Note: We deliberately do NOT change the fbk_status here. 
        // By leaving it as 0, the human staff can still process it manually!
    }
}