@extends('layouts.app')

@section('title', 'QR Scanner - Webcam')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">QR Code Scanner</h1>
                    <p class="text-gray-600 mt-1">Use your webcam to scan QR codes for attendance</p>
                </div>
                <a href="{{ route('student.qr_sessions.index') }}" 
                   class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Sessions
                </a>
            </div>
        </div>

        <!-- Active Sessions Alert -->
        @if(isset($activeSessions) && $activeSessions->count() > 0)
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-info-circle text-green-500 mr-3"></i>
                <div>
                    <p class="font-medium text-green-800">Active sessions available!</p>
                    <p class="text-sm text-green-600 mt-1">You have {{ $activeSessions->count() }} active session(s) to scan</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Scanner Interface -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Webcam View -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Webcam Scanner</h2>
                
                <!-- Camera Preview -->
                <div id="cameraPreview" class="w-full h-64 bg-gray-100 rounded-lg mb-4 flex items-center justify-center">
                    <p class="text-gray-500" id="cameraStatus">Camera not started</p>
                    <video id="video" autoplay playsinline style="display: none; width: 100%; height: 100%; object-fit: cover; border-radius: 8px;"></video>
                    <canvas id="canvas" style="display: none;"></canvas>
                </div>
                
                <!-- Camera Controls -->
                <div class="space-y-3">
                    <button onclick="initCamera()" id="initBtn"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium">
                        <i class="fas fa-camera mr-2"></i> Initialize Camera
                    </button>
                    
                    <button onclick="startScanning()" id="startBtn" disabled
                            class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-medium">
                        <i class="fas fa-play mr-2"></i> Start Scanning
                    </button>
                    
                    <button onclick="stopCamera()" id="stopBtn" disabled
                            class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg font-medium">
                        <i class="fas fa-stop mr-2"></i> Stop Camera
                    </button>
                </div>
                
                <!-- Manual Input -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="font-semibold text-gray-700 mb-2">Or Enter QR Code Manually</h3>
                    <div class="flex">
                        <input type="text" id="manualInput" 
                               placeholder="Paste QR code content here" 
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button onclick="processManualInput()" 
                                class="bg-purple-600 text-white px-4 py-2 rounded-r-lg hover:bg-purple-700">
                            <i class="fas fa-check"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Scan Results -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Scan Results</h2>
                
                <!-- Result Display -->
                <div id="resultContainer" class="hidden">
                    <div class="p-4 rounded-lg mb-4" id="resultMessage">
                        <!-- Result will appear here -->
                    </div>
                    
                    <div id="attendanceDetails" class="hidden space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Session:</span>
                            <span id="sessionName" class="font-medium"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subject:</span>
                            <span id="subjectName" class="font-medium"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Lecturer:</span>
                            <span id="lecturerName" class="font-medium"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span id="attendanceStatus" class="font-medium"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Time:</span>
                            <span id="markedTime" class="font-medium"></span>
                        </div>
                    </div>
                </div>
                
                <!-- Empty State -->
                <div id="emptyState" class="text-center py-8">
                    <i class="fas fa-qrcode text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Scan a QR code to mark attendance</p>
                    <p class="text-sm text-gray-400 mt-2">or enter manually</p>
                </div>
                
                <!-- Active Sessions List -->
                @if(isset($activeSessions) && $activeSessions->count() > 0)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="font-semibold text-gray-700 mb-3">Active Sessions</h3>
                    <div class="space-y-3">
                        @foreach($activeSessions as $session)
                        <div class="bg-blue-50 border border-blue-200 rounded p-3">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $session->subject->name ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-600">{{ $session->lecturer->user->name ?? 'N/A' }}</p>
                                </div>
                                <button onclick="useSessionToken('{{ $session->qr_token }}')"
                                        class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                    Use Token
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Instructions -->
        <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">
                <i class="fas fa-info-circle mr-2"></i> How to Use
            </h3>
            <ul class="list-disc pl-5 space-y-2 text-gray-700">
                <li>Click <strong>Initialize Camera</strong> to setup webcam</li>
                <li>Then click <strong>Start Scanning</strong> to begin QR detection</li>
                <li>Point your camera at the QR code displayed by your lecturer</li>
                <li>Hold steady until the scanner detects the QR code</li>
                <li>Attendance will be marked automatically</li>
                <li>You can also enter QR code manually or use session tokens</li>
            </ul>
            
            <!-- Troubleshooting -->
            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded">
                <h4 class="font-semibold text-yellow-800 mb-2">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Troubleshooting
                </h4>
                <ul class="list-disc pl-5 text-sm text-yellow-700 space-y-1">
                    <li>Ensure camera permissions are allowed in your browser</li>
                    <li>Try using Google Chrome or Microsoft Edge</li>
                    <li>Make sure there is good lighting</li>
                    <li>If camera fails, use manual input option</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Scanner Library -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>

<script>
let video = document.getElementById('video');
let canvas = document.getElementById('canvas');
let canvasContext = canvas.getContext('2d');
let mediaStream = null;
let scanning = false;
let scanInterval = null;

