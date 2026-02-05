<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Course;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User; 

class SubjectController extends Controller
{
    /**
     * List all subjects
     */
    public function index()
    {
        $subjects = Subject::withCount(['courses', 'lecturers', 'students'])
            ->orderBy('name')
            ->get();

        return view('admin.subjects.index', compact('subjects'));
    }

    /**
     * Show the create subject form
     */
    public function create()
    {
        $courses = Course::orderBy('name')->get();

        return view('admin.subjects.create', compact('courses'));
    }

    /**
     * Store a new subject
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:50|unique:subjects,code',
            'description' => 'nullable|string',
            'credits'     => 'required|integer|min:1|max:10',

            // subject belongs to multiple courses
            'courses'     => 'required|array|min:1',
            'courses.*'   => 'exists:courses,id',
        ]);

        // Create subject
        $subject = Subject::create($validated);

        // Attach selected courses (pivot)
        $subject->courses()->sync($validated['courses']);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject created successfully!');
    }

    /**
     * Edit subject
     */
    public function edit(Subject $subject)
    {
        $courses = Course::orderBy('name')->get();
        $selectedCourses = $subject->courses()->pluck('id')->toArray();

        return view('admin.subjects.edit', compact('subject', 'courses', 'selectedCourses'));
    }

    /**
     * Update subject
     */
    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:50|unique:subjects,code,' . $subject->id,
            'description' => 'nullable|string',
            'credits'     => 'required|integer|min:1|max:10',

            'courses'     => 'required|array|min:1',
            'courses.*'   => 'exists:courses,id',
        ]);

        $subject->update($validated);

        // Sync pivot table
        $subject->courses()->sync($validated['courses']);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject updated successfully!');
    }

    /**
     * Delete subject
     */
    public function destroy(Subject $subject)
    {
        try {
            // Remove all pivot relationships first
            $subject->courses()->detach();
            $subject->lecturers()->detach();
            $subject->students()->detach();

            $subject->delete();

            return redirect()->route('admin.subjects.index')
                ->with('success', 'Subject deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete subject. It has related data.');
        }
    }

    /**
     * Show subject details with courses & lecturers
     */
    public function show(Subject $subject)
{
    // Load subject with relationships
    $subject->load([
        'courses.department', 
        'lecturers.user',  // Include user relationship
        'lecturers.department',
        'students.user'
    ]);
    
    // Get available lecturers WITH user and department relationships
    $availableLecturers = Lecturer::with(['user', 'department'])
        ->whereDoesntHave('subjects', function ($query) use ($subject) {
            $query->where('subject_id', $subject->id);
        })
        ->get();
    
    // Ensure all available lecturers have users (auto-fix)
    foreach ($availableLecturers as $lecturer) {
        if (!$lecturer->user) {
            $this->createUserForLecturer($lecturer);
        }
    }
    
    // Reload available lecturers with fixed data
    $availableLecturers = Lecturer::with(['user', 'department'])
        ->whereDoesntHave('subjects', function ($query) use ($subject) {
            $query->where('subject_id', $subject->id);
        })
        ->get();
    
    return view('admin.subjects.show', compact('subject', 'availableLecturers'));
}

private function createUserForLecturer($lecturer)
{
    $baseEmail = strtolower($lecturer->employee_id) . '@university.edu';
    $email = $baseEmail;
    $counter = 1;
    
    // Ensure email is unique
    while (User::where('email', $email)->exists()) {
        $email = str_replace('@', $counter . '@', $baseEmail);
        $counter++;
    }
    
    $user = User::create([
        'name' => 'Lecturer ' . $lecturer->employee_id,
        'email' => $email,
        'password' => bcrypt('password123'),
        'role_id' => 2,
        'must_change_password' => 1,
        'phone' => $lecturer->phone,
    ]);
    
    $lecturer->user_id = $user->id;
    $lecturer->save();
}

/**
 * Assign students to subject
 */
public function assignStudents(Request $request, Subject $subject)
{
    $request->validate([
        'students' => 'required|array',
        'students.*' => 'exists:students,id'
    ]);

    // Verify students are from courses that have this subject
    $courseIds = $subject->courses->pluck('id')->toArray();
    $validStudents = Student::whereIn('id', $request->students)
        ->whereIn('course_id', $courseIds)
        ->pluck('id')
        ->toArray();

    if (empty($validStudents)) {
        return redirect()->back()->with('error', 'Selected students are not from courses with this subject.');
    }

    $subject->students()->syncWithoutDetaching($validStudents);

    return redirect()->route('admin.subjects.show', $subject)
        ->with('success', 'Students assigned successfully!');
}

/**
 * Remove student from subject
 */
public function removeStudent(Subject $subject, Student $student)
{
    $subject->students()->detach($student->id);

    return redirect()->route('admin.subjects.show', $subject)
        ->with('success', 'Student removed successfully!');
}










    /**
     * Assign lecturers to subject
     */
    public function assignLecturers(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'lecturers' => 'required|array|min:1',
            'lecturers.*' => 'exists:lecturers,id',
        ]);

        $subject->lecturers()->syncWithoutDetaching($validated['lecturers']);

        return back()->with('success', 'Lecturers assigned successfully!');
    }

    /**
     * Remove lecturer from subject
     */
    public function removeLecturer(Subject $subject, Lecturer $lecturer)
    {
        $subject->lecturers()->detach($lecturer->id);

        return back()->with('success', 'Lecturer removed successfully!');
    }


    /**
 * Bulk assign lecturers to subject
 */
public function bulkAssignLecturers(Request $request, Subject $subject)
{
    $validated = $request->validate([
        'lecturers' => 'required|array|min:1',
        'lecturers.*' => 'exists:lecturers,id'
    ]);

    $subject->lecturers()->syncWithoutDetaching($validated['lecturers']);

    return back()->with('success', 'Lecturers bulk assigned to subject successfully!');
}
}
