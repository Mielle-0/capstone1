<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\FeedbackType;
use App\Models\ThematicValue;
use App\Models\Branch;
use Illuminate\Support\Str; 
use Illuminate\Http\Request;

class FeedbackPortalController extends Controller
{
    /**
     * Show the public feedback form.
     */
    public function showForm()
    {
        $types = FeedbackType::where('typ_active', 1)->get();
        $themes = ThematicValue::where('thm_active', 1)->get();
        $branches = Branch::all(); 
        
        return view('feedback_portal', compact('types', 'themes', 'branches'));
    }

    /**
     * Handle the form submission.
     */
    public function submitFeedback(Request $request)
    {
        $request->validate([
            'id_number' => 'nullable|numeric',
            'last_name' => 'required|string',
            'first_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|digits:11',
            'branch' => 'required|exists:branches,branch_id',
            'category' => 'required|integer', 
            'thm_id' => 'required|integer', 
            'message' => 'required|string|min:10',
            'consent' => 'accepted',
        ]);

        $fullName = trim($request->first_name . ' ' . $request->middle_initial . ' ' . $request->last_name);

        $feedback = new Feedback();
        $feedback->fbk_uuid = (string) Str::uuid();
        $feedback->std_id_no = $request->id_number;
        $feedback->std_name = $fullName;
        $feedback->branch_id = $request->branch; 
        $feedback->typ_id = $request->category; 
        $feedback->thm_id = $request->thm_id;   
        $feedback->fbk_details = $request->message;
        
        $feedback->fbk_status = 0;
        $feedback->fbk_date_created = now();
        $feedback->save();

        return redirect()->back()->with('success', 'Your feedback has been submitted successfully! Reference ID: ' . substr($feedback->fbk_uuid, 0, 8));
    }
}