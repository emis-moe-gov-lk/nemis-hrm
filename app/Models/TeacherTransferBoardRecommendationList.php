<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Blameable;

class TeacherTransferBoardRecommendationList extends Model
{
    use HasFactory, Blameable;

    protected $table = 'teacher_transfer_board_recommendation_lists';

    protected $primaryKey = 'id';

    protected $fillable = [
        'ttbr_list_id',
        'decision',
        'created_by',
        'updated_by',
        'active_status',
    ];

    protected $casts = [
        'active_status' => 'boolean',
    ];

    /**
     * Relationships
     */

    public function creator()
    {
        return $this->belongsTo(People::class, 'created_by', 'people_id');
    }

    public function updater()
    {
        return $this->belongsTo(People::class, 'updated_by', 'people_id');
    }

    /**
     * Auto-generate ttbr_list_id
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->ttbr_list_id)) {
                $last = self::latest('id')->first();
                $number = $last ? ((int) substr($last->ttbr_list_id, -4)) + 1 : 1;

                $model->ttbr_list_id = 'TTBR-' . str_pad($number, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
