<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'must_change_password',
        'nic',
        'phone',
        'address',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'must_change_password' => 'boolean'
        
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function lecturer()
    {
        return $this->hasOne(Lecturer::class);
    }

    public function isAdmin()
    {
        return $this->role_id === 1;
    }

    public function isLecturer()
    {
        return $this->role_id === 2;
    }

    public function isStudent()
    {
        return $this->role_id === 3;
    }

    /**
     * Get the dashboard route name based on user role
     */
    public function getDashboardRoute(): string
    {
        return match($this->role_id) {
            1 => 'dashboard',      // Admin
            2 => 'lecturer.dashboard', // Lecturer
            3 => 'student.dashboard',  // Student
            default => 'dashboard',
        };
    }

    /**
     * Get redirect route (checks password change requirement)
     */
    public function getRedirectRoute()
    {
        if ($this->must_change_password) {
            return route('password.change');
        }

        return route($this->getDashboardRoute());
    }
}