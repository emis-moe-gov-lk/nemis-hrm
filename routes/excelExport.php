<?php

use App\Http\Controllers\Excel\TeacherController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    Route::get('export/teachers', [TeacherController::class, 'exportTeachers'])
        ->name('teacher.export');
});
