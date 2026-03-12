<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PredictionCandidate extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'prediction_id',
        'dep_id',
        'probability',
        'rank'
    ];

    /**
     * Relationship back to the main Prediction header.
     */
    public function prediction(): BelongsTo
    {
        return $this->belongsTo(FeedbackPrediction::class, 'prediction_id');
    }

    /**
     * Relationship to the Departments table.
     * This is where the "Human Readable" names come from.
     */
    public function department(): BelongsTo
    {
        // Assuming your department primary key is 'dep_id'
        return $this->belongsTo(Department::class, 'dep_id', 'dep_id');
    }
}
