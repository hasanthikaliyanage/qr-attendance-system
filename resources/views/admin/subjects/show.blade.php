@extends('layouts.app')

@section('title', $subject->name)

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800">{{ $subject->name }}</h1>
    <a href="{{ route('admin.subjects.index') }}" 
       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
        <i class="fas fa-arrow-left mr-2"></i> Back
    </a>
</div>

<!-- Subject Info -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Subject Information</h3>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-sm font-medium text-gray-500">Code</p>
            <p class="mt-1 text-gray-900">{{ $subject->code }}</p>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Credits</p>
            <p class="mt-1 text-gray-900">{{ $subject->credits }}</p>
        </div>
        <div class="col-span-2">
            <p class="text-sm font-medium text-gray-500">Description</p>
            <p class="mt-1 text-gray-900">{{ $subject->description ?? 'N/A' }}</p>
        </div>
    </div>
</div>

<!-- Courses -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Assigned to Courses ({{ $subject->courses->count() }})</h3>
    @if($subject->courses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($subject->courses as $course)
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium text-gray-900">{{ $course->name }}</h4>
                    <p class="text-sm text-gray-500 mt-1">{{ $course->code }}</p>
                    <p class="text-xs text-gray-400 mt-2">{{ $course->department->name }}</p>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500">Not assigned to any courses.</p>
    @endif
</div>

<!-- Assigned Lecturers -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Assigned Lecturers ({{ $subject->lecturers->count() }})</h3>

    @if($subject->lecturers->count() > 0)
        <div class="overflow-x-auto mb-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($subject->lecturers as $lecturer)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $lecturer->employee_id ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $lecturer->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $lecturer->user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <form action="{{ route('admin.subjects.remove.lecturer', [$subject, $lecturer]) }}" 
                                      method="POST" 
                                      class="inline"
                                      onsubmit="return confirm('Remove this lecturer?');">
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
        <p class="text-gray-500 mb-6">No lecturers assigned yet.</p>
    @endif

    <!-- Assign Lecturers Form -->
    @if($availableLecturers->count() > 0)
        <div class="border-t pt-6">
            <h4 class="text-md font-semibold text-gray-900 mb-4">Assign Lecturers</h4>
            <form method="POST" action="{{ route('admin.subjects.assign.lecturers', $subject) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Select Lecturers to Assign
                    </label>
                    <select name="lecturers[]" 
                            multiple 
                            size="10"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($availableLecturers as $lecturer)
    <option value="{{ $lecturer->id }}">
        {{ $lecturer->employee_id ?? 'N/A' }} - {{ $lecturer->user?->name ?? 'Lecturer ' . $lecturer->employee_id }} 
        @if($lecturer->department)
            ({{ $lecturer->department->name }})
        @else
            (No Department)
        @endif
    </option>
@endforeach
                    </select>
                    <p class="mt-1 text-sm text-gray-500">Hold Ctrl (Cmd on Mac) to select multiple lecturers</p>
                </div>
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Assign Selected Lecturers
                </button>
            </form>
        </div>
    @endif
</div>

<!-- Enrolled Students -->
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Enrolled Students ({{ $subject->students->count() }})</h3>
    @if($subject->students->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($subject->students as $student)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $student->student_id ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $student->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->course->name ?? 'N/A' }}</td>
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