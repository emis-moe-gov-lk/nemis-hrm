<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\InstitutionGroups\Index;
use App\Livewire\InstitutionGroups\InstitutionList;
use App\Livewire\InstitutionGroups\PrincipleList;
use App\Livewire\InstitutionGroups\TeachersList;

Route::middleware(['auth'])->group(function () {
    Route::get('institution-groups/index', Index::class)
        ->name('institution-groups.index');
    Route::get('institution-groups/institutions-list', InstitutionList::class)
        ->name('institution-groups.institutions-list');
    Route::get('institution-groups/principle-list', PrincipleList::class)
        ->name('institution-groups.principle-list');
    Route::get('institution-groups/teachers-list', TeachersList::class)
        ->name('institution-groups.teachers-list');
});
