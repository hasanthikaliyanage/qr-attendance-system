@extends('layouts.app')

@section('title', 'My Profile - Student')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border">
        <!-- Profile Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-t-xl p-6 text-white">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center">
                        <i class="fas fa-user-graduate text-4xl text-blue-600"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">{{ $student->user->name }}</h1>
                        <p class="text-blue-100">{{ $student->student_id }}</p>
                    </div>
                </div>
                <div class="mt-4 md:mt-0 text-center md:text-right">
                    <span class="inline-block px-3 py-1 text-sm font-medium bg-white/20 rounded-full">
                        {{ $student->status == 'active' ? 'Active Student' : ucfirst($student->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Profile Content -->
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Personal Info -->
                <div class="lg:col-span-2">
                    <!-- Personal Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-user-circle mr-2 text-blue-600"></i> Personal Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <div class="p-3 bg-gray-50 rounded-lg border">
                                    <p class="text-gray-800">{{ $student->user->name }}</p>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <div class="p-3 bg-gray-50 rounded-lg border">
                                    <p class="text-gray-800">{{ $student->user->email }}</p>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Student ID</label>
                                <div class="p-3 bg-gray-50 rounded-lg border">
                                    <p class="text-gray-800">{{ $student->student_id }}</p>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">NIC Number</label>
                                <div class="p-3 bg-gray-50 rounded-lg border">
                                    <p class="text-gray-800">{{ $student->nic ?? 'Not provided' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-address-book mr-2 text-green-600"></i> Contact Information
                        </h3>
                        <form action="{{ route('student.profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                    <input type="text" name="phone" id="phone" value="{{ $student->phone ?? '' }}" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="Enter phone number">
                                </div>
                                
                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                    <input type="text" name="address" id="address" value="{{ $student->address ?? '' }}" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="Enter address">
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <button type="submit" 
                                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                    <i class="fas fa-save mr-2"></i>Update Contact Information
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right Column - Academic Info -->
                <div>
                    <!-- Academic Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-graduation-cap mr-2 text-purple-600"></i> Academic Information
                        </h3>
                        <div class="space-y-4">
                            <div class="p-4 bg-gray-50 rounded-lg border">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-building text-gray-400 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-600">Department</span>
                                </div>
                                <p class="font-medium text-gray-800">{{ $student->department->department_name ?? 'Not assigned' }}</p>
                                <p class="text-sm text-gray-600">{{ $student->department->department_code ?? '' }}</p>
                            </div>
                            
                            <div class="p-4 bg-gray-50 rounded-lg border">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-book text-gray-400 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-600">Course</span>
                                </div>
                                <p class="font-medium text-gray-800">{{ $student->course->course_name ?? 'Not enrolled' }}</p>
                                <p class="text-sm text-gray-600">{{ $student->course->course_code ?? '' }}</p>
                            </div>
                            
                            <div class="p-4 bg-gray-50 rounded-lg border">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-600">Enrollment Date</span>
                                </div>
                                <p class="font-medium text-gray-800">{{ $student->created_at->format('F d, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Account Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-shield-alt mr-2 text-red-600"></i> Account Information
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-lock text-gray-400 mr-2"></i>
                                    <span class="text-sm text-gray-600">Password</span>
                                </div>
                                <a href="{{ route('password.change') }}" 
                                   class="text-sm text-blue-600 hover:text-blue-800">
                                    Change
                                </a>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-check text-gray-400 mr-2"></i>
                                    <span class="text-sm text-gray-600">Member Since</span>
                                </div>
                                <span class="text-sm text-gray-800">{{ $student->user->created_at->format('M Y') }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-sign-in-alt text-gray-400 mr-2"></i>
                                    <span class="text-sm text-gray-600">Last Login</span>
                                </div>
<span class="text-sm text-gray-800">
    @if($student->user->last_login_at)
        {{ \Carbon\Carbon::parse($student->user->last_login_at)->format('M d, Y') }}
    @else
        Never
    @endif
</span>                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-6 text-center">
        <a href="{{ route('student.dashboard') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
        </a>
    </div>
</div>
@endsection