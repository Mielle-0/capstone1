<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\FeedbackType;
use App\Models\ThematicValue;
use App\Models\Branch;
use App\Models\Ticket;
use App\Models\UserResponse;
use Illuminate\Support\Str; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;
use App\Mail\GuestFeedbackMail;

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

    public function sendCode(Request $request)
    {
        $validatedData = $request->validate([
            'id_number' => 'nullable|numeric',
            'last_name' => 'required|string',
            'first_name' => 'required|string',
            'middle_initial' => 'nullable|string|max:1',
            'email' => 'required|email',
            'phone' => 'required|digits:11',
            'branch' => 'required|exists:branches,branch_id',
        ]);

        $verificationCode = rand(100000, 999999);

        // 3. Send the code (Mocked here - replace with actual Mail or SMS logic)
        // Mail::to($request->email)->send(new \App\Mail\YourVerificationMail($verificationCode));
        \Log::info("Verification code for {$request->email} is {$verificationCode}");

        // Store the validated data and code in the session
        session([
            'personal_info' => $validatedData,
            'verification_code' => $verificationCode,
            'step_one_complete' => true,
        ]);

        Mail::to($request->email)->send(
            new VerificationCodeMail($verificationCode, $validatedData['first_name'])
        );
        
        return redirect()->back()->with('status', 'Verification code sent to your email/phone!');
    }

    /**
     * Handle the form submission.
     */
    public function submitFeedback(Request $request)
    {
        // Ensure the user actually completed step 1
        if (!session()->has('personal_info') || !session()->has('verification_code')) {
            return redirect()->route('feedback.form')->withErrors(['error' => 'Please fill out your personal information and request a code first.']);
        }
        
        // Validate the feedback details AND the inputted code
        $request->validate([
            'verification_code' => 'required|numeric',
            'category' => 'required|integer', 
            'thm_id' => 'required|integer', 
            'message' => 'required|string|min:10',
            'consent' => 'accepted',
        ]);

        // Check if the code matches what we stored in the session
        if ($request->verification_code != session('verification_code')) {
            return redirect()->back()->withErrors(['verification_code' => 'The verification code is incorrect.'])->withInput();
        }

        // Retrieve personal info from session
        $personalInfo = session('personal_info');
        
        $middleInitial = isset($personalInfo['middle_initial']) ? $personalInfo['middle_initial'] . ' ' : '';
        $fullName = trim($personalInfo['first_name'] . ' ' . $middleInitial . $personalInfo['last_name']);

        // Save the Feedback
        $feedback = new Feedback();
        $feedback->fbk_uuid = (string) Str::uuid();
        $feedback->std_id_no = $personalInfo['id_number'] ?? null;
        $feedback->std_name = $fullName;
        $feedback->std_email = $personalInfo['email'];
        $feedback->branch_id = $personalInfo['branch']; 
        $feedback->typ_id = $request->category; 
        $feedback->thm_id = $request->thm_id;   
        $feedback->fbk_details = $request->message;
        $feedback->fbk_status = 0;
        $feedback->fbk_date_created = now();
        $feedback->save();

        // Clear the session so the form resets for the next submission
        session()->forget(['personal_info', 'verification_code', 'step_one_complete']);

        return redirect()->route('feedback.form')->with('success', 'Your feedback has been submitted successfully! Reference ID: ' . substr($feedback->fbk_uuid, 0, 8));
    }

    /*
        User Requests their Feedbacks  
     */
    public function sendHistoryLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        // Generate the signed URL expiring in 24 hours
        $temporaryUrl = URL::temporarySignedRoute(
            'feedback.guest.view', 
            now()->addHours(24), 
            ['email' => $request->email]
        );

        // Send the email
        Mail::to($request->email)->send(new GuestFeedbackMail($temporaryUrl));

        return response()->json(['status' => 'success', 'message' => 'Link sent!']);
    }

    public function guestView(Request $request, $email)
    {
        // Fetch all feedback belonging to this exact email.
        // We use 'std_email' to match your Feedback model's schema.
        $feedbacks = Feedback::with([
                'tickets.department', 
                'tickets.actions',
            ])
            ->where('std_email', $email) 
            ->orderBy('fbk_date_created', 'desc')
            ->get();

        // If for some reason they have no feedback (e.g., it was deleted), handle it gracefully
        if ($feedbacks->isEmpty()) {
            return redirect('/')
                ->withErrors(['error' => 'Email has no previous feedbacks.']);
        }

        // Return the view displaying their list of feedback
        return view('guest_list', compact('feedbacks', 'email'));
    }

    public function guestTimeline(Request $request, $email, $id)
    {
        $feedback = Feedback::with([
            'tickets.department',
            'tickets.responses',
            'tickets.actions' => function($query) {
                $query->where('act_status', 1);
            },
            'tickets.actions.creator'
        ])
        ->where('std_email', $email)
        ->findOrFail($id);

        return view('guest_timeline', compact('feedback', 'email'));
    }

    // Function 1: Rate and Close
    public function guestRate(Request $request, $id)
    {
        $request->validate([
            'email'    => 'required|email',
            'tck_rate' => 'required|integer|min:1|max:5',
        ]);

        $ticket = Ticket::with('feedback')->findOrFail($id);

        if ($ticket->feedback->std_email !== $request->email) {
            abort(403, 'Unauthorized action.');
        }

        // Apply rating and mark as resolved/closed
        $ticket->update([
            'tck_rate' => $request->tck_rate,
            'tck_rate_date' => now(), // Assuming this is your "resolved" flag based on previous snippets
        ]);

        return back()->with('success', 'Ticket closed successfully. Thank you for your feedback!');
    }

    // Function 2: Respond (Keep Open)
    public function guestReply(Request $request, $id)
    {
        $request->validate([
            'email'       => 'required|email',
            'res_message' => 'required|string',
        ]);

        $ticket = Ticket::with('feedback')->findOrFail($id);

        if ($ticket->feedback->std_email !== $request->email) {
            abort(403, 'Unauthorized action.');
        }

        // Create the response
        $ticket->responses()->create([
            'res_message'      => $request->res_message,
            'res_date_created' => now(),
        ]);

        // Send back to department's pending list (forces them to submit another action)
        $ticket->update(['tck_date_action' => null]);

        return back()->with('success', 'Your reply has been sent back to the department.');
    }
}