<?php

use App\Livewire\NationalSchool\DmsApprovedCadre;
use App\Livewire\NationalSchool\NationalSchoolOverview;
use App\Livewire\NationalSchool\PrincipalList;
use App\Livewire\NationalSchool\SchoolList;
use App\Livewire\NationalSchool\SleasList;
use App\Livewire\NationalSchool\TeacherList;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    Route::get('national-school/overview', NationalSchoolOverview::class)
        ->name('national-school.overview')
        ->middleware(['permission:national-school.overview.view']);

    /*
    |--------------------------------------------------------------------------
    | National School List
    |--------------------------------------------------------------------------
    */
    Route::get('national-school/list', SchoolList::class)
        ->name('national-school.list')
        ->middleware(['permission:national-school.list.view']);

    Route::get('national-school/teacher-list', TeacherList::class)
        ->name('national-school.teacher-list')
        ->middleware(['permission:national-school.teacher-list.view']);

    Route::get('national-school/principal-list', PrincipalList::class)
        ->name('national-school.principal-list')
        ->middleware(['permission:national-school.principal-list.view']);

    Route::get('national-school/sleas-list', SleasList::class)
        ->name('national-school.sleas-list')
        ->middleware(['permission:national-school.sleas-list.view']);

    Route::get('national-school/dms-approved-cadre', DmsApprovedCadre::class)
        ->name('national-school.dms-approved-cadre')
        ->middleware(['permission:national-school.dms-approved-cadre.view']);
});
