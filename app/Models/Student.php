<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_id',
        'department_id',
        'course_id',
        'nic',
        'phone',
        'address',
        'status'
    ];

    protected $attributes = [
        'status' => 'active'
    ];

    /**
     * Get the user associated with the student
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department of the student
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the course of the student
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get subjects enrolled by this student
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'enrollments', 'student_id', 'subject_id')
                    ->withPivot('academic_year', 'semester', 'status', 'grade')
                    ->withTimestamps();
    }
public function attendances()
{
    return $this->hasMany(Attendance::class);
}

public function enrolledSubjects()
{
    return $this->subjects()->wherePivot('status', 'enrolled');
}
    /**
     * Get enrollments for this student
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get current subject enrollments
     */
    public function currentSubjectEnrollments()
    {
        $currentYear = date('Y');
        return $this->enrollments()->where('academic_year', $currentYear);
    }

    /**
     * Check if student is enrolled in a subject
     */
    public function isEnrolledInSubject($subjectId, $academicYear = null)
    {
        if (!$academicYear) {
            $academicYear = date('Y');
        }
        
        return $this->enrollments()
            ->where('subject_id', $subjectId)
            ->where('academic_year', $academicYear)
            ->exists();
    }
}