<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\TransferModule\IndexModule;
use App\Livewire\TransferModule\TransferPolicy;
use App\Livewire\TransferModule\TeacherTransferRequest;
use App\Livewire\Teacher\Transfer\TeacherTransferApplicationView;
use App\Livewire\TransferModule\TransferPolicyList;
use App\Livewire\TransferModule\TransferPolicyView;
use App\Livewire\TransferModule\TransferBoard\ProvinceTeacherAppealBoard;
use App\Livewire\TransferModule\TransferBoard\ProvinceTeacherTransferBoard;
use App\Livewire\TransferModule\TransferBoard\ZoneTeacherAppealBoard;
use App\Livewire\TransferModule\TransferBoard\ZoneTeacherTransferBoard;
use App\Livewire\TransferModule\TransferBoard\NtionalTeacherTransferBoard;
use App\Livewire\TransferModule\TransferBoard\TeacherProfileForAppealBoard;
use App\Livewire\TransferModule\TransferBoard\TeacherProfileForTransferBoard;
use App\Livewire\TransferModule\TransferBoard\InstitutionProfileForTransferBoard;
use App\Http\Controllers\Pdf\TeacherTransferApplicationPdfController;
use App\Http\Controllers\Pdf\TransferBoardAppealReportPdfController;
use App\Http\Controllers\Pdf\TransferBoardDecisionReportPdfController;
use App\Http\Controllers\Pdf\TransferBoardSchoolBalanceReportPdfController;
use App\Livewire\Teacher\Transfer\TeacherTransferPotal;
use App\Livewire\Teacher\Transfer\TeacherMutualTransfer;
use App\Livewire\Teacher\Transfer\TeacherSpecialRequest;
use App\Livewire\Teacher\Transfer\TeacherAnnualTransfer;
use App\Livewire\Teacher\Transfer\TeacherTransferApplication;
use App\Livewire\Teacher\Transfer\TeacherTransferGuidelines;
use App\Livewire\Institutions\Transfer\TransferRequests;
use App\Livewire\Offices\Zeo\Transfer\TeachersTransferRequests;


Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Transfer Module
    |--------------------------------------------------------------------------
    */
    Route::get('transfer/index-module', IndexModule::class)
        ->name('transfer.index-module');

    Route::get('transfer/transfer-policies', TransferPolicyList::class)
        ->name('transfer.transfer-policies');

    Route::get('transfer/transfer-policy/{id}/view', TransferPolicyView::class)
        ->name('transfer.transfer-policy.view');

    Route::get('transfer/transfer-policy/create', TransferPolicy::class)
        ->name('transfer.transfer-policy');

    Route::get('transfer/transfer-policy/{id}/edit', TransferPolicy::class)
        ->name('transfer.transfer-policy.edit');

    Route::get('transfer/teacher-transfer-request', TeacherTransferRequest::class)
        ->name('transfer.teacher-transfer-request');

    Route::get('transfer/teacher-transfer-application/{id}/view', TeacherTransferApplicationView::class)
        ->name('transfer.teacher-transfer-application.view');

    Route::get('transfer/teacher-transfer-application/{id}/download', [TeacherTransferApplicationPdfController::class, 'download'])
        ->name('transfer.teacher-transfer-application.download');

    Route::get('transfer/transfer-board/{boardId}/decision-report/download', [TransferBoardDecisionReportPdfController::class, 'download'])
        ->name('transfer.transfer-board.decision-report.download');

    Route::get('transfer/transfer-board/{boardId}/appeal-report/download', [TransferBoardAppealReportPdfController::class, 'download'])
        ->name('transfer.transfer-board.appeal-report.download');

    Route::get('transfer/transfer-board/{boardId}/school-balance-report/{type}/download', [TransferBoardSchoolBalanceReportPdfController::class, 'download'])
        ->name('transfer.transfer-board.school-balance-report.download');


    /*
    |--------------------------------------------------------------------------
    | Teacher Transfer Potal
    |--------------------------------------------------------------------------
    */
    Route::get('my-transfer/teacher-transfer-potal', TeacherTransferPotal::class)
        ->name('my-transfer.teacher-transfer-potal');

    Route::get('my-transfer/teacher-transfer-application/{id?}', TeacherTransferApplication::class)
        ->name('my-transfer.teacher-transfer-application');

    Route::get('my-transfer/teacher-annual-transfer', TeacherAnnualTransfer::class)
        ->name('my-transfer.teacher-annual-transfer');

    Route::get('my-transfer/teacher-mutual-transfer', TeacherMutualTransfer::class)
        ->name('my-transfer.teacher-mutual-transfer');

    Route::get('my-transfer/teacher-special-request', TeacherSpecialRequest::class)
        ->name('my-transfer.teacher-special-request');

    Route::get('my-transfer/teacher-transfer-guidelines', TeacherTransferGuidelines::class)
        ->name('my-transfer.teacher-transfer-guidelines');


    /*
    |--------------------------------------------------------------------------
    | Institution Transfer Requests
    |--------------------------------------------------------------------------
    */
    Route::get('institutions/{id}/profile/institution-transfer-requests', TransferRequests::class)
        ->name('institutions.institution-transfer-requests');

    /*
    |--------------------------------------------------------------------------
    | Zone Transfer Requests
    |--------------------------------------------------------------------------
    */
    Route::get('offices/zeo/{id}/profile/teachers-transfer-requests', TeachersTransferRequests::class)
        ->name('offices.zeo.profile.teachers-transfer-requests');

    /*
    |--------------------------------------------------------------------------
    | Teacher Transfer Board
    |--------------------------------------------------------------------------
    */

    Route::get('transfer/province-teacher-transfer-board', ProvinceTeacherTransferBoard::class)
        ->name('transfer-board.province-teacher-transfer');

    Route::get('transfer/province-teacher-appeal-board', ProvinceTeacherAppealBoard::class)
        ->name('transfer-board.province-teacher-appeal');

    Route::get('transfer/zone-teacher-transfer-board', ZoneTeacherTransferBoard::class)
        ->name('transfer-board.zone-teacher-transfer');

    Route::get('transfer/zone-teacher-appeal-board', ZoneTeacherAppealBoard::class)
        ->name('transfer-board.zone-teacher-appeal');

    Route::get('transfer/ntional-teacher-transfer-board', NtionalTeacherTransferBoard::class)
        ->name('transfer-board.ntional-teacher-transfer');

    Route::get('transfer/teacher-profile-for-transfer-board/{applicationId}/institution/{institutionId}', InstitutionProfileForTransferBoard::class)
        ->name('transfer-board.institution-profile-for-transfer-board');

    Route::get('transfer/teacher-profile-for-transfer-board/{id}', TeacherProfileForTransferBoard::class)
        ->name('transfer-board.teacher-profile-for-transfer-board');

    Route::get('transfer/teacher-profile-for-appeal-board/{id}', TeacherProfileForAppealBoard::class)
        ->name('transfer-board.teacher-profile-for-appeal-board');
});
