<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Department;
use App\Models\Course;
use App\Models\Subject;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Requests\StoreLecturerRequest;
use App\Http\Requests\UpdateLecturerRequest;
use App\Mail\CredentialsMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function dashboard()
    {
        $totalStudents = Student::count();
        $totalLecturers = Lecturer::count();
        $totalDepartments = Department::count();
        $totalCourses = Course::count();
        
        return view('dashboard', compact('totalStudents', 'totalLecturers', 'totalDepartments', 'totalCourses'));
    }

    // ==================== STUDENT MANAGEMENT ====================

    /**
     * Display list of students with search and filters
     */
    public function studentsIndex(Request $request)
    {
        $query = Student::with(['user', 'department', 'course']);
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('student_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%")
                               ->orWhere('phone', 'like', "%{$search}%")
                               ->orWhere('nic', 'like', "%{$search}%");
                  });
            });
        }
        
        // Department filter
        if ($request->has('department') && $request->department != '') {
            $query->where('department_id', $request->department);
        }
        
        // Status filter
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        $students = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Pass departments for filter dropdown
        $departments = Department::all();
        
        return view('admin.students.index', compact('students', 'departments'));
    }

    /**
     * Show form to create new student
     */
    public function studentsCreate()
    {
        $departments = Department::all();
        return view('admin.students.create', compact('departments'));
    }

    /**
     * Store a newly created student
     */
    public function studentsStore(StoreStudentRequest $request)
    {
        try {
            // Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->nic),
                'role_id' => 3, // Student role
                'must_change_password' => true,
                'nic' => $request->nic,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            // Create Student
            $student = Student::create([
                'user_id' => $user->id,
                'department_id' => $request->department_id,
                'course_id' => $request->course_id,
                'student_id' => $request->student_id,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'emergency_contact' => $request->emergency_contact,
                'enrollment_date' => $request->enrollment_date ?? now(),
                'status' => 'active',
            ]);

            // Enroll in subjects
            $academicYear = now()->format('Y');
            foreach ($request->subjects as $subjectId) {
                $student->subjects()->attach($subjectId, [
                    'course_id' => $request->course_id,
                    'academic_year' => $academicYear,
                    'semester' => 1,
                    'status' => 'enrolled'
                ]);
            }

            // Send credentials email with user type
            Mail::to($user->email)->queue(new CredentialsMail($user, $request->nic, 'Student'));

            return redirect()->route('admin.students.index')
                ->with('success', 'Student created successfully. Credentials have been sent to their email.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating student: ' . $e->getMessage());
        }
    }

    /**
     * Show form to edit student
     */
    public function studentsEdit(Student $student)
    {
        $student->load(['user', 'subjects']);
        $departments = Department::all();
        $courses = Course::where('department_id', $student->department_id)->get();
        $subjects = Subject::whereHas('courses', function($query) use ($student) {
            $query->where('course_id', $student->course_id);
        })->get();
        
        $selectedSubjects = $student->subjects->pluck('id')->toArray();
        
        return view('admin.students.edit', compact('student', 'departments', 'courses', 'subjects', 'selectedSubjects'));
    }

    /**
     * Update student information
     */
    public function studentsUpdate(UpdateStudentRequest $request, Student $student)
    {
        try {
            // Update User
            $student->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'nic' => $request->nic,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            // Update Student
            $student->update([
                'department_id' => $request->department_id,
                'course_id' => $request->course_id,
                'student_id' => $request->student_id,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'emergency_contact' => $request->emergency_contact,
                'enrollment_date' => $request->enrollment_date,
                'status' => $request->status,
            ]);

            // Update subject enrollments
            $student->subjects()->sync($request->subjects);

            return redirect()->route('admin.students.index')
                ->with('success', 'Student updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating student: ' . $e->getMessage());
        }
    }

    /**
     * Delete student
     */
    public function studentsDestroy(Student $student)
    {
        try {
            $user = $student->user;
            $student->delete();
            $user->delete();
            
            return redirect()->route('admin.students.index')
                ->with('success', 'Student deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting student: ' . $e->getMessage());
        }
    }

    /**
     * Resend credentials to student
     */
    public function resendStudentCredentials(Student $student)
    {
        try {
            $user = $student->user;
            $password = $user->nic; // Original NIC as password
            
            // Send credentials email
            Mail::to($user->email)->queue(new CredentialsMail($user, $password, 'Student'));
            
            return response()->json([
                'success' => true,
                'message' => 'Credentials have been resent to ' . $user->email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error resending credentials: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== LECTURER MANAGEMENT ====================

    /**
     * Display list of lecturers with search and filters
     */
    public function lecturersIndex(Request $request)
    {
        $query = Lecturer::with(['user', 'department']);
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('employee_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%")
                               ->orWhere('phone', 'like', "%{$search}%")
                               ->orWhere('nic', 'like', "%{$search}%");
                  });
            });
        }
        
        // Department filter
        if ($request->has('department') && $request->department != '') {
            $query->where('department_id', $request->department);
        }
        
        // Status filter
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Employment type filter
        if ($request->has('employment_type') && $request->employment_type != '') {
            $query->where('employment_type', $request->employment_type);
        }
        
        $lecturers = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Pass departments for filter dropdown
        $departments = Department::all();
        
        return view('admin.lecturers.index', compact('lecturers', 'departments'));
    }

    /**
     * Show form to create new lecturer
     */
    public function lecturersCreate()
    {
        $departments = Department::all();
        return view('admin.lecturers.create', compact('departments'));
    }

    /**
     * Store a newly created lecturer
     */
    public function lecturersStore(StoreLecturerRequest $request)
    {
        try {
            // Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->nic),
                'role_id' => 2, // Lecturer role
                'must_change_password' => true,
                'nic' => $request->nic,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            // Create Lecturer
            $lecturer = Lecturer::create([
                'user_id' => $user->id,
                'department_id' => $request->department_id,
                'employee_id' => $request->employee_id,
                'qualification' => $request->qualification,
                'specialization' => $request->specialization,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'employment_type' => $request->employment_type,
                'joined_date' => $request->joined_date ?? now(),
                'status' => 'active',
            ]);

            // Assign subjects
            $lecturer->subjects()->sync($request->subjects);

            // Send credentials email with user type
            Mail::to($user->email)->queue(new CredentialsMail($user, $request->nic, 'Lecturer'));

            return redirect()->route('admin.lecturers.index')
                ->with('success', 'Lecturer created successfully. Credentials have been sent to their email.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating lecturer: ' . $e->getMessage());
        }
    }

    /**
     * Show form to edit lecturer
     */
    public function lecturersEdit(Lecturer $lecturer)
    {
        $lecturer->load(['user', 'subjects']);
        $departments = Department::all();
        $courses = Course::where('department_id', $lecturer->department_id)->get();
        $subjects = Subject::whereHas('courses', function($query) use ($lecturer) {
            $query->where('course_id', $lecturer->subjects->first()?->courses->first()?->id ?? null);
        })->get();
        
        $selectedSubjects = $lecturer->subjects->pluck('id')->toArray();
        
        return view('admin.lecturers.edit', compact('lecturer', 'departments', 'courses', 'subjects', 'selectedSubjects'));
    }

    /**
     * Update lecturer information
     */
    public function lecturersUpdate(UpdateLecturerRequest $request, Lecturer $lecturer)
    {
        try {
            // Update User
            $lecturer->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'nic' => $request->nic,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            // Update Lecturer
            $lecturer->update([
                'department_id' => $request->department_id,
                'employee_id' => $request->employee_id,
                'qualification' => $request->qualification,
                'specialization' => $request->specialization,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'employment_type' => $request->employment_type,
                'joined_date' => $request->joined_date,
                'status' => $request->status,
            ]);

            // Update subject assignments
            $lecturer->subjects()->sync($request->subjects);

            return redirect()->route('admin.lecturers.index')
                ->with('success', 'Lecturer updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating lecturer: ' . $e->getMessage());
        }
    }

    /**
     * Delete lecturer
     */
    public function lecturersDestroy(Lecturer $lecturer)
    {
        try {
            $user = $lecturer->user;
            $lecturer->delete();
            $user->delete();
            
            return redirect()->route('admin.lecturers.index')
                ->with('success', 'Lecturer deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting lecturer: ' . $e->getMessage());
        }
    }

    /**
     * Resend credentials to lecturer
     */
    public function resendLecturerCredentials(Lecturer $lecturer)
    {
        try {
            $user = $lecturer->user;
            $password = $user->nic; // Original NIC as password
            
            // Send credentials email
            Mail::to($user->email)->queue(new CredentialsMail($user, $password, 'Lecturer'));
            
            return response()->json([
                'success' => true,
                'message' => 'Credentials have been resent to ' . $user->email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error resending credentials: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== AJAX ENDPOINTS ====================

    /**
     * Get courses by department (AJAX)
     */
    public function getCourses(Request $request)
    {
        try {
            $courses = Course::where('department_id', $request->department_id)->get();
            return response()->json($courses);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load courses'], 500);
        }
    }

    /**
     * Get subjects by course (AJAX)
     */
    public function getSubjects(Request $request)
    {
        try {
            $subjects = Subject::whereHas('courses', function($query) use ($request) {
                $query->where('course_id', $request->course_id);
            })->get();
            
            return response()->json($subjects);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load subjects'], 500);
        }
    }

    /**
     * Resend credentials to any user (General function)
     */
    public function resendCredentials(Request $request, User $user)
    {
        try {
            // Determine user type
            $userType = null;
            if ($user->role_id == 2) {
                $userType = 'Lecturer';
            } elseif ($user->role_id == 3) {
                $userType = 'Student';
            } elseif ($user->role_id == 1) {
                $userType = 'Admin';
            }
            
            // Get password (use NIC or existing password)
            $password = $user->nic ?: 'password123'; // Default if NIC not set
            
            // Send credentials email
            Mail::to($user->email)->queue(new CredentialsMail($user, $password, $userType));
            
            return response()->json([
                'success' => true,
                'message' => 'Credentials have been resent to ' . $user->email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error resending credentials: ' . $e->getMessage()
            ], 500);
        }
    }
}