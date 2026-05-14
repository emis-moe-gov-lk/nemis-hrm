<?php

use App\Livewire\NationalSchool\DmsApprovedCadre;
use App\Livewire\NationalSchool\PrincipalList;
use App\Livewire\NationalSchool\SchoolList;
use App\Livewire\NationalSchool\SleasList;
use App\Livewire\NationalSchool\TeacherList;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | National School List
    |--------------------------------------------------------------------------
    */
    Route::get('national-school/list', SchoolList::class)
        ->name('national-school.list');

    Route::get('national-school/teacher-list', TeacherList::class)
        ->name('national-school.teacher-list');

    Route::get('national-school/principal-list', PrincipalList::class)
        ->name('national-school.principal-list');

    Route::get('national-school/sleas-list', SleasList::class)
        ->name('national-school.sleas-list');

    Route::get('national-school/dms-approved-cadre', DmsApprovedCadre::class)
        ->name('national-school.dms-approved-cadre');
});
