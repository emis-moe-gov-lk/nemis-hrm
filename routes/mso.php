<?php

use App\Livewire\MSO\MSOEdit;
use App\Livewire\MSO\MSOList;
use App\Livewire\MSO\MSOCreate;
use App\Livewire\MSO\BulkMSOImport;
use Illuminate\Support\Facades\Route;
use App\Livewire\MSO\Profile\MSOIndex;
use App\Exports\TeachersTemplateExport;
use App\Http\Controllers\Pdf\TeacherId;
use App\Livewire\MSO\Profile\MSOFamily;
use App\Http\Controllers\Pdf\TeacherPdf;
use App\Livewire\MSO\Profile\EditRequest;
use App\Livewire\MSO\Profile\MSOEmployment;
use App\Livewire\MSO\Profile\PensionPayment;
use App\Livewire\MSO\Profile\MSOQualification;



Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | MSO List
    |--------------------------------------------------------------------------
    */
    Route::get('mso/list', MSOList::class)
        ->name('mso.list')
        ->middleware(['permission:mso.list.view']);


    /*
    |--------------------------------------------------------------------------
    | MSO Profile – View Pages
    |--------------------------------------------------------------------------
    */
    Route::get('mso/profile/{id}/index', MSOIndex::class)
        ->name('mso.profile.index')
        ->middleware(['permission:mso.profile.general.view']);

    Route::get('mso/profile/{id}/qualification', MSOQualification::class)
        ->name('mso.profile.qualification')
        ->middleware(['permission:mso.profile.qualification.view']);

    Route::get('mso/profile/{id}/employment', MSOEmployment::class)
        ->name('mso.profile.employment')
        ->middleware(['permission:mso.profile.employment.view']);

    Route::get('mso/profile/{id}/family', MSOFamily::class)
        ->name('mso.profile.family')
        ->middleware(['permission:mso.profile.family.view']);

    Route::get('mso/profile/{id}/edit-request', EditRequest::class)
        ->name('mso.profile.edit-request')
        ->middleware(['permission:mso.profile.edit-request.view']);

    Route::get('mso/profile/{id}/pension-and-payment', PensionPayment::class)
        ->name('mso.profile.pension-and-payment')
        ->middleware(['permission:mso.profile.pension-and-payment.view']);


    /*
    |--------------------------------------------------------------------------
    | MSO Create
    |--------------------------------------------------------------------------
    */
    Route::get('mso/create', MSOCreate::class)
        ->name('mso.create')
        ->middleware(['permission:mso.create']);


    /*
    |--------------------------------------------------------------------------
    | MSO Edit
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:mso.update'])->group(function () {

        Route::get('mso/edit', MSOEdit::class)
            ->name('mso.edit');

    });


    /*
    |--------------------------------------------------------------------------
    | MSO Bulk Upload (Optional – commented)
    |--------------------------------------------------------------------------
    */
    /*
    Route::middleware(['permission:mso.bulk.upload'])->group(function () {

        Route::get('mso/bulk-upload', BulkMSOImport::class)
            ->name('mso.bulk.upload');

        Route::get('/download-mso-template', function () {
            return Excel::download(
                new TeachersTemplateExport,
                'mso_import_template.xlsx'
            );
        })->name('mso.download.template');

    });
    */

});

