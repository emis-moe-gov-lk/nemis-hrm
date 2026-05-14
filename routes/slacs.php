<?php

use App\Livewire\Slacs\SlacsEdit;
use App\Livewire\Slacs\SlacsList;
use App\Livewire\Slacs\SlacsCreate;
use Maatwebsite\Excel\Facades\Excel;
use App\Livewire\Slacs\BulkSlacsImport;
use Illuminate\Support\Facades\Route;
use App\Exports\TeachersTemplateExport;
use App\Livewire\Slacs\Profile\SlacsIndex;
use App\Livewire\Slacs\Profile\SlacsFamily;
use App\Livewire\Slacs\Profile\EditRequest;
use App\Livewire\Slacs\Profile\PensionPayment;
use App\Livewire\Slacs\Profile\SlacsEmployment;
use App\Livewire\Slacs\Profile\SlacsQualification;




Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Slacs List
    |--------------------------------------------------------------------------
    */
    Route::get('slacs/list', SlacsList::class)
        ->name('slacs.list')
        ->middleware(['permission:slacs.list.view']);


    /*
    |--------------------------------------------------------------------------
    | Slacs Profile – View Pages
    |--------------------------------------------------------------------------
    */
    Route::get('slacs/profile/{id}/index', SlacsIndex::class)
        ->name('slacs.profile.index')
        ->middleware(['permission:slacs.profile.general.view']);

    Route::get('slacs/profile/{id}/qualification', SlacsQualification::class)
        ->name('slacs.profile.qualification')
        ->middleware(['permission:slacs.profile.qualification.view']);

    Route::get('slacs/profile/{id}/employment', SlacsEmployment::class)
        ->name('slacs.profile.employment')
        ->middleware(['permission:slacs.profile.employment.view']);

    Route::get('slacs/profile/{id}/family', SlacsFamily::class)
        ->name('slacs.profile.family')
        ->middleware(['permission:slacs.profile.family.view']);

    Route::get('slacs/profile/{id}/edit-request', EditRequest::class)
        ->name('slacs.profile.edit-request')
        ->middleware(['permission:slacs.profile.edit-request.view']);

    Route::get('slacs/profile/{id}/pension-and-payment', PensionPayment::class)
        ->name('slacs.profile.pension-and-payment')
        ->middleware(['permission:slacs.profile.pension-and-payment.view']);
        // (Add permission later if needed)


    /*
    |--------------------------------------------------------------------------
    | Slacs Create
    |--------------------------------------------------------------------------
    */
    Route::get('slacs/create', SlacsCreate::class)
        ->name('slacs.create')
        ->middleware(['permission:slacs.create']);


    /*
    |--------------------------------------------------------------------------
    | Slacs Edit
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:slacs.update'])->group(function () {
        Route::get('slacs/edit', SlacsEdit::class)
            ->name('slacs.edit');
    });


    /*
    |--------------------------------------------------------------------------
    | Slacs Bulk Upload (Optional – currently disabled)
    |--------------------------------------------------------------------------
    */
    /*
    Route::middleware(['permission:slacs.bulk.upload'])->group(function () {

        Route::get('slacs/bulk-upload', BulkSlacsImport::class)
            ->name('slacs.bulk.upload');

        Route::get('/download-slacs-template', function () {
            return Excel::download(
                new TeachersTemplateExport,
                'slacs_import_template.xlsx'
            );
        })->name('slacs.download.template');

    });
    */

});

