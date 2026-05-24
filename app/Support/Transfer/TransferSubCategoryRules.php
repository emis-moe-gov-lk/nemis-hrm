<?php

namespace App\Support\Transfer;

use App\Models\TeacherTransferSubCategory;

class TransferSubCategoryRules
{
    public const OFFICE_LEVEL_PMOE = 'OLID002';
    public const OFFICE_LEVEL_PEO = 'OLID003';
    public const OFFICE_LEVEL_ZEO = 'OLID004';

    public const CODE_INTER_ZONE = 'inter_zone';
    public const CODE_ANOTHER_ZONE = 'another_zone';
    public const CODE_ANOTHER_PROVINCE = 'another_province';
    public const CODE_NATIONAL_SCHOOL = 'national_school';

    public const BOARD_STAGE_ZEO = 'zeo';
    public const BOARD_STAGE_PEO = 'peo';
    public const BOARD_STAGE_PMOE = 'pmoe';

    public const ZONE_SCOPE_CURRENT_ZONE_ONLY = 'current_zone_only';
    public const ZONE_SCOPE_SOURCE_PROVINCE_ONLY = 'source_province_only';
    public const ZONE_SCOPE_SELECTED_TARGET_PROVINCE = 'selected_target_province';

    public const INSTITUTION_SCOPE_PROVINCIAL_ONLY = 'provincial_only';
    public const INSTITUTION_SCOPE_NATIONAL_ONLY = 'national_only';

    public const POLICY_OFFICE_LEVEL_MAP = [
        self::CODE_INTER_ZONE => self::OFFICE_LEVEL_ZEO,
        self::CODE_ANOTHER_ZONE => self::OFFICE_LEVEL_PEO,
        self::CODE_ANOTHER_PROVINCE => self::OFFICE_LEVEL_PEO,
        self::CODE_NATIONAL_SCHOOL => self::OFFICE_LEVEL_PEO,
    ];

    public const FIRST_BOARD_OFFICE_LEVEL_MAP = [
        self::CODE_INTER_ZONE => self::OFFICE_LEVEL_ZEO,
        self::CODE_ANOTHER_ZONE => self::OFFICE_LEVEL_PEO,
        self::CODE_ANOTHER_PROVINCE => self::OFFICE_LEVEL_PEO,
        self::CODE_NATIONAL_SCHOOL => self::OFFICE_LEVEL_PEO,
    ];

    public const SECOND_BOARD_OFFICE_LEVEL_MAP = [
        self::CODE_NATIONAL_SCHOOL => self::OFFICE_LEVEL_PMOE,
    ];

    public const DISPLAY_NAME_MAP = [
        self::CODE_INTER_ZONE => 'Intra Zone - Within Zone',
        self::CODE_ANOTHER_ZONE => 'To Another Zone',
        self::CODE_ANOTHER_PROVINCE => 'To Another Province',
        self::CODE_NATIONAL_SCHOOL => 'To National School',
    ];

    public const DESCRIPTION_MAP = [
        self::CODE_INTER_ZONE => 'Transfers handled inside the teacher’s current zone and decided at the Zonal Education Office.',
        self::CODE_ANOTHER_ZONE => 'Transfers to another zone within the teacher’s source province and decided at Provincial Education Office level.',
        self::CODE_ANOTHER_PROVINCE => 'Transfers to another province and decided at Provincial Education Office level.',
        self::CODE_NATIONAL_SCHOOL => 'Transfers to National Schools that move through Provincial Education Office first and Provincial Ministry second.',
    ];

    public const ZONE_SCOPE_MAP = [
        self::CODE_INTER_ZONE => self::ZONE_SCOPE_CURRENT_ZONE_ONLY,
        self::CODE_ANOTHER_ZONE => self::ZONE_SCOPE_SOURCE_PROVINCE_ONLY,
        self::CODE_ANOTHER_PROVINCE => self::ZONE_SCOPE_SELECTED_TARGET_PROVINCE,
        self::CODE_NATIONAL_SCHOOL => self::ZONE_SCOPE_SELECTED_TARGET_PROVINCE,
    ];

    public const INSTITUTION_SCOPE_MAP = [
        self::CODE_INTER_ZONE => self::INSTITUTION_SCOPE_PROVINCIAL_ONLY,
        self::CODE_ANOTHER_ZONE => self::INSTITUTION_SCOPE_PROVINCIAL_ONLY,
        self::CODE_ANOTHER_PROVINCE => self::INSTITUTION_SCOPE_PROVINCIAL_ONLY,
        self::CODE_NATIONAL_SCHOOL => self::INSTITUTION_SCOPE_NATIONAL_ONLY,
    ];

    public static function allowedPolicyOfficeLevelIds(): array
    {
        return array_values(array_unique(array_values(self::POLICY_OFFICE_LEVEL_MAP)));
    }

