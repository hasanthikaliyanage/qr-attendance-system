@extends('layouts.app')

@section('title', 'Create QR Session')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('lecturer.qr_sessions.index') }}" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Back to Sessions
        </a>
        <h1 class="text-3xl font-bold text-gray-800">Create New QR Session</h1>
        <p class="text-gray-600 mt-1">Generate a QR code for attendance tracking</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-3xl">
        <form action="{{ route('lecturer.qr_sessions.store') }}" method="POST" id="createSessionForm">
            @csrf

            <!-- Session Name -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Session Name <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="session_name" 
                       value="{{ old('session_name') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="e.g., Database Systems - Lecture 5"
                       required>
                @error('session_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea name="description" 
                          rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Optional description or notes">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Department -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Department <span class="text-red-500">*</span>
                </label>
                <select name="department_id" 
                        id="department_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required>
                    <option value="">Select Department</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }} ({{ $department->code }})
                        </option>
                    @endforeach
                </select>
                @error('department_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Course -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Course <span class="text-red-500">*</span>
                </label>
                <select name="course_id" 
                        id="course_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required>
                    <option value="">Select Department First</option>
                </select>
                @error('course_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Subject -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Subject <span class="text-red-500">*</span>
                </label>
                <select name="subject_id" 
                        id="subject_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required>
                    <option value="">Select Course First</option>
                </select>
                @error('subject_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date and Time -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Session Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="session_date" 
                           value="{{ old('session_date', date('Y-m-d')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                    @error('session_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Start Time <span class="text-red-500">*</span>
                    </label>
                    <input type="time" 
                           name="start_time" 
                           value="{{ old('start_time') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                    @error('start_time')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        End Time <span class="text-red-500">*</span>
                    </label>
                    <input type="time" 
                           name="end_time" 
                           value="{{ old('end_time') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                    @error('end_time')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Duration -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Duration (minutes) <span class="text-red-500">*</span>
                </label>
                <select name="duration_minutes"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required>
                    <option value="15" {{ old('duration_minutes') == 15 ? 'selected' : '' }}>15 minutes</option>
                    <option value="30" {{ old('duration_minutes', 30) == 30 ? 'selected' : '' }}>30 minutes</option>
                    <option value="45" {{ old('duration_minutes') == 45 ? 'selected' : '' }}>45 minutes</option>
                    <option value="60" {{ old('duration_minutes') == 60 ? 'selected' : '' }}>1 hour</option>
                    <option value="90" {{ old('duration_minutes') == 90 ? 'selected' : '' }}>1.5 hours</option>
                    <option value="120" {{ old('duration_minutes') == 120 ? 'selected' : '' }}>2 hours</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">QR code will be valid for this duration after session starts</p>
                @error('duration_minutes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('lecturer.qr_sessions.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                    <i class="fas fa-qrcode mr-2"></i> Create Session & Generate QR Code
                </button>
            </div>
        </form>
    </div>
</div>

<!-- AJAX Script for Dynamic Dropdowns -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const departmentSelect = document.getElementById('department_id');
    const courseSelect = document.getElementById('course_id');
    const subjectSelect = document.getElementById('subject_id');

    // When department changes, load courses
    departmentSelect.addEventListener('change', function() {
        const departmentId = this.value;
        
        courseSelect.innerHTML = '<option value="">Loading...</option>';
        subjectSelect.innerHTML = '<option value="">Select Course First</option>';

        if (!departmentId) {
            courseSelect.innerHTML = '<option value="">Select Department First</option>';
            return;
        }

        fetch(`/lecturer/ajax/courses-by-department/${departmentId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                courseSelect.innerHTML = '<option value="">Select Course</option>';
                
                // Check if data is an array
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(course => {
                        courseSelect.innerHTML += `<option value="${course.id}">${course.name} (${course.code})</option>`;
                    });
                } else if (data.error) {
                    console.error('Server error:', data.error);
                    courseSelect.innerHTML = '<option value="">Error loading courses</option>';
                } else {
                    console.log('No courses found or data is not an array:', data);
                    courseSelect.innerHTML = '<option value="">No courses found</option>';
                }
            })
            .catch(error => {
                console.error('Error fetching courses:', error);
                courseSelect.innerHTML = '<option value="">Error loading courses</option>';
            });
    });

    // When course changes, load subjects
    courseSelect.addEventListener('change', function() {
        const courseId = this.value;
        
        subjectSelect.innerHTML = '<option value="">Loading...</option>';

        if (!courseId) {
            subjectSelect.innerHTML = '<option value="">Select Course First</option>';
            return;
        }

        fetch(`/lecturer/ajax/subjects-by-course/${courseId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                subjectSelect.innerHTML = '<option value="">Select Subject</option>';
                
                // Check if data is an array
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(subject => {
                        subjectSelect.innerHTML += `<option value="${subject.id}">${subject.name} (${subject.code})</option>`;
                    });
                } else {
                    console.log('No subjects found or data is not an array');
                    subjectSelect.innerHTML = '<option value="">No subjects found</option>';
                }
            })
            .catch(error => {
                console.error('Error fetching subjects:', error);
                subjectSelect.innerHTML = '<option value="">Error loading subjects</option>';
            });
    });
});
</script>
@endsection