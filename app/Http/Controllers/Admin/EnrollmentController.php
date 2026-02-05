<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Course;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of enrollments
     */
    public function index()
    {
        $enrollments = Enrollment::with(['student.user', 'course', 'subject'])
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->paginate(20);
        
        return view('admin.enrollments.index', compact('enrollments'));
    }

    /**
     * Show the form for creating a new enrollment
     */
    public function create()
{
     $students = Student::with('user')->get(['id', 'user_id', 'student_id']);
    $courses = Course::with('department')->get(['id', 'name', 'department_id']);
    
    // FIXED: Remove ->active() scope
    $subjects = Subject::get(['id', 'name', 'code']); // CHANGED from Subject::active()->get()
    
    return view('admin.enrollments.create', compact('students', 'courses', 'subjects'));
}

    /**
     * Store a newly created enrollment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'subject_id' => 'required|exists:subjects,id',
            'academic_year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'semester' => 'required|integer|in:1,2,3,4,5,6,7,8',
            'status' => 'required|in:enrolled,completed,dropped,failed',
            'grade' => 'nullable|numeric|min:0|max:4'
        ]);

        // Check if student is already enrolled in this subject for this academic year
        $existingEnrollment = Enrollment::where('student_id', $validated['student_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('academic_year', $validated['academic_year'])
            ->first();
        
        if ($existingEnrollment) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Student is already enrolled in this subject for the selected academic year.');
        }

        // Check if subject belongs to the selected course
        $subjectInCourse = DB::table('course_subject')
            ->where('course_id', $validated['course_id'])
            ->where('subject_id', $validated['subject_id'])
            ->exists();
        
        if (!$subjectInCourse) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Selected subject is not part of the selected course.');
        }

        // Check if student is in the same department as course
        $student = Student::find($validated['student_id']);
        $course = Course::find($validated['course_id']);
        
        if ($student->department_id !== $course->department_id) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Student department does not match course department.');
        }

       

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Student enrolled in subject successfully!');
    }

    /**
     * Display the specified enrollment
     */
    public function show(Enrollment $enrollment)
    {
        $enrollment->load(['student.user', 'course.department', 'subject']);
        
        return view('admin.enrollments.show', compact('enrollment'));
    }

    /**
     * Show the form for editing an enrollment
     */
    public function edit(Enrollment $enrollment)
    {
        $students = Student::with('user')->get(['id', 'user_id', 'student_id']);
        $courses = Course::with('department')->get(['id', 'name', 'department_id']);
        $subjects = Subject::active()->get(['id', 'name', 'code']);
        
        return view('admin.enrollments.edit', compact('enrollment', 'students', 'courses', 'subjects'));
    }

    /**
     * Update the specified enrollment
     */
    public function update(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'status' => 'required|in:enrolled,completed,dropped,failed',
            'grade' => 'nullable|numeric|min:0|max:4',
            'semester' => 'required|integer|in:1,2,3,4,5,6,7,8'
        ]);

        $enrollment->update($validated);

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment updated successfully!');
    }

    /**
     * Remove the specified enrollment
     */
    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();
        
        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment deleted successfully!');
    }

    /**
 * Get subjects by course for AJAX
 */
public function getSubjectsByCourse($courseId)
{
    try {
        // FIXED: Remove the ->active() scope since we don't have is_active column
        $subjects = Subject::whereHas('courses', function($query) use ($courseId) {
            $query->where('courses.id', $courseId);
        })->get(['id', 'name', 'code']); // REMOVED: ->active()
        
        return response()->json($subjects);
    } catch (\Exception $e) {
        \Log::error('Error loading subjects: ' . $e->getMessage());
        return response()->json([
            'error' => 'Failed to load subjects',
            'message' => $e->getMessage()
        ], 500);
    }
}
    /**
     * Get students by course for AJAX
     */
    public function getStudentsByCourse($courseId)
    {
        $students = Student::where('course_id', $courseId)
            ->with('user')
            ->get(['id', 'user_id', 'student_id'])
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->user->name,
                    'student_id' => $student->student_id
                ];
            });
        
        return response()->json($students);
    }

    /**
     * Bulk enrollment
     */
    public function bulkEnroll(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'subject_id' => 'required|exists:subjects,id',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'academic_year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'semester' => 'required|integer|in:1,2,3,4,5,6,7,8'
        ]);

        $enrolledCount = 0;
        $alreadyEnrolled = 0;

        foreach ($validated['student_ids'] as $studentId) {
            // Check if already enrolled
            $existing = Enrollment::where('student_id', $studentId)
                ->where('subject_id', $validated['subject_id'])
                ->where('academic_year', $validated['academic_year'])
                ->exists();
            
            if (!$existing) {
                Enrollment::create([
                    'student_id' => $studentId,
                    'course_id' => $validated['course_id'],
                    'subject_id' => $validated['subject_id'],
                    'academic_year' => $validated['academic_year'],
                    'semester' => $validated['semester'],
                    'status' => 'enrolled',
                    'enrollment_date' => now()
                ]);
                $enrolledCount++;
            } else {
                $alreadyEnrolled++;
            }
        }

        $message = "Bulk enrollment completed. ";
        $message .= "Successfully enrolled: {$enrolledCount} students. ";
        if ($alreadyEnrolled > 0) {
            $message .= "Already enrolled: {$alreadyEnrolled} students.";
        }

        return redirect()->route('admin.enrollments.index')
            ->with('success', $message);
    }
}