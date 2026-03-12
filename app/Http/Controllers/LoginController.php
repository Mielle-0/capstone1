<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
}
