<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name',
        'code',
        'description',
        'duration_months',
        
    ];

    /**
     * Get the department this course belongs to
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get subjects assigned to this course (many-to-many)
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'course_subject')
                    ->withTimestamps();
    }

    /**
     * Get students enrolled in this course
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}