<?php

use App\Livewire\Employees\EmployeesOverview;
use App\Livewire\Employees\AnyEmployeeCreate;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Employees\EmployeePromotionControl;
use App\Livewire\Employees\EmpoyeeTerminationControl;
use App\Livewire\Employees\MyTransfer;
use App\Livewire\Employees\EmployeeChangingWorkplace;
use App\Livewire\Employees\EmployeeWorkplaceChangeForm;


Route::middleware(['auth'])->group(function () {
    Route::get('employees/overview', EmployeesOverview::class)
        ->name('employees.overview')
        ->middleware(['permission:employee.overview.view']);

    Route::get('employees/create/any', AnyEmployeeCreate::class)
        ->name('employees.create.any')
        ->middleware(['permission:employee.profile.create.any']);

    Route::post('employees/register', [EmployeeController::class, 'register'])
        ->name('employees.register')
        ->middleware(['permission:employee.profile.create.any']);

    Route::get('employees/promotion-control/{employee}', EmployeePromotionControl::class)
        ->name('employees.promotion-control')
        ->middleware(['permission:employee.promotion.manage']);

    Route::get('employees/termination-of-service/{employee}', EmpoyeeTerminationControl::class)
        ->name('employees.termination-of-service')
        ->middleware(['permission:employee.termination.manage']);

    Route::get('my-transfer', MyTransfer::class)
        ->name('my-transfer')
        ->middleware(['permission:employee.mytransfer']);


    Route::get('employees/changing-workplace', EmployeeChangingWorkplace::class)
        ->name('employees.changing-workplace')
        ->middleware(['permission:employee.changing-workplace.manage']);

    Route::get('employees/changing-workplace/{employee}', EmployeeWorkplaceChangeForm::class)
        ->name('employees.changing-workplace.form')
        ->middleware(['permission:employee.changing-workplace.manage']);
});
