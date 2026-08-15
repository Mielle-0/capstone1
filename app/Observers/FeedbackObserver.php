<?php

namespace App\Observers;

use App\Models\Feedback;
use App\Jobs\PredictFeedbackDepartment;

class FeedbackObserver
{
    /**
     * Handle the Feedback "created" event.
     */
    public function created(Feedback $feedback): void
    {
        \Log::info("Observer Fired for Feedback ID: " . $feedback->fbk_id);
        
        $details = trim($feedback->fbk_details ?? '');

        if (empty($details)) {
            Log::warning("Prediction job skipped for Feedback ID: {$feedback->fbk_id}. Reason: fbk_details is empty.");
            return; // Exit early, do not dispatch the job
        }

        // If feedback is saved with status of Declined/Ignored, do not run Job
        if ($feedback->fbk_status == 2) {
            \Log::info("Prediction job skipped for Feedback ID: {$feedback->fbk_id}. Reason: Matched with FAQ.");
            return;
        }

        PredictFeedbackDepartment::dispatch(
            $feedback->fbk_id,
            $details,
            $feedback->branch_id 
        );
    }

    /**
     * Handle the Feedback "updated" event.
     */
    public function updated(Feedback $feedback): void
    {
        //
    }

    /**
     * Handle the Feedback "deleted" event.
     */
    public function deleted(Feedback $feedback): void
    {
        //
    }

    /**
     * Handle the Feedback "restored" event.
     */
    public function restored(Feedback $feedback): void
    {
        //
    }

    /**
     * Handle the Feedback "force deleted" event.
     */
    public function forceDeleted(Feedback $feedback): void
    {
        //
    }
}
