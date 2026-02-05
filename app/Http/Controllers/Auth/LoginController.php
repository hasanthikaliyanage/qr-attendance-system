<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Update last login timestamp
            $user = Auth::user();
            $user->last_login_at = now();
            $user->save();

            // Check if user must change password (for non-admin users)
            if ($user->must_change_password == 1 && $user->role !== 'admin') {
                return redirect()->route('password.change');
            }

            // Redirect based on user role
            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->role === 'lecturer') {
                return redirect()->intended(route('lecturer.dashboard'));
            } elseif ($user->role === 'student') {
                return redirect()->intended(route('student.dashboard'));
            }

            // Default redirect
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }

    /**
     * Display the change password form.
     */
    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    /**
     * Handle password change request.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = Auth::user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'The provided password does not match your current password.',
            ]);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->must_change_password = 0; // Reset the flag
        $user->save();

        // Redirect based on user role
        if ($user->role === 'student') {
            return redirect()->route('student.dashboard')
                ->with('success', 'Password changed successfully!');
        } elseif ($user->role === 'lecturer') {
            return redirect()->route('lecturer.dashboard')
                ->with('success', 'Password changed successfully!');
        } elseif ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('success', 'Password changed successfully!');
        }

        return redirect('/');
    }
}