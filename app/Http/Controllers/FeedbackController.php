<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;

class FeedbackController extends Controller
{

    public function encodeFeedback()
    {
        // Fetch only active types and their related themes
        $types = Type::where('typ_active', 1)->get();
        $themes = Theme::where('thm_active', 1)->get();

        return view('encoder.create', compact('types', 'themes'));
    }

    public function forValidation(Request $request)
    {
        $query = Feedback::with(['type', 'theme'])
            ->where('fbk_status', 0) // 0 = Pending
            ->where('fbk_active', 1);

        $feedbacks = $this->applyFeedbackFilters($query, $request)
            ->orderBy('fbk_date_created', 'desc')
            ->paginate(20);

        $stats = [
            'pending' => Feedback::where('fbk_status', 0)->count(),
            'total_today' => Feedback::whereDate('fbk_date_created', today())->count(),
        ];

        return view('feedbacks.for_validation', compact('feedbacks', 'stats'));
    }
}
