<?php
// database/seeders/QRSessionsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QRSession;
use App\Models\Department;
use App\Models\Course;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;

class QRSessionsSeeder extends Seeder
{
    public function run()
    {
        // Get admin user
        $admin = User::where('email', 'admin@university.edu')->first();
        
        if (!$admin) return;
        
        // Get IT department
$itDept = Department::where('name', 'like', '%Information Technology%')->first();
        
        if (!$itDept) return;
        
        // Get first course
        $course = Course::where('department_id', $itDept->id)->first();
        
        if (!$course) return;
        
        // Get first subject
        $subject = Subject::whereHas('courses', function($q) use ($course) {
            $q->where('course_id', $course->id);
        })->first();
        
        if (!$subject) return;
        
        // Create sample QR sessions
        QRSession::create([
            'session_name' => 'Programming Basics - Class 1',
            'description' => 'Introduction to Programming Concepts',
            'department_id' => $itDept->id,
            'course_id' => $course->id,
            'subject_id' => $subject->id,
            'created_by' => $admin->id,
            'session_date' => Carbon::today(),
            'start_time' => '09:00:00',
            'end_time' => '10:30:00',
            'duration_minutes' => 90,
            'qr_token' => hash_hmac('sha256', uniqid(), config('app.key')),
            'is_active' => true,
            'is_global_one_time' => false,
        ]);
        
        QRSession::create([
            'session_name' => 'Web Development - Lab Session',
            'description' => 'HTML & CSS Practical Session',
            'department_id' => $itDept->id,
            'course_id' => $course->id,
            'subject_id' => $subject->id,
            'created_by' => $admin->id,
            'session_date' => Carbon::tomorrow(),
            'start_time' => '14:00:00',
            'end_time' => '16:00:00',
            'duration_minutes' => 120,
            'qr_token' => hash_hmac('sha256', uniqid(), config('app.key')),
            'is_active' => true,
            'is_global_one_time' => true,
        ]);
        
        $this->command->info('QR Sessions seeded successfully!');
    }
}