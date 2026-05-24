<?php

namespace App\Models;

use App\Support\Transfer\TransferSubCategoryRules;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherTransferSubCategory extends Model
{
    use HasFactory;

    protected $table = 'teacher_transfer_sub_categories';

    protected $primaryKey = 'id';

    protected $fillable = [
        'transfer_sub_category_id',
        'code',
        'name',
        'description',
        'policy_office_level_id',
        'first_board_office_level_id',
        'second_board_office_level_id',
        'requires_target_province_selection',
        'zone_scope_mode',
        'institution_scope_mode',
        'active_status',
        'display_order',
    ];

    protected $casts = [
        'requires_target_province_selection' => 'boolean',
        'active_status' => 'boolean',
        'display_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (blank($model->transfer_sub_category_id)) {
                $lastId = static::query()
                    ->where('transfer_sub_category_id', 'like', 'TSC-%')
                    ->orderByDesc('id')
                    ->value('transfer_sub_category_id');

                $nextNumber = 1;

                if (is_string($lastId) && preg_match('/^TSC-(\d{5})$/', $lastId, $matches)) {
                    $nextNumber = ((int) $matches[1]) + 1;
                }

                $model->transfer_sub_category_id = 'TSC-' . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function policyOfficeLevel()
    {
        return $this->belongsTo(OfficeLevel::class, 'policy_office_level_id', 'office_level_id');
    }

    public function firstBoardOfficeLevel()
    {
        return $this->belongsTo(OfficeLevel::class, 'first_board_office_level_id', 'office_level_id');
    }

    public function secondBoardOfficeLevel()
    {
        return $this->belongsTo(OfficeLevel::class, 'second_board_office_level_id', 'office_level_id');
    }

    public function categories()
    {
        return $this->hasMany(TeacherTransferCategory::class, 'transfer_sub_category_id', 'transfer_sub_category_id');
    }

    public function applications()
    {
        return $this->hasMany(TeacherTransferApplication::class, 'transfer_sub_category_id', 'transfer_sub_category_id');
    }

    public function boards()
    {
        return $this->hasMany(TeacherTransferBoard::class, 'transfer_sub_category_id', 'transfer_sub_category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active_status', true);
    }

    public function usesCurrentZoneOnly(): bool
    {
        return $this->zone_scope_mode === TransferSubCategoryRules::ZONE_SCOPE_CURRENT_ZONE_ONLY;
    }

    public function usesSourceProvinceZones(): bool
    {
        return $this->zone_scope_mode === TransferSubCategoryRules::ZONE_SCOPE_SOURCE_PROVINCE_ONLY;
    }

    public function usesSelectedProvinceZones(): bool
    {
        return $this->zone_scope_mode === TransferSubCategoryRules::ZONE_SCOPE_SELECTED_TARGET_PROVINCE;
    }

    public function usesNationalInstitutions(): bool
    {
        return $this->institution_scope_mode === TransferSubCategoryRules::INSTITUTION_SCOPE_NATIONAL_ONLY;
    }

    public function isNationalSchool(): bool
    {
        return $this->code === TransferSubCategoryRules::CODE_NATIONAL_SCHOOL;
    }

    public function hasSecondBoardStage(): bool
    {
        return filled($this->second_board_office_level_id);
    }
}
