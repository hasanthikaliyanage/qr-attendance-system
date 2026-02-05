@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">QR Sessions</h1>

    <!-- Quick Action Buttons -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <button onclick="openSimpleWebcamScanner()" 
           class="bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-lg shadow-md flex items-center justify-center">
            <i class="fas fa-camera text-2xl mr-3"></i>
            <div>
                <h3 class="font-bold">Webcam Scanner</h3>
                <p class="text-sm opacity-90">Use your camera to scan</p>
            </div>
        </button>
        
        <a href="{{ route('student.qr_scanner') }}" 
           class="bg-green-600 hover:bg-green-700 text-white p-4 rounded-lg shadow-md flex items-center justify-center">
            <i class="fas fa-qrcode text-2xl mr-3"></i>
            <div>
                <h3 class="font-bold">Manual Scanner</h3>
                <p class="text-sm opacity-90">Scan with device camera</p>
            </div>
        </a>
        
        <a href="{{ route('student.attendance.report') }}" 
           class="bg-purple-600 hover:bg-purple-700 text-white p-4 rounded-lg shadow-md flex items-center justify-center">
            <i class="fas fa-chart-bar text-2xl mr-3"></i>
            <div>
                <h3 class="font-bold">Attendance Report</h3>
                <p class="text-sm opacity-90">View your statistics</p>
            </div>
        </a>
    </div>

    <!-- Active Sessions -->
    @if($activeSessions->count() > 0)
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Active Sessions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($activeSessions as $session)
            <div class="bg-white rounded-lg shadow-lg border border-green-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">{{ $session->subject->name ?? 'N/A' }}</h3>
                            <p class="text-sm text-gray-500">{{ $session->subject->code ?? 'N/A' }}</p>
                        </div>
                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                            Active Now
                        </span>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-calendar-day mr-2 text-blue-500"></i>
                            <span>{{ \Carbon\Carbon::parse($session->session_date)->format('d M Y') }}</span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-clock mr-2 text-blue-500"></i>
                            <span>
                                {{ \Carbon\Carbon::parse($session->start_time)->format('h:i A') }} - 
                                {{ \Carbon\Carbon::parse($session->end_time)->format('h:i A') }}
                            </span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>
                            <span>{{ $session->department->name ?? 'N/A' }} - {{ $session->course->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-chalkboard-teacher mr-2 text-blue-500"></i>
                            <span>{{ optional($session->lecturer)->user->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="{{ route('student.qr_sessions.scan', $session->id) }}" 
                           class="flex-1 text-center bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-semibold text-sm">
                            <i class="fas fa-qrcode mr-1"></i> Manual Scan
                        </a>
                        <button onclick="openSessionScanner('{{ $session->qr_token }}', {{ $session->id }})" 
                                class="flex-1 text-center bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 font-semibold text-sm">
                            <i class="fas fa-camera mr-1"></i> Webcam Scan
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Simple Webcam Scanner Modal -->
    <div id="simpleScannerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-800">Webcam QR Scanner</h3>
                        <button onclick="closeSimpleScanner()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-2xl"></i>
                        </button>
                    </div>
                    
                    <div id="scannerContainer" class="mb-4">
                        <div id="reader" class="w-full h-64 bg-gray-100 rounded-lg flex items-center justify-center">
                            <p class="text-gray-500">Scanner will appear here</p>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <button onclick="startSimpleScanner()" id="simpleStartBtn"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium">
                            <i class="fas fa-camera mr-2"></i> Start Scanner
                        </button>
                        <button onclick="stopSimpleScanner()" id="simpleStopBtn" disabled
                                class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-3 rounded-lg font-medium">
                            <i class="fas fa-stop mr-2"></i> Stop Scanner
                        </button>
                    </div>
                    
                    <!-- Manual Input -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="font-semibold text-gray-700 mb-2">Or Enter QR Code Manually</h4>
                        <div class="flex">
                            <input type="text" id="manualTokenInput" 
                                   placeholder="Paste QR token here" 
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button onclick="processManualToken()" 
                                    class="bg-purple-600 text-white px-4 py-2 rounded-r-lg hover:bg-purple-700">
                                Submit
                            </button>
                        </div>
                        <p id="currentTokenDisplay" class="text-sm text-gray-500 mt-2 hidden">
                            Current Token: <span id="tokenPreview" class="font-mono"></span>
                        </p>
                    </div>
                    
                    <!-- Results -->
                    <div id="scannerResult" class="mt-6 hidden">
                        <div class="p-4 rounded-lg mb-4" id="scannerMessage">
                            <!-- Results will appear here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Sessions -->
    @if($upcomingSessions->count() > 0)
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Upcoming Sessions</h2>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lecturer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($upcomingSessions as $session)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $session->subject->name ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">{{ $session->subject->code ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($session->session_date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($session->start_time)->format('h:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ optional($session->lecturer)->user->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Upcoming
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Attendance -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Recent Attendance</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($attendances as $attendance)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ optional($attendance->qrSession)->subject->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($attendance->marked_at)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($attendance->marked_at)->format('h:i A') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($attendance->status == 'present')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i> Present
                            </span>
                            @elseif($attendance->status == 'late')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i> Late
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($attendance->scan_method == 'webcam')
                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">Webcam</span>
                            @else
                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded">Manual</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No attendance records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $attendances->links() }}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Simple scanner styles */
    #reader video {
        width: 100% !important;
        border-radius: 8px;
    }
    
    .fixed {
        z-index: 9999;
    }
    
    /* Make sure Font Awesome icons show */
    .fas {
        font-family: "Font Awesome 6 Free" !important;
    }
</style>
@endpush

@push('scripts')
<!-- Simple QR Scanner Library -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
let simpleScanner = null;
let currentToken = '';

// ✅ FIXED: Create the message element if it doesn't exist
function ensureScannerMessageElement() {
    let scannerMessage = document.getElementById('scannerMessage');
    const scannerResult = document.getElementById('scannerResult');
    
    if (!scannerResult) {
        console.error('scannerResult element not found');
        return null;
    }
    
    if (!scannerMessage) {
        console.log('Creating scannerMessage element...');
        scannerMessage = document.createElement('div');
        scannerMessage.id = 'scannerMessage';
        scannerMessage.className = 'p-4 rounded-lg mb-4 bg-blue-100 text-blue-800 border border-blue-200';
        scannerResult.prepend(scannerMessage); // Add at the beginning
    }
    
    return scannerMessage;
}

// ✅ FIXED: showScannerMessage function
function showScannerMessage(message, type) {
    console.log('Showing message:', message, 'Type:', type);
    
    // Ensure the element exists
    const scannerMessage = ensureScannerMessageElement();
    const scannerResult = document.getElementById('scannerResult');
    
    if (!scannerMessage || !scannerResult) {
        console.error('Cannot show message - elements not found');
        return;
    }
    
    // Make sure result container is visible
    scannerResult.classList.remove('hidden');
    
    // Set the classes based on type
    scannerMessage.className = 'p-4 rounded-lg mb-4';
    
    switch(type) {
        case 'success':
            scannerMessage.classList.add('bg-green-100', 'text-green-800', 'border', 'border-green-200');
            break;
        case 'error':
            scannerMessage.classList.add('bg-red-100', 'text-red-800', 'border', 'border-red-200');
            break;
        case 'warning':
            scannerMessage.classList.add('bg-yellow-100', 'text-yellow-800', 'border', 'border-yellow-200');
            break;
        default:
            scannerMessage.classList.add('bg-blue-100', 'text-blue-800', 'border', 'border-blue-200');
    }
    
    // Set content with fallback for Font Awesome
    let iconHtml = '<i class="fas fa-info-circle mr-2"></i>';
    
    if (type === 'success') {
        iconHtml = '<i class="fas fa-check-circle mr-2"></i>';
    } else if (type === 'error') {
        iconHtml = '<i class="fas fa-exclamation-circle mr-2"></i>';
    } else if (type === 'warning') {
        iconHtml = '<i class="fas fa-exclamation-triangle mr-2"></i>';
    }
    
    scannerMessage.innerHTML = `
        <div class="flex items-center">
            ${iconHtml}
            <span>${message}</span>
        </div>
    `;
}

// Simple function to open scanner
function openSimpleWebcamScanner() {
    currentToken = '';
    document.getElementById('simpleScannerModal').classList.remove('hidden');
    document.getElementById('manualTokenInput').value = '';
    document.getElementById('currentTokenDisplay').classList.add('hidden');
    document.getElementById('scannerResult').classList.add('hidden');
    
    // Reset button states
    document.getElementById('simpleStartBtn').disabled = false;
    document.getElementById('simpleStopBtn').disabled = true;
    
    // Clear any existing scanner
    if (simpleScanner) {
        simpleScanner.clear().catch(() => {});
        simpleScanner = null;
    }
    
    // Clear scanner container
    document.getElementById('reader').innerHTML = '<p class="text-gray-500">Scanner will appear here</p>';
    
    showScannerMessage('Ready to scan. Click "Start Scanner" to begin.', 'info');
}

function openSessionScanner(token, sessionId) {
    currentToken = token;
    document.getElementById('simpleScannerModal').classList.remove('hidden');
    document.getElementById('manualTokenInput').value = token;
    document.getElementById('tokenPreview').textContent = token.substring(0, 20) + '...';
    document.getElementById('currentTokenDisplay').classList.remove('hidden');
    document.getElementById('scannerResult').classList.add('hidden');
    
    // Reset button states
    document.getElementById('simpleStartBtn').disabled = false;
    document.getElementById('simpleStopBtn').disabled = true;
    
    // Clear any existing scanner
    if (simpleScanner) {
        simpleScanner.clear().catch(() => {});
        simpleScanner = null;
    }
    
    // Clear scanner container
    document.getElementById('reader').innerHTML = '<p class="text-gray-500">Scanner will appear here</p>';
    
    showScannerMessage(`Ready to scan session: ${sessionId}`, 'info');
}

function closeSimpleScanner() {
    console.log('Closing scanner...');
    
    // Stop scanner if running
    if (simpleScanner) {
        simpleScanner.clear().catch(err => {
            console.log('Scanner already stopped');
        });
        simpleScanner = null;
    }
    
    // Hide modal
    document.getElementById('simpleScannerModal').classList.add('hidden');
}

// ✅ FIXED: startSimpleScanner function
function startSimpleScanner() {
    console.log('Starting scanner...');
    
    if (simpleScanner) {
        showScannerMessage('Scanner is already running', 'warning');
        return;
    }
    
    try {
        // Initialize scanner
        simpleScanner = new Html5QrcodeScanner(
            "reader",
            { 
                fps: 10,
                qrbox: { width: 250, height: 250 },
                rememberLastUsedCamera: true,
                supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
            },
            false
        );
        
        // Render scanner
        simpleScanner.render(onSimpleScanSuccess, onSimpleScanError);
        
        // Update button states
        document.getElementById('simpleStartBtn').disabled = true;
        document.getElementById('simpleStopBtn').disabled = false;
        
        showScannerMessage('Scanner started. Point camera at QR code.', 'success');
        
    } catch (error) {
        console.error('Scanner error:', error);
        showScannerMessage(`Error starting scanner: ${error.message}`, 'error');
    }
}

// ✅ FIXED: stopSimpleScanner function
function stopSimpleScanner() {
    console.log('Stopping scanner...');
    
    if (!simpleScanner) {
        console.log('No scanner to stop');
        return;
    }
    
    // Clear scanner
    simpleScanner.clear().then(() => {
        console.log('Scanner cleared successfully');
        simpleScanner = null;
        
        // Update button states
        document.getElementById('simpleStartBtn').disabled = false;
        document.getElementById('simpleStopBtn').disabled = true;
        
        showScannerMessage('Scanner stopped.', 'warning');
        
    }).catch(error => {
        console.error('Error clearing scanner:', error);
        simpleScanner = null;
        
        // Still update buttons
        document.getElementById('simpleStartBtn').disabled = false;
        document.getElementById('simpleStopBtn').disabled = true;
    });
}

function onSimpleScanSuccess(decodedText) {
    console.log('QR Code detected:', decodedText);
    
    // Stop scanner immediately
    stopSimpleScanner();
    
    // Process the QR code
    processSimpleQRCode(decodedText);
}

function onSimpleScanError(error) {
    // Ignore normal scan errors (no QR code found)
    console.log('Scan error (normal):', error);
}

function processManualToken() {
    const tokenInput = document.getElementById('manualTokenInput');
    const token = tokenInput.value.trim();
    
    if (!token) {
        showScannerMessage('Please enter a QR token', 'warning');
        return;
    }
    
    // Stop scanner if running
    if (simpleScanner) {
        stopSimpleScanner();
    }
    
    processSimpleQRCode(token);
    tokenInput.value = '';
}

function processSimpleQRCode(qrData) {
    console.log('Processing QR code:', qrData);
    
    showScannerMessage('Processing attendance...', 'info');
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (!csrfToken) {
        showScannerMessage('CSRF token not found', 'error');
        return;
    }
    
    // ✅ 100% වැඩ කරන URL එක
    const apiUrl = '/student/process-scan';
    
    console.log('Sending POST to:', apiUrl);
    console.log('QR Data:', qrData);
    console.log('CSRF Token exists:', !!csrfToken);
    
    // Send request to server
    fetch(apiUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            qr_data: qrData
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            return response.text().then(text => {
                console.log('Error response text:', text);
                try {
                    const errorData = JSON.parse(text);
                    throw new Error(errorData.message || `Server error: ${response.status}`);
                } catch (e) {
                    throw new Error(`Server error ${response.status}: ${text.substring(0, 100)}`);
                }
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Success response data:', data);
        
        if (data.success) {
            let successMessage = `✅ ${data.message}`;
            
            if (data.data) {
                successMessage += `
                    <div class="mt-3 p-2 bg-green-50 border border-green-200 rounded">
                        <div><strong>Session:</strong> ${data.data.session_name || 'N/A'}</div>
                        <div><strong>Subject:</strong> ${data.data.subject || 'N/A'}</div>
                        <div><strong>Status:</strong> <span class="font-bold ${data.data.status === 'present' ? 'text-green-600' : 'text-yellow-600'}">${data.data.status || 'N/A'}</span></div>
                        <div><strong>Time:</strong> ${data.data.marked_at || 'N/A'}</div>
                    </div>
                `;
            }
            
            showScannerMessage(successMessage, 'success');
            
            // Close modal and reload after success
            setTimeout(() => {
                const modal = document.getElementById('simpleScannerModal');
                if (modal) modal.classList.add('hidden');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }, 3000);
            
        } else {
            showScannerMessage(`❌ ${data.message}`, 'error');
            
            // Restart scanner after error
            setTimeout(() => {
                startSimpleScanner();
            }, 3000);
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        showScannerMessage(`❌ Error: ${error.message}`, 'error');
        
        // Restart scanner after error
        setTimeout(() => {
            startSimpleScanner();
        }, 3000);
    });
}
    
    

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Close modal when clicking outside
    const modal = document.getElementById('simpleScannerModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeSimpleScanner();
            }
        });
    }
    
    // Close with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('simpleScannerModal');
            if (modal && !modal.classList.contains('hidden')) {
                closeSimpleScanner();
            }
        }
    });
    
    // Enter key in manual input
    const manualInput = document.getElementById('manualTokenInput');
    if (manualInput) {
        manualInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                processManualToken();
            }
        });
    }
});

// Font Awesome fallback
if (typeof window.FontAwesome === 'undefined') {
    console.warn('Font Awesome not loaded, using emoji fallback');
    
    // Replace Font Awesome icons with emojis
    const iconReplacements = {
        'fa-camera': '📷',
        'fa-qrcode': '📱',
        'fa-chart-bar': '📊',
        'fa-times': '✕',
        'fa-check': '✓',
        'fa-exclamation-circle': '⚠',
        'fa-info-circle': 'ℹ',
        'fa-calendar-day': '📅',
        'fa-clock': '🕐',
        'fa-map-marker-alt': '📍',
        'fa-chalkboard-teacher': '👨‍🏫',
        'fa-stop': '⏹',
        'fa-check-circle': '✅',
        'fa-exclamation-triangle': '⚠',
        'fa-play': '▶'
    };
    
    // Replace icons on page load
    setTimeout(() => {
        document.querySelectorAll('.fas').forEach(icon => {
            const classes = Array.from(icon.classList);
            const faClass = classes.find(cls => cls.startsWith('fa-'));
            if (faClass && iconReplacements[faClass]) {
                icon.textContent = iconReplacements[faClass];
                icon.classList.remove('fas');
            }
        });
    }, 100);
}
</script>
@endpush
