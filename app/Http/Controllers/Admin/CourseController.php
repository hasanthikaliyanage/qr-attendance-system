<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Department;
use App\Models\Subject;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of courses
     */
    public function index()
    {
        $courses = Course::with('department')
                        ->withCount(['subjects', 'students'])
                        ->orderBy('name')
                        ->get();
        
        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new course
     */
    public function create()
    {
        $departments = Department::orderBy('name')->get();
        
        return view('admin.courses.create', compact('departments'));
    }

    /**
     * Store a newly created course
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:courses,code',
            'description' => 'nullable|string',
            'duration_years' => 'required|integer|min:1|max:10',
        ]);

        Course::create($validated);

        return redirect()->route('admin.courses.index')
                        ->with('success', 'Course created successfully!');
    }

    /**
     * Show the form for editing a course
     */
    public function edit(Course $course)
    {
        $departments = Department::orderBy('name')->get();
        
        return view('admin.courses.edit', compact('course', 'departments'));
    }

    /**
     * Update the specified course
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:courses,code,' . $course->id,
            'description' => 'nullable|string',
            'duration_years' => 'required|integer|min:1|max:10',
        ]);

        $course->update($validated);

        return redirect()->route('admin.courses.index')
                        ->with('success', 'Course updated successfully!');
    }

    /**
     * Remove the specified course
     */
    public function destroy(Course $course)
    {
        try {
            $course->delete();
            return redirect()->route('admin.courses.index')
                            ->with('success', 'Course deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete course with associated records.');
        }
    }

    /**
     * Show course details with assigned subjects
     */
    public function show(Course $course)
    {
        $course->load(['department', 'subjects', 'students']);
        $availableSubjects = Subject::whereDoesntHave('courses', function($query) use ($course) {
            $query->where('course_id', $course->id);
        })->orderBy('name')->get();
        
        return view('admin.courses.show', compact('course', 'availableSubjects'));
    }

    /**
     * Assign subjects to course
     */
    public function assignSubjects(Request $request, Course $course)
    {
        $validated = $request->validate([
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'exists:subjects,id',
        ]);

        $course->subjects()->syncWithoutDetaching($validated['subjects']);

        return back()->with('success', 'Subjects assigned to course successfully!');
    }

    /**
     * Remove subject from course
     */
    public function removeSubject(Course $course, Subject $subject)
    {
        $course->subjects()->detach($subject->id);

        return back()->with('success', 'Subject removed from course successfully!');
    }

    /**
     * Bulk assign subjects to course
     */
    public function bulkAssignSubjects(Request $request, Course $course)
    {
        $validated = $request->validate([
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'exists:subjects,id',
            'semester' => 'required|integer|in:1,2,3,4,5,6,7,8',
            'is_core' => 'boolean'
        ]);

        $pivotData = [];
        foreach ($validated['subjects'] as $subjectId) {
            $pivotData[$subjectId] = [
                'semester' => $validated['semester'],
                'is_core' => $validated['is_core'] ?? true
            ];
        }

        $course->subjects()->syncWithoutDetaching($pivotData);

        return back()->with('success', 'Subjects bulk assigned to course successfully!');
    }
}
// Remove everything after this line - the function was outside the class