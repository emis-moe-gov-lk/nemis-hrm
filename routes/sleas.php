<?php


use App\Livewire\Sleas\SleasEdit;
use App\Livewire\Sleas\SleasList;
use App\Livewire\Sleas\SleasCreate;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use App\Exports\TeachersTemplateExport;
use App\Http\Controllers\Pdf\TeacherId;
use App\Livewire\Sleas\BulkSleasImport;
use App\Http\Controllers\Pdf\TeacherPdf;
use App\Livewire\Sleas\Profile\SleasIndex;
use App\Livewire\Sleas\Profile\EditRequest;
use App\Livewire\Sleas\Profile\SleasFamily;
use App\Livewire\Sleas\Profile\PensionPayment;
use App\Livewire\Sleas\Profile\SleasEmployment;
use App\Livewire\Sleas\Profile\SleasQualification;


Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | SLEAS List
    |--------------------------------------------------------------------------
    */
    Route::get('sleas/list', SleasList::class)
        ->name('sleas.list')
        ->middleware(['permission:sleas.list.view']);


    /*
    |--------------------------------------------------------------------------
    | SLEAS Profile – View Pages
    |--------------------------------------------------------------------------
    */
    Route::get('sleas/profile/{id}/index', SleasIndex::class)
        ->name('sleas.profile.index')
        ->middleware(['permission:sleas.profile.general.view']);

    Route::get('sleas/profile/{id}/qualification', SleasQualification::class)
        ->name('sleas.profile.qualification')
        ->middleware(['permission:sleas.profile.qualification.view']);

    Route::get('sleas/profile/{id}/employment', SleasEmployment::class)
        ->name('sleas.profile.employment')
        ->middleware(['permission:sleas.profile.employment.view']);

    Route::get('sleas/profile/{id}/family', SleasFamily::class)
        ->name('sleas.profile.family')
        ->middleware(['permission:sleas.profile.family.view']);

    Route::get('sleas/profile/{id}/pension-and-payment', PensionPayment::class)
        ->name('sleas.profile.pension-and-payment')
        ->middleware(['permission:sleas.profile.pension-and-payment.view']);

    Route::get('sleas/profile/{id}/edit-request', EditRequest::class)
        ->name('sleas.profile.edit-request')
        ->middleware(['permission:sleas.profile.edit-request.view']);


    /*
    |--------------------------------------------------------------------------
    | SLEAS Create
    |--------------------------------------------------------------------------
    */
    Route::get('sleas/create', SleasCreate::class)
        ->name('sleas.create')
        ->middleware(['permission:sleas.create']);


    /*
    |--------------------------------------------------------------------------
    | SLEAS Edit
    |--------------------------------------------------------------------------
    */
    Route::middleware(['permission:sleas.update'])->group(function () {

        Route::get('sleas/edit', SleasEdit::class)
            ->name('sleas.edit');

    });


    /*
    |--------------------------------------------------------------------------
    | SLEAS Bulk Upload (Optional – commented)
    |--------------------------------------------------------------------------
    */
    /*
    Route::middleware(['permission:sleas.bulk.upload'])->group(function () {

        Route::get('sleas/bulk-upload', BulkSleasImport::class)
            ->name('sleas.bulk.upload');

        Route::get('/download-sleas-template', function () {
            return Excel::download(
                new TeachersTemplateExport,
                'sleas_import_template.xlsx'
            );
        })->name('sleas.download.template');

    });
    */

});

