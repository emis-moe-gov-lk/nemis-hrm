<?php

namespace App\Livewire\TransferModule\Teacher\TeacherTransferBoard;

use App\Models\TeacherTransferCategory;
use App\Models\TeacherTransferBoard;
use App\Support\Transfer\TransferSubCategoryRules;

class ProvincialMinistryTeacherTransferBoard extends ProvinceTeacherTransferBoard
{
    protected function boardRouteScope(): string
    {
        return 'pmoe';
    }

    protected function boardScopeOfficeLevelId(): string
    {
        return 'OLID002';
    }

    protected function categoryOfficeLevelForWorkspace(): string
    {
        return $this->boardScopeOfficeLevelId();
    }

    protected function supportedSubCategoryCodes(): array
    {
        return [
            TransferSubCategoryRules::CODE_ANOTHER_ZONE,
            TransferSubCategoryRules::CODE_ANOTHER_PROVINCE,
            TransferSubCategoryRules::CODE_NATIONAL_SCHOOL,
        ];
    }

    protected function createBoardStageForCurrentScope(): string
    {
        return TeacherTransferBoard::STAGE_PMOE;
    }

    protected function assertBoardCreationAllowedForStage(TeacherTransferCategory $category): void
    {
        // PMOE board routing is selected directly on each policy category.
        // If the category is visible in this workspace, it is eligible for PMOE board creation.
    }

    protected function boardScopeRelationName(): string
    {
        return 'provincialMinistry';
    }

    protected function boardScopeTitle(): string
    {
        return 'Provincial Ministry';
    }

    protected function boardScopeNameLower(): string
    {
        return 'provincial ministry';
    }

    protected function boardScopeNamePlural(): string
    {
        return 'provincial ministries';
    }

    protected function boardScopeAdjectiveLower(): string
    {
        return 'provincial ministry';
    }
}
