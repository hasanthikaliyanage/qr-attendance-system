<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\QRSession;
use App\Models\Student;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;

class AttendanceController extends Controller
{
    // Student scans QR via API
    public function markAttendance(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:qr_sessions,id',
            'token' => 'required|string'
        ]);

        $user = Auth::user();
        
        if (!$user || $user->role->name !== 'student') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $student = $user->student;
        $session = QRSession::findOrFail($request->session_id);

        // Validate token
        if ($session->qr_token !== $request->token) {
            return response()->json(['error' => 'Invalid QR code'], 400);
        }

        // Check if session is active
        if (!$session->is_active) {
            return response()->json(['error' => 'This session is not active'], 400);
        }

        // Check time window
        $now = Carbon::now('Asia/Colombo');
        $sessionDate = Carbon::parse($session->session_date);
        $startTime = Carbon::parse($session->session_date . ' ' . $session->start_time);
        $endTime = Carbon::parse($session->session_date . ' ' . $session->end_time);

        if (!$now->between($startTime, $endTime)) {
            return response()->json(['error' => 'Session is not active at this time'], 400);
        }

        // Check if global one-time and already scanned
        if ($session->is_global_one_time && $session->is_scanned) {
            return response()->json(['error' => 'This QR code has already been used'], 400);
        }

        // Check if student is enrolled in this subject
        $isEnrolled = Enrollment::where('student_id', $student->id)
            ->where('subject_id', $session->subject_id)
            ->exists();

        if (!$isEnrolled) {
            return response()->json(['error' => 'You are not enrolled in this subject'], 400);
        }

        // Check if already marked attendance
        $existing = Attendance::where('qr_session_id', $session->id)
            ->where('student_id', $student->id)
            ->first();

        if ($existing) {
            return response()->json(['error' => 'You have already marked attendance for this session'], 400);
        }

        // Determine status (present or late)
        $lateThreshold = $startTime->copy()->addMinutes(15);
        $status = $now->lte($lateThreshold) ? 'present' : 'late';

        // Mark attendance
        Attendance::create([
            'qr_session_id' => $session->id,
            'student_id' => $student->id,
            'status' => $status,
            'marked_at' => $now,
            'ip_address' => $request->ip(),
            'device_info' => $request->header('User-Agent')
        ]);

        // Mark session as scanned if global one-time
        if ($session->is_global_one_time) {
            $session->update([
                'is_scanned' => true,
                'scanned_at' => $now
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Attendance marked successfully!',
            'status' => $status
        ]);
    }

    // View attendance report
    public function viewReport($sessionId)
    {
        $session = QRSession::with(['attendances.student', 'department', 'course', 'subject'])
            ->findOrFail($sessionId);

        return view('admin.qr_sessions.attendance_report', compact('session'));
    }

    // Export PDF
    public function exportPDF($sessionId)
    {
        $session = QRSession::with(['attendances.student', 'department', 'course', 'subject', 'lecturer'])
            ->findOrFail($sessionId);

        $pdf = Pdf::loadView('admin.qr_sessions.attendance_pdf', compact('session'));
        
        return $pdf->download('attendance_' . $session->session_name . '_' . date('Y-m-d') . '.pdf');
    }

    // Export Excel
    public function exportExcel($sessionId)
    {
        $session = QRSession::findOrFail($sessionId);
        
        return Excel::download(
            new AttendanceExport($sessionId), 
            'attendance_' . $session->session_name . '_' . date('Y-m-d') . '.xlsx'
        );
    }
}