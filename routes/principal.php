<?php

use Illuminate\Support\Facades\Route;
use App\Exports\TeachersTemplateExport;
use App\Http\Controllers\Pdf\TeacherId;
use App\Http\Controllers\Pdf\TeacherPdf;
use App\Livewire\Principal\PrincipalEdit;
use App\Livewire\Principal\PrincipalList;
use App\Livewire\Principal\PrincipalCreate;
use App\Livewire\Principal\Profile\EditRequest;
use App\Livewire\Principal\BulkPrincipalsImport;
use App\Livewire\Principal\Profile\PensionPayment;
use App\Livewire\Principal\Profile\PrincipalIndex;
use App\Livewire\Principal\Profile\PrincipalFamily;
use App\Livewire\Principal\Profile\PrincipalEmployment;
use App\Livewire\Principal\Profile\PrincipalQualification;


Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Principal List
    |--------------------------------------------------------------------------
    */
    Route::get('principal/list', PrincipalList::class)
        ->name('principal.list')
        ->middleware(['permission:principal.list.view']);


    /*
    |--------------------------------------------------------------------------
    | Principal Profile – View Pages
    |--------------------------------------------------------------------------
    */
    Route::get('principal/profile/{id}/index', PrincipalIndex::class)
        ->name('principal.profile.index')
        ->middleware(['permission:principal.profile.general.view']);

    Route::get('principal/profile/{id}/qualification', PrincipalQualification::class)
        ->name('principal.profile.qualification')
        ->middleware(['permission:principal.profile.qualification.view']);

    Route::get('principal/profile/{id}/employment', PrincipalEmployment::class)
        ->name('principal.profile.employment')
        ->middleware(['permission:principal.profile.employment.view']);

    Route::get('principal/profile/{id}/family', PrincipalFamily::class)
        ->name('principal.profile.family')
        ->middleware(['permission:principal.profile.family.view']);

    Route::get('principal/profile/{id}/edit-request', EditRequest::class)
        ->name('principal.profile.edit-request')
        ->middleware(['permission:principal.profile.edit-request.view']);

    Route::get('principal/profile/{id}/pension-and-payment', PensionPayment::class)
        ->name('principal.profile.pension-and-payment')
        ->middleware(['permission:principal.profile.pension-and-payment.view']);


    /*
    |--------------------------------------------------------------------------
    | Principal Create
    |--------------------------------------------------------------------------
    */
    Route::get('principal/create', PrincipalCreate::class)
        ->name('principal.create')
        ->middleware(['permission:principal.create']);


    /*
    |--------------------------------------------------------------------------
    | Principal Edit
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:principal.update'])->group(function () {

        Route::get('principal/edit', PrincipalEdit::class)
            ->name('principal.edit');

    });


    /*
    |--------------------------------------------------------------------------
    | Principal Bulk Upload (Optional – commented)
    |--------------------------------------------------------------------------
    */
    /*
    Route::middleware(['permission:principal.bulk.upload'])->group(function () {

        Route::get('principal/bulk-upload', BulkPrincipalsImport::class)
            ->name('principal.bulk.upload');

        Route::get('/download-principals-template', function () {
            return Excel::download(
                new TeachersTemplateExport,
                'principals_import_template.xlsx'
            );
        })->name('principals.download.template');

    });
    */

});

