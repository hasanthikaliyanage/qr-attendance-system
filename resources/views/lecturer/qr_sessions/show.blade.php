@extends('layouts.app')

@section('title', 'QR Session Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('lecturer.qr_sessions.index') }}" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Back to Sessions
        </a>
        <h1 class="text-3xl font-bold text-gray-800">{{ $session->session_name }}</h1>
        <p class="text-gray-600 mt-1">{{ $session->subject->name }} - {{ $session->course->name }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- QR Code Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 text-center">QR Code</h2>
                
                <!-- Status Badge -->
                <div class="text-center mb-4">
                    @if($session->is_active)
                        <span class="px-4 py-2 text-sm font-semibold rounded-full bg-green-100 text-green-800 inline-block">
                            <i class="fas fa-check-circle"></i> Active
                        </span>
                    @else
                        <span class="px-4 py-2 text-sm font-semibold rounded-full bg-red-100 text-red-800 inline-block">
                            <i class="fas fa-times-circle"></i> Inactive
                        </span>
                    @endif
                </div>

                <!-- QR Code Display -->
                <div class="bg-gray-50 p-6 rounded-lg mb-4 flex flex-col items-center">
                    <!-- Generate QR Code using qrcode.js -->
                    <div id="qrcode" class="mb-4"></div>
                    
                    <!-- QR Token Display -->
                    <div class="text-center">
                        <p class="text-xs text-gray-500 mb-1">QR Token:</p>
                        <p class="text-xs font-mono bg-white px-3 py-1 rounded border border-gray-300">
                            {{ substr($session->qr_token, 0, 16) }}...
                        </p>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-blue-800 font-medium mb-2">
                        <i class="fas fa-info-circle"></i> Instructions:
                    </p>
                    <ol class="text-sm text-blue-700 space-y-1 list-decimal list-inside">
                        <li>Display this QR code to students</li>
                        <li>Students scan using their app</li>
                        <li>Attendance marked automatically</li>
                    </ol>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-2">
                    <button onclick="window.print()" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                        <i class="fas fa-print mr-2"></i> Print QR Code
                    </button>
                    
                    <button onclick="downloadQR()" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                        <i class="fas fa-download mr-2"></i> Download Image
                    </button>

                    <form action="{{ route('lecturer.qr_sessions.toggle', $session->id) }}" method="POST">
    @csrf
    <!-- @method('PUT') වෙනුවට මෙය භාවිතා කරන්න -->
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    
    <button type="submit" 
            class="w-full {{ $session->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white px-4 py-2 rounded-lg font-medium">
        <i class="fas fa-{{ $session->is_active ? 'stop' : 'play' }}-circle mr-2"></i>
        {{ $session->is_active ? 'Deactivate' : 'Activate' }} Session
    </button>
</form>
                </div>
            </div>
        </div>

        <!-- Session Details & Attendance -->
        <div class="lg:col-span-2">
            <!-- Session Info Card -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Session Information</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Department</p>
                        <p class="font-medium">{{ $session->department->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Course</p>
                        <p class="font-medium">{{ $session->course->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Subject</p>
                        <p class="font-medium">{{ $session->subject->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Lecturer</p>
                        <p class="font-medium">{{ $session->lecturer->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Date</p>
                        <p class="font-medium">{{ \Carbon\Carbon::parse($session->session_date)->format('l, M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Time</p>
                        <p class="font-medium">
                            {{ \Carbon\Carbon::parse($session->start_time)->format('h:i A') }} - 
                            {{ \Carbon\Carbon::parse($session->end_time)->format('h:i A') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Duration</p>
                        <p class="font-medium">{{ $session->duration_minutes }} minutes</p>
                    </div>
                </div>

                @if($session->description)
                <div class="mt-4 pt-4 border-t">
                    <p class="text-sm text-gray-600 mb-1">Description</p>
                    <p class="text-gray-800">{{ $session->description }}</p>
                </div>
                @endif
            </div>

            <!-- Attendance Stats -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Attendance Statistics</h2>
                
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-blue-600">{{ $session->attendances->count() }}</p>
                        <p class="text-sm text-gray-600 mt-1">Total Present</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-green-600">{{ $session->attendances->where('status', 'present')->count() }}</p>
                        <p class="text-sm text-gray-600 mt-1">On Time</p>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-yellow-600">{{ $session->attendances->where('status', 'late')->count() }}</p>
                        <p class="text-sm text-gray-600 mt-1">Late</p>
                    </div>
                </div>
            </div>

            <!-- Attendance List -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Attendance Records</h2>
                
                @if($session->attendances->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($session->attendances as $index => $attendance)
                            <tr>
                                <td class="px-4 py-3 text-sm">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $attendance->student->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $attendance->student->student_id }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $attendance->marked_at->format('h:i A') }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($attendance->status === 'present')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check"></i> Present
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock"></i> Late
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-users text-4xl text-gray-300 mb-2"></i>
                    <p class="text-gray-500">No attendance records yet</p>
                    <p class="text-sm text-gray-400 mt-1">Students will appear here when they scan the QR code</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- QR Code Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<!-- Scripts -->
<script>
// Generate QR Code
document.addEventListener('DOMContentLoaded', function() {
    const qrToken = '{{ $session->qr_token }}';
    
    new QRCode(document.getElementById("qrcode"), {
        text: qrToken,
        width: 256,
        height: 256,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
});

// Download QR Code
function downloadQR() {
    const canvas = document.querySelector('#qrcode canvas');
    if (canvas) {
        const link = document.createElement('a');
        link.download = 'QR_{{ $session->session_name }}_{{ \Carbon\Carbon::parse($session->session_date)->format("Y-m-d") }}.png';
        link.href = canvas.toDataURL();
        link.click();
    }
}
</script>
@endsection