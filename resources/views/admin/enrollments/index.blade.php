@extends('layouts.app')

@section('title', 'Enrollments Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Enrollments Management</h1>
        <div class="flex space-x-3">
            <a href="{{ route('admin.enrollments.create') }}" 
               class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                <i class="fas fa-plus mr-2"></i> New Enrollment
            </a>
            <button onclick="openBulkModal()"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-users mr-2"></i> Bulk Enroll
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-lg shadow">
            <p class="text-sm text-gray-600">Total Enrollments</p>
            <p class="text-2xl font-bold">{{ App\Models\Enrollment::count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <p class="text-sm text-gray-600">Active Enrollments</p>
            <p class="text-2xl font-bold">{{ App\Models\Enrollment::where('status', 'enrolled')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <p class="text-sm text-gray-600">Current Academic Year</p>
            <p class="text-2xl font-bold">{{ App\Models\Enrollment::where('academic_year', date('Y'))->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <p class="text-sm text-gray-600">Completed</p>
            <p class="text-2xl font-bold">{{ App\Models\Enrollment::where('status', 'completed')->count() }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
                <select name="academic_year" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">All Years</option>
                    @for($year = date('Y'); $year >= 2020; $year--)
                        <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                <select name="semester" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">All Semesters</option>
                    @for($i = 1; $i <= 8; $i++)
                        <option value="{{ $i }}" {{ request('semester') == $i ? 'selected' : '' }}>
                            Semester {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">All Status</option>
                    <option value="enrolled" {{ request('status') == 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="dropped" {{ request('status') == 'dropped' ? 'selected' : '' }}>Dropped</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Filter
                </button>
                <a href="{{ route('admin.enrollments.index') }}" 
                   class="ml-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Enrollments Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Student
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Course & Subject
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Academic Details
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status & Grade
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($enrollments as $enrollment)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-indigo-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $enrollment->student->user->name ?? 'N/A' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $enrollment->student->student_id ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                {{ $enrollment->course->name ?? 'N/A' }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $enrollment->subject->name ?? 'N/A' }} ({{ $enrollment->subject->code ?? 'N/A' }})
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                Year: {{ $enrollment->academic_year }}
                            </div>
                            <div class="text-sm text-gray-500">
                                Semester: {{ $enrollment->semester }}
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $enrollment->enrollment_date ? $enrollment->enrollment_date->format('M d, Y') : 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $enrollment->status == 'enrolled' ? 'bg-green-100 text-green-800' : 
                                   ($enrollment->status == 'completed' ? 'bg-blue-100 text-blue-800' : 
                                   ($enrollment->status == 'dropped' ? 'bg-yellow-100 text-yellow-800' : 
                                   'bg-red-100 text-red-800')) }}">
                                {{ ucfirst($enrollment->status) }}
                            </span>
                            @if($enrollment->grade)
                            <div class="mt-1 text-sm font-medium text-gray-900">
                                Grade: {{ $enrollment->grade }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.enrollments.show', $enrollment) }}" 
                                   class="text-indigo-600 hover:text-indigo-900" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.enrollments.edit', $enrollment) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.enrollments.destroy', $enrollment) }}" 
                                      method="POST" class="inline"
                                      onsubmit="return confirm('Are you sure you want to delete this enrollment?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-book text-4xl text-gray-300 mb-4"></i>
                            <p class="text-lg">No enrollments found</p>
                            <p class="text-sm mt-2">Create your first enrollment to get started</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $enrollments->links() }}
    </div>
</div>

<!-- Bulk Enrollment Modal -->
<div id="bulkModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-2/3 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Bulk Enrollment</h3>
            <button onclick="closeBulkModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route('admin.enrollments.bulk') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Course *</label>
                    <select id="bulkCourse" name="course_id" required 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">Select Course</option>
                        @foreach(App\Models\Course::all() as $course)
                            <option value="{{ $course->id }}">
                                {{ $course->name }} - {{ $course->department->code }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                    <select id="bulkSubject" name="subject_id" required 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2" disabled>
                        <option value="">Select Subject</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year *</label>
                    <select name="academic_year" required 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        @for($year = date('Y'); $year >= 2020; $year--)
                            <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Semester *</label>
                    <select name="semester" required 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}">Semester {{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Students</label>
                <div id="studentCheckboxes" class="border border-gray-300 rounded-lg p-3 max-h-60 overflow-y-auto">
                    <p class="text-gray-500 text-center">Select a course first to load students</p>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeBulkModal()" 
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Enroll Selected Students
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openBulkModal() {
    document.getElementById('bulkModal').classList.remove('hidden');
}

function closeBulkModal() {
    document.getElementById('bulkModal').classList.add('hidden');
}

// Load subjects when course is selected
document.getElementById('bulkCourse').addEventListener('change', function() {
    const courseId = this.value;
    const subjectSelect = document.getElementById('bulkSubject');
    const studentCheckboxes = document.getElementById('studentCheckboxes');
    
    if (!courseId) {
        subjectSelect.disabled = true;
        subjectSelect.innerHTML = '<option value="">Select Subject</option>';
        studentCheckboxes.innerHTML = '<p class="text-gray-500 text-center">Select a course first to load students</p>';
        return;
    }
    
    // Load subjects
    fetch(`/admin/enrollments/course/${courseId}/subjects`)
        .then(response => response.json())
        .then(subjects => {
            subjectSelect.disabled = false;
            subjectSelect.innerHTML = '<option value="">Select Subject</option>';
            subjects.forEach(subject => {
                subjectSelect.innerHTML += `<option value="${subject.id}">${subject.code} - ${subject.name}</option>`;
            });
        });
    
    // Load students
    fetch(`/admin/enrollments/course/${courseId}/students`)
        .then(response => response.json())
        .then(students => {
            studentCheckboxes.innerHTML = '';
            if (students.length === 0) {
                studentCheckboxes.innerHTML = '<p class="text-gray-500 text-center">No students found in this course</p>';
                return;
            }
            
            students.forEach(student => {
                studentCheckboxes.innerHTML += `
                    <div class="flex items-center mb-2">
                        <input type="checkbox" name="student_ids[]" value="${student.id}" 
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label class="ml-2 text-sm text-gray-700">
                            ${student.name} (${student.student_id})
                        </label>
                    </div>
                `;
            });
            
            // Add select all checkbox
            studentCheckboxes.innerHTML += `
                <div class="flex items-center mt-3 pt-3 border-t">
                    <input type="checkbox" id="selectAll" 
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="selectAll" class="ml-2 text-sm font-medium text-gray-700">
                        Select All Students
                    </label>
                </div>
            `;
            
            // Add select all functionality
            document.getElementById('selectAll').addEventListener('change', function() {
                const checkboxes = studentCheckboxes.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(checkbox => {
                    if (checkbox.id !== 'selectAll') {
                        checkbox.checked = this.checked;
                    }
                });
            });
        });
});
</script>
@endsection