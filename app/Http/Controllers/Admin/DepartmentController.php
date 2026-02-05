<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments
     */
    public function index()
    {
        $departments = Department::withCount(['courses', 'students', 'lecturers'])
                                 ->orderBy('name')
                                 ->get();
        
        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new department
     */
    public function create()
    {
        return view('admin.departments.create');
    }

    /**
     * Store a newly created department
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:departments,code',
            'description' => 'nullable|string',
        ]);

        Department::create($validated);

        return redirect()->route('admin.departments.index')
                        ->with('success', 'Department created successfully!');
    }

    /**
     * Show the form for editing a department
     */
    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * Update the specified department
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:departments,code,' . $department->id,
            'description' => 'nullable|string',
        ]);

        $department->update($validated);

        return redirect()->route('admin.departments.index')
                        ->with('success', 'Department updated successfully!');
    }

    /**
     * Remove the specified department
     */
    public function destroy(Department $department)
    {
        try {
            $department->delete();
            return redirect()->route('admin.departments.index')
                            ->with('success', 'Department deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete department with associated records.');
        }
    }

    /**
     * Show department details
     */
    public function show(Department $department)
    {
        $department->load(['courses', 'students', 'lecturers']);
        
        return view('admin.departments.show', compact('department'));
    }
}