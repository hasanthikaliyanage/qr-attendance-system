@extends('layouts.app')

@section('title', 'Manage Students')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Students Management</h1>
            <p class="text-gray-600">Manage all student accounts and information</p>
        </div>
        <a href="{{ route('admin.students.create') }}" 
           class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 flex items-center space-x-2">
            <i class="fas fa-user-plus"></i>
            <span>Add New Student</span>
        </a>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-sm border p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search -->
            <form method="GET" action="{{ route('admin.students.index') }}" class="md:col-span-2">
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search by name, email, or student ID..." 
                           class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </form>

            <!-- Filters -->
            <div class="flex space-x-2">
                <select id="department_filter" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Departments</option>
                    @foreach(App\Models\Department::all() as $dept)
                    <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
                <select id="status_filter" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Student
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Student ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Department
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Course
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
                    @forelse($students as $student)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <i class="fas fa-user text-indigo-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $student->user->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $student->user->email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $student->student_id }}</div>
                            <div class="text-sm text-gray-500">{{ $student->user->phone }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $student->department->name }}</div>
                            <div class="text-sm text-gray-500">{{ $student->department->code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $student->course->name }}</div>
                            <div class="text-sm text-gray-500">{{ $student->course->code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'active' => 'bg-green-100 text-green-800',
                                    'inactive' => 'bg-red-100 text-red-800',
                                    'graduated' => 'bg-blue-100 text-blue-800',
                                    'suspended' => 'bg-yellow-100 text-yellow-800'
                                ];
                            @endphp
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$student->status] }}">
                                {{ ucfirst($student->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.students.edit', $student) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 p-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" 
                                   class="text-blue-600 hover:text-blue-900 p-1" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.students.destroy', $student) }}" 
                                      method="POST" 
                                      class="inline"
                                      onsubmit="return confirm('Are you sure you want to delete this student?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 p-1" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <button onclick="resendCredentials({{ $student->user_id }}, '{{ $student->user->name }}')" 
                                   class="text-green-600 hover:text-green-900 p-1" title="Resend Credentials">
                                    <i class="fas fa-envelope"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-user-graduate text-4xl text-gray-300 mb-4"></i>
                                <p class="text-lg">No students found</p>
                                <p class="text-sm mt-2">Add your first student by clicking the "Add New Student" button</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($students->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $students->links() }}
        </div>
        @endif
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-indigo-50 p-4 rounded-lg">
            <p class="text-sm text-indigo-700">Total Students</p>
            <p class="text-2xl font-bold text-indigo-900">{{ $students->total() }}</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg">
            <p class="text-sm text-green-700">Active Students</p>
            <p class="text-2xl font-bold text-green-900">
                {{ App\Models\Student::where('status', 'active')->count() }}
            </p>
        </div>
        <div class="bg-blue-50 p-4 rounded-lg">
            <p class="text-sm text-blue-700">Graduated</p>
            <p class="text-2xl font-bold text-blue-900">
                {{ App\Models\Student::where('status', 'graduated')->count() }}
            </p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg">
            <p class="text-sm text-yellow-700">Inactive</p>
            <p class="text-2xl font-bold text-yellow-900">
                {{ App\Models\Student::where('status', 'inactive')->count() }}
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Search debounce
    let searchTimer;
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                this.form.submit();
            }, 500);
        });
    }

    // Filter change
    document.getElementById('department_filter')?.addEventListener('change', function() {
        const departmentId = this.value;
        updateUrlWithFilter('department', departmentId);
    });

    document.getElementById('status_filter')?.addEventListener('change', function() {
        const status = this.value;
        updateUrlWithFilter('status', status);
    });

    function updateUrlWithFilter(key, value) {
        const url = new URL(window.location.href);
        const params = new URLSearchParams(url.search);
        
        if (value) {
            params.set(key, value);
        } else {
            params.delete(key);
        }
        
        // Keep search if exists
        const search = document.querySelector('input[name="search"]')?.value;
        if (search) {
            params.set('search', search);
        }
        
        window.location.href = `${url.pathname}?${params.toString()}`;
    }

    // Resend credentials
    function resendCredentials(userId, userName) {
        if (confirm(`Resend credentials to ${userName}?`)) {
            fetch(`/admin/resend-credentials/${userId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Credentials resent successfully!');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error resending credentials');
                console.error('Error:', error);
            });
        }
    }

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        // Set filter values from URL
        const urlParams = new URLSearchParams(window.location.search);
        const departmentFilter = document.getElementById('department_filter');
        const statusFilter = document.getElementById('status_filter');
        
        if (departmentFilter && urlParams.get('department')) {
            departmentFilter.value = urlParams.get('department');
        }
        
        if (statusFilter && urlParams.get('status')) {
            statusFilter.value = urlParams.get('status');
        }
    });
</script>
@endpush
@endsection