<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lecturer;
use App\Models\Subject;
use App\Models\QRSession;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LecturerDashboardController extends Controller
{
    /**
     * Display lecturer dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $lecturer = Lecturer::where('user_id', $user->id)
            ->with(['department', 'subjects.course', 'user'])
            ->first();
        
        if (!$lecturer) {
            // Create lecturer profile if not exists (auto-create)
            $lecturer = Lecturer::create([
                'user_id' => $user->id,
                'employee_id' => 'LEC' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'department_id' => 1, // Default department
                'status' => 'active'
            ]);
            
            // Reload with relations
            $lecturer = Lecturer::where('user_id', $user->id)
                ->with(['department', 'subjects.course', 'user'])
                ->first();
        }
        
        // Get lecturer's subjects
        $assignedSubjects = $lecturer->subjects ?? collect();
        
        // Get today's QR sessions
        $todaySessions = QRSession::where('lecturer_id', $lecturer->id)
            ->whereDate('session_date', Carbon::today())
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();
        
        // Get recent sessions (last 7 days)
        $recentSessions = QRSession::where('lecturer_id', $lecturer->id)
            ->whereDate('session_date', '>=', Carbon::now()->subDays(7))
            ->orderBy('session_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->take(5)
            ->get();
        
        // Get total sessions count
        $totalSessions = QRSession::where('lecturer_id', $lecturer->id)->count();
        
        // Statistics
        $totalSubjects = $assignedSubjects->count();
        $todaySessionsCount = $todaySessions->count();
        
        return view('lecturer.dashboard', compact(
            'lecturer',
            'todaySessions',
            'assignedSubjects',
            'recentSessions',
            'totalSessions',
            'todaySessionsCount',
            'totalSubjects'
        ));
    }
    
    /**
     * Display lecturer profile
     */
    public function profile()
    {
        $user = Auth::user();
        $lecturer = Lecturer::where('user_id', $user->id)
            ->with(['department', 'subjects.course', 'user'])
            ->firstOrFail();
            
        return view('lecturer.profile', compact('lecturer'));
    }
    
    /**
     * Update lecturer profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $lecturer = Lecturer::where('user_id', $user->id)->firstOrFail();
        
        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
            'qualification' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'employee_id' => 'required|string|max:50|unique:lecturers,employee_id,' . $lecturer->id,
        ]);
        
        $lecturer->update($validated);
        
        return redirect()->route('lecturer.profile')
            ->with('success', 'Profile updated successfully!');
    }
}