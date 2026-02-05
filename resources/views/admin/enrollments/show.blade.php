@extends('layouts.app')

@section('title', 'Enrollment Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Enrollment Details</h1>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.enrollments.edit', $enrollment) }}" 
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </a>
                    <a href="{{ route('admin.enrollments.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        <!-- Enrollment Details -->
        <div class="p-6">
            <!-- Student Information -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Student Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-indigo-100 rounded-lg">
                                <i class="fas fa-user text-indigo-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Student Name</p>
                                <p class="font-medium text-gray-800">{{ $enrollment->student->user->name }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <i class="fas fa-id-card text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Student ID</p>
                                <p class="font-medium text-gray-800">{{ $enrollment->student->student_id }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <i class="fas fa-building text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Department</p>
                                <p class="font-medium text-gray-800">{{ $enrollment->student->department->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Academic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-6 bg-gray-50 rounded-lg">
                        <h3 class="font-semibold text-gray-800 mb-3">Course Details</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Course Name:</span>
                                <span class="font-medium">{{ $enrollment->course->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Course Code:</span>
                                <span class="font-medium">{{ $enrollment->course->code }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Department:</span>
                                <span class="font-medium">{{ $enrollment->course->department->name }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6 bg-gray-50 rounded-lg">
                        <h3 class="font-semibold text-gray-800 mb-3">Subject Details</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subject Name:</span>
                                <span class="font-medium">{{ $enrollment->subject->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subject Code:</span>
                                <span class="font-medium">{{ $enrollment->subject->code }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Credit Hours:</span>
                                <span class="font-medium">{{ $enrollment->subject->credit_hours }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enrollment Status -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Enrollment Status</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">Academic Year</p>
                        <p class="text-xl font-bold text-gray-800">{{ $enrollment->academic_year }}</p>
                    </div>
                    
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">Semester</p>
                        <p class="text-xl font-bold text-gray-800">{{ $enrollment->semester }}</p>
                    </div>
                    
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">Status</p>
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                            {{ $enrollment->status == 'enrolled' ? 'bg-green-100 text-green-800' : 
                               ($enrollment->status == 'completed' ? 'bg-blue-100 text-blue-800' : 
                               ($enrollment->status == 'dropped' ? 'bg-yellow-100 text-yellow-800' : 
                               'bg-red-100 text-red-800')) }}">
                            {{ ucfirst($enrollment->status) }}
                        </span>
                    </div>
                    
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">Grade</p>
                        <p class="text-xl font-bold text-gray-800">
                            {{ $enrollment->grade ? $enrollment->grade : 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Dates -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Dates</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">Enrollment Date</p>
                        <p class="font-medium text-gray-800">
                            {{ $enrollment->enrollment_date ? $enrollment->enrollment_date->format('F d, Y') : 'N/A' }}
                        </p>
                    </div>
                    
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">Last Updated</p>
                        <p class="font-medium text-gray-800">
                            {{ $enrollment->updated_at->format('F d, Y h:i A') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection