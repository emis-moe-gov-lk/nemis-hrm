<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRank extends Model
{
    use HasFactory;

    protected $table = 'service_ranks';
    protected $primaryKey = 'id';

    protected $fillable = [
        'rank_id',
        'service_id',
        'rank_name',
        'rank_order',
        'description',
        'active_status',
    ];

    // If you want to filter active institutions by default
    public function scopeActive($query)
    {
        return $query->where('active_status', 1);
    }
    /**
     * A Service Rank belongs to a Service.
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }

    public function nextRank()
    {
        return self::where('service_id', $this->service_id)
            ->where('rank_id', '>', $this->rank_id)
            ->orderBy('rank_id', 'asc')
            ->first();
    }

    public function previousRank()
    {
        return self::where('service_id', $this->service_id)
            ->where('rank_id', '<', $this->rank_id)
            ->orderBy('rank_id', 'desc')
            ->first();
    }
}
