<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'usr_code' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('usr_code', $credentials['usr_code'])
            ->where('usr_active', 1)
            ->first();

        if ($user && Hash::check($credentials['password'], $user->usr_password)) {
            Auth::login($user);

            // --- CACHE DEPARTMENTS IN SESSION ---
            // Store an array of IDs for quick 'in_array' checks
            $departmentIds = $user->departments()->pluck('departments.dep_id')->toArray();
            session(['user_department_ids' => $departmentIds]);
            
            // Store names to display them in the sidebar without querying
            $departmentNames = $user->departments()->pluck('dep_name', 'departments.dep_id')->toArray();
            session(['user_department_names' => $departmentNames]);

            return redirect('/dashboard');
        }

        return back()->with('error', 'Invalid credentials');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login.form');
    }

    public function create(User $user)
    {
        // Pass the user ID to the view so the form knows who is setting the password
        return view('setup-password', compact('user'));
    }

    // 2. Save the new password
    public function store(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        // Hash and save the password
        $user->update([
            'usr_password' => Hash::make($request->password)
        ]);

        // Optional: Auto-login the user after they set their password
        auth()->login($user);

        return redirect()->route('dashboard')->with('success', 'Your password has been set successfully!');
    }

    // Show the Forgot Password Form
    public function showForgotForm()
    {
        return view('forgot-password');
    }

    // Process the Email Request
    public function sendResetLink(Request $request)
    {
        $request->validate(['usr_email' => 'required|email']);

        $user = User::where('usr_email', $request->usr_email)->first();

        if (!$user) {
            return back()->with('error', 'We could not find a user with that email address.');
        }

        // Generate a random 64-character token
        $token = Str::random(64);

        // Upsert the token into the database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->usr_email],
            [
                'token' => Hash::make($token), // Hash it for security
                'created_at' => Carbon::now()
            ]
        );

        // Build the reset URL
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->usr_email]);

        // Send the email
        Mail::to($user->usr_email)->send(new ResetPasswordMail($user, $resetUrl));

        return back()->with('success', 'A password reset link has been sent to your email.');
    }

    // Show the Reset Password Form 
    public function showResetForm(Request $request, $token)
    {
        // Pass the token and the email from the URL down to the view
        return view('reset-password', [
            'token' => $token,
            'email' => $request->query('email')
        ]);
    }

    // Process the New Password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|confirmed', 
        ]);

        // Fetch the token record
        $resetRecord = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            return back()->with('error', 'Invalid or expired password reset token.');
        }

        // Check if token is older than 60 minutes
        if (Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->with('error', 'Your password reset link has expired. Please request a new one.');
        }

        // Update the user's password
        $user = User::where('usr_email', $request->email)->first();
        $user->update([
            'usr_password' => Hash::make($request->password)
        ]);

        // Delete the token so it cannot be used again
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Your password has been successfully reset! You may now log in.');
    }
}
