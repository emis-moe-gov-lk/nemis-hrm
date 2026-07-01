<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Peoples\PeoplesList;
use App\Livewire\Peoples\Profile\PeopleProfile;

Route::middleware(['auth'])->group(function () {

/*
    |--------------------------------------------------------------------------
    | Peoples List
    |--------------------------------------------------------------------------
    */
    Route::get('peoples/list', PeoplesList::class)
        ->name('peoples.list')
        ->middleware(['permission:peoples.list.view']);

    Route::get('peoples/profile/{id}', PeopleProfile::class)
        ->name('peoples.profile.view')
        ->middleware(['permission:peoples.profile.view']);

});
