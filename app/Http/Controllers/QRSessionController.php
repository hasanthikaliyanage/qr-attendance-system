<?php

namespace App\Http\Controllers;

use App\Models\QRSession;
use App\Models\Department;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode; 


class QRSessionController extends Controller
{
    /**
     * ==================================================
     * ADMIN METHODS
     * ==================================================
     */
    /**
 * Handle admincreate route for backward compatibility
 */
public function admincreate()
{
    // Call the same create method
    return $this->create();
}
    /**
     * Display a listing of QR sessions (Admin)
     */
    

    /**
     * ==================================================
     * LECTURER METHODS
     * ==================================================
     */
    
    /**
     * Display a listing of QR sessions (Lecturer)
     */
    public function lecturerIndex()
    {
        $user = Auth::user();
        $lecturer = Lecturer::where('user_id', $user->id)->first();

        if (!$lecturer) {
            return redirect()->back()->with('error', 'Lecturer profile not found');
        }

        $sessions = QRSession::where('lecturer_id', $lecturer->id)
            ->with(['department', 'course', 'subject', 'attendances'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('lecturer.qr_sessions.index', compact('sessions'));
    }

    /**
     * Show the form for creating a new QR session (Lecturer)
     */
    public function create()
    {
        $user = Auth::user();
        $lecturer = Lecturer::where('user_id', $user->id)->first();

        if (!$lecturer) {
            return redirect()->back()->with('error', 'Lecturer profile not found');
        }

        // Get lecturer's subjects only
        $subjects = $lecturer->subjects()->with('course.department')->get();
        
        // Get unique departments from lecturer's subjects
        $departments = Department::whereHas('courses.subjects', function($query) use ($lecturer) {
            $query->whereHas('lecturers', function($q) use ($lecturer) {
                $q->where('lecturer_id', $lecturer->id);
            });
        })->get();

        return view('lecturer.qr_sessions.create', compact('departments', 'subjects', 'lecturer'));
    }

    /**
     * Store a newly created QR session
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'session_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'course_id' => 'required|exists:courses,id',
            'subject_id' => 'required|exists:subjects,id',
            'session_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'duration_minutes' => 'required|integer|min:5|max:180',
        ]);

        try {
            $user = Auth::user();
            $lecturer = Lecturer::where('user_id', $user->id)->firstOrFail();

            // Generate unique QR token
            $qrToken = Str::random(32);

            $qrSession = QRSession::create([
                'lecturer_id' => $lecturer->id,
                'session_name' => $validated['session_name'],
                'description' => $validated['description'],
                'department_id' => $validated['department_id'],
                'course_id' => $validated['course_id'],
                'subject_id' => $validated['subject_id'],
                'created_by' => $user->id,
                'session_date' => $validated['session_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'duration_minutes' => $validated['duration_minutes'],
                'is_active' => false, // Default inactive - lecturer can activate later
                'qr_token' => $qrToken,
            ]);

            Log::info('QR Session created', [
                'session_id' => $qrSession->id,
                'lecturer_id' => $lecturer->id,
                'qr_token' => $qrSession->qr_token
            ]);

            return redirect()->route('lecturer.qr_sessions.show', $qrSession->id)
                ->with('success', 'QR Session created successfully!');

        } catch (\Exception $e) {
            Log::error('QR Session creation failed', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to create QR session: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified QR session with attendance details
     */
    public function show($id)
    {
        $session = QRSession::with([
            'department', 
            'course', 
            'subject', 
            'lecturer.user', 
            'attendances.student.user'
        ])->findOrFail($id);

        // Check authorization
        $user = Auth::user();
        if ($user->isLecturer()) {
            $lecturer = Lecturer::where('user_id', $user->id)->first();
            if ($session->lecturer_id !== $lecturer->id) {
                abort(403, 'Unauthorized access');
            }
        }

        // Get enrolled students for this course/subject
        $students = $session->course->students()
            ->with('user')
            ->get();
        
        // Get attendance for each student
        $attendanceData = [];
        foreach ($students as $student) {
            $attendance = Attendance::where('qr_session_id', $session->id)
                ->where('student_id', $student->id)
                ->first();
                
            $attendanceData[] = [
                'student' => $student,
                'attendance' => $attendance,
                'status' => $attendance ? $attendance->status : 'Absent'
            ];
        }

        return view('lecturer.qr_sessions.show', compact('session', 'attendanceData'));
    }

    /**
 * Generate QR Code display page
 */
public function generateQr($id)
{
    $session = QRSession::with(['department', 'course', 'subject', 'lecturer.user'])
        ->findOrFail($id);
    
    // Check authorization
    $user = Auth::user();
    if ($user->isLecturer()) {
        $lecturer = Lecturer::where('user_id', $user->id)->first();
        if ($session->lecturer_id !== $lecturer->id) {
            abort(403, 'Unauthorized access');
        }
    }
    
    // Generate the QR code data (the URL students will scan)
    $attendanceUrl = route('student.scan_by_token', ['token' => $session->qr_token]);
    
    // ⭐ IMPORTANT: Generate the QR code as SVG (not PNG) ⭐
    $qrCodeSvg = QrCode::format('svg')
        ->size(300) // QR code size
        ->margin(2) // White border around QR
        ->errorCorrection('H') // High error correction
        ->generate($attendanceUrl);
    
    // Convert the SVG string to a format we can use in HTML
    $qrCodeDataUri = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);
    
    return view('lecturer.qr_sessions.qr_code', compact('session', 'qrCodeDataUri'));
}

    /**
     * Toggle QR session status (active/inactive)
     */
    public function toggleStatus($id)
    {
        try {
            $session = QRSession::findOrFail($id);
            
            // Check authorization
            $user = Auth::user();
            if ($user->isLecturer()) {
                $lecturer = Lecturer::where('user_id', $user->id)->first();
                if ($session->lecturer_id !== $lecturer->id) {
                    abort(403, 'Unauthorized access');
                }
            }

            $session->is_active = !$session->is_active;
            $session->save();

            Log::info('QR Session status toggled', [
                'session_id' => $session->id,
                'new_status' => $session->is_active ? 'active' : 'inactive'
            ]);

            return redirect()->back()
                ->with('success', 'Session status updated successfully');

        } catch (\Exception $e) {
            Log::error('Failed to toggle session status', [
                'error' => $e->getMessage(),
                'session_id' => $id
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    /**
     * Delete QR session
     */
    public function destroy($id)
    {
        try {
            $session = QRSession::findOrFail($id);
            
            // Check authorization
            $user = Auth::user();
            if ($user->isLecturer()) {
                $lecturer = Lecturer::where('user_id', $user->id)->first();
                if ($session->lecturer_id !== $lecturer->id) {
                    abort(403, 'Unauthorized access');
                }
            }

            $sessionName = $session->session_name;
            $session->delete();

            Log::info('QR Session deleted', [
                'session_id' => $id,
                'session_name' => $sessionName
            ]);

            return redirect()->route('lecturer.qr_sessions.index')
                ->with('success', 'QR Session "' . $sessionName . '" deleted successfully');

        } catch (\Exception $e) {
            Log::error('Failed to delete session', [
                'error' => $e->getMessage(),
                'session_id' => $id
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to delete session: ' . $e->getMessage());
        }
    }

    /**
     * ==================================================
     * STUDENT METHODS
     * ==================================================
     */
    
    

/**
 * Student QR Sessions Index (with AJAX support)
 */
/**
 * Display QR sessions for student
 */

/**
 * Display QR sessions for student
 */
public function studentIndex()
{
    $user = Auth::user();
    $student = $user->student;
    
    // Check if student exists
    if (!$student) {
        return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
    }
    
    // ✅ **නිවැරදි කළ query: 'status' වෙනුවට 'is_active' use කරන්න**
    $activeSessions = QRSession::where('is_active', true) // 'status' -> 'is_active'
        ->where('course_id', $student->course_id)
        ->whereDate('session_date', Carbon::today())
        ->whereTime('end_time', '>=', Carbon::now()->format('H:i:s'))
        ->with(['subject', 'lecturer.user', 'department', 'course'])
        ->get();

    // Get upcoming sessions
    $upcomingSessions = QRSession::where('is_active', true) // ✅ 'status' -> 'is_active'
        ->where('course_id', $student->course_id)
        ->whereDate('session_date', '>=', Carbon::today())
        ->where(function($query) {
            $query->whereDate('session_date', '>', Carbon::today())
                ->orWhereTime('start_time', '>', Carbon::now()->format('H:i:s'));
        })
        ->with(['subject', 'lecturer.user'])
        ->orderBy('session_date')
        ->orderBy('start_time')
        ->get();

    // Get recent attendance for the student
    $attendances = Attendance::where('student_id', $student->id)
        ->with(['qrSession.subject'])
        ->orderBy('marked_at', 'desc')
        ->paginate(10);

    return view('student.qr_sessions.index', compact(
        'activeSessions', 
        'upcomingSessions', 
        'attendances'
    ));
}
    /**
     * Student scanner page
     */
    public function studentScanner()
    {
        return view('student.qr_scanner');
    }

    /**
     * Scan QR code and mark attendance (Student)
     */
    public function scanQR(Request $request)
    {
        $validated = $request->validate([
            'qr_code' => 'required|string', // This will be the qr_token
        ]);

        try {
            $user = Auth::user();
            $student = Student::where('user_id', $user->id)->firstOrFail();

            // Find QR session using qr_token
            $session = QRSession::where('qr_token', $validated['qr_code'])->first();

            if (!$session) {
                Log::warning('Invalid QR code scanned', [
                    'qr_code' => substr($validated['qr_code'], 0, 10) . '...',
                    'student_id' => $student->id
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code'
                ], 404);
            }

            // Check if session is active
            if (!$session->is_active) {
                Log::info('Attempt to scan inactive session', [
                    'session_id' => $session->id,
                    'student_id' => $student->id
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'This session is not active. Please wait for lecturer to activate it.'
                ], 400);
            }

            // Check if session date is today
            if (!Carbon::parse($session->session_date)->isToday()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This session is not for today'
                ], 400);
            }

            // Check if already marked
            $existingAttendance = Attendance::where('qr_session_id', $session->id)
                ->where('student_id', $student->id)
                ->first();

            if ($existingAttendance) {
                Log::info('Student already marked attendance', [
                    'session_id' => $session->id,
                    'student_id' => $student->id,
                    'existing_status' => $existingAttendance->status
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'You have already marked attendance for this session'
                ], 400);
            }

            // Determine status (present or late)
            $now = Carbon::now();
            $sessionStart = Carbon::parse($session->session_date . ' ' . $session->start_time);
            $lateThreshold = $sessionStart->copy()->addMinutes(15); // 15 minutes grace period
            
            $status = $now->greaterThan($lateThreshold) ? 'late' : 'present';

            // Mark attendance
            $attendance = Attendance::create([
                'qr_session_id' => $session->id,
                'student_id' => $student->id,
                'subject_id' => $session->subject_id,
                'marked_at' => now(),
                'status' => $status,
                'device_info' => $request->userAgent(),
                'ip_address' => $request->ip(),
            ]);

            // Update session scanned status
            if (!$session->is_scanned) {
                $session->is_scanned = true;
                $session->scanned_at = now();
                $session->save();
            }

            Log::info('Attendance marked successfully', [
                'student_id' => $student->id,
                'session_id' => $session->id,
                'attendance_id' => $attendance->id,
                'status' => $status,
                'marked_at' => $attendance->marked_at
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Attendance marked successfully!',
                'data' => [
                    'status' => ucfirst($status),
                    'marked_at' => $attendance->marked_at->format('Y-m-d H:i:s'),
                    'session_name' => $session->session_name,
                    'subject' => $session->subject->name,
                    'course' => $session->course->name,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Attendance marking failed', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark attendance. Please try again or contact support.'
            ], 500);
        }
    }

    /**
     * ==================================================
     * AJAX METHODS (for dynamic dropdowns)
     * ==================================================
     */
    
    /**
 * Get courses by department (AJAX)
 */
public function getCoursesByDepartment($departmentId)
{
    try {
        \Log::info('AJAX Request - getCoursesByDepartment', [
            'department_id' => $departmentId,
            'user_id' => Auth::id()
        ]);

        // REMOVE: ->where('is_active', true) 
        // because is_active column doesn't exist in your courses table
        $courses = Course::where('department_id', $departmentId)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        \Log::info('Courses fetched successfully', [
            'department_id' => $departmentId,
            'count' => $courses->count()
        ]);

        return response()->json($courses);
        
    } catch (\Exception $e) {
        \Log::error('Failed to fetch courses', [
            'department_id' => $departmentId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

   /**
 * Get subjects by course (AJAX) - Only for logged-in lecturer
 */
/**
 * Get subjects by course (AJAX) - Only for logged-in lecturer
 */
public function getSubjectsByCourse($courseId)
{
    try {
        \Log::info('AJAX Request - getSubjectsByCourse', [
            'course_id' => $courseId,
            'user_id' => Auth::id()
        ]);
        
        $user = Auth::user();
        $lecturer = Lecturer::where('user_id', $user->id)->first();

        if (!$lecturer) {
            \Log::warning('Lecturer not found for user', ['user_id' => $user->id]);
            return response()->json([]); // Empty array return කරන්න
        }

        // Check if course_id column exists in subjects table
        // If not, get all subjects assigned to lecturer
        $subjects = Subject::whereHas('lecturers', function($query) use ($lecturer) {
                $query->where('lecturer_id', $lecturer->id);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        \Log::info('Subjects fetched', [
            'course_id' => $courseId,
            'lecturer_id' => $lecturer->id,
            'count' => $subjects->count()
        ]);

        return response()->json($subjects);
        
    } catch (\Exception $e) {
        \Log::error('Failed to fetch subjects', [
            'course_id' => $courseId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        // Return empty array instead of error object
        return response()->json([]);
    }
}
    /**
     * Get lecturers by subject (AJAX) - For admin
     */
    public function getLecturersBySubject($subjectId)
    {
        try {
            $lecturers = Lecturer::whereHas('subjects', function($query) use ($subjectId) {
                $query->where('subject_id', $subjectId);
            })->with('user')->get();

            $result = $lecturers->map(function($lecturer) {
                return [
                    'id' => $lecturer->id,
                    'name' => $lecturer->user->name,
                    'staff_id' => $lecturer->staff_id,
                ];
            });

            return response()->json($result);
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch lecturers', [
                'subject_id' => $subjectId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([], 500);
        }
    }

    /**
     * ==================================================
     * REPORTS & UTILITIES
     * ==================================================
     */
    
    /**
     * Get attendance summary (AJAX)
     */
    public function getAttendanceSummary(Request $request)
    {
        try {
            $sessionId = $request->input('session_id');
            
            if (!$sessionId) {
                return response()->json(['error' => 'Session ID required'], 400);
            }

            $session = QRSession::with(['attendances', 'course'])->findOrFail($sessionId);
            
            $totalStudents = $session->course->students()->count();
            $presentCount = $session->attendances()->where('status', 'present')->count();
            $lateCount = $session->attendances()->where('status', 'late')->count();
            $absentCount = $totalStudents - $presentCount - $lateCount;
            
            $summary = [
                'total_students' => $totalStudents,
                'present' => $presentCount,
                'late' => $lateCount,
                'absent' => $absentCount,
                'percentage' => $totalStudents > 0 
                    ? round((($presentCount + $lateCount) / $totalStudents) * 100, 2)
                    : 0
            ];

            return response()->json($summary);
            
        } catch (\Exception $e) {
            Log::error('Failed to get attendance summary', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Failed to load summary'], 500);
        }
    }

    /**
     * Download attendance report (Future Implementation)
     */
    public function downloadAttendanceReport($sessionId)
    {
        // TODO: Implement PDF/Excel export
        return redirect()->back()->with('info', 'Export feature coming soon!');
    }
}