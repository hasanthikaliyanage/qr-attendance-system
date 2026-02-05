<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Department;
use App\Models\Course;

class DashboardController extends Controller
{
    /**
     * Display the dashboard based on user role.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Redirect non-admin users to their dashboards
        if (!$user->isAdmin()) {
            return redirect()->route($user->getDashboardRoute());
        }
        
        // Admin dashboard data
        $totalStudents = Student::count();
        $totalLecturers = Lecturer::count();
        $totalDepartments = Department::count();
        $totalCourses = Course::count();
        
        return view('dashboard', compact(
            'totalStudents', 
            'totalLecturers', 
            'totalDepartments', 
            'totalCourses'
        ));
    }
}