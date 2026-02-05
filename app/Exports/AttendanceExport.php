<?php

namespace App\Exports;

use App\Models\QRSession;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $sessionId;

    public function __construct($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    public function collection()
    {
        return QRSession::with(['attendances.student'])
            ->findOrFail($this->sessionId)
            ->attendances;
    }

    public function headings(): array
    {
        return [
            'Student ID',
            'Student Name',
            'Email',
            'Status',
            'Marked At',
            'IP Address'
        ];
    }

    public function map($attendance): array
    {
        return [
            $attendance->student->id,
            $attendance->student->name,
            $attendance->student->user->email,
            ucfirst($attendance->status),
            $attendance->marked_at->format('Y-m-d H:i:s'),
            $attendance->ip_address
        ];
    }
}