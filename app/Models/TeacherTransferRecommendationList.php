<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use App\Traits\Blameable;

class TeacherTransferRecommendationList extends Model
{
    use HasFactory, Blameable;

    protected $table = 'teacher_transfer_recommendation_lists';

    protected $primaryKey = 'id';

    protected $fillable = [
        'transfer_recommendation_list_id',
        'office_level_id',
        'decision',
        'created_by',
        'updated_by',
        'active_status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Auto Generate ULID
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::creating(function ($model) {

            if (empty($model->transfer_recommendation_list_id)) {

                $lastId = self::where('transfer_recommendation_list_id', 'like', 'TRL-%')
                    ->orderBy('transfer_recommendation_list_id', 'desc')
                    ->value('transfer_recommendation_list_id');

                $nextNumber = $lastId
                    ? (int) substr($lastId, 4) + 1
                    : 1;

                $model->transfer_recommendation_list_id =
                    'TRL-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            }
        });
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

    public function scopeInactive($query)
    {
        return $query->where('active_status', false);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Office Level (Institution / ZEO / Provincial)
    public function officeLevel()
    {
        return $this->belongsTo(
            OfficeLevel::class,
            'office_level_id',
            'office_level_id'
        );
    }

    // Created By
    public function creator()
    {
        return $this->belongsTo(
            People::class,
            'created_by',
            'people_id'
        );
    }

    // Updated By
    public function updater()
    {
        return $this->belongsTo(
            People::class,
            'updated_by',
            'people_id'
        );
    }
}
