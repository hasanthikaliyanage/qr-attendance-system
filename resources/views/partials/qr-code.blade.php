{{-- resources/views/partials/qr-code.blade.php --}}
<div class="text-center">
    <div class="mb-4 p-4 bg-white inline-block rounded-lg border">
        {!! $qrCode !!}
    </div>
    <p class="text-sm text-gray-600 mb-2">Scan this QR code to mark attendance</p>
    <div class="text-xs text-gray-500 space-y-1">
        <p>Session: {{ $session->title }}</p>
        <p>Subject: {{ $session->subject->name }}</p>
        <p>Time: {{ $session->start_time->format('M d, Y H:i') }} - {{ $session->end_time->format('H:i') }}</p>
    </div>
</div>