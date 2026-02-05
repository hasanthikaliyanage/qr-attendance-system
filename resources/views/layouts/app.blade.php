<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" 
          integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" 
          crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- Tailwind CSS -->
    @if(app()->environment('production'))
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @else
    <script src="https://cdn.tailwindcss.com"></script>
    @endif    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
          integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Alpine.js CDN - IMPORTANT FOR DROPDOWN FUNCTIONALITY -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        .sidebar {
            transition: all 0.3s ease;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
        }
        /* Hide Tailwind warning */
        .tailwind-warning {
            display: none !important;
        }
        /* Dropdown animation */
        .dropdown-enter {
            opacity: 0;
            transform: scale(0.95);
        }
        .dropdown-enter-active {
            opacity: 1;
            transform: scale(1);
            transition: opacity 150ms ease-out, transform 150ms ease-out;
        }
        .dropdown-leave {
            opacity: 1;
            transform: scale(1);
        }
        .dropdown-leave-active {
            opacity: 0;
            transform: scale(0.95);
            transition: opacity 75ms ease-in, transform 75ms ease-in;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Mobile menu button -->
    <div class="lg:hidden fixed top-4 left-4 z-50">
        <button id="mobile-menu-button" class="p-2 bg-indigo-600 text-white rounded-lg">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="sidebar fixed lg:relative w-64 bg-gray-900 text-white h-full z-40">
            <!-- Close button for mobile -->
            <div class="lg:hidden absolute top-4 right-4">
                <button id="close-menu-button" class="p-2 text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Logo -->
            <div class="p-6 border-b border-gray-800">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-indigo-600 rounded-lg">
                        <i class="fas fa-qrcode text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold">QR Attendance</h1>
                        <p class="text-xs text-gray-400">University System</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="p-4">
                <ul class="space-y-2">
                    @if(auth()->user()->isAdmin())
                        <!-- Admin Menu -->
                        <li>
                            <a href="{{ route('admin.dashboard') }}" 
                               class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800' : '' }}">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.students.index') }}" 
                               class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.students.*') ? 'bg-gray-800' : '' }}">
                                <i class="fas fa-user-graduate"></i>
                                <span>Students</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.lecturers.index') }}" 
                               class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.lecturers.*') ? 'bg-gray-800' : '' }}">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <span>Lecturers</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.departments.index') }}" 
                               class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.departments.*') ? 'bg-gray-800' : '' }}">
                                <i class="fas fa-building"></i>
                                <span>Departments</span>
                            </a>
                        </li>
                        <li>
                           <a href="{{ route('admin.courses.index') }}" 
                               class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.courses.*') ? 'bg-gray-800' : '' }}">
                                <i class="fas fa-graduation-cap"></i>
                                <span>Courses</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.subjects.index') }}" 
                               class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.subjects.*') ? 'bg-gray-800' : '' }}">
                                <i class="fas fa-book"></i>
                                <span>Subjects</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.enrollments.index') }}" 
                               class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-800 {{ request()->routeIs('admin.enrollments.*') ? 'bg-gray-800' : '' }}">
                                <i class="fas fa-user-graduate"></i>
                                <span>Enrollments</span>
                            </a>
                        </li>
                        
                          
                    @elseif(auth()->user()->isLecturer())
                        <!-- Lecturer Menu -->
                        <li>
                            <a href="{{ route('lecturer.dashboard') }}" 
                               class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-800 {{ request()->routeIs('lecturer.dashboard') ? 'bg-gray-800' : '' }}">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('lecturer.profile') }}" 
                               class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-800 {{ request()->routeIs('lecturer.profile') ? 'bg-gray-800' : '' }}">
                                <i class="fas fa-user"></i>
                                <span>Profile</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('lecturer.qr_sessions.index') }}" 
                               class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-800 {{ request()->routeIs('lecturer.qr-sessions.*') ? 'bg-gray-800' : '' }}">
                                <i class="fas fa-qrcode"></i>
                                <span>QR Sessions</span>
                            </a>
                        </li>

                    @elseif(auth()->user()->isStudent())
                        <!-- Student Menu -->
                        <li>
                            <a href="{{ route('student.dashboard') }}" 
                               class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-800 {{ request()->routeIs('student.dashboard') ? 'bg-gray-800' : '' }}">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('student.profile') }}" 
                               class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-800 {{ request()->routeIs('student.profile') ? 'bg-gray-800' : '' }}">
                                <i class="fas fa-user"></i>
                                <span>Profile</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('student.qr_sessions.index') }}" 
                               class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-800 {{ request()->routeIs('student.qr-sessions.*') ? 'bg-gray-800' : '' }}">
                                <i class="fas fa-qrcode"></i>
                                <span>QR Sessions</span>
                            </a>
                        </li>
                    @endif
                </ul>

                <!-- Settings -->
                <div class="mt-8 pt-6 border-t border-gray-800">
                    <ul class="space-y-2">
                        <li>
                            <a href="#" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-800">
                                <i class="fas fa-cog"></i>
                                <span>Settings</span>
                            </a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-800 w-full text-left">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-b">
                <div class="px-6 py-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">@yield('title', 'Dashboard')</h2>
                            <p class="text-sm text-gray-600">@yield('subtitle', 'Welcome back, ' . auth()->user()->name)</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Notifications -->
                            <button class="relative p-2 text-gray-600 hover:text-gray-900">
                                <i class="fas fa-bell"></i>
                                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                            </button>
                            
                            <!-- User Menu with Alpine.js -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center space-x-3 focus:outline-none">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-indigo-600"></i>
                                    </div>
                                    <div class="text-left hidden md:block">
                                        <p class="font-medium text-gray-800">{{ auth()->user()->name }}</p>
                                        <p class="text-sm text-gray-600 capitalize">{{ auth()->user()->role->name ?? 'User' }}</p>
                                    </div>
                                    <i class="fas fa-chevron-down text-gray-600" :class="{ 'transform rotate-180': open }"></i>
                                </button>
                                
                                <!-- Dropdown Menu -->
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     @click.away="open = false"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border py-2 z-50">
                                    @if(auth()->user()->isLecturer())
                                        <a href="{{ route('lecturer.profile') }}" 
                                           class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-user mr-2"></i> Profile
                                        </a>
                                    @elseif(auth()->user()->isStudent())
                                        <a href="{{ route('student.profile') }}" 
                                           class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-user mr-2"></i> Profile
                                        </a>
                                    @endif
                                    <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-cog mr-2"></i> Settings
                                    </a>
                                    <div class="border-t my-2"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">
                                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-3"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-3"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('info'))
                    <div class="mb-6 p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle mr-3"></i>
                            <span>{{ session('info') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mb-6 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle mr-3"></i>
                            <span>{{ session('warning') }}</span>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.add('open');
        });

        document.getElementById('close-menu-button').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.remove('open');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            
            if (!sidebar.contains(event.target) && !mobileMenuButton.contains(event.target)) {
                sidebar.classList.remove('open');
            }
        });

        // Auto-hide flash messages after 5 seconds
        setTimeout(() => {
            const flashMessages = document.querySelectorAll('.bg-green-100, .bg-red-100, .bg-blue-100, .bg-yellow-100');
            flashMessages.forEach(message => {
                message.style.transition = 'opacity 0.5s';
                message.style.opacity = '0';
                setTimeout(() => message.remove(), 500);
            });
        }, 5000);
    </script>

    @stack('scripts')
</body>
</html>