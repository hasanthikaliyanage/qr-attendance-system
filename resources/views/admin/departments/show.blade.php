@extends('layouts.app')

@section('title', $department->name)

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800">{{ $department->name }}</h1>
    <a href="{{ route('admin.departments.index') }}" 
       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
        <i class="fas fa-arrow-left mr-2"></i> Back
    </a>
</div>

<!-- Department Info -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Department Information</h3>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-sm font-medium text-gray-500">Code</p>
            <p class="mt-1 text-gray-900">{{ $department->code }}</p>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Name</p>
            <p class="mt-1 text-gray-900">{{ $department->name }}</p>
        </div>
        <div class="col-span-2">
            <p class="text-sm font-medium text-gray-500">Description</p>
            <p class="mt-1 text-gray-900">{{ $department->description ?? 'N/A' }}</p>
        </div>
    </div>
</div>

<!-- Courses -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Courses ({{ $department->courses->count() }})</h3>
    @if($department->courses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($department->courses as $course)
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium text-gray-900">{{ $course->name }}</h4>
                    <p class="text-sm text-gray-500 mt-1">{{ $course->code }}</p>
                    <p class="text-xs text-gray-400 mt-2">{{ $course->duration_months }} months</p>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500">No courses.</p>
    @endif
</div>

<!-- Students -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Students ({{ $department->students->count() }})</h3>
    @if($department->students->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($department->students as $student)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $student->student_id ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $student->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->course->name ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500">No students.</p>
    @endif
</div>

<!-- Lecturers -->
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Lecturers ({{ $department->lecturers->count() }})</h3>
    @if($department->lecturers->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($department->lecturers as $lecturer)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $lecturer->employee_id ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $lecturer->user->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500">No lecturers.</p>
    @endif
</div>
@endsection