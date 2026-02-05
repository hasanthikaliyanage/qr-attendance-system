@extends('layouts.app')

@section('title', 'Manage Lecturers')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Lecturers Management</h1>
            <p class="text-gray-600">Manage all lecturer accounts and information</p>
        </div>
        <a href="{{ route('admin.lecturers.create') }}" 
           class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 flex items-center space-x-2">
            <i class="fas fa-chalkboard-teacher"></i>
            <span>Add New Lecturer</span>
        </a>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-sm border p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search -->
            <form method="GET" action="{{ route('admin.lecturers.index') }}" class="md:col-span-2">
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search by name, email, or employee ID..." 
                           class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </form>

            <!-- Filters -->
            <div class="flex space-x-2">
                <select class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                    <option>All Departments</option>
                    @foreach(App\Models\Department::all() as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
                <select class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                    <option>All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="on-leave">On Leave</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Lecturers Table -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Lecturer
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Employee ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Department
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Subjects
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($lecturers as $lecturer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                        <i class="fas fa-chalkboard-teacher text-green-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $lecturer->user?->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $lecturer->user?->email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $lecturer->employee_id }}</div>
                            <div class="text-sm text-gray-500">{{ $lecturer->user?->phone }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $lecturer->department->name }}</div>
                            <div class="text-sm text-gray-500">{{ $lecturer->department->code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @php
                                    $subjectCount = $lecturer->subjects->count();
                                @endphp
                                {{ $subjectCount }} {{ Str::plural('subject', $subjectCount) }}
                            </div>
                            @if($lecturer->subjects->count() > 0)
                            <div class="text-xs text-gray-500 truncate max-w-xs">
                                {{ $lecturer->subjects->pluck('code')->implode(', ') }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'active' => 'bg-green-100 text-green-800',
                                    'inactive' => 'bg-red-100 text-red-800',
                                    'on-leave' => 'bg-yellow-100 text-yellow-800'
                                ];
                            @endphp
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$lecturer->status] }}">
                                {{ ucfirst($lecturer->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.lecturers.edit', $lecturer) }}" 
                                   class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" 
                                   class="text-blue-600 hover:text-blue-900" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.lecturers.destroy', $lecturer) }}" 
                                      method="POST" 
                                      class="inline"
                                      onsubmit="return confirm('Are you sure you want to delete this lecturer?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <a href="#" 
                                   class="text-green-600 hover:text-green-900" title="Send Credentials">
                                    <i class="fas fa-envelope"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-chalkboard-teacher text-4xl text-gray-300 mb-4"></i>
                                <p class="text-lg">No lecturers found</p>
                                <p class="text-sm mt-2">Add your first lecturer by clicking the "Add New Lecturer" button</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($lecturers->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $lecturers->links() }}
        </div>
        @endif
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-green-50 p-4 rounded-lg">
            <p class="text-sm text-green-700">Total Lecturers</p>
            <p class="text-2xl font-bold text-green-900">{{ $lecturers->total() }}</p>
        </div>
        <div class="bg-blue-50 p-4 rounded-lg">
            <p class="text-sm text-blue-700">Full-time</p>
            <p class="text-2xl font-bold text-blue-900">
                {{ App\Models\Lecturer::where('employment_type', 'full-time')->count() }}
            </p>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg">
            <p class="text-sm text-purple-700">Part-time</p>
            <p class="text-2xl font-bold text-purple-900">
                {{ App\Models\Lecturer::where('employment_type', 'part-time')->count() }}
            </p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg">
            <p class="text-sm text-yellow-700">On Leave</p>
            <p class="text-2xl font-bold text-yellow-900">
                {{ App\Models\Lecturer::where('status', 'on-leave')->count() }}
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Search debounce
    let searchTimer;
    document.querySelector('input[name="search"]').addEventListener('input', function(e) {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            this.form.submit();
        }, 500);
    });
</script>
@endpush
@endsection