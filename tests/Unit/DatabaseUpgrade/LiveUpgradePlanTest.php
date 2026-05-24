<?php

use App\Support\DatabaseUpgrade\LiveUpgradePlan;

test('targeted seeders stay scoped to reference data only', function () {
    $seeders = LiveUpgradePlan::targetedSeeders();

    expect($seeders)->toContain(Database\Seeders\ReasonForTerminationSeeder::class);
    expect($seeders)->toContain(Database\Seeders\TransferScoreCriteriaSeeder::class);
    expect($seeders)->not->toContain(Database\Seeders\DatabaseSeeder::class);
    expect($seeders)->not->toContain(Database\Seeders\TransferCategorySeeder::class);
});

test('reconciliation rules cover both legacy tables and already-present columns', function () {
    $rules = LiveUpgradePlan::reconciliationRules();
    $migrations = array_column($rules, 'migration');

    expect($migrations)->toContain('0001_01_01_000022_create_employer_appointments_table');
    expect($migrations)->toContain('2026_01_21_163407_create_employer_cadre_subjects_table');
    expect($migrations)->toContain('2026_01_19_180359_add_category_to_subject_lists_table');
    expect($migrations)->toContain('2026_01_20_162712_add_start_end_grade_to_grade_spans_table');

    $columnRule = collect($rules)->firstWhere('migration', '2026_01_19_180359_add_category_to_subject_lists_table');

    expect($columnRule['mode'])->toBe('columns');
    expect($columnRule['columns'])->toBe(['type', 'grade_mask', 'language_mask', 'category_mask']);
});
