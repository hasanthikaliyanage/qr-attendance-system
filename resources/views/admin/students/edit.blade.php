@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Edit Student</h1>
        <p class="text-gray-600">Update student information for {{ $student->user->name }}</p>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.students.update', $student) }}" method="POST" id="studentForm">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-xl shadow-sm border p-6 space-y-6">
            <!-- Personal Information -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Personal Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Full Name *
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $student->user->name) }}"
                               required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address *
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $student->user->email) }}"
                               required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NIC -->
                    <div>
                        <label for="nic" class="block text-sm font-medium text-gray-700 mb-2">
                            NIC Number *
                        </label>
                        <input type="text" 
                               id="nic" 
                               name="nic" 
                               value="{{ old('nic', $student->user->nic) }}"
                               required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @error('nic')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number *
                        </label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone', $student->user->phone) }}"
                               required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Address *
                        </label>
                        <textarea id="address" 
                                  name="address" 
                                  rows="3"
                                  required
                                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('address', $student->user->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                            Date of Birth
                        </label>
                        <input type="date" 
                               id="date_of_birth" 
                               name="date_of_birth" 
                               value="{{ old('date_of_birth', $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '') }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>

                    <!-- Gender -->
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                            Gender
                        </label>
                        <select id="gender" 
                                name="gender"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">Select Gender</option>
                            <option value="male" {{ (old('gender', $student->gender) == 'male') ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ (old('gender', $student->gender) == 'female') ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ (old('gender', $student->gender) == 'other') ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <!-- Emergency Contact -->
                    <div>
                        <label for="emergency_contact" class="block text-sm font-medium text-gray-700 mb-2">
                            Emergency Contact
                        </label>
                        <input type="text" 
                               id="emergency_contact" 
                               name="emergency_contact" 
                               value="{{ old('emergency_contact', $student->emergency_contact) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>

                    <!-- Enrollment Date -->
                    <div>
                        <label for="enrollment_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Enrollment Date
                        </label>
                        <input type="date" 
                               id="enrollment_date" 
                               name="enrollment_date" 
                               value="{{ old('enrollment_date', $student->enrollment_date ? $student->enrollment_date->format('Y-m-d') : '') }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Academic Information</h2>
                
                <!-- Student ID -->
                <div class="mb-6">
                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Student ID *
                    </label>
                    <input type="text" 
                           id="student_id" 
                           name="student_id" 
                           value="{{ old('student_id', $student->student_id) }}"
                           required
                           class="w-full md:w-1/2 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('student_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="mb-6">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status *
                    </label>
                    <select id="status" 
                            name="status"
                            required
                            class="w-full md:w-1/2 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="active" {{ (old('status', $student->status) == 'active') ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ (old('status', $student->status) == 'inactive') ? 'selected' : '' }}>Inactive</option>
                        <option value="graduated" {{ (old('status', $student->status) == 'graduated') ? 'selected' : '' }}>Graduated</option>
                        <option value="suspended" {{ (old('status', $student->status) == 'suspended') ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>

                <!-- Department, Course, Subjects -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Department -->
                    <div>
                        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Department *
                        </label>
                        <select id="department_id" 
                                name="department_id" 
                                required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent department-select">
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" 
                                    {{ (old('department_id', $student->department_id) == $department->id) ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Course -->
                    <div>
                        <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Course *
                        </label>
                        <select id="course_id" 
                                name="course_id" 
                                required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent course-select">
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" 
                                    {{ (old('course_id', $student->course_id) == $course->id) ? 'selected' : '' }}>
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subjects -->
                    <div id="subjects-container" class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Select Subjects *
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($subjects as $subject)
                                <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50">
                                    <input type="checkbox" 
                                           id="subject_{{ $subject->id }}" 
                                           name="subjects[]" 
                                           value="{{ $subject->id }}"
                                           {{ in_array($subject->id, $selectedSubjects) ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="subject_{{ $subject->id }}" class="ml-3 text-sm text-gray-700">
                                        <span class="font-medium">{{ $subject->name }}</span>
                                        <span class="block text-xs text-gray-500">{{ $subject->code }} ({{ $subject->credit_hours }} credits)</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Select all subjects that the student is enrolled in.</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('admin.students.index') }}" 
                   class="px-6 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Update Student</span>
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Load courses when department changes
    document.getElementById('department_id').addEventListener('change', function() {
        const departmentId = this.value;
        const courseSelect = document.getElementById('course_id');
        
        if (!departmentId) {
            courseSelect.innerHTML = '<option value="">Select Department First</option>';
            return;
        }

        // Show loading
        courseSelect.innerHTML = '<option value="">Loading courses...</option>';

        // Fetch courses via AJAX
        fetch(`/admin/courses-by-department/${departmentId}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    let options = '<option value="">Select Course</option>';
                    const currentCourseId = {{ $student->course_id }};
                    data.forEach(course => {
                        const selected = course.id == currentCourseId ? 'selected' : '';
                        options += `<option value="${course.id}" ${selected}>${course.name}</option>`;
                    });
                    courseSelect.innerHTML = options;
                } else {
                    courseSelect.innerHTML = '<option value="">No courses found</option>';
                }
            })
            .catch(error => {
                console.error('Error loading courses:', error);
                courseSelect.innerHTML = '<option value="">Error loading courses</option>';
            });
    });

    // Load subjects when course changes
    document.getElementById('course_id').addEventListener('change', function() {
        const courseId = this.value;
        if (courseId) {
            loadSubjects(courseId);
        }
    });

    function loadSubjects(courseId) {
        const container = document.getElementById('subjects-container');
        const selectedSubjects = @json($selectedSubjects);
        
        // Show loading
        container.innerHTML = `
            <div class="col-span-3">
                <div class="flex items-center space-x-2 text-gray-600">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Loading subjects...</span>
                </div>
            </div>
        `;

        // Fetch subjects via AJAX
        fetch(`/admin/subjects-by-course/${courseId}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    let html = `
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Select Subjects *
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    `;
                    
                    data.forEach(subject => {
                        const isSelected = selectedSubjects.includes(subject.id);
                        html += `
                            <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50">
                                <input type="checkbox" 
                                       id="subject_${subject.id}" 
                                       name="subjects[]" 
                                       value="${subject.id}"
                                       ${isSelected ? 'checked' : ''}
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="subject_${subject.id}" class="ml-3 text-sm text-gray-700">
                                    <span class="font-medium">${subject.name}</span>
                                    <span class="block text-xs text-gray-500">${subject.code} (${subject.credit_hours} credits)</span>
                                </label>
                            </div>
                        `;
                    });
                    
                    html += `
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Select all subjects that the student is enrolled in.</p>
                    `;
                    
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `
                        <div class="col-span-3">
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-book text-3xl mb-3"></i>
                                <p>No subjects found for this course.</p>
                            </div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading subjects:', error);
                container.innerHTML = `
                    <div class="col-span-3">
                        <div class="text-center py-8 text-red-500">
                            <i class="fas fa-exclamation-triangle text-3xl mb-3"></i>
                            <p>Error loading subjects.</p>
                        </div>
                    </div>
                `;
            });
    }

    // Form validation
    document.getElementById('studentForm').addEventListener('submit', function(e) {
        const subjects = document.querySelectorAll('input[name="subjects[]"]:checked');
        
        if (subjects.length === 0) {
            e.preventDefault();
            alert('Please select at least one subject.');
        }
    });
</script>
@endpush
@endsection