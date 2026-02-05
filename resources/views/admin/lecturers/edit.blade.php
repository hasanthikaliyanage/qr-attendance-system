@extends('layouts.app')

@section('title', 'Edit Lecturer')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Edit Lecturer</h1>
        <p class="text-gray-600">Update lecturer information for {{ $lecturer->user->name }}</p>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.lecturers.update', $lecturer) }}" method="POST" id="lecturerForm">
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
                               value="{{ old('name', $lecturer->user->name) }}"
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
                               value="{{ old('email', $lecturer->user->email) }}"
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
                               value="{{ old('nic', $lecturer->user->nic) }}"
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
                               value="{{ old('phone', $lecturer->user->phone) }}"
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
                                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('address', $lecturer->user->address) }}</textarea>
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
                               value="{{ old('date_of_birth', $lecturer->date_of_birth ? $lecturer->date_of_birth->format('Y-m-d') : '') }}"
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
                            <option value="male" {{ (old('gender', $lecturer->gender) == 'male') ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ (old('gender', $lecturer->gender) == 'female') ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ (old('gender', $lecturer->gender) == 'other') ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <!-- Qualification -->
                    <div>
                        <label for="qualification" class="block text-sm font-medium text-gray-700 mb-2">
                            Qualification
                        </label>
                        <input type="text" 
                               id="qualification" 
                               name="qualification" 
                               value="{{ old('qualification', $lecturer->qualification) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>

                    <!-- Specialization -->
                    <div>
                        <label for="specialization" class="block text-sm font-medium text-gray-700 mb-2">
                            Specialization
                        </label>
                        <input type="text" 
                               id="specialization" 
                               name="specialization" 
                               value="{{ old('specialization', $lecturer->specialization) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Employment Information -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Employment Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Employee ID -->
                    <div>
                        <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Employee ID *
                        </label>
                        <input type="text" 
                               id="employee_id" 
                               name="employee_id" 
                               value="{{ old('employee_id', $lecturer->employee_id) }}"
                               required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @error('employee_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Employment Type -->
                    <div>
                        <label for="employment_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Employment Type *
                        </label>
                        <select id="employment_type" 
                                name="employment_type" 
                                required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">Select Type</option>
                            <option value="full-time" {{ (old('employment_type', $lecturer->employment_type) == 'full-time') ? 'selected' : '' }}>Full-time</option>
                            <option value="part-time" {{ (old('employment_type', $lecturer->employment_type) == 'part-time') ? 'selected' : '' }}>Part-time</option>
                            <option value="visiting" {{ (old('employment_type', $lecturer->employment_type) == 'visiting') ? 'selected' : '' }}>Visiting</option>
                        </select>
                    </div>

                    <!-- Joined Date -->
                    <div>
                        <label for="joined_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Joined Date
                        </label>
                        <input type="date" 
                               id="joined_date" 
                               name="joined_date" 
                               value="{{ old('joined_date', $lecturer->joined_date ? $lecturer->joined_date->format('Y-m-d') : '') }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status *
                        </label>
                        <select id="status" 
                                name="status"
                                required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="active" {{ (old('status', $lecturer->status) == 'active') ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ (old('status', $lecturer->status) == 'inactive') ? 'selected' : '' }}>Inactive</option>
                            <option value="on-leave" {{ (old('status', $lecturer->status) == 'on-leave') ? 'selected' : '' }}>On Leave</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Academic Assignment -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Academic Assignment</h2>
                
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
                                    {{ (old('department_id', $lecturer->department_id) == $department->id) ? 'selected' : '' }}>
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
                                    {{ (old('course_id', optional($lecturer->subjects->first()?->courses->first())->id) == $course->id) ? 'selected' : '' }}>
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
                            Assign Subjects *
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
                        <p class="mt-2 text-sm text-gray-500">Select subjects that this lecturer will be teaching.</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('admin.lecturers.index') }}" 
                   class="px-6 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Update Lecturer</span>
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
                    data.forEach(course => {
                        options += `<option value="${course.id}">${course.name}</option>`;
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
                            Assign Subjects *
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
                        <p class="mt-2 text-sm text-gray-500">Select subjects that this lecturer will be teaching.</p>
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
    document.getElementById('lecturerForm').addEventListener('submit', function(e) {
        const subjects = document.querySelectorAll('input[name="subjects[]"]:checked');
        
        if (subjects.length === 0) {
            e.preventDefault();
            alert('Please assign at least one subject.');
        }
    });
</script>
@endpush
@endsection