<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_id',
        'department_id',
        'nic',
        'phone',
        'address',
        'qualification',
        'specialization',
        'status',
        'date_of_birth',
        'gender',
        'employmeny_type',
        'joined_date'
    ];

    protected $attributes = [
        'status' => 'active'
    ];

    // Add this to automatically create user when creating lecturer
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($lecturer) {
            // Auto-generate employee_id if not provided
            if (empty($lecturer->employee_id)) {
                $lecturer->employee_id = 'LEC' . str_pad(Lecturer::count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
        
        static::created(function ($lecturer) {
            // If lecturer doesn't have a user, create one
            if (!$lecturer->user_id) {
                $email = strtolower($lecturer->employee_id) . '@university.edu';
                
                // Check if user already exists
                $user = User::where('email', $email)->first();
                
                if (!$user) {
                    $user = User::create([
                        'name' => 'Lecturer ' . $lecturer->employee_id,
                        'email' => $email,
                        'password' => bcrypt('password123'),
                        'role_id' => 2,
                        'must_change_password' => 1,
                        'phone' => $lecturer->phone,
                    ]);
                }
                
                $lecturer->user_id = $user->id;
                $lecturer->save();
            }
        });
    }

    /**
     * Get the user associated with the lecturer
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department of the lecturer
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get subjects taught by this lecturer
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'lecturer_subject')
                    ->withTimestamps();
    }

    public function qrSessions()
    {
        return $this->hasMany(QRSession::class);
    }

    /**
     * Get courses this lecturer teaches (through subjects)
     */
    public function courses()
    {
        return $this->hasManyThrough(
            Course::class,
            'lecturer_subject',
            'lecturer_id',
            'id',
            'id',
            'subject_id'
        )->distinct();
    }

    /**
     * Check if lecturer teaches a subject
     */
    public function teachesSubject($subjectId)
    {
        return $this->subjects()->where('subject_id', $subjectId)->exists();
    }

    /**
     * Get assigned subjects count
     */
    public function getAssignedSubjectsCountAttribute()
    {
        return $this->subjects()->count();
    }
}