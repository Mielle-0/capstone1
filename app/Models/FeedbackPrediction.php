<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackPrediction extends Model
{
    protected $fillable = [
        'fbk_id', 
        'model_version', 
        'verified_dept_ids',
        'category_model_version',
        'predicted_category',
        'category_confidence',
        'detected_language',
        'verified_category',
        'input_word_count',
        'requires_intervention',
        'threshold_applied',
        'action_taken',
        'action_taken_by',
        'action_taken_at',
        'processing_time_ms'
    ];

    protected $casts = [
        'verified_dept_ids' => 'array',
        'category_confidence' => 'float',
        'threshold_applied' => 'float',
        'requires_intervention' => 'boolean',
        'action_taken_at' => 'datetime',
    ];

    /**
     * The Top 3 guesses 
     */
    public function candidates(): HasMany
    {
        return $this->hasMany(PredictionCandidate::class, 'prediction_id')->orderBy('rank', 'asc');
    }

    /**
     * The winner (Rank 1) helper.
     */
    public function topCandidate(): HasOne
    {
        return $this->hasOne(PredictionCandidate::class, 'prediction_id')->where('rank', 1);
    }

    /**
     * Relationship to the staff member who took action (manual verification/correction).
     */
    public function actionTakenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'action_taken_by', 'usr_id');
    }

    /**
     * Helper to get the actual Department models that were verified.
     * Since verified_dept_ids is an array, we use a custom accessor instead of a standard BelongsTo.
     * Usage: $prediction->verified_departments
     */
    public function getVerifiedDepartmentsAttribute()
    {
        if (empty($this->verified_dept_ids)) {
            return collect(); // Return empty Eloquent collection if none exist
        }
        
        return Department::whereIn('dep_id', $this->verified_dept_ids)->get();
    }

    /**
     * Helper to check if the AI was correct.
     * Now checks if the Rank 1 candidate's ID exists inside the JSON array of verified IDs.
     */
    public function wasAiCorrect(): bool
    {
        if (empty($this->verified_dept_ids) || !$this->topCandidate) {
            return false;
        }
        
        return in_array($this->topCandidate->dep_id, $this->verified_dept_ids);
    }

    /**
     * Standardize language codes to human-readable names.
     */
    public function getFormattedLanguageAttribute(): string
    {
        $code = strtolower($this->detected_language ?? '');

        return match ($code) {
            'en', 'english' => 'English',
            'tl', 'tagalog' => 'Tagalog',
            'ceb', 'cebuano', 'bisaya' => 'Cebuano',
            default => ucfirst($this->detected_language ?? 'Unknown')
        };
    }
}