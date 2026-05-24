<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReasonsForTerminationOfService extends Model
{
    use HasFactory;

    protected $table = 'reasons_for_termination_of_services';

    protected $primaryKey = 'id';

    protected $fillable = [
        'termination_id',
        'reason',
        'active_status',
    ];

    protected $casts = [
        'active_status' => 'boolean',
    ];

    // If you want to filter active institutions by default
    public function scopeActive($query)
    {
        return $query->where('active_status', 1);
    }
}
