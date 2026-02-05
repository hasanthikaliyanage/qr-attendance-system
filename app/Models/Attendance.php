<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'qr_session_id',
        'student_id',
        'subject_id',
        'marked_at',
        'status',
        'device_info',
        'ip_address',
        'scan_method',
        'remarks',
    ];

    protected $casts = [
        'marked_at' => 'datetime',
    ];

    // Relationship with QRSession
    public function qrSession()
    {
        return $this->belongsTo(QRSession::class, 'qr_session_id');
    }

    // Relationship with Student
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // Relationship with Subject
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    // Scope for present attendance
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    // Scope for late attendance
    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    // Scope for absent
    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    // Scope for today's attendance
    public function scopeToday($query)
    {
        return $query->whereDate('marked_at', today());
    }

    // Scope for this week
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('marked_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    // Scope for this month
    public function scopeThisMonth($query)
    {
        return $query->whereBetween('marked_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }

    // Get formatted marked time
    public function getFormattedMarkedAtAttribute()
    {
        return $this->marked_at->format('Y-m-d h:i A');
    }

    // Get date only
    public function getDateAttribute()
    {
        return $this->marked_at->format('Y-m-d');
    }

    // Get time only
    public function getTimeAttribute()
    {
        return $this->marked_at->format('h:i A');
    }

    // Get status badge class
    public function getStatusBadgeClassAttribute()
    {
        $classes = [
            'present' => 'bg-green-100 text-green-800 border border-green-200',
            'late' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
            'absent' => 'bg-red-100 text-red-800 border border-red-200',
        ];
        
        return $classes[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    // Get status icon
    public function getStatusIconAttribute()
    {
        $icons = [
            'present' => 'fas fa-check-circle text-green-500',
            'late' => 'fas fa-clock text-yellow-500',
            'absent' => 'fas fa-times-circle text-red-500',
        ];
        
        return $icons[$this->status] ?? 'fas fa-question-circle text-gray-500';
    }

    // Get scan method badge
    public function getScanMethodBadgeClassAttribute()
    {
        $classes = [
            'webcam' => 'bg-blue-100 text-blue-800',
            'direct' => 'bg-purple-100 text-purple-800',
            'manual' => 'bg-gray-100 text-gray-800',
        ];
        
        return $classes[$this->scan_method] ?? 'bg-gray-100 text-gray-800';
    }

    // Check if attendance is recent (within 24 hours)
    public function getIsRecentAttribute()
    {
        return $this->marked_at->gt(now()->subDay());
    }

    // Get attendance duration (if session has duration)
    public function getDurationAttribute()
    {
        if ($this->qrSession && $this->qrSession->duration_minutes) {
            return $this->qrSession->duration_minutes . ' minutes';
        }
        return 'N/A';
    }
}