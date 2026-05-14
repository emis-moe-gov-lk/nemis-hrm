<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\OfficesController;
use App\Http\Controllers\API\InstitutionController;
use App\Http\Controllers\API\AuthenticationController;
use App\Http\Controllers\API\ApointedSubjectController;
use App\Http\Controllers\API\AuthorityController;
use App\Http\Controllers\API\BloodGroupController;
use App\Http\Controllers\API\TitleController;
use App\Http\Controllers\API\TeacherTypeController;
use App\Http\Controllers\API\TeacherCategoryController;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\ServiceRankController;
use App\Http\Controllers\API\SubjectListController;
use App\Http\Controllers\API\TeacherApiController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('')->group(function () {

    Route::controller(AuthenticationController::class)->group(function () {
        Route::post('/login', 'login');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::controller(ApointedSubjectController::class)->group(function () {
            Route::get('/apointed-subjects', 'index');          // GET (all)
            Route::patch('/apointed-subjects/{id}', 'update');    // UPDATE (one)
        });

        Route::controller(AuthorityController::class)->group(function () {
            Route::get('/authorities', 'index');          // GET (all)
            Route::put('/authorities/{id}', 'update');    // UPDATE (one)
        });

        Route::controller(BloodGroupController::class)->group(function () {
            Route::get('/blood-groups', 'index');        // GET (all)
            Route::put('/blood-groups/{id}', 'update');  // UPDATE (one)
        });

        Route::controller(TitleController::class)->group(function () {
            Route::get('/titles', 'index');        // GET all
            Route::put('/titles/{id}', 'update');  // UPDATE
        });

        Route::controller(TeacherTypeController::class)->group(function () {
            Route::get('/teacher-types', 'index');        // GET all
            Route::put('/teacher-types/{id}', 'update');  // UPDATE
        });

        Route::controller(TeacherCategoryController::class)->group(function () {
            Route::get('/teacher-categories', 'index');        // GET all
            Route::get('/teacher-categories/{id}', 'show');    // GET one
            Route::put('/teacher-categories/{id}', 'update');  // UPDATE
        });

        Route::controller(ServiceController::class)->group(function () {
            Route::get('/services', 'index');        // GET all
            Route::get('/services/{id}', 'show');    // GET one
            Route::put('/services/{id}', 'update');  // UPDATE
        });

        Route::controller(ServiceRankController::class)->group(function () {
            Route::get('/service-ranks', 'index');        // Get all ranks
            Route::get('/service-ranks/{id}', 'show');    // Get one rank
            Route::put('/service-ranks/{id}', 'update');  // Update rank
        });

        Route::controller(SubjectListController::class)->group(function () {
            Route::get('/subjects', 'index');         // GET all
            Route::get('/subjects/{id}', 'show');     // GET one
            Route::put('/subjects/{id}', 'update');   // UPDATE
        });

        Route::controller(OfficesController::class)->group(function () {
            Route::get('/moe-list', 'moeList');          // GET (all)
            Route::get('/pmoe-list', 'pmoeList');         // GET (all)
            Route::get('/peo-list', 'peoList');           // GET (all)
            Route::get('/zeo-list', 'zeoList');           // GET (all)
            Route::get('/deo-list', 'deoList');           // GET (all)
            Route::get('/office/{type}/{workplace_id}', 'singleOffice');
        });

        Route::controller(InstitutionController::class)->group(function () {
            Route::get('/institutions', 'index');         // GET all
            Route::get('/institutions/{id}', 'show');     // GET one
            Route::put('/institutions/{id}', 'update');   // UPDATE
        });

        Route::controller(TeacherApiController::class)->group(function () {
            Route::get('/teacher-settings', 'index');     // GET all
            Route::get('/teachers-list', 'teacherList');  // GET all teachers
            Route::post('/teacher-create', 'store');      // POST
            Route::get('/teacher/{people_id}', 'getTeacher');
            Route::get('/teachers/check-nic/{nic}', 'getTeacherWithNIC');
            Route::get('/teachers/personal-form-data', 'getPersonalFromData');
            Route::get('/teachers/appointment-form-data', 'getAppoinmentFromData');  // UPDATE
        });
    });
});
