<?php

use App\Livewire\DOS\DOSEdit;
use App\Livewire\DOS\DOSList;
use App\Livewire\DOS\DOSCreate;
use App\Livewire\DOS\BulkDOSImport;
use Illuminate\Support\Facades\Route;
use App\Livewire\DOS\Profile\DOSIndex;
use App\Exports\TeachersTemplateExport;
use App\Http\Controllers\Pdf\TeacherId;
use App\Livewire\DOS\Profile\DOSFamily;
use App\Http\Controllers\Pdf\TeacherPdf;
use App\Livewire\DOS\Profile\EditRequest;
use App\Livewire\DOS\Profile\DOSEmployment;
use App\Livewire\DOS\Profile\PensionPayment;
use App\Livewire\DOS\Profile\DOSQualification;



Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DOS List
    |--------------------------------------------------------------------------
    */
    Route::get('dos/list', DOSList::class)
        ->name('dos.list')
        ->middleware(['permission:dos.list.view']);


    /*
    |--------------------------------------------------------------------------
    | DOS Profile – View Pages
    |--------------------------------------------------------------------------
    */
    Route::get('dos/profile/{id}/index', DOSIndex::class)
        ->name('dos.profile.index')
        ->middleware(['permission:dos.profile.general.view']);

    Route::get('dos/profile/{id}/qualification', DOSQualification::class)
        ->name('dos.profile.qualification')
        ->middleware(['permission:dos.profile.qualification.view']);

    Route::get('dos/profile/{id}/employment', DOSEmployment::class)
        ->name('dos.profile.employment')
        ->middleware(['permission:dos.profile.employment.view']);

    Route::get('dos/profile/{id}/family', DOSFamily::class)
        ->name('dos.profile.family')
        ->middleware(['permission:dos.profile.family.view']);

    Route::get('dos/profile/{id}/edit-request', EditRequest::class)
        ->name('dos.profile.edit-request')
        ->middleware(['permission:dos.profile.edit-request.view']);

    Route::get('dos/profile/{id}/pension-and-payment', PensionPayment::class)
        ->name('dos.profile.pension-and-payment')
        ->middleware(['permission:dos.profile.pension-and-payment.view']);
        // (Add permission later if needed)
    


    /*
    |--------------------------------------------------------------------------
    | DOS Create
    |--------------------------------------------------------------------------
    */
    Route::get('dos/create', DOSCreate::class)
        ->name('dos.create')
        ->middleware(['permission:dos.create']);


    /*
    |--------------------------------------------------------------------------
    | DOS Edit
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:dos.update'])->group(function () {
        Route::get('dos/edit', DOSEdit::class)
            ->name('dos.edit');
    });


    /*
    |--------------------------------------------------------------------------
    | DOS Bulk Upload (Optional – currently disabled)
    |--------------------------------------------------------------------------
    */
    /*
    Route::middleware(['permission:dos.bulk.upload'])->group(function () {

        Route::get('dos/bulk-upload', BulkDOSImport::class)
            ->name('dos.bulk.upload');

        Route::get('/download-dos-template', function () {
            return Excel::download(
                new TeachersTemplateExport,
                'dos_import_template.xlsx'
            );
        })->name('dos.download.template');

    });
    */

});

