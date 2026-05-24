<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use App\Models\TeacherTransferApplication;

/**
 * Class TransferReason
 *
 * @property string $id
 * @property string $reason
 */
class TransferReason extends Model
{
    use HasFactory;

    protected $table = 'transfer_reasons';

    protected $fillable = [
        'reason_id',
        'title',
        'description',
        'category',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Boot - Auto Generate ULID
    |--------------------------------------------------------------------------
    */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            if (empty($model->reason_id)) {

                // Get last TRA ID
                $lastId = self::where('reason_id', 'like', 'TRA-%')
                    ->orderBy('reason_id', 'desc')
                    ->value('reason_id');

                $nextNumber = $lastId
                    ? (int) substr($lastId, 4) + 1
                    : 1;

                $model->reason_id = 'TRA-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
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
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function applications()
    {
        return $this->hasMany(
            TeacherTransferApplication::class,
            'reason_id',
            'reason_id'
        );
    }
}
