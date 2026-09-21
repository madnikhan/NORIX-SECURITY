<?php

use App\Http\Controllers\CandidatePortalController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\MarketingController;
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
