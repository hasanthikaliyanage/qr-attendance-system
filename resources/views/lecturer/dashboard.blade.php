@extends('layouts.app')

@section('title', 'Lecturer Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Welcome, {{ $lecturer->user->name }}!</h1>
                <div class="flex flex-wrap items-center gap-4 mt-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-id-badge mr-1"></i> {{ $lecturer->employee_id }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-chalkboard-teacher mr-1"></i> Lecturer
                    </span>
                    @if($lecturer->qualification)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                        <i class="fas fa-graduation-cap mr-1"></i> {{ $lecturer->qualification }}
                    </span>
                    @endif
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-indigo-100 rounded-lg">
                    <i class="fas fa-chalkboard-teacher text-3xl text-indigo-600"></i>
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
                    <p class="text-xl font-bold text-gray-800 mt-2">{{ $lecturer->department->department_name ?? 'Not Assigned' }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $lecturer->department->department_code ?? '' }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-building text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Assigned Subjects -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Assigned Subjects</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $assignedSubjects->count() }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        Across {{ $assignedSubjects->pluck('course')->unique()->count() }} courses
                    </p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-book text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Lecturer Status -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <p class="text-xl font-bold text-gray-800 mt-2">{{ ucfirst($lecturer->status) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-calendar-alt mr-1"></i> Joined {{ $lecturer->created_at->format('M d, Y') }}
                    </p>
                </div>
                <div class="p-3 {{ $lecturer->status == 'active' ? 'bg-green-100' : 'bg-yellow-100' }} rounded-lg">
                    <i class="fas {{ $lecturer->status == 'active' ? 'fa-check-circle text-green-600' : 'fa-exclamation-circle text-yellow-600' }} text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Subjects by Course -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">My Assigned Subjects</h3>
            <span class="px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-800">
                {{ $assignedSubjects->count() }} Subjects
            </span>
        </div>
        
        @if($assignedSubjects->count() > 0)
        @foreach($assignedSubjects->groupBy('course_id') as $courseId => $subjects)
        @php
            $course = $subjects->first()->course;
        @endphp
        <div class="mb-8 last:mb-0">
            <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                <i class="fas fa-book mr-2 text-blue-600"></i>
                {{ $course->course_name ?? 'Unknown Course' }}
                <span class="ml-2 text-sm font-normal text-gray-500">({{ $course->course_code ?? '' }})</span>
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($subjects as $subject)
                <div class="border rounded-lg p-4 hover:bg-gray-50 transition duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex-1">
                            <h5 class="font-medium text-gray-800">{{ $subject->name }}</h5>
                            <p class="text-sm text-gray-600">{{ $subject->code }}</p>
                        </div>
                        <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full 
                            {{ $subject->type == 'core' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                            {{ ucfirst($subject->type ?? 'Core') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between mt-3">
                        <div class="text-sm text-gray-500">
                            {{ $subject->credit_hours }} credits
                            @if($subject->semester)
                            • Sem {{ $subject->semester }}
                            @endif
                        </div>
                        <div class="flex space-x-2">
                            <button class="text-xs text-indigo-600 hover:text-indigo-800">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-xs text-green-600 hover:text-green-800">
                                <i class="fas fa-qrcode"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
        @else
        <div class="text-center py-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                <i class="fas fa-book-open text-2xl text-gray-400"></i>
            </div>
            <p class="text-gray-500">No subjects assigned yet</p>
            <p class="text-sm text-gray-400 mt-2">Contact your department head for subject assignments</p>
        </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('lecturer.profile') }}" 
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
                        <i class="fas fa-qrcode text-green-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Create QR</p>
                        <p class="text-sm text-gray-600">Attendance session</p>
                    </div>
                </div>
            </a>

            <a href="#" 
               class="p-4 border border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition duration-200 group">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-purple-100 rounded-lg group-hover:bg-white">
                        <i class="fas fa-chart-bar text-purple-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Attendance</p>
                        <p class="text-sm text-gray-600">View reports</p>
                    </div>
                </div>
            </a>

            <a href="#" 
               class="p-4 border border-gray-200 rounded-lg hover:border-yellow-300 hover:bg-yellow-50 transition duration-200 group">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-yellow-100 rounded-lg group-hover:bg-white">
                        <i class="fas fa-calendar-alt text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Timetable</p>
                        <p class="text-sm text-gray-600">View schedule</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Upcoming Classes (Placeholder) -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Upcoming Classes</h3>
            <span class="text-sm text-gray-500">Today</span>
        </div>
        
        <div class="space-y-3">
            @for($i = 1; $i <= 2; $i++)
            <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-book text-blue-600"></i>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-800">Database Management Systems</h4>
                        <p class="text-sm text-gray-600">CS302 • Room: LAB-{{ $i }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-medium text-gray-800">09:00 AM - 10:30 AM</p>
                    <p class="text-sm text-gray-500">In 15 minutes</p>
                </div>
            </div>
            @endfor
        </div>
        
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <p class="text-sm text-gray-600 text-center">
                <i class="fas fa-info-circle mr-1"></i> 
                QR session creation and class scheduling will be available in Part 4
            </p>
        </div>
    </div>
</div>
@endsection