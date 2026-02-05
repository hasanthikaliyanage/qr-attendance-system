<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'credit_hours',
        'type', // Add this
        'semester', // Add this
        'course_id', // Add this (assuming single course per subject)
        'department_id',
    ];

    /**
     * Get the course this subject belongs to
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get courses this subject belongs to (many-to-many - keep this if needed)
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_subject')
                    ->withTimestamps();
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get lecturers teaching this subject (many-to-many)
     */
    public function lecturers()
    {
        return $this->belongsToMany(Lecturer::class, 'lecturer_subject')
                    ->withTimestamps();
    }

    /**
     * Get students enrolled in this subject (many-to-many)
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'enrollments')
                    ->withTimestamps();
    }
    
    public function qrSessions()
    {
        return $this->hasMany(QRSession::class);
    }
}