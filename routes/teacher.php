<?php

use Maatwebsite\Excel\Facades\Excel;
use App\Livewire\Teacher\TeacherEdit;
use App\Livewire\Teacher\TeacherList;
use Illuminate\Support\Facades\Route;
use App\Exports\TeachersTemplateExport;
use App\Http\Controllers\Pdf\TeacherId;
use App\Livewire\Teacher\TeacherCreate;
use App\Http\Controllers\Pdf\TeacherPdf;
use App\Livewire\Teacher\BulkTeachersImport;
use App\Livewire\Teacher\Profile\TeacherIndex;
use App\Livewire\Teacher\Profile\TeacherFamily;
use App\Livewire\Teacher\Profile\PensionPayment;
use App\Livewire\Teacher\Profile\TeacherEmployment;
use App\Livewire\Teacher\Profile\TeacherQualification;
use App\Livewire\Teacher\Profile\EditRequest;
use App\Livewire\Teacher\TeacherOverview;
use App\Livewire\Teacher\LeaveOperations;
use App\Livewire\Teacher\TeacherAttachments;
use App\Livewire\Teacher\Promotions;
use App\Livewire\Teacher\PensionSystem;
use App\Livewire\Teacher\Specializations;
use App\Livewire\Teacher\BenefitsAndHealth;

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Teacher List
    |--------------------------------------------------------------------------
    */
    Route::get('teacher/list', TeacherList::class)
        ->name('teacher.list')
        ->middleware(['permission:teacher.list.view']);


    /*
    |--------------------------------------------------------------------------
    | Teacher Profile – View Pages
    |--------------------------------------------------------------------------
    */
    Route::get('teacher/profile/{id}/index', TeacherIndex::class)
        ->name('teacher.profile.index')
        ->middleware(['permission:teacher.profile.general.view']);

    Route::get('teacher/profile/{id}/qualification', TeacherQualification::class)
        ->name('teacher.profile.qualification')
        ->middleware(['permission:teacher.profile.qualification.view']);

    Route::get('teacher/profile/{id}/employment', TeacherEmployment::class)
        ->name('teacher.profile.employment')
        ->middleware(['permission:teacher.profile.employment.view']);

    Route::get('teacher/profile/{id}/family', TeacherFamily::class)
        ->name('teacher.profile.family')
        ->middleware(['permission:teacher.profile.family.view']);

    Route::get('teacher/profile/{id}/pension-and-payment', PensionPayment::class)
        ->name('teacher.profile.pension-and-payment')
        ->middleware(['permission:teacher.profile.pension-and-payment.view']);

    Route::get('teacher/profile/{id}/edit-request', EditRequest::class)
        ->name('teacher.profile.edit-request')
        ->middleware(['permission:teacher.profile.edit-request.view']);

    Route::get('/pdf/id/{id}', [TeacherId::class, 'generatePDF'])
        ->name('teacher.id.pdf')
        ->middleware(['permission:teacher.profile.id.view']);

    Route::get('/pdf/profile/{id}', [TeacherPdf::class, 'generateSimplePdf'])
        ->name('teacher.profile.pdf')
        ->middleware(['permission:teacher.profile.pdf.view']);

    Route::get('teacher/overview', TeacherOverview::class)
        ->name('teacher.overview')
        ->middleware(['permission:teacher.overview.view']);

    Route::get('teacher/leave-operations', LeaveOperations::class)
        ->name('teacher.leave-operations')
        ->middleware(['permission:teacher.leave-operations.view']);

    Route::get('teacher/attachments', TeacherAttachments::class)
        ->name('teacher.attachments')
        ->middleware(['permission:teacher.attachments.view']);

    Route::get('teacher/promotions', Promotions::class)
        ->name('teacher.promotions')
        ->middleware(['permission:teacher.promotion.view']);

    Route::get('teacher/pension-system', PensionSystem::class)
        ->name('teacher.pension-system')
        ->middleware(['permission:teacher.pension-system.view']);

    Route::get('teacher/specializations', Specializations::class)
        ->name('teacher.specializations')
        ->middleware(['permission:teacher.specializations.view']);

    Route::get('teacher/benefits-and-health', BenefitsAndHealth::class)
        ->name('teacher.benefits-and-health')
        ->middleware(['permission:teacher.benefits-and-health.view']);

    /*
    |--------------------------------------------------------------------------
    | Teacher Create
    |--------------------------------------------------------------------------
    */
    Route::get('teacher/create', TeacherCreate::class)
        ->name('teacher.create')
        ->middleware(['permission:teacher.create']);


    /*
    |--------------------------------------------------------------------------
    | Teacher Bulk Upload
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:teacher.bulk.upload'])->group(function () {

        Route::get('teacher/bulk-upload', BulkTeachersImport::class)
            ->name('teacher.bulk.upload');

        Route::get('/download-teachers-template', function () {
            return Excel::download(
                new TeachersTemplateExport,
                'teachers_import_template.xlsx'
            );
        })->name('teachers.download.template');
    });


    /*
    |--------------------------------------------------------------------------
    | Teacher Edit
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:teacher.update'])->group(function () {

        Route::get('teacher/edit', TeacherEdit::class)
            ->name('teacher.edit');
    });
});
