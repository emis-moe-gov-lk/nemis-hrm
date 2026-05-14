<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherTransferScoreRouteDistance extends Model
{
    protected $table = 'teacher_transfer_score_route_distances';

    protected $fillable = [
        'transfer_application_id',
        'current_workplace_id',
        'origin_latitude',
        'origin_longitude',
        'destination_latitude',
        'destination_longitude',
        'route_hash',
        'distance_km',
        'provider',
        'calculated_at',
    ];

    protected $casts = [
        'origin_latitude' => 'decimal:7',
        'origin_longitude' => 'decimal:7',
        'destination_latitude' => 'decimal:7',
        'destination_longitude' => 'decimal:7',
        'distance_km' => 'decimal:2',
        'calculated_at' => 'datetime',
    ];
}
