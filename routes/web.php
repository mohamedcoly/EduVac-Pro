<?php

use App\Http\Controllers\Admin\VerificationController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::patch('/ecoles/{school}/verification', [VerificationController::class, 'updateSchool'])
        ->middleware('can:verify,school')
        ->name('schools.verification');

    Route::patch('/enseignants/{profile}/verification', [VerificationController::class, 'updateTeacher'])
        ->middleware('can:verify,profile')
        ->name('teachers.verification');
});

require __DIR__.'/auth.php';
