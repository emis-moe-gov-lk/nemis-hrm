<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\MyProfile\MyProfileIndex;
use App\Livewire\MyProfile\MyQualification;
use App\Livewire\MyProfile\MyEmployment;
use App\Livewire\MyProfile\MyFamily;
use App\Livewire\MyProfile\EditRequest;
use App\Livewire\MyProfile\PensionPayment;



Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | My Profile
    |--------------------------------------------------------------------------
    */
    Route::get('my-profile/index', MyProfileIndex::class)
        ->name('my-profile.index')
        ->middleware(['permission:my-profile.general.view']);

    Route::get('my-profile/qualification', MyQualification::class)
        ->name('my-profile.qualification')
        ->middleware(['permission:my-profile.qualification.view']);

    Route::get('my-profile/employment', MyEmployment::class)
        ->name('my-profile.employment')
        ->middleware(['permission:my-profile.employment.view']);

    Route::get('my-profile/family', MyFamily::class)
        ->name('my-profile.family')
        ->middleware(['permission:my-profile.family.view']);

    Route::get('my-profile/pension-and-payment', PensionPayment::class)
        ->name('my-profile.pension-and-payment')
        ->middleware(['permission:my-profile.pension-and-payment.view']);

    Route::get('my-profile/edit-request', EditRequest::class)
        ->name('my-profile.edit-request')
        ->middleware(['permission:my-profile.edit-request.view']);
});
