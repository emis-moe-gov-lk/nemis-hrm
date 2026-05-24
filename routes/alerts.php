<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Alerts\AlertsOverview;
use App\Livewire\Alerts\PendingConfirmation;
use App\Livewire\Alerts\PendingVerification;


Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Alerts Overview
    |--------------------------------------------------------------------------
    */
    Route::get('alerts/overview', AlertsOverview::class)
    ->name('alerts.overview')
    ->middleware(['permission:alerts.overview.view']);

    /*
    |--------------------------------------------------------------------------
    | Pending Confirmation
    |--------------------------------------------------------------------------
    */
    Route::get('alerts/pending-confirmation', PendingConfirmation::class)
        ->name('alerts.pending-confirmation')
        ->middleware(['permission:teacher.profile.confirm|principal.profile.confirm|dos.profile.confirm|mso.profile.confirm|sleas.profile.confirm|sltas.profile.confirm|sltes.profile.confirm']);

    /*
    |--------------------------------------------------------------------------
    | Pending Verification
    |--------------------------------------------------------------------------
    */
    Route::get('alerts/pending-verification', PendingVerification::class)
    ->name('alerts.pending-verification')
    ->middleware([
        'permission:teacher.profile.verify|principal.profile.verify|dos.profile.verify|mso.profile.verify|sleas.profile.verify|sltas.profile.verify|sltes.profile.verify'
    ]);

});

