<?php

use App\Http\Controllers\Admin\VerificationController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SchoolJobOfferController;
use App\Http\Controllers\TeacherDirectoryController;
use App\Http\Controllers\TeacherDocumentController;
use App\Http\Controllers\TeacherJobOfferController;
use App\Http\Controllers\TeacherMatchingController;
use App\Http\Controllers\TeacherProfileController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', 'role:teacher'])->group(function (): void {
    Route::get('/mon-profil', [TeacherProfileController::class, 'edit'])->name('teacher-profile.edit');
    Route::put('/mon-profil', [TeacherProfileController::class, 'update'])->name('teacher-profile.update');
    Route::post('/mes-documents', [TeacherDocumentController::class, 'store'])->name('teacher-documents.store');
    Route::get('/offres', [TeacherJobOfferController::class, 'index'])->name('teacher-offers.index');
    Route::get('/offres/{offer}', [TeacherJobOfferController::class, 'show'])->name('teacher-offers.show');
    Route::get('/mes-candidatures', [ApplicationController::class, 'index'])->name('applications.index');
    Route::post('/offres/{offer}/candidater', [ApplicationController::class, 'store'])->name('applications.store');
    Route::patch('/mes-candidatures/{application}/retirer', [ApplicationController::class, 'withdraw'])->name('applications.withdraw');
});

Route::middleware(['auth', 'verified', 'role:school_manager|school_recruiter|platform_admin'])->group(function (): void {
    Route::get('/etablissement/offres', [SchoolJobOfferController::class, 'index'])->name('school-offers.index');
    Route::get('/etablissement/offres/creer', [SchoolJobOfferController::class, 'create'])->name('school-offers.create');
    Route::post('/etablissement/offres', [SchoolJobOfferController::class, 'store'])->name('school-offers.store');
    Route::get('/etablissement/offres/{offer}/modifier', [SchoolJobOfferController::class, 'edit'])->name('school-offers.edit');
    Route::put('/etablissement/offres/{offer}', [SchoolJobOfferController::class, 'update'])->name('school-offers.update');
    Route::patch('/etablissement/offres/{offer}/publier', [SchoolJobOfferController::class, 'publish'])->name('school-offers.publish');
    Route::patch('/etablissement/offres/{offer}/cloturer', [SchoolJobOfferController::class, 'close'])->name('school-offers.close');
    Route::get('/etablissement/offres/{offer}/matching', [TeacherMatchingController::class, 'index'])->name('school-offers.matching');
    Route::get('/etablissement/enseignants', [TeacherDirectoryController::class, 'index'])->name('school-teachers.index');
    Route::get('/etablissement/candidatures', [ApplicationController::class, 'index'])->name('school-applications.index');
    Route::patch('/etablissement/candidatures/{application}/statut', [ApplicationController::class, 'updateStatus'])->name('school-applications.status');
});

Route::get('/documents/{document}/download', [TeacherDocumentController::class, 'download'])
    ->middleware(['auth', 'verified', 'can:view,document'])->name('teacher-documents.download');

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::patch('/ecoles/{school}/verification', [VerificationController::class, 'updateSchool'])->middleware('can:verify,school')->name('schools.verification');
    Route::patch('/enseignants/{profile}/verification', [VerificationController::class, 'updateTeacher'])->middleware('can:verify,profile')->name('teachers.verification');
});

require __DIR__.'/auth.php';
