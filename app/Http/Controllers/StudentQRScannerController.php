<?php

namespace App\Http\Controllers;

use App\Models\QRSession;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StudentQRScannerController extends Controller
{
    // StudentQRScannerController.php - scanner() method එක update කරන්න
    public function scanner()
    {
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }
        
        // Get active sessions for today
        $activeSessions = QRSession::where('is_active', true)
            ->where('course_id', $student->course_id)
            ->whereDate('session_date', Carbon::today())
            ->whereTime('end_time', '>=', Carbon::now()->format('H:i:s'))
            ->with(['subject', 'lecturer.user', 'department', 'course'])
            ->get();
        
        // Return the CORRECT view
        return view('qr_scanner.webcam', compact('activeSessions'));
    }
    
    /**
     * Process QR Code Scan from Webcam
     */
    public function processScan(Request $request)
    {
        Log::info('=== QR SCAN PROCESS STARTED ===', [
            'user_id' => Auth::id(),
            'qr_data' => $request->qr_data,
            'ip' => $request->ip()
        ]);
        
        $request->validate([
            'qr_data' => 'required|string',
        ]);
        
        try {
            $user = Auth::user();
            Log::info('User authenticated:', ['user_id' => $user->id, 'email' => $user->email]);
            
            $student = $user->student;
            Log::info('Student check:', ['has_student' => !empty($student), 'student_id' => $student->id ?? 'N/A']);
            
            if (!$student) {
                Log::error('Student profile not found for user:', ['user_id' => $user->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Student profile not found'
                ], 404);
            }
            
            // QR data එකෙන් session token එක extract කරන්න
            $qrData = $request->qr_data;
            Log::info('QR Data received:', ['qr_data' => substr($qrData, 0, 50) . (strlen($qrData) > 50 ? '...' : '')]);
            // URL එකක් නම් token එක extract කරන්න
            $token = $this->extractTokenFromQR($qrData);
            Log::info('Token extracted:', ['token' => $token]);
            if (!$token) {
                Log::warning('Invalid QR code format:', ['qr_data' => $qrData]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code format'
                ], 400);
            }
            
            // Find QR session using token
            $session = QRSession::with(['subject', 'lecturer.user'])
                ->where('qr_token', $token)
                ->first();
            
            Log::info('Session search:', [
                'token' => $token,
                'found' => !empty($session),
                'session_id' => $session->id ?? 'N/A'
            ]);
            
            if (!$session) {
                Log::warning('Invalid QR code scanned via webcam', [
                    'token' => $token,
                    'student_id' => $student->id
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR session'
                ], 404);
            }
            
            Log::info('Session details:', [
                'session_id' => $session->id,
                'session_name' => $session->session_name,
                'subject' => $session->subject->name ?? 'N/A',
                'is_active' => $session->is_active,
                'session_date' => $session->session_date,
                'start_time' => $session->start_time,
                'course_id' => $session->course_id,
                'student_course_id' => $student->course_id
            ]);
            
            // Check if session is active
            if (!$session->is_active) {
                Log::warning('Session not active', ['session_id' => $session->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'This session is not active'
                ], 400);
            }
            
            // ===========================================
            // FIXED: Check if session date is valid
            // ===========================================
            try {
                // Extract date part from session_date (it might be datetime like "2025-12-11 00:00:00")
                $sessionDateStr = $session->session_date;
                
                // If it contains space, it's datetime - get only date part
                if (strpos($sessionDateStr, ' ') !== false) {
                    $sessionDateStr = explode(' ', $sessionDateStr)[0];
                }
                
                $sessionDate = Carbon::parse($sessionDateStr);
                $today = Carbon::today();
                
                Log::info('Date comparison:', [
                    'original_date' => $session->session_date,
                    'extracted_date' => $sessionDateStr,
                    'session_date_parsed' => $sessionDate->format('Y-m-d'),
                    'today' => $today->format('Y-m-d')
                ]);
                
                if (!$sessionDate->isSameDay($today)) {
                    Log::warning('Session not for today', [
                        'session_date' => $sessionDate->format('Y-m-d'),
                        'today' => $today->format('Y-m-d')
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'This session is not for today'
                    ], 400);
                }
            } catch (\Exception $dateError) {
                Log::error('Date parsing error, but continuing:', [
                    'session_date' => $session->session_date,
                    'error' => $dateError->getMessage()
                ]);
                // Continue processing - don't block attendance due to date parsing error
            }
            
            // Check if student belongs to the session's course
            if ($session->course_id != $student->course_id) {
                Log::warning('Course mismatch', [
                    'session_course' => $session->course_id,
                    'student_course' => $student->course_id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'You are not enrolled in this course'
                ], 403);
            }
            
            // Check if already marked attendance
            $existingAttendance = Attendance::where('qr_session_id', $session->id)
                ->where('student_id', $student->id)
                ->first();
                
            if ($existingAttendance) {
                Log::warning('Already marked attendance', [
                    'attendance_id' => $existingAttendance->id,
                    'status' => $existingAttendance->status,
                    'marked_at' => $existingAttendance->marked_at
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'You have already marked attendance for this session'
                ], 400);
            }
            
            // ===========================================
            // FIXED: Determine status (present or late)
            // ===========================================
            $now = Carbon::now();
            
            // Create session start time properly
            try {
                // Extract date part
                $datePart = $session->session_date;
                if (strpos($datePart, ' ') !== false) {
                    $datePart = explode(' ', $datePart)[0];
                }
                
                $sessionStart = Carbon::parse($datePart . ' ' . $session->start_time);
                
                // If parsing fails, try alternative format
                if (!$sessionStart) {
                    $sessionStart = Carbon::createFromFormat('Y-m-d H:i:s', $datePart . ' ' . $session->start_time);
                }
            } catch (\Exception $timeError) {
                Log::error('Failed to parse session start time, using current time:', [
                    'error' => $timeError->getMessage(),
                    'session_date' => $session->session_date,
                    'start_time' => $session->start_time
                ]);
                // Default to current time minus 1 hour
                $sessionStart = $now->copy()->subHour();
            }
            
            $lateThreshold = $sessionStart->copy()->addMinutes(15);
            
            $status = $now->greaterThan($lateThreshold) ? 'late' : 'present';
            
            Log::info('Attendance timing:', [
                'now' => $now->format('Y-m-d H:i:s'),
                'session_start' => $sessionStart->format('Y-m-d H:i:s'),
                'late_threshold' => $lateThreshold->format('Y-m-d H:i:s'),
                'status' => $status
            ]);
            
            // Create attendance record
            $attendance = Attendance::create([
                'qr_session_id' => $session->id,
                'student_id' => $student->id,
                //'subject_id' => $session->subject_id,
                'marked_at' => now(),
                'status' => $status,
                'device_info' => $request->header('User-Agent') ?? 'Unknown',
                'ip_address' => $request->ip(),
                //'scan_method' => 'webcam',
            ]);
            
            Log::info('Attendance created successfully', [
                'attendance_id' => $attendance->id,
                'student_id' => $student->id,
                'session_id' => $session->id,
                'status' => $status,
                'marked_at' => $attendance->marked_at
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Attendance marked successfully!',
                'data' => [
                    'session_name' => $session->session_name,
                    'subject' => $session->subject->name ?? 'N/A',
                    'lecturer' => $session->lecturer->user->name ?? 'N/A',
                    'status' => $status,
                    'marked_at' => $attendance->marked_at->format('h:i A'),
                    'session_date' => Carbon::parse($session->session_date)->format('Y-m-d'),
                    'session_time' => $session->start_time
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('QR SCAN PROCESSING FAILED', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'qr_data' => $request->qr_data ?? 'N/A'
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process QR scan: ' . $e->getMessage(),
                'debug_info' => [
                    'error' => $e->getMessage(),
                    'user_id' => Auth::id(),
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ]
            ], 500);
        }
    }
    
    /**
     * Extract token from QR data (URL or raw token)
     */
    private function extractTokenFromQR($qrData)
    {
        // Check if it's a URL
        if (filter_var($qrData, FILTER_VALIDATE_URL)) {
            // Extract token from URL like /student/scan/{token}
            $path = parse_url($qrData, PHP_URL_PATH);
            $parts = explode('/', $path);
            $token = end($parts);
            
            // Check if token is valid UUID format
            if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $token)) {
                return $token;
            }
        }
        
        // If it's already a token (UUID format)
        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $qrData)) {
            return $qrData;
        }
        
        // Check if it's a base64 encoded URL
        if (base64_decode($qrData, true) !== false) {
            $decoded = base64_decode($qrData);
            if (filter_var($decoded, FILTER_VALIDATE_URL)) {
                $path = parse_url($decoded, PHP_URL_PATH);
                $parts = explode('/', $path);
                $token = end($parts);
                
                if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $token)) {
                    return $token;
                }
            }
        }
        
        return $qrData;
    }
    
    /**
     * Get attendance statistics
     */
    public function getStats()
    {
        try {
            $user = Auth::user();
            $student = $user->student;
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student profile not found'
                ], 404);
            }
            
            // Get total sessions for student's course
            $totalSessions = QRSession::where('course_id', $student->course_id)
                ->where('is_active', true)
                ->whereDate('session_date', '<=', Carbon::today())
                ->count();
                
            // Get student's attendance counts
            $presentCount = Attendance::where('student_id', $student->id)
                ->where('status', 'present')
                ->count();
                
            $lateCount = Attendance::where('student_id', $student->id)
                ->where('status', 'late')
                ->count();
                
            // Calculate attendance rate
            $attendanceRate = 0;
            if ($totalSessions > 0) {
                $attendanceRate = round((($presentCount + $lateCount) / $totalSessions) * 100, 1);
            }
            
            // Get today's attendance
            $todayAttendance = Attendance::where('student_id', $student->id)
                ->whereDate('marked_at', Carbon::today())
                ->with('qrSession.subject')
                ->get()
                ->map(function($att) {
                    return [
                        'session' => optional($att->qrSession)->session_name ?? 'N/A',
                        'subject' => optional(optional($att->qrSession)->subject)->name ?? 'N/A',
                        'status' => $att->status,
                        'time' => $att->marked_at->format('h:i A')
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_sessions' => $totalSessions,
                    'present' => $presentCount,
                    'late' => $lateCount,
                    'attendance_rate' => $attendanceRate,
                    'today_attendance' => $todayAttendance
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to get attendance stats', [
                'error' => $e->getMessage(),
                'student_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Show Attendance Report
     */
    public function report(Request $request)
    {
        try {
            $user = Auth::user();
            $student = $user->student;
            
            if (!$student) {
                abort(404, 'Student profile not found');
            }
            
            // Get attendance records with eager loading
            $attendances = Attendance::with([
                'qrSession.subject', 
                'qrSession.lecturer.user'
            ])
            ->where('student_id', $student->id)
            ->orderBy('marked_at', 'desc')
            ->paginate(15);
            
            // Calculate statistics
            $totalSessions = QRSession::where('course_id', $student->course_id)
                ->where('is_active', true)
                ->whereDate('session_date', '<=', Carbon::today())
                ->count();
                
            $presentCount = Attendance::where('student_id', $student->id)
                ->where('status', 'present')
                ->count();
                
            $lateCount = Attendance::where('student_id', $student->id)
                ->where('status', 'late')
                ->count();
                
            // Calculate attendance percentage
            $attendanceRate = 0;
            if ($totalSessions > 0) {
                $attendanceRate = round((($presentCount + $lateCount) / $totalSessions) * 100, 1);
            }
            
            // Get recent attendance (last 7 days)
            $recentAttendance = Attendance::with('qrSession.subject')
                ->where('student_id', $student->id)
                ->whereDate('marked_at', '>=', Carbon::now()->subDays(7))
                ->orderBy('marked_at', 'desc')
                ->get();
            
            return view('student.attendance.report', compact(
                'attendances',
                'totalSessions',
                'presentCount',
                'lateCount',
                'attendanceRate',
                'recentAttendance'
            ));
            
        } catch (\Exception $e) {
            Log::error('Failed to load attendance report', [
                'error' => $e->getMessage(),
                'student_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('student.dashboard')->with('error', 'Failed to load attendance report: ' . $e->getMessage());
        }
    }
    
    /**
     * Get attendance by date range
     */
    public function getAttendanceByDate(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        
        try {
            $user = Auth::user();
            $student = $user->student;
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student profile not found'
                ], 404);
            }
            
            $attendances = Attendance::with(['qrSession.subject', 'qrSession.lecturer.user'])
                ->where('student_id', $student->id)
                ->whereBetween('marked_at', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ])
                ->orderBy('marked_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $attendances->map(function($att) {
                    return [
                        'id' => $att->id,
                        'session_name' => optional($att->qrSession)->session_name ?? 'N/A',
                        'subject' => optional(optional($att->qrSession)->subject)->name ?? 'N/A',
                        'lecturer' => optional(optional(optional($att->qrSession)->lecturer)->user)->name ?? 'N/A',
                        'status' => $att->status,
                        'marked_at' => $att->marked_at->format('Y-m-d H:i:s'),
                        'formatted_time' => $att->marked_at->format('h:i A'),
                        'formatted_date' => $att->marked_at->format('Y-m-d'),
                        'device_info' => $att->device_info,
                        'scan_method' => $att->scan_method
                    ];
                }),
                'summary' => [
                    'total' => $attendances->count(),
                    'present' => $attendances->where('status', 'present')->count(),
                    'late' => $attendances->where('status', 'late')->count()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to get attendance by date', [
                'error' => $e->getMessage(),
                'student_id' => Auth::id(),
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch attendance data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Test method for debugging
     */
    public function testScan(Request $request)
    {
        try {
            $token = $request->token ?? 'b32127a5-e2fb-4767-9a91-ac2bad86278b';
            
            $session = QRSession::where('qr_token', $token)->first();
            
            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found',
                    'token' => $token
                ], 404);
            }
            
            // Check session_date format
            $sessionDate = $session->session_date;
            $hasTime = strpos($sessionDate, ' ') !== false;
            
            return response()->json([
                'success' => true,
                'session' => [
                    'id' => $session->id,
                    'name' => $session->session_name,
                    'session_date' => $sessionDate,
                    'has_time_component' => $hasTime,
                    'date_only' => $hasTime ? explode(' ', $sessionDate)[0] : $sessionDate,
                    'start_time' => $session->start_time,
                    'is_active' => $session->is_active,
                    'course_id' => $session->course_id
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage()
            ], 500);
        }
    }
}