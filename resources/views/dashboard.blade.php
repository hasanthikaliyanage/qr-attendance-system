@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Students Card -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Students</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalStudents }}</p>
                    <p class="text-xs text-green-600 mt-1">
                        <i class="fas fa-arrow-up mr-1"></i> 12% from last month
                    </p>
                </div>
                <div class="p-3 bg-indigo-100 rounded-lg">
                    <i class="fas fa-user-graduate text-2xl text-indigo-600"></i>
                </div>
            </div>
            <a href="{{ route('admin.students.index') }}" 
               class="block mt-4 text-sm text-indigo-600 hover:text-indigo-500">
                View all students →
            </a>
        </div>

        <!-- Total Lecturers Card -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Lecturers</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalLecturers }}</p>
                    <p class="text-xs text-green-600 mt-1">
                        <i class="fas fa-arrow-up mr-1"></i> 5% from last month
                    </p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-chalkboard-teacher text-2xl text-green-600"></i>
                </div>
            </div>
            <a href="{{ route('admin.lecturers.index') }}" 
               class="block mt-4 text-sm text-indigo-600 hover:text-indigo-500">
                View all lecturers →
            </a>
        </div>

        <!-- Departments Card -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Departments</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalDepartments }}</p>
                    <p class="text-xs text-blue-600 mt-1">
                        <i class="fas fa-building mr-1"></i> All active
                    </p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-building text-2xl text-blue-600"></i>
                </div>
            </div>
            <a href="#" class="block mt-4 text-sm text-indigo-600 hover:text-indigo-500">
                View departments →
            </a>
        </div>

        <!-- Courses Card -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Courses</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalCourses }}</p>
                    <p class="text-xs text-purple-600 mt-1">
                        <i class="fas fa-book mr-1"></i> 9 active courses
                    </p>
                </div>
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i class="fas fa-book text-2xl text-purple-600"></i>
                </div>
            </div>
            <a href="#" class="block mt-4 text-sm text-indigo-600 hover:text-indigo-500">
                View courses →
            </a>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.students.create') }}" 
               class="p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 transition duration-200 group">
                <div class="flex items-center justify-center space-x-3">
                    <div class="p-2 bg-indigo-100 rounded-lg group-hover:bg-indigo-200">
                        <i class="fas fa-user-plus text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Add New Student</p>
                        <p class="text-sm text-gray-600">Create student account</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.lecturers.create') }}" 
               class="p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-green-500 hover:bg-green-50 transition duration-200 group">
                <div class="flex items-center justify-center space-x-3">
                    <div class="p-2 bg-green-100 rounded-lg group-hover:bg-green-200">
                        <i class="fas fa-chalkboard-teacher text-green-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Add New Lecturer</p>
                        <p class="text-sm text-gray-600">Create lecturer account</p>
                    </div>
                </div>
            </a>

            <!-- QR Session Creation Link - Updated with correct route -->
            
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Students -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Recent Students</h3>
                <a href="{{ route('admin.students.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                    View all
                </a>
            </div>
            <div class="space-y-4">
                @php
                    $recentStudents = App\Models\Student::with('user')->latest()->take(5)->get();
                @endphp
                @forelse($recentStudents as $student)
                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-indigo-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $student->user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $student->student_id }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 text-xs font-medium rounded-full 
                        {{ $student->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($student->status) }}
                    </span>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">No students found.</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Lecturers -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Recent Lecturers</h3>
                <a href="{{ route('admin.lecturers.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                    View all
                </a>
            </div>
            <div class="space-y-4">
                @php
                    $recentLecturers = App\Models\Lecturer::with('user')->latest()->take(5)->get();
                @endphp
                @forelse($recentLecturers as $lecturer)
                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-chalkboard-teacher text-green-600"></i>
                        </div>
                        <div>
<p class="font-medium text-gray-800">{{ $lecturer->user->name ?? 'Unknown User' }}</p>                            <p class="text-sm text-gray-600">{{ $lecturer->employee_id }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 text-xs font-medium rounded-full 
                        {{ $lecturer->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($lecturer->status) }}
                    </span>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">No lecturers found.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">System Status</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-database text-blue-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Database</p>
                        <p class="text-sm text-green-600">Connected</p>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-envelope text-green-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Email Service</p>
                        <p class="text-sm text-green-600">Active</p>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i class="fas fa-qrcode text-purple-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">QR Generation</p>
                        <p class="text-sm text-green-600">Ready</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-refresh dashboard data every 30 seconds
    setInterval(() => {
        window.location.reload();
    }, 30000);
</script>
@endsection