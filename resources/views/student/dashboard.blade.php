@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Welcome, {{ $student->user->name }}!</h1>
                <div class="flex flex-wrap items-center gap-4 mt-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-id-card mr-1"></i> {{ $student->student_id }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-graduation-cap mr-1"></i> Student
                    </span>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-indigo-100 rounded-lg">
                    <i class="fas fa-user-graduate text-3xl text-indigo-600"></i>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Last Login</p>
                    <p class="text-sm font-medium text-gray-800">{{ now()->format('M d, Y - h:i A') }}</p>
                </div>
                
            </div>
        </div>
    </div>

    <!-- Academic Information -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Department Card -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Department</p>
                    <p class="text-xl font-bold text-gray-800 mt-2">{{ $student->department->department_name ?? 'Not Assigned' }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $student->department->department_code ?? '' }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-building text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Course Card -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Course</p>
                    <p class="text-xl font-bold text-gray-800 mt-2">{{ $student->course->course_name ?? 'Not Enrolled' }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $student->course->course_code ?? '' }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-book text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Enrollment Status -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Enrollment Status</p>
                    <p class="text-xl font-bold text-gray-800 mt-2">{{ ucfirst($student->status) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-calendar-alt mr-1"></i> Enrolled on {{ $student->created_at->format('M d, Y') }}
                    </p>
                </div>
                <div class="p-3 {{ $student->status == 'active' ? 'bg-green-100' : 'bg-yellow-100' }} rounded-lg">
                    <i class="fas {{ $student->status == 'active' ? 'fa-check-circle text-green-600' : 'fa-exclamation-circle text-yellow-600' }} text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Enrolled Subjects -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">My Subjects</h3>
            <span class="px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-800">
                {{ $enrolledSubjects->count() }} Subjects
            </span>
        </div>
        
        @if($enrolledSubjects->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($enrolledSubjects as $subject)
            <div class="border rounded-lg p-4 hover:bg-gray-50 transition duration-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2 bg-indigo-100 rounded-lg">
                        <i class="fas fa-book-open text-indigo-600"></i>
                    </div>
                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                        {{ $subject->type == 'core' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                        {{ ucfirst($subject->type ?? 'Core') }}
                    </span>
                </div>
                <h4 class="font-medium text-gray-800">{{ $subject->name }}</h4>
                <p class="text-sm text-gray-600">{{ $subject->code }}</p>
                <div class="mt-3 flex items-center justify-between">
                    <div>
                        <span class="text-sm text-gray-500">{{ $subject->credit_hours }} credits</span>
                        @if($subject->semester)
                        <span class="text-sm text-gray-500 mx-2">•</span>
                        <span class="text-sm text-gray-500">Sem {{ $subject->semester }}</span>
                        @endif
                    </div>
                    <span class="text-xs font-medium px-2 py-1 rounded bg-green-100 text-green-800">
                        Enrolled
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                <i class="fas fa-book text-2xl text-gray-400"></i>
            </div>
            <p class="text-gray-500">No subjects enrolled yet</p>
            <p class="text-sm text-gray-400 mt-2">Contact your department for subject enrollment</p>
        </div>
        @endif
    </div>

    <!-- Quick Actions -->

    
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('student.profile') }}" 
               class="p-4 border border-gray-200 rounded-lg hover:border-indigo-300 hover:bg-indigo-50 transition duration-200 group">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-indigo-100 rounded-lg group-hover:bg-white">
                        <i class="fas fa-user text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">My Profile</p>
                        <p class="text-sm text-gray-600">View & edit profile</p>
                    </div>
                </div>
            </a>

            <a href="#" 
               class="p-4 border border-gray-200 rounded-lg hover:border-green-300 hover:bg-green-50 transition duration-200 group">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-green-100 rounded-lg group-hover:bg-white">
                        <i class="fas fa-calendar-check text-green-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Attendance</p>
                        <p class="text-sm text-gray-600">View attendance</p>
                    </div>
                </div>
            </a>

            <a href="#" 
               class="p-4 border border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition duration-200 group">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-purple-100 rounded-lg group-hover:bg-white">
                        <i class="fas fa-qrcode text-purple-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">QR Scanner</p>
                        <p class="text-sm text-gray-600">Mark attendance</p>
                    </div>
                </div>
            </a>

            <a href="#" 
               class="p-4 border border-gray-200 rounded-lg hover:border-yellow-300 hover:bg-yellow-50 transition duration-200 group">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-yellow-100 rounded-lg group-hover:bg-white">
                        <i class="fas fa-chart-bar text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Reports</p>
                        <p class="text-sm text-gray-600">View reports</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Attendance Summary (Placeholder for Part 5) -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Attendance Summary</h3>
            <span class="text-sm text-gray-500">This Week</span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-700">Total Sessions</p>
                <p class="text-2xl font-bold text-blue-800 mt-2">{{ $attendanceSummary['total_sessions'] }}</p>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <p class="text-sm text-green-700">Present</p>
                <p class="text-2xl font-bold text-green-800 mt-2">{{ $attendanceSummary['present'] }}</p>
            </div>
            <div class="text-center p-4 bg-red-50 rounded-lg">
                <p class="text-sm text-red-700">Absent</p>
                <p class="text-2xl font-bold text-red-800 mt-2">{{ $attendanceSummary['absent'] }}</p>
            </div>
            <div class="text-center p-4 bg-purple-50 rounded-lg">
                <p class="text-sm text-purple-700">Attendance Rate</p>
                <p class="text-2xl font-bold text-purple-800 mt-2">{{ $attendanceSummary['percentage'] }}%</p>
            </div>
        </div>
        
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <p class="text-sm text-gray-600 text-center">
                <i class="fas fa-info-circle mr-1"></i> 
                Attendance tracking will be available in Part 5 when QR sessions are created
            </p>
        </div>
    </div>
</div>
@endsection