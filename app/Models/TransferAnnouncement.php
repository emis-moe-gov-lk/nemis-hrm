<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferAnnouncement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'type',
        'is_active',
        'publish_date',
        'expiry_date',
        'link_text',
        'link_route',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'publish_date' => 'datetime',
        'expiry_date' => 'datetime',
        'display_order' => 'integer',
    ];

    /**
     * Scope a query to only include active announcements.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('publish_date')
                    ->orWhere('publish_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>=', now());
            })
            ->orderBy('display_order', 'asc')
            ->orderBy('created_at', 'desc');
    }
}
