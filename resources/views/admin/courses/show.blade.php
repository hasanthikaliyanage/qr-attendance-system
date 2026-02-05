@extends('layouts.app')

@section('title', $course->name)

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800">{{ $course->name }}</h1>
    <a href="{{ route('admin.courses.index') }}" 
       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
        <i class="fas fa-arrow-left mr-2"></i> Back
    </a>
</div>

<!-- Course Info -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Course Information</h3>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-sm font-medium text-gray-500">Code</p>
            <p class="mt-1 text-gray-900">{{ $course->code }}</p>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Department</p>
            <p class="mt-1 text-gray-900">{{ $course->department->name }}</p>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Duration</p>
            <p class="mt-1 text-gray-900">{{ $course->duration_months }} months</p>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Students Enrolled</p>
            <p class="mt-1 text-gray-900">{{ $course->students->count() }}</p>
        </div>
        <div class="col-span-2">
            <p class="text-sm font-medium text-gray-500">Description</p>
            <p class="mt-1 text-gray-900">{{ $course->description ?? 'N/A' }}</p>
        </div>
    </div>
</div>

<!-- Assigned Subjects -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Assigned Subjects ({{ $course->subjects->count() }})</h3>

    @if($course->subjects->count() > 0)
        <div class="overflow-x-auto mb-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Credits</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($course->subjects as $subject)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $subject->code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $subject->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $subject->credits }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <form action="{{ route('admin.courses.remove.subject', [$course, $subject]) }}" 
                                      method="POST" 
                                      class="inline"
                                      onsubmit="return confirm('Remove this subject?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-times"></i> Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500 mb-6">No subjects assigned yet.</p>
    @endif

    <!-- Assign Subjects Form -->
    @if($availableSubjects->count() > 0)
        <div class="border-t pt-6">
            <h4 class="text-md font-semibold text-gray-900 mb-4">Assign Subjects</h4>
            <form method="POST" action="{{ route('admin.courses.assign.subjects', $course) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Select Subjects to Assign
                    </label>
                    <select name="subjects[]" 
                            multiple 
                            size="10"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($availableSubjects as $subject)
                            <option value="{{ $subject->id }}">
                                {{ $subject->code }} - {{ $subject->name }} ({{ $subject->credits }} credits)
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-sm text-gray-500">Hold Ctrl (Cmd on Mac) to select multiple subjects</p>
                </div>
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Assign Selected Subjects
                </button>
            </form>
        </div>
    @endif
</div>

<!-- Enrolled Students -->
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Enrolled Students ({{ $course->students->count() }})</h3>
    @if($course->students->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($course->students as $student)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $student->student_id ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $student->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->user->email }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500">No students enrolled.</p>
    @endif
</div>
@endsection