@extends('layouts.app')

@section('title', 'QR Code - ' . $session->session_name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('lecturer.qr_sessions.show', $session->id) }}" 
           class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Back to Session
        </a>
        <h1 class="text-3xl font-bold text-gray-800">QR Code for {{ $session->session_name }}</h1>
        <p class="text-gray-600 mt-1">Scan this QR code for attendance</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- QR Code Section -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">QR Code</h2>
            
            <div class="text-center">
                <!-- QR Code Image -->
                <div class="mb-4 inline-block p-4 bg-white border border-gray-200 rounded-lg">
                    <img src="{{ $qrCodeDataUri }}" alt="Attendance QR Code for {{ $session->session_name }}" class="img-fluid">
                </div>
                
                <!-- Session Information -->
                <div class="mb-6">
                    <p class="text-sm text-gray-600 mb-1">Session Token:</p>
                    <code class="bg-gray-100 px-3 py-1 rounded text-sm font-mono break-all">
                        {{ $session->qr_token }}
                    </code>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-wrap justify-center gap-3">
                    <button onclick="downloadQR()" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                        <i class="fas fa-download mr-2"></i> Download QR Code
                    </button>
                    
                    <a href="{{ route('lecturer.qr_sessions.show', $session->id) }}" 
                       class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg font-medium">
                        <i class="fas fa-eye mr-2"></i> View Session
                    </a>
                    
                    <form action="{{ route('lecturer.qr_sessions.toggle', $session->id) }}" method="POST" class="inline">
                        @csrf
                        @method('PUT')
                        <button type="submit" 
                                class="px-4 py-2 {{ $session->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg font-medium">
                            <i class="fas {{ $session->is_active ? 'fa-pause' : 'fa-play' }} mr-2"></i>
                            {{ $session->is_active ? 'Deactivate' : 'Activate' }} Session
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Session Details Section -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Session Details</h2>
            
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600">Session Name</p>
                    <p class="font-medium">{{ $session->session_name }}</p>
                </div>
                
                @if($session->description)
                <div>
                    <p class="text-sm text-gray-600">Description</p>
                    <p class="font-medium">{{ $session->description }}</p>
                </div>
                @endif
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Department</p>
                        <p class="font-medium">{{ $session->department->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Course</p>
                        <p class="font-medium">{{ $session->course->name }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Subject</p>
                        <p class="font-medium">{{ $session->subject->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Lecturer</p>
                        <p class="font-medium">{{ $session->lecturer->user->name }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Date</p>
                        <p class="font-medium">{{ \Carbon\Carbon::parse($session->session_date)->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Start Time</p>
                        <p class="font-medium">{{ \Carbon\Carbon::parse($session->start_time)->format('h:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">End Time</p>
                        <p class="font-medium">{{ \Carbon\Carbon::parse($session->end_time)->format('h:i A') }}</p>
                    </div>
                </div>
                
                <div>
                    <p class="text-sm text-gray-600">Duration</p>
                    <p class="font-medium">{{ $session->duration_minutes }} minutes</p>
                </div>
                
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                        {{ $session->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $session->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                
                <div>
                    <p class="text-sm text-gray-600">Scan URL</p>
                    <a href="{{ route('student.scan_by_token', $session->qr_token) }}" 
                       target="_blank"
                       class="text-blue-600 hover:text-blue-800 break-all">
                        {{ route('student.scan_by_token', $session->qr_token) }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Instructions -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-800 mb-3">
            <i class="fas fa-info-circle mr-2"></i> How to Use This QR Code
        </h3>
        <ul class="list-disc pl-5 space-y-2 text-blue-700">
            <li>Click the <strong>Activate Session</strong> button when you're ready for students to scan</li>
            <li>Display this QR code on your projector or screen</li>
            <li>Students should scan the QR code using their phone camera or QR scanner app</li>
            <li>They will be directed to a confirmation page to mark their attendance</li>
            <li>You can deactivate the session anytime to stop accepting scans</li>
            <li>Download the QR code to print or share digitally if needed</li>
        </ul>
    </div>
</div>

<script>
function downloadQR() {
    const qrImage = document.querySelector('img[alt*="QR Code"]');
    const sessionName = "{{ $session->session_name }}";
    const sanitizedName = sessionName.replace(/[^a-z0-9]/gi, '_').toLowerCase();
    
    // Create a temporary link
    const link = document.createElement('a');
    link.href = qrImage.src;
    link.download = `qr_${sanitizedName}_{{ $session->qr_token }}.png`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endsection