@extends('layouts.app')

@section('title', 'Edit Enrollment')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Enrollment</h1>
            <p class="text-gray-600">Update enrollment details</p>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.enrollments.update', $enrollment) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Display student and course info (readonly) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Student
                    </label>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="font-medium">{{ $enrollment->student->user->name }}</p>
                        <p class="text-sm text-gray-600">{{ $enrollment->student->student_id }}</p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Course & Subject
                    </label>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="font-medium">{{ $enrollment->course->name }}</p>
                        <p class="text-sm text-gray-600">{{ $enrollment->subject->name }} ({{ $enrollment->subject->code }})</p>
                    </div>
                </div>
            </div>

            <!-- Academic Details -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Academic Year
                    </label>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="font-medium">{{ $enrollment->academic_year }}</p>
                    </div>
                </div>
                
                <div>
                    <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">
                        Semester *
                    </label>
                    <select id="semester" name="semester" required 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ $enrollment->semester == $i ? 'selected' : '' }}>
                                Semester {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status *
                    </label>
                    <select id="status" name="status" required 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="enrolled" {{ $enrollment->status == 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                        <option value="completed" {{ $enrollment->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="dropped" {{ $enrollment->status == 'dropped' ? 'selected' : '' }}>Dropped</option>
                        <option value="failed" {{ $enrollment->status == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
            </div>

            <!-- Grade -->
            <div class="mb-8">
                <label for="grade" class="block text-sm font-medium text-gray-700 mb-2">
                    Grade
                </label>
                <input type="number" id="grade" name="grade" step="0.01" min="0" max="4"
                       value="{{ $enrollment->grade }}"
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
                    Update Enrollment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection