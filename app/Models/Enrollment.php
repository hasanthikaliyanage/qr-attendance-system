<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    protected $fillable = [
        'student_id', 
        'course_id', 
        'subject_id',
        'academic_year',
        'semester',
        'status',
        'grade',
        'enrollment_date'
    ];

    protected $attributes = [
        'status' => 'enrolled',
        'semester' => 1
    ];

    /**
     * Get the student
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the course
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the subject
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Scope for current academic year
     */
    public function scopeCurrentAcademicYear($query)
    {
        return $query->where('academic_year', date('Y'));
    }

    /**
     * Scope for active enrollments
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'enrolled');
    }

    /**
     * Check if enrollment is active
     */
    public function isActive()
    {
        return $this->status === 'enrolled';
    }

    /**
     * Mark as completed
     */
    public function markAsCompleted($grade = null)
    {
        $this->status = 'completed';
        if ($grade) {
            $this->grade = $grade;
        }
        $this->save();
    }
}