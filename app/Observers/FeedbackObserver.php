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
        PredictFeedbackDepartment::dispatch(
            $feedback->fbk_id,
            $feedback->fbk_details,
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
