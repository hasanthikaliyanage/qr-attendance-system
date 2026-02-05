<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    /**
     * Display student dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)
            ->with(['department', 'course', 'subjects'])
            ->first();
            
        if (!$student) {
return redirect()->route('student.profile.create')
            ->with('error', 'මුලින්ම ඔබේ Student Profile සම්පුර්ණ කරන්න.');        }
        
        // Get enrolled subjects
        $enrolledSubjects = $student->subjects;
        
    
        // Attendance will be added in Part 5
        $todayAttendance = collect();
        $recentAttendance = collect();
        
        // Default attendance statistics (will be real in Part 5)
        $totalAttendance = 0;
        $totalSessions = 0;
        $presentCount = 0;
        $absentCount = 0;
        $attendancePercentage = 0;
        
        // Create attendance summary array with all required keys
        $attendanceSummary = [
            'total' => $totalAttendance,
            'total_sessions' => $totalSessions,
            'present' => $presentCount,
            'absent' => $absentCount,
            'percentage' => $attendancePercentage
        ];
            
        return view('student.dashboard', compact(
            'student',
            'enrolledSubjects',
            'todayAttendance',
            'totalAttendance',
            'presentCount',
            'attendancePercentage',
            'attendanceSummary',
            'recentAttendance'
        ));
    }




     public function dashboard()
    {
        $student = Auth::user()->student;
        $enrolledSubjects = $student->subjects;
        
        // Calculate attendance summary
        $totalSessions = \App\Models\QRSession::whereHas('subject', function($query) use ($student) {
            $query->whereIn('id', $student->subjects->pluck('id'));
        })->count();
        
        $presentCount = \App\Models\Attendance::where('student_id', $student->id)
            ->where('status', 'present')
            ->count();
        
        $absentCount = $totalSessions - $presentCount;
        
        $percentage = $totalSessions > 0 ? round(($presentCount / $totalSessions) * 100, 1) : 0;
        
        $attendanceSummary = [
            'total_sessions' => $totalSessions,
            'present' => $presentCount,
            'absent' => $absentCount,
            'percentage' => $percentage
        ];
        
        return view('student.dashboard', compact('student', 'enrolledSubjects', 'attendanceSummary'));
    }
    
    
    /**
     * Display student profile
     */
    public function profile()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)
            ->with(['department', 'course', 'subjects', 'user'])
            ->firstOrFail();
            
        return view('student.profile', compact('student'));
    }
    // 🎯 NEW: QR Scanner method
    public function qrScanner()
    {
        return view('student.qr_scanner');
    }
    
    /**
     * Update student profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();
        
        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);
        
        $student->update($validated);
        
        return redirect()->route('student.profile')
            ->with('success', 'Profile updated successfully!');
    }
}