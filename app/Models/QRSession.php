<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class QRSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'qr_sessions';

    protected $fillable = [
        'lecturer_id',
        'session_name',
        'description',
        'department_id',
        'course_id',
        'subject_id',
        'created_by',
        'session_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'qr_token',
        'qr_code_path',
        'is_active',
        'is_global_one_time',
        'is_scanned',
        'scanned_at',
    ];

    protected $casts = [
        'session_date' => 'date',
        'scanned_at' => 'datetime',
        'is_active' => 'boolean',
        'is_global_one_time' => 'boolean',
        'is_scanned' => 'boolean',
        'duration_minutes' => 'integer',
    ];

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'qr_session_id');
    }

    // Auto-generate QR code when creating session
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($session) {
            // Generate unique QR token
            $session->qr_token = Str::uuid()->toString();
            
            // Set created_by if not set
            if (!$session->created_by) {
                $session->created_by = auth()->id();
            }
            
            // Generate QR code image and save to storage
            $qrCode = QrCode::format('svg')
                ->size(500)
                ->errorCorrection('H')
                ->generate($session->qr_token);
            
            // Save QR code to storage
            $fileName = 'qr-codes/' . $session->qr_token . '.svg';
            Storage::disk('public')->put($fileName, $qrCode);
            $session->qr_code_path = $fileName;
        });

        static::deleting(function ($session) {
            // Delete QR code file when session is deleted
            if ($session->qr_code_path) {
                Storage::disk('public')->delete($session->qr_code_path);
            }
        });
    }

    // Get QR code as base64 (for display without file)
    public function getQrImageBase64Attribute()
    {
        if ($this->qr_code_path && Storage::disk('public')->exists($this->qr_code_path)) {
            $image = Storage::disk('public')->get($this->qr_code_path);
            return base64_encode($image);
        }
        
        // Generate on-the-fly if file doesn't exist
        $qrCode = QrCode::format('svg')
            ->size(500)
            ->errorCorrection('H')
            ->generate($this->qr_token);
        
        return base64_encode($qrCode);
    }

    // Get QR code URL
    public function getQrCodeUrlAttribute()
    {
        if ($this->qr_code_path) {
            return Storage::url($this->qr_code_path);
        }
        return null;
    }

    // Check if session is active (based on time)
    public function isActive()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        $sessionDate = $this->session_date;
        $startTime = \Carbon\Carbon::parse($this->start_time);
        $endTime = \Carbon\Carbon::parse($this->end_time);
        
        // Check if today is session date
        if (!$sessionDate->isToday()) {
            return false;
        }

        // Check if current time is within session time + duration
        $sessionStart = $sessionDate->copy()->setTimeFrom($startTime);
        $sessionEnd = $sessionDate->copy()->setTimeFrom($endTime)->addMinutes($this->duration_minutes);
        
        return $now->between($sessionStart, $sessionEnd);
    }

    // Check if session is expired
    public function isExpired()
    {
        $now = now();
        $sessionDate = $this->session_date;
        $endTime = \Carbon\Carbon::parse($this->end_time);
        
        $sessionEnd = $sessionDate->copy()->setTimeFrom($endTime)->addMinutes($this->duration_minutes);
        
        return $now->greaterThan($sessionEnd) || !$this->is_active;
    }

    // Get expiry time
    public function getExpiresAtAttribute()
    {
        $sessionDate = $this->session_date;
        $endTime = \Carbon\Carbon::parse($this->end_time);
        return $sessionDate->copy()->setTimeFrom($endTime)->addMinutes($this->duration_minutes);
    }

    // Get total students attended
    public function getTotalAttendedAttribute()
    {
        return $this->attendances()->count();
    }

    // Get attendance percentage
    public function getAttendancePercentageAttribute()
    {
        $totalStudents = $this->course->students()->count();
        if ($totalStudents === 0) return 0;
        
        return round(($this->total_attended / $totalStudents) * 100, 2);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('session_date', today());
    }

    public function scopeForLecturer($query, $lecturerId)
    {
        return $query->where('lecturer_id', $lecturerId);
    }

    public function scopeForSubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('session_date', '>=', today())
            ->where('is_active', true);
    }
}