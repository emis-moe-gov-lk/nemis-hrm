<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class TransferCategory extends Model
{
    use HasFactory;

    protected $table = 'transfer_categories';

    protected $primaryKey = 'id';

    protected $fillable = [
        'transfer_category_id',
        'policy_id',
        'office_level_id',
        'transfer_owner_workplace_id',
        'transfer_category_name',
        'description',
        'active_status',
    ];

    protected $casts = [
        'active_status' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Auto Generate ULID
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::creating(function ($model) {

            if (empty($model->transfer_category_id)) {
                $model->transfer_category_id = static::generateNextTransferCategoryId();
            }
        });
    }

    protected static function generateNextTransferCategoryId(): string
    {
        $lastId = static::query()
            ->where('transfer_category_id', 'like', 'TCA-%')
            ->orderByDesc('id')
            ->value('transfer_category_id');

        $nextNumber = 1;

        if (is_string($lastId) && preg_match('/^TCA-(\d{5})$/', $lastId, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return 'TCA-' . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Transfer policy
    public function transferPolicy()
    {
        return $this->belongsTo(
            TransferPolicy::class,
            'policy_id',
            'policy_id'
        );
    }

    // Office Level (Ministry / Provincial / ZEO)
    public function officeLevel()
    {
        return $this->belongsTo(
            OfficeLevel::class,
            'office_level_id',
            'office_level_id'
        );
    }

    public function teacherApplications()
    {
        return $this->hasMany(
            TeacherTransferApplication::class,
            'transfer_category',
            'transfer_category_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('active_status', true);
    }

    public function scopeForPolicy($query, ?string $policyId)
    {
        if ($policyId) {
            $query->where('policy_id', $policyId);
        }

        return $query;
    }

    public function scopeOwnedByWorkplace($query, ?string $workplaceId)
    {
        if ($workplaceId) {
            $query->where('transfer_owner_workplace_id', $workplaceId);
        }

        return $query;
    }

    public function scopeForOfficeLevel($query, ?string $officeLevelId)
    {
        if ($officeLevelId) {
            $query->where('office_level_id', $officeLevelId);
        }

        return $query;
    }

    public static function scopedListQuery(
        ?string $policyId = null,
        ?string $officeLevelId = null,
        ?string $workplaceId = null
    ): Builder {
        $query = static::query()
            ->active()
            ->forPolicy($policyId)
            ->forOfficeLevel($officeLevelId);

        if ($workplaceId) {
            $query->ownedByWorkplace($workplaceId);
        }

        return $query;
    }
}
