<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\LeadershipController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MinistryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

// Auth routes (built-in)
require __DIR__ . '/auth.php';

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Members
    Route::resource('members', MemberController::class);
    Route::post('members/{member}/departments', [MemberController::class, 'assignDepartment'])->name('members.departments.assign');
    Route::delete('members/{member}/departments', [MemberController::class, 'removeDepartment'])->name('members.departments.remove');
    Route::post('members/{member}/ministries', [MemberController::class, 'assignMinistry'])->name('members.ministries.assign');
    Route::delete('members/{member}/ministries', [MemberController::class, 'removeMinistry'])->name('members.ministries.remove');
    Route::post('members/{member}/leadership', [MemberController::class, 'assignLeadership'])->name('members.leadership.assign');
    Route::delete('members/{member}/leadership', [MemberController::class, 'removeLeadership'])->name('members.leadership.remove');

    // Departments
    Route::resource('departments', DepartmentController::class);

    // Ministries
    Route::resource('ministries', MinistryController::class);

    // Leadership
    Route::resource('leadership', LeadershipController::class);
    Route::post('leadership/{leadership}/members', [LeadershipController::class, 'assignMember'])->name('leadership.members.assign');
    Route::delete('leadership/{leadership}/members', [LeadershipController::class, 'removeMember'])->name('leadership.members.remove');

    // Reports
    Route::resource('reports', ReportController::class);
    Route::patch('reports/{report}/submit', [ReportController::class, 'submit'])->name('reports.submit');
    Route::patch('reports/{report}/review', [ReportController::class, 'review'])->name('reports.review');

    // Finance
    Route::resource('finance', FinanceController::class);

    // Branches
    Route::resource('branches', BranchController::class);

    // Events & Attendance
    Route::resource('events', EventController::class);
    Route::post('events/{event}/attendance', [EventController::class, 'markAttendance'])->name('events.attendance.save');

    // Announcements
    Route::resource('announcements', AnnouncementController::class);

    // Messages
    Route::resource('messages', MessageController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

    // Users (admin only)
    Route::middleware(['can:manage-users'])->group(function () {
        Route::resource('users', UserController::class);
    });
});
