<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\LecturerDashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\QRSessionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\StudentQRScannerController;

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Logout Route
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Change Password Routes
Route::middleware('auth')->group(function () {
    Route::get('/change-password', [ChangePasswordController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/change-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');
});

// Main Dashboard (redirects based on role)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Admin Routes
Route::middleware(['auth', 'admin', 'must.change.password'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Departments
    Route::resource('departments', DepartmentController::class);

    // Courses
    Route::resource('courses', CourseController::class);
    Route::post('/courses/{course}/assign-subjects', [CourseController::class, 'assignSubjects'])->name('courses.assign.subjects');
    Route::delete('/courses/{course}/subjects/{subject}', [CourseController::class, 'removeSubject'])->name('courses.remove.subject');
    Route::post('/courses/{course}/bulk-assign-subjects', [CourseController::class, 'bulkAssignSubjects'])->name('courses.bulk.assign.subjects');
    
    // Subjects
    Route::resource('subjects', SubjectController::class);
    Route::post('/subjects/{subject}/assign-lecturers', [SubjectController::class, 'assignLecturers'])->name('subjects.assign.lecturers');
    Route::delete('/subjects/{subject}/lecturers/{lecturer}', [SubjectController::class, 'removeLecturer'])->name('subjects.remove.lecturer');
    Route::post('/subjects/{subject}/bulk-assign-lecturers', [SubjectController::class, 'bulkAssignLecturers'])->name('subjects.bulk.assign.lecturers');
    
    // Subject Student Assignment
    Route::post('subjects/{subject}/assign-students', [SubjectController::class, 'assignStudents'])->name('subjects.assign.students');
    Route::delete('subjects/{subject}/students/{student}', [SubjectController::class, 'removeStudent'])->name('subjects.remove.student');

    // Students
    Route::get('/students', [AdminController::class, 'studentsIndex'])->name('students.index');
    Route::get('/students/create', [AdminController::class, 'studentsCreate'])->name('students.create');
    Route::post('/students', [AdminController::class, 'studentsStore'])->name('students.store');
    Route::get('/students/{student}/edit', [AdminController::class, 'studentsEdit'])->name('students.edit');
    Route::put('/students/{student}', [AdminController::class, 'studentsUpdate'])->name('students.update');
    Route::delete('/students/{student}', [AdminController::class, 'studentsDestroy'])->name('students.destroy');
    Route::post('/students/{student}/resend-credentials', [AdminController::class, 'resendStudentCredentials'])->name('students.resend.credentials');
    
    // Lecturers
    Route::get('/lecturers', [AdminController::class, 'lecturersIndex'])->name('lecturers.index');
    Route::get('/lecturers/create', [AdminController::class, 'lecturersCreate'])->name('lecturers.create');
    Route::post('/lecturers', [AdminController::class, 'lecturersStore'])->name('lecturers.store');
    Route::get('/lecturers/{lecturer}/edit', [AdminController::class, 'lecturersEdit'])->name('lecturers.edit');
    Route::put('/lecturers/{lecturer}', [AdminController::class, 'lecturersUpdate'])->name('lecturers.update');
    Route::delete('/lecturers/{lecturer}', [AdminController::class, 'lecturersDestroy'])->name('lecturers.destroy');
    Route::post('/lecturers/{lecturer}/resend-credentials', [AdminController::class, 'resendLecturerCredentials'])->name('lecturers.resend.credentials');

    // Enrollment Routes
    Route::get('/enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
    Route::get('/enrollments/create', [EnrollmentController::class, 'create'])->name('enrollments.create');
    Route::post('/enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store');
    Route::get('/enrollments/{enrollment}', [EnrollmentController::class, 'show'])->name('enrollments.show');
    Route::get('/enrollments/{enrollment}/edit', [EnrollmentController::class, 'edit'])->name('enrollments.edit');
    Route::put('/enrollments/{enrollment}', [EnrollmentController::class, 'update'])->name('enrollments.update');
    Route::delete('/enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])->name('enrollments.destroy');
    Route::post('/enrollments/bulk', [EnrollmentController::class, 'bulkEnroll'])->name('enrollments.bulk');

    // AJAX Routes for enrollments
    Route::get('/enrollments/course/{courseId}/subjects', [EnrollmentController::class, 'getSubjectsByCourse'])->name('enrollments.subjects.by.course');
    Route::get('/enrollments/course/{courseId}/students', [EnrollmentController::class, 'getStudentsByCourse'])->name('enrollments.students.by.course');

    // Admin AJAX Endpoints (for dependent dropdowns in admin forms)
    Route::get('/courses-by-department/{department_id}', [AdminController::class, 'getCourses'])->name('courses.by.department');
    Route::get('/subjects-by-course/{course_id}', [AdminController::class, 'getSubjects'])->name('subjects.by.course');

    // Admin Attendance Reports
    Route::get('/attendance', [AttendanceController::class, 'adminIndex'])->name('attendance.index');
    Route::post('/attendance/manual', [AttendanceController::class, 'manualEntry'])->name('attendance.manual');
    Route::get('/attendance/export/{session}', [AttendanceController::class, 'exportPDF'])->name('attendance.export.pdf');
    Route::get('/attendance/export-excel/{session}', [AttendanceController::class, 'exportExcel'])->name('attendance.export.excel');
});

// Student Routes
Route::middleware(['auth', 'student', 'must.change.password'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [StudentDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [StudentDashboardController::class, 'updateProfile'])->name('profile.update');

    // QR Sessions
    Route::prefix('qr_sessions')->name('qr_sessions.')->group(function () {
        Route::get('/', [QRSessionController::class, 'studentIndex'])->name('index');
        Route::get('/scan', [QRSessionController::class, 'scan'])->name('scan');
    });

    // QR Scanner
    Route::get('/qr_scanner', [QRSessionController::class, 'studentScanner'])->name('qr_scanner');
    
    // ✅ Student Webcam QR Scanner Routes
    Route::get('/scanner/webcam', [StudentQRScannerController::class, 'scanner'])->name('scanner.webcam');
    
    // ⭐⭐⭐ CRITICAL: මේ POST route එක තියන්න! ⭐⭐⭐
    Route::post('/process-scan', [StudentQRScannerController::class, 'processScan'])->name('process.scan');
    
    Route::get('/attendance/stats', [StudentQRScannerController::class, 'getStats'])->name('attendance.stats');
    Route::get('/attendance/report', [StudentQRScannerController::class, 'report'])->name('attendance.report');
    Route::post('/attendance/by-date', [StudentQRScannerController::class, 'getAttendanceByDate'])->name('attendance.by.date');

    // Attendance History
    Route::get('/attendance/history', [AttendanceController::class, 'studentHistory'])->name('attendance.history');
    
    // QR Scan POST route (QRSessionController භාවිතා කරන එක)
    Route::post('/attendance/scan', [QRSessionController::class, 'scanQR'])->name('attendance.scan');
});

// LECTURER ROUTES
Route::middleware(['auth', 'lecturer', 'must.change.password'])->prefix('lecturer')->name('lecturer.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [LecturerDashboardController::class, 'index'])->name('dashboard');
    
    // Profile Routes
    Route::get('/profile', [LecturerDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [LecturerDashboardController::class, 'updateProfile'])->name('profile.update');
    
    // QR Sessions
    Route::get('/qr-sessions', [QRSessionController::class, 'lecturerIndex'])->name('qr_sessions.index');
    Route::get('/qr-sessions/create', [QRSessionController::class, 'create'])->name('qr_sessions.create');
    Route::post('/qr-sessions', [QRSessionController::class, 'store'])->name('qr_sessions.store');
    Route::get('/qr-sessions/{id}', [QRSessionController::class, 'show'])->name('qr_sessions.show');
    Route::get('/qr-sessions/{id}/generate', [QRSessionController::class, 'generateQr'])->name('qr_sessions.generate');
    Route::put('/qr-sessions/{id}/toggle', [QRSessionController::class, 'toggleStatus'])->name('qr_sessions.toggle');
    Route::delete('/qr-sessions/{id}', [QRSessionController::class, 'destroy'])->name('qr_sessions.destroy');
    
    // Lecturer AJAX Routes
    Route::get('/ajax/courses-by-department/{departmentId}', [QRSessionController::class, 'getCoursesByDepartment']);
    Route::get('/ajax/subjects-by-course/{courseId}', [QRSessionController::class, 'getSubjectsByCourse']);
});

// Lecturer Attendance Management
Route::middleware(['auth', 'lecturer'])->prefix('lecturer')->name('lecturer.')->group(function () {
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'lecturerIndex'])->name('index');
        Route::get('/{qrSession}/export-pdf', [AttendanceController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/{qrSession}/export-excel', [AttendanceController::class, 'exportExcel'])->name('export.excel');
    });
});

// General AJAX Routes (for all authenticated users)
Route::middleware(['auth'])->prefix('ajax')->name('ajax.')->group(function () {
    Route::get('/attendance/summary', [QRSessionController::class, 'getAttendanceSummary']);
});