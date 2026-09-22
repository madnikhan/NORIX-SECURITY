<?php

use App\Http\Controllers\CandidatePortalController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\Staff\StaffAuthController;
use App\Http\Controllers\Staff\StaffPortalController;
use Illuminate\Support\Facades\Route;

Route::redirect('/login', '/admin/login');

Route::get('/', [MarketingController::class, 'home'])->name('home');
Route::get('/about', [MarketingController::class, 'about'])->name('about');
Route::get('/services', [MarketingController::class, 'services'])->name('services');
Route::get('/policies', [MarketingController::class, 'policies'])->name('policies');
Route::get('/policies/{policy}/download', [MarketingController::class, 'downloadPolicy'])->name('policies.download');
Route::get('/contact', [MarketingController::class, 'contact'])->name('contact');
Route::post('/contact', [MarketingController::class, 'storeContact'])->name('contact.store');
Route::get('/llms.txt', [MarketingController::class, 'llmsTxt']);
Route::get('/sitemap.xml', [MarketingController::class, 'sitemap']);

Route::get('/careers', [CareerController::class, 'index'])->name('careers.index');
Route::get('/careers/{job}/apply', [CareerController::class, 'apply'])->name('careers.apply');
Route::post('/careers/{job}/apply', [CareerController::class, 'store'])->name('careers.store');

Route::get('/candidate/login', [CandidatePortalController::class, 'showLogin'])->name('candidate.login');
Route::post('/candidate/login', [CandidatePortalController::class, 'login'])->name('candidate.login.submit');

Route::middleware('auth:candidate')->group(function () {
    Route::get('/candidate', [CandidatePortalController::class, 'dashboard'])->name('candidate.dashboard');
    Route::post('/candidate/logout', [CandidatePortalController::class, 'logout'])->name('candidate.logout');
    Route::post('/candidate/documents/{document}/reupload', [CandidatePortalController::class, 'reupload'])->name('candidate.reupload');
});

Route::prefix('staff')->name('staff.')->group(function () {
    Route::get('/login', [StaffAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [StaffAuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login.submit');
    Route::get('/set-password/{token}', [StaffAuthController::class, 'showSetPassword'])->name('set-password');
    Route::post('/set-password/{token}', [StaffAuthController::class, 'setPassword'])
        ->middleware('throttle:10,1')
        ->name('set-password.submit');

    Route::middleware('auth:staff')->group(function () {
        Route::post('/logout', [StaffAuthController::class, 'logout'])->name('logout');
        Route::get('/', [StaffPortalController::class, 'home'])->name('home');
        Route::get('/schedule', [StaffPortalController::class, 'schedule'])->name('schedule');
        Route::post('/shifts/{shift}/clock', [StaffPortalController::class, 'clock'])
            ->middleware('throttle:30,1')
            ->name('clock');
        Route::get('/hours', [StaffPortalController::class, 'hours'])->name('hours');
        Route::get('/hours/export', [StaffPortalController::class, 'hoursExport'])->name('hours.export');
        Route::get('/analytics', [StaffPortalController::class, 'analytics'])->name('analytics');
        Route::get('/incidents', [StaffPortalController::class, 'incidents'])->name('incidents');
        Route::post('/incidents', [StaffPortalController::class, 'storeIncident'])->name('incidents.store');
        Route::get('/messages', [StaffPortalController::class, 'messages'])->name('messages');
        Route::post('/messages', [StaffPortalController::class, 'storeMessage'])->name('messages.store');
        Route::get('/leave', [StaffPortalController::class, 'leave'])->name('leave');
        Route::post('/leave', [StaffPortalController::class, 'storeLeave'])->name('leave.store');
        Route::get('/alerts', [StaffPortalController::class, 'alerts'])->name('alerts');
        Route::get('/profile', [StaffPortalController::class, 'profile'])->name('profile');
        Route::post('/profile/password', [StaffPortalController::class, 'updatePassword'])->name('profile.password');
        Route::post('/policies/{policy}/acknowledge', [StaffPortalController::class, 'acknowledgePolicy'])->name('policies.acknowledge');
    });
});
