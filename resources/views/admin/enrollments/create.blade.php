@extends('layouts.app')

@section('title', 'Create Enrollment')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Enroll Student in Subject</h1>
            <p class="text-gray-600">Add a new student enrollment to a subject</p>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.enrollments.store') }}" method="POST">
            @csrf

            <!-- Student Selection -->
            <div class="mb-6">
                <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Student *
                </label>
                <select id="student_id" name="student_id" required 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">Select Student</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">
                            {{ $student->user->name }} ({{ $student->student_id }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Course Selection -->
            <div class="mb-6">
                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Course *
                </label>
                <select id="course_id" name="course_id" required 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">Select Course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">
                            {{ $course->name }} - {{ $course->department->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Subject Selection (will be populated by AJAX) -->
            <div class="mb-6">
                <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Subject *
                </label>
                <select id="subject_id" name="subject_id" required 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" disabled>
                    <option value="">Select a course first</option>
                </select>
            </div>

            <!-- Academic Details -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-2">
                        Academic Year *
                    </label>
                    <select id="academic_year" name="academic_year" required 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @for($year = date('Y'); $year >= 2020; $year--)
                            <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>
                </div>
                
                <div>
                    <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">
                        Semester *
                    </label>
                    <select id="semester" name="semester" required 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ $i == 1 ? 'selected' : '' }}>Semester {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status *
                    </label>
                    <select id="status" name="status" required 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="enrolled" selected>Enrolled</option>
                        <option value="completed">Completed</option>
                        <option value="dropped">Dropped</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
            </div>

            <!-- Grade (optional) -->
            <div class="mb-8">
                <label for="grade" class="block text-sm font-medium text-gray-700 mb-2">
                    Grade (Optional)
                </label>
                <input type="number" id="grade" name="grade" step="0.01" min="0" max="4"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="e.g., 3.5">
                <p class="mt-1 text-sm text-gray-500">
                    Enter grade if applicable (0.0 to 4.0 scale)
                </p>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.enrollments.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    Create Enrollment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// AJAX to load subjects when course is selected
document.getElementById('course_id').addEventListener('change', function() {
    const courseId = this.value;
    const subjectSelect = document.getElementById('subject_id');
    
    if (!courseId) {
        subjectSelect.disabled = true;
        subjectSelect.innerHTML = '<option value="">Select a course first</option>';
        return;
    }
    
    // Fetch subjects for this course
    fetch(`/admin/enrollments/course/${courseId}/subjects`)
        .then(response => response.json())
        .then(subjects => {
            subjectSelect.disabled = false;
            subjectSelect.innerHTML = '<option value="">Select Subject</option>';
            subjects.forEach(subject => {
                subjectSelect.innerHTML += `<option value="${subject.id}">${subject.code} - ${subject.name}</option>`;
            });
        })
        .catch(error => {
            console.error('Error loading subjects:', error);
            subjectSelect.innerHTML = '<option value="">Error loading subjects</option>';
        });
});
</script>
@endsection