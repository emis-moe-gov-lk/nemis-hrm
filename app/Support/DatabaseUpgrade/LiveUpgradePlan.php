<?php

namespace App\Support\DatabaseUpgrade;

final class LiveUpgradePlan
{
    /**
     * @return array<int, array{migration: string, table: string, mode: 'table'|'columns', columns: array<int, string>}>
     */
    public static function reconciliationRules(): array
    {
        return [
            [
                'migration' => '0001_01_01_000022_create_employer_appointments_table',
                'table' => 'employer_appointments',
                'mode' => 'table',
                'columns' => [],
            ],
            [
                'migration' => '2026_01_06_144401_create_versions_table',
                'table' => 'versions',
                'mode' => 'table',
                'columns' => [],
            ],
            [
                'migration' => '2026_01_07_102343_create_change_logs_table',
                'table' => 'change_logs',
                'mode' => 'table',
                'columns' => [],
            ],
            [
                'migration' => '2026_01_14_062906_create_cadre_circulars_table',
                'table' => 'cadre_circulars',
                'mode' => 'table',
                'columns' => [],
            ],
            [
                'migration' => '2026_01_19_062601_create_cadre_d_m_s_approveds_table',
                'table' => 'cadre_d_m_s_approveds',
                'mode' => 'table',
                'columns' => [],
            ],
            [
                'migration' => '2026_01_21_163407_create_employer_cadre_subjects_table',
                'table' => 'employer_cadre_subjects',
                'mode' => 'table',
                'columns' => [],
            ],
            [
                'migration' => '2026_01_19_180359_add_category_to_subject_lists_table',
                'table' => 'subject_lists',
                'mode' => 'columns',
                'columns' => ['type', 'grade_mask', 'language_mask', 'category_mask'],
            ],
            [
                'migration' => '2026_01_20_162712_add_start_end_grade_to_grade_spans_table',
                'table' => 'grade_spans',
                'mode' => 'columns',
                'columns' => ['start_grade', 'end_grade'],
            ],
            [
                'migration' => '2026_04_25_034049_create_personal_access_tokens_table',
                'table' => 'personal_access_tokens',
                'mode' => 'table',
                'columns' => [],
            ],
        ];
    }

    /**
     * @return array<int, class-string>
     */
    public static function targetedSeeders(): array
    {
        return [
            \Database\Seeders\ReasonForTerminationSeeder::class,
            \Database\Seeders\GradeSpanSeeder::class,
            \Database\Seeders\SubjectGradeMaskSeeder::class,
            \Database\Seeders\TransferReasonSeeder::class,
            \Database\Seeders\TransferScoreCriteriaSeeder::class,
            \Database\Seeders\TeacherTransferRecommendationListSeeder::class,
            \Database\Seeders\TeacherTransferBoardRecommendationListSeeder::class,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function coreCountTables(): array
    {
        return [
            'users',
            'people',
            'teachers',
            'institutions',
            'employer_appointments',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function expectedNewTables(): array
    {
        return [
            'reasons_for_termination_of_services',
            'institutional_facility_histories',
            'institutional_ns_cat_histories',
            'employer_cadre_subjects',
            'deleted_users_archive',
            'institution_groups',
            'institution_group_institutions',
            'teacher_transfer_policies',
            'teacher_transfer_policy_steps',
            'transfer_reasons',
            'teacher_transfer_categories',
            'teacher_transfer_recommendation_lists',
            'teacher_transfer_boards',
            'teacher_transfer_board_members',
            'teacher_transfer_board_member_attendances',
            'teacher_transfer_board_recommendation_lists',
            'teacher_transfer_applications',
            'teacher_transfer_application_preferences',
            'teacher_transfer_application_recommendations',
            'teacher_transfer_board_recommendations',
            'teacher_transfer_appeals',
            'teacher_transfer_board_subjects',
            'teacher_transfer_score_criteria',
            'transfer_policy_score_rules',
            'transfer_policy_facility_score_rules',
            'transfer_policy_achievement_level_scores',
            'teacher_transfer_application_achievements',
            'teacher_transfer_score_route_distances',
        ];
    }

    /**
     * @return array<string, int>
     */
    public static function seedExpectations(): array
    {
        return [
            'reasons_for_termination_of_services' => 1,
            'transfer_reasons' => 1,
            'teacher_transfer_score_criteria' => 1,
            'teacher_transfer_recommendation_lists' => 1,
            'teacher_transfer_board_recommendation_lists' => 1,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function preservedMetricQueries(): array
    {
        return [
            'subject_lists.grade_mask_non_default' => "select count(*) as aggregate from `subject_lists` where `grade_mask` <> '0000000000000'",
            'subject_lists.language_mask_non_default' => "select count(*) as aggregate from `subject_lists` where `language_mask` <> '0000'",
            'subject_lists.category_mask_non_default' => "select count(*) as aggregate from `subject_lists` where `category_mask` <> '0000000000000'",
            'subject_lists.type_present' => 'select count(*) as aggregate from `subject_lists` where `type` is not null',
            'grade_spans.with_bounds' => 'select count(*) as aggregate from `grade_spans` where `start_grade` is not null and `end_grade` is not null',
        ];
    }
}