    public static function allowedCodesForOfficeLevel(?string $officeLevelId): array
    {
        if (!$officeLevelId) {
            return [];
        }

        return collect(self::POLICY_OFFICE_LEVEL_MAP)
            ->filter(fn (string $allowedOfficeLevelId) => $allowedOfficeLevelId === $officeLevelId)
            ->keys()
            ->values()
            ->all();
    }

    public static function allowedOfficeLevelForCode(?string $code): ?string
    {
        return $code ? (self::POLICY_OFFICE_LEVEL_MAP[$code] ?? null) : null;
    }

    public static function isAllowedOfficeLevelForCode(?string $code, ?string $officeLevelId): bool
    {
        return filled($code)
            && filled($officeLevelId)
            && self::allowedOfficeLevelForCode($code) === $officeLevelId;
    }

    public static function allowedPolicyBoardOfficeLevelIdsForCode(?string $code): array
    {
        if ($code === self::CODE_INTER_ZONE) {
            return [self::OFFICE_LEVEL_ZEO];
        }

        if (in_array($code, [
            self::CODE_ANOTHER_ZONE,
            self::CODE_ANOTHER_PROVINCE,
            self::CODE_NATIONAL_SCHOOL,
        ], true)) {
            return [self::OFFICE_LEVEL_PEO, self::OFFICE_LEVEL_PMOE];
        }

        return [];
    }

    public static function defaultPolicyBoardOfficeLevelForCode(?string $code): ?string
    {
        return self::allowedPolicyBoardOfficeLevelIdsForCode($code)[0] ?? null;
    }

    public static function isAllowedPolicyBoardOfficeLevelForCode(?string $code, ?string $officeLevelId): bool
    {
        return in_array($officeLevelId, self::allowedPolicyBoardOfficeLevelIdsForCode($code), true);
    }

    public static function labelForCode(?string $code): string
    {
        return self::DISPLAY_NAME_MAP[$code] ?? ucfirst(str_replace('_', ' ', (string) $code));
    }

    public static function secondBoardStageRequired(?string $code): bool
    {
        return filled(self::SECOND_BOARD_OFFICE_LEVEL_MAP[$code] ?? null);
    }

    public static function boardStagesFor(?TeacherTransferSubCategory $subCategory): array
    {
        if (!$subCategory) {
            return [];
        }

        $firstStage = self::stageForOfficeLevel($subCategory->first_board_office_level_id);

        $stages = $firstStage
            ? [$firstStage => $subCategory->first_board_office_level_id]
            : [];

        if (filled($subCategory->second_board_office_level_id)) {
            $stages[self::BOARD_STAGE_PMOE] = $subCategory->second_board_office_level_id;
        }

        return $stages;
    }

    public static function stageForOfficeLevel(?string $officeLevelId): ?string
    {
        return match ($officeLevelId) {
            self::OFFICE_LEVEL_ZEO => self::BOARD_STAGE_ZEO,
            self::OFFICE_LEVEL_PEO => self::BOARD_STAGE_PEO,
            self::OFFICE_LEVEL_PMOE => self::BOARD_STAGE_PMOE,
            default => null,
        };
    }

    public static function displayLabelForStage(?string $stage): string
    {
        return match ($stage) {
            self::BOARD_STAGE_ZEO => 'ZEO',
            self::BOARD_STAGE_PEO => 'PEO',
            self::BOARD_STAGE_PMOE => 'PMOE',
            default => strtoupper((string) $stage),
        };
    }

    public static function displayLabelForBoardStage(?string $stage, ?string $officeLevelId): string
    {
        return self::displayLabelForStage(self::stageForOfficeLevel($officeLevelId) ?? $stage);
    }

    public static function buildRows(): array
    {
        return collect(self::DISPLAY_NAME_MAP)
            ->map(fn (string $name, string $code) => [
                'code' => $code,
                'name' => $name,
                'description' => self::DESCRIPTION_MAP[$code],
                'policy_office_level_id' => self::POLICY_OFFICE_LEVEL_MAP[$code],
                'first_board_office_level_id' => self::FIRST_BOARD_OFFICE_LEVEL_MAP[$code],
                'second_board_office_level_id' => self::SECOND_BOARD_OFFICE_LEVEL_MAP[$code] ?? null,
                'requires_target_province_selection' => in_array($code, [self::CODE_ANOTHER_PROVINCE, self::CODE_NATIONAL_SCHOOL], true),
                'zone_scope_mode' => self::ZONE_SCOPE_MAP[$code],
                'institution_scope_mode' => self::INSTITUTION_SCOPE_MAP[$code],
                'active_status' => true,
                'display_order' => array_search($code, array_keys(self::DISPLAY_NAME_MAP), true) + 1,
            ])
            ->values()
            ->all();
    }
}
