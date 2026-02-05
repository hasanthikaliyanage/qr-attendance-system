<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ChangePasswordController extends Controller
{
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
            'password' => ['required', 'confirmed', 'min:8'],
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