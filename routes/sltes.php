<?php

use App\Livewire\Sltes\SltesEdit;
use App\Livewire\Sltes\SltesList;
use App\Livewire\Sltes\SltesCreate;
use App\Livewire\Sltes\BulksltesImport;
use Illuminate\Support\Facades\Route;
use App\Livewire\Sltes\Profile\SltesIndex;
use App\Exports\TeachersTemplateExport;
use App\Http\Controllers\Pdf\TeacherId;
use App\Livewire\Sltes\Profile\SltesFamily;
use App\Http\Controllers\Pdf\TeacherPdf;
use App\Livewire\Sltes\Profile\EditRequest;
use App\Livewire\Sltes\Profile\SltesEmployment;
use App\Livewire\Sltes\Profile\PensionPayment;
use App\Livewire\Sltes\Profile\SltesQualification;

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | sltes List
    |--------------------------------------------------------------------------
    */
    Route::get('sltes/list', SltesList::class)
        ->name('sltes.list')
        ->middleware(['permission:sltes.list.view']);


    /*
    |--------------------------------------------------------------------------
    | sltes Profile – View Pages
    |--------------------------------------------------------------------------
    */
    Route::get('sltes/profile/{id}/index', SltesIndex::class)
        ->name('sltes.profile.index')
        ->middleware(['permission:sltes.profile.general.view']);

    Route::get('sltes/profile/{id}/qualification', SltesQualification::class)
        ->name('sltes.profile.qualification')
        ->middleware(['permission:sltes.profile.qualification.view']);

    Route::get('sltes/profile/{id}/employment', SltesEmployment::class)
        ->name('sltes.profile.employment')
        ->middleware(['permission:sltes.profile.employment.view']);

    Route::get('sltes/profile/{id}/family', SltesFamily::class)
        ->name('sltes.profile.family')
        ->middleware(['permission:sltes.profile.family.view']);

    Route::get('sltes/profile/{id}/edit-request', EditRequest::class)
        ->name('sltes.profile.edit-request')
        ->middleware(['permission:sltes.profile.edit-request.view']);

    Route::get('sltes/profile/{id}/pension-and-payment', PensionPayment::class)
        ->name('sltes.profile.pension-and-payment')
        ->middleware(['permission:sltes.profile.pension-and-payment.view']);
    // (Add permission later if needed)


    /*
    |--------------------------------------------------------------------------
    | sltes Create
    |--------------------------------------------------------------------------
    */
    Route::get('sltes/create', SltesCreate::class)
        ->name('sltes.create')
        ->middleware(['permission:sltes.create']);


    /*
    |--------------------------------------------------------------------------
    | sltes Edit
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:sltes.update'])->group(function () {
        Route::get('sltes/edit', SltesEdit::class)
            ->name('sltes.edit');
    });


    /*
    |--------------------------------------------------------------------------
    | sltes Bulk Upload (Optional – currently disabled)
    |--------------------------------------------------------------------------
    */
    /*
    Route::middleware(['permission:sltes.bulk.upload'])->group(function () {

        Route::get('sltes/bulk-upload', BulksltesImport::class)
            ->name('sltes.bulk.upload');

        Route::get('/download-sltes-template', function () {
            return Excel::download(
                new TeachersTemplateExport,
                'sltes_import_template.xlsx'
            );
        })->name('sltes.download.template');

    });
    */
});
