<?php

use App\Livewire\Slas\SlasEdit;
use App\Livewire\Slas\SlasList;
use App\Livewire\Slas\SlasCreate;
use Maatwebsite\Excel\Facades\Excel;
use App\Livewire\Slas\BulkSlasImport;
use Illuminate\Support\Facades\Route;
use App\Exports\TeachersTemplateExport;
use App\Livewire\Slas\Profile\SlasIndex;
use App\Livewire\Slas\Profile\SlasFamily;
use App\Livewire\Slas\Profile\EditRequest;
use App\Livewire\Slas\Profile\PensionPayment;
use App\Livewire\Slas\Profile\SlasEmployment;
use App\Livewire\Slas\Profile\SlasQualification;




Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Slas List
    |--------------------------------------------------------------------------
    */
    Route::get('slas/list', SlasList::class)
        ->name('slas.list')
        ->middleware(['permission:slas.list.view']);


    /*
    |--------------------------------------------------------------------------
    | Slas Profile – View Pages
    |--------------------------------------------------------------------------
    */
    Route::get('slas/profile/{id}/index', SlasIndex::class)
        ->name('slas.profile.index')
        ->middleware(['permission:slas.profile.general.view']);

    Route::get('slas/profile/{id}/qualification', SlasQualification::class)
        ->name('slas.profile.qualification')
        ->middleware(['permission:slas.profile.qualification.view']);

    Route::get('slas/profile/{id}/employment', SlasEmployment::class)
        ->name('slas.profile.employment')
        ->middleware(['permission:slas.profile.employment.view']);

    Route::get('slas/profile/{id}/family', SlasFamily::class)
        ->name('slas.profile.family')
        ->middleware(['permission:slas.profile.family.view']);

    Route::get('slas/profile/{id}/edit-request', EditRequest::class)
        ->name('slas.profile.edit-request')
        ->middleware(['permission:slas.profile.edit-request.view']);

    Route::get('slas/profile/{id}/pension-and-payment', PensionPayment::class)
        ->name('slas.profile.pension-and-payment')
        ->middleware(['permission:slas.profile.pension-and-payment.view']);
        // (Add permission later if needed)


    /*
    |--------------------------------------------------------------------------
    | Slas Create
    |--------------------------------------------------------------------------
    */
    Route::get('slas/create', SlasCreate::class)
        ->name('slas.create')
        ->middleware(['permission:slas.create']);


    /*
    |--------------------------------------------------------------------------
    | Slas Edit
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:slas.update'])->group(function () {
        Route::get('slas/edit', SlasEdit::class)
            ->name('slas.edit');
    });


    /*
    |--------------------------------------------------------------------------
    | Slas Bulk Upload (Optional – currently disabled)
    |--------------------------------------------------------------------------
    */
    /*
    Route::middleware(['permission:sltas.bulk.upload'])->group(function () {

        Route::get('slas/bulk-upload', BulkSlasImport::class)
            ->name('slas.bulk.upload');

        Route::get('/download-slas-template', function () {
            return Excel::download(
                new TeachersTemplateExport,
                'sltas_import_template.xlsx'
            );
        })->name('slas.download.template');

    });
    */

});

