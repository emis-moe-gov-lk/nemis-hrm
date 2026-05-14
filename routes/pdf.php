<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pdf\TeacherPdf;

use App\Http\Controllers\Pdf\EmployeeController;
use App\Http\Controllers\Pdf\ZeoPeportController;
use App\Http\Controllers\Pdf\InstitutionsReportController;

Route::middleware(['auth'])->group(function () {
    Route::get('pdf/zeo-teachers-list/{id}', [ZeoPeportController::class, 'teacherList'])->name('pdf.zeo-teachers-list');
    Route::get('pdf/institutions-teachers-list/{id}', [InstitutionsReportController::class, 'teacherList'])->name('pdf.institutions-teachers-list');
    Route::get('pdf/zeo-institution-list/{id}', [ZeoPeportController::class, 'zeoInstitutionList'])->name('pdf.zeo-institution-list');
    Route::get('pdf/zeo-staff-list/{id}', [ZeoPeportController::class, 'zeoStaffList'])->name('pdf.zeo-staff-list');
    Route::get('/pdf/employee/{id}', [EmployeeController::class, 'generateSimplePdf'])->name('employee.profile.pdf');
    Route::get('/pdf/profile/{id}', [TeacherPdf::class, 'generateSimplePdf'])->name('teacher.profile.pdf')->middleware(['permission:teacher.profile.pdf.view']);
});
