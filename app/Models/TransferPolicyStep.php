<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransferPolicyStep extends Model
{
    use HasFactory;

    protected $table = 'transfer_policy_steps';

    protected $fillable = [
        'policy_id',
        'office_level_id',
        'step_order',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'step_order' => 'integer',
    ];

    public function policy()
    {
        return $this->belongsTo(
            TransferPolicy::class,
            'policy_id',
            'policy_id'
        );
    }

    public function officeLevel()
    {
        return $this->belongsTo(
            OfficeLevel::class,
            'office_level_id',
            'office_level_id'
        );
    }
}