async function initCamera() {
    try {
        showResult('Initializing camera...', 'info');
        
        // Stop existing stream if any
        if (mediaStream) {
            mediaStream.getTracks().forEach(track => track.stop());
        }
        
        // Get camera access
        mediaStream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'environment', // Prefer back camera
                width: { ideal: 1280 },
                height: { ideal: 720 }
            },
            audio: false
        });
        
        // Setup video element
        video.srcObject = mediaStream;
        video.style.display = 'block';
        document.getElementById('cameraStatus').style.display = 'none';
        
        // Set canvas dimensions
        video.onloadedmetadata = () => {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
        };
        
        // Enable buttons
        document.getElementById('initBtn').disabled = true;
        document.getElementById('startBtn').disabled = false;
        document.getElementById('stopBtn').disabled = true;
        
        showResult('Camera initialized successfully!', 'success');
        
    } catch (error) {
        console.error('Camera error:', error);
        
        // Try with user facing mode if environment fails
        if (error.name === 'NotAllowedError') {
            showResult('Camera permission denied. Please allow camera access.', 'error');
        } else if (error.name === 'NotFoundError') {
            showResult('No camera found. Please use manual input.', 'error');
        } else if (error.name === 'NotReadableError') {
            showResult('Camera is already in use by another application.', 'error');
        } else {
            showResult('Camera error: ' + error.message, 'error');
        }
    }
}

function startScanning() {
    if (!mediaStream) {
        showResult('Please initialize camera first', 'warning');
        return;
    }
    
    scanning = true;
    document.getElementById('startBtn').disabled = true;
    document.getElementById('stopBtn').disabled = false;
    
    showResult('Scanning for QR codes...', 'info');
    
    // Start scanning loop
    scanInterval = setInterval(() => {
        if (!scanning) return;
        
        try {
            // Draw video frame to canvas
            canvasContext.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            // Get image data
            const imageData = canvasContext.getImageData(0, 0, canvas.width, canvas.height);
            
            // Try to decode QR code
            const code = jsQR(imageData.data, imageData.width, imageData.height);
            
            if (code) {
                console.log('QR Code found:', code.data);
                processQRCode(code.data);
                stopScanning();
            }
        } catch (error) {
            console.error('Scan error:', error);
        }
    }, 200); // Check every 200ms
}

function stopScanning() {
    scanning = false;
    if (scanInterval) {
        clearInterval(scanInterval);
        scanInterval = null;
    }
    
    document.getElementById('startBtn').disabled = false;
    document.getElementById('stopBtn').disabled = true;
    
    showResult('Scanning stopped', 'warning');
}

function stopCamera() {
    stopScanning();
    
    if (mediaStream) {
        mediaStream.getTracks().forEach(track => track.stop());
        mediaStream = null;
    }
    
    video.style.display = 'none';
    document.getElementById('cameraStatus').style.display = 'block';
    document.getElementById('cameraStatus').textContent = 'Camera stopped';
    
    document.getElementById('initBtn').disabled = false;
    document.getElementById('startBtn').disabled = true;
    document.getElementById('stopBtn').disabled = true;
    
    showResult('Camera stopped', 'warning');
}

function processManualInput() {
    const input = document.getElementById('manualInput');
    const qrData = input.value.trim();
    
    if (!qrData) {
        showResult('Please enter QR code content', 'warning');
        return;
    }
    
    processQRCode(qrData);
    input.value = '';
}

function useSessionToken(token) {
    document.getElementById('manualInput').value = token;
    showResult('Token copied to input field. Click Submit or press Enter.', 'info');
}

function processQRCode(qrData) {
    showResult('Processing QR code...', 'info');
    
    // Send to server
    fetch('/student/process-scan', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            qr_data: qrData
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success message
            showResult(data.message, 'success');
            
            // Show attendance details
            document.getElementById('sessionName').textContent = data.data.session_name;
            document.getElementById('subjectName').textContent = data.data.subject;
            document.getElementById('lecturerName').textContent = data.data.lecturer;
            document.getElementById('attendanceStatus').textContent = data.data.status;
            document.getElementById('markedTime').textContent = data.data.marked_at;
            
            // Update UI
            document.getElementById('emptyState').classList.add('hidden');
            document.getElementById('resultContainer').classList.remove('hidden');
            document.getElementById('attendanceDetails').classList.remove('hidden');
            
        } else {
            showResult(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showResult('Failed to process QR code. Please try again.', 'error');
    });
}

function showResult(message, type) {
    const resultContainer = document.getElementById('resultContainer');
    const resultMessage = document.getElementById('resultMessage');
    
    resultContainer.classList.remove('hidden');
    
    // Clear and set classes
    resultMessage.className = 'p-4 rounded-lg mb-4';
    
    switch(type) {
        case 'success':
            resultMessage.classList.add('bg-green-100', 'text-green-800', 'border', 'border-green-200');
            break;
        case 'error':
            resultMessage.classList.add('bg-red-100', 'text-red-800', 'border', 'border-red-200');
            break;
        case 'warning':
            resultMessage.classList.add('bg-yellow-100', 'text-yellow-800', 'border', 'border-yellow-200');
            break;
        case 'info':
            resultMessage.classList.add('bg-blue-100', 'text-blue-800', 'border', 'border-blue-200');
            break;
    }
    
    resultMessage.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${getIconForType(type)} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
}

function getIconForType(type) {
    switch(type) {
        case 'success': return 'check-circle';
        case 'error': return 'exclamation-circle';
        case 'warning': return 'exclamation-triangle';
        default: return 'info-circle';
    }
}

// Allow pressing Enter in manual input
document.getElementById('manualInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        processManualInput();
    }
});

// Clean up on page unload
window.addEventListener('beforeunload', function() {
    if (mediaStream) {
        mediaStream.getTracks().forEach(track => track.stop());
    }
});
</script>

<style>
#cameraPreview {
    position: relative;
    overflow: hidden;
}

#video {
    transform: scaleX(-1); /* Mirror effect for selfie view */
}
</style>
@endsection