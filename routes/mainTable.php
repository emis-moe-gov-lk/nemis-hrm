<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\MainTables\MainTablesGender;
use App\Livewire\MainTables\MainTablesTitles;
use App\Livewire\MainTables\MainTablesCityList;
use App\Livewire\MainTables\MainTablesDistrict;
use App\Livewire\MainTables\MainTablesDSOffice;
use App\Livewire\MainTables\MainTablesOverview;
use App\Livewire\MainTables\MainTablesServices;
use App\Livewire\MainTables\MainTablesVersions;
use App\Livewire\MainTables\MainTablesPositions;
use App\Livewire\MainTables\MainTablesReligions;
use App\Livewire\MainTables\MainTablesChangeLogs;
use App\Livewire\MainTables\MainTablesAuthorities;
use App\Livewire\MainTables\MainTablesBloodGroups;
use App\Livewire\MainTables\MainTablesCivilStatus;
use App\Livewire\MainTables\MainTablesEthnicities;
use App\Livewire\MainTables\MainTablesGnDivisions;
use App\Livewire\MainTables\MainTablesOfficeLevels;
use App\Livewire\MainTables\MainTablesServiceRanks;
use App\Livewire\MainTables\MainTablesTeacherTypes;
use App\Livewire\MainTables\MainTablesCadreCirculars;
use App\Livewire\MainTables\MainTablesPoliceStations;
use App\Livewire\MainTables\MainTablesProvincesLists;
use App\Livewire\MainTables\MainTablesApointedSubjects;
use App\Livewire\MainTables\MainTablesInstitutionTypes;
use App\Livewire\MainTables\MainTablesOccupationsLists;
use App\Livewire\MainTables\MainTablesTeachingSubjects;
use App\Livewire\MainTables\MainTablesTeacherCategories;
use App\Livewire\MainTables\MainTablesInstitutionGenders;
use App\Livewire\MainTables\MainTablesInstitutionLanguages;
use App\Livewire\MainTables\MainTablesMediumOfInstructions;
use App\Livewire\MainTables\MainTablesInstitutionCategories;
use App\Livewire\MainTables\MainTablesInstitutionGradeSpans;
use App\Livewire\MainTables\MainTablesInstitutionAuthorities;
use App\Livewire\MainTables\MainTablesInstitutionEthnicities;
use App\Livewire\MainTables\MainTablesEducationQualifications;
use App\Livewire\MainTables\MainTablesInstitutionalFacilities;
use App\Livewire\MainTables\MainTablesEducationalQualificationGrades;
use App\Livewire\MainTables\MainTablesPrincipalRecruitmentCategories;
use App\Livewire\MainTables\MainTablesEducationAdministratorServiceSubjects;
use App\Livewire\MainTables\MainTablesEducationAdministratorServiceCategories;

Route::middleware(['auth'])->group(function () {
    // In web.php or api.php
    Route::middleware(['role:super admin'])->group(function () {

        Route::get('main-table/overview', MainTablesOverview::class)->name('main-tables.overview');
        Route::get('main-table/authorities', MainTablesAuthorities::class)->name('main-tables.authorities');
        Route::get('main-table/blood-group', MainTablesBloodGroups::class)->name('main-tables.blood-group');
        Route::get('main-table/city-list', MainTablesCityList::class)->name('main-tables.city-list');
        Route::get('main-table/civil-status', MainTablesCivilStatus::class)->name('main-tables.civil-status');
        Route::get('main-table/district', MainTablesDistrict::class)->name('main-tables.district');
        Route::get('main-table/ds-office', MainTablesDSOffice::class)->name('main-tables.ds-office');
        Route::get('main-table/education-qualifications', MainTablesEducationQualifications::class)->name('main-tables.education-qualifications');
        Route::get('main-table/educational-qualification-grades', MainTablesEducationalQualificationGrades::class)->name('main-tables.educational-qualification-grades');
        Route::get('main-table/ethnicities', MainTablesEthnicities::class)->name('main-tables.ethnicities');
        Route::get('main-table/genders', MainTablesGender::class)->name('main-tables.genders');
        Route::get('main-table/gn-divisions', MainTablesGnDivisions::class)->name('main-tables.gn-divisions');
        Route::get('main-table/institution-grade-spans', MainTablesInstitutionGradeSpans::class)->name('main-tables.institution-grade-spans');
        Route::get('main-table/institution-authorities', MainTablesInstitutionAuthorities::class)->name('main-tables.institution-authorities');
        Route::get('main-table/institution-categories', MainTablesInstitutionCategories::class)->name('main-tables.institution-categories');
        Route::get('main-table/institution-ethnicities', MainTablesInstitutionEthnicities::class)->name('main-tables.institution-ethnicities');
        Route::get('main-table/institution-genders', MainTablesInstitutionGenders::class)->name('main-tables.institution-genders');
        Route::get('main-table/institution-languages', MainTablesInstitutionLanguages::class)->name('main-tables.institution-languages');
        Route::get('main-table/institution-types', MainTablesInstitutionTypes::class)->name('main-tables.institution-types');
        Route::get('main-table/institutional-facilities', MainTablesInstitutionalFacilities::class)->name('main-tables.institutional-facilities');
        Route::get('main-table/medium-of-instructions', MainTablesMediumOfInstructions::class)->name('main-tables.medium-of-instructions');
        Route::get('main-table/occupations-lists', MainTablesOccupationsLists::class)->name('main-tables.occupations-lists');
        Route::get('main-table/office-levels', MainTablesOfficeLevels::class)->name('main-tables.office-levels');
        Route::get('main-table/police-stations', MainTablesPoliceStations::class)->name('main-tables.police-stations');
        Route::get('main-table/positions', MainTablesPositions::class)->name('main-tables.positions');
        Route::get('main-table/principal-recruitment-categories', MainTablesPrincipalRecruitmentCategories::class)->name('main-tables.principal-recruitment-categories');
        Route::get('main-table/provinces-lists', MainTablesProvincesLists::class)->name('main-tables.provinces-lists');
        Route::get('main-table/religions', MainTablesReligions::class)->name('main-tables.religions');
        Route::get('main-table/sleas-categories', MainTablesEducationAdministratorServiceCategories::class)->name('main-tables.sleas-categories');
        Route::get('main-table/sleas-subjects', MainTablesEducationAdministratorServiceSubjects::class)->name('main-tables.sleas-subjects');
        Route::get('main-table/services', MainTablesServices::class)->name('main-tables.services');
        Route::get('main-table/service-ranks', MainTablesServiceRanks::class)->name('main-tables.service-ranks');
        Route::get('main-table/teacher-categories', MainTablesTeacherCategories::class)->name('main-tables.teacher-categories');
        Route::get('main-table/teacher-types', MainTablesTeacherTypes::class)->name('main-tables.teacher-types');
        Route::get('main-table/titles', MainTablesTitles::class)->name('main-tables.titles');
        Route::get('main-table/appointed-subjects', MainTablesApointedSubjects::class)->name('main-tables.appointed-subjects');
        Route::get('main-table/teaching-subjects', MainTablesTeachingSubjects::class)->name('main-tables.teaching-subjects');

        Route::get('main-table/versions', MainTablesVersions::class)->name('main-tables.versions');
        Route::get('main-table/change-logs', MainTablesChangeLogs::class)->name('main-tables.change-logs');

        Route::get('main-table/cadre-circulars', MainTablesCadreCirculars::class)->name('main-tables.cadre-circulars');
        

    });
});
