<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Blameable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Validation\ValidationException;

class TransferBoardMembers extends Model
{
    use HasFactory, Blameable, LogsActivity;

    protected $table = 'transfer_board_members';

    protected $primaryKey = 'id';

    protected $fillable = [
        'tbm_id',
        'board_id',
        'people_id',
        'association',
        'role',
        'active_status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'active_status' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Boot (Auto TBM ID + Validation)
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::creating(function ($model) {

            // Auto ID
            if (empty($model->tbm_id)) {
                $model->tbm_id = 'TBM-' . date('Y') . '-' . rand(1000, 9999);
            }

            // normalize role
            $role = strtolower($model->role);

            // Prevent duplicate member (friendly validation)
            $exists = self::where('board_id', $model->board_id)
                ->where('people_id', $model->people_id)
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'people_id' => 'This member is already assigned to this board'
                ]);
            }

            // Only ONE Chairman
            if ($role === 'chairman') {
                $chairmanExists = self::where('board_id', $model->board_id)
                    ->whereIn('role', ['Chairman', 'chairman', 'CHAIRMAN'])
                    ->exists();

                if ($chairmanExists) {
                    throw ValidationException::withMessages([
                        'role' => 'Chairman already exists for this board'
                    ]);
                }
            }

            // Only ONE Secretary
            if ($role === 'secretary') {
                $secretaryExists = self::where('board_id', $model->board_id)
                    ->whereIn('role', ['Secretary', 'secretary', 'SECRETARY'])
                    ->exists();

                if ($secretaryExists) {
                    throw ValidationException::withMessages([
                        'role' => 'Secretary already exists for this board'
                    ]);
                }
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function board()
    {
        return $this->belongsTo(
            TransferBoard::class,
            'board_id',
            'board_id'
        );
    }

    public function person()
    {
        return $this->belongsTo(
            People::class,
            'people_id',
            'people_id'
        );
    }

    public function attendances()
    {
        return $this->hasMany(
            TransferBoardMemberAttendances::class,
            'tbm_id',
            'tbm_id'
        );
    }

    public function createdBy()
    {
        return $this->belongsTo(
            People::class,
            'created_by',
            'people_id'
        );
    }

    public function updatedBy()
    {
        return $this->belongsTo(
            People::class,
            'updated_by',
            'people_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Activity Log
    |--------------------------------------------------------------------------
    */

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('transfer_board_members')
            ->dontSubmitEmptyLogs();
    }
}
