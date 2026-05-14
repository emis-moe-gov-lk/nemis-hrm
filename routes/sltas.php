<?php

use App\Livewire\Sltas\SltasEdit;
use App\Livewire\Sltas\SltasList;
use App\Livewire\Sltas\SltasCreate;
use App\Livewire\Sltas\BulkSltasImport;
use Illuminate\Support\Facades\Route;
use App\Livewire\Sltas\Profile\SltasIndex;
use App\Exports\TeachersTemplateExport;
use App\Http\Controllers\Pdf\TeacherId;
use App\Livewire\Sltas\Profile\SltasFamily;
use App\Http\Controllers\Pdf\TeacherPdf;
use App\Livewire\Sltas\Profile\EditRequest;
use App\Livewire\Sltas\Profile\SltasEmployment;
use App\Livewire\Sltas\Profile\PensionPayment;
use App\Livewire\Sltas\Profile\SltasQualification;



Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Sltas List
    |--------------------------------------------------------------------------
    */
    Route::get('sltas/list', SltasList::class)
        ->name('sltas.list')
        ->middleware(['permission:sltas.list.view']);


    /*
    |--------------------------------------------------------------------------
    | Sltas Profile – View Pages
    |--------------------------------------------------------------------------
    */
    Route::get('sltas/profile/{id}/index', SltasIndex::class)
        ->name('sltas.profile.index')
        ->middleware(['permission:sltas.profile.general.view']);

    Route::get('sltas/profile/{id}/qualification', SltasQualification::class)
        ->name('sltas.profile.qualification')
        ->middleware(['permission:sltas.profile.qualification.view']);

    Route::get('sltas/profile/{id}/employment', SltasEmployment::class)
        ->name('sltas.profile.employment')
        ->middleware(['permission:sltas.profile.employment.view']);

    Route::get('sltas/profile/{id}/family', SltasFamily::class)
        ->name('sltas.profile.family')
        ->middleware(['permission:sltas.profile.family.view']);

    Route::get('sltas/profile/{id}/edit-request', EditRequest::class)
        ->name('sltas.profile.edit-request')
        ->middleware(['permission:sltas.profile.edit-request.view']);

    Route::get('sltas/profile/{id}/pension-and-payment', PensionPayment::class)
        ->name('sltas.profile.pension-and-payment')
        ->middleware(['permission:sltas.profile.pension-and-payment.view']);
        // (Add permission later if needed)


    /*
    |--------------------------------------------------------------------------
    | Sltas Create
    |--------------------------------------------------------------------------
    */
    Route::get('sltas/create', SltasCreate::class)
        ->name('sltas.create')
        ->middleware(['permission:sltas.create']);


    /*
    |--------------------------------------------------------------------------
    | Sltas Edit
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:sltas.update'])->group(function () {
        Route::get('sltas/edit', SltasEdit::class)
            ->name('sltas.edit');
    });


    /*
    |--------------------------------------------------------------------------
    | Sltas Bulk Upload (Optional – currently disabled)
    |--------------------------------------------------------------------------
    */
    /*
    Route::middleware(['permission:sltas.bulk.upload'])->group(function () {

        Route::get('sltas/bulk-upload', BulkSltasImport::class)
            ->name('sltas.bulk.upload');

        Route::get('/download-sltas-template', function () {
            return Excel::download(
                new TeachersTemplateExport,
                'sltas_import_template.xlsx'
            );
        })->name('sltas.download.template');

    });
    */

});

