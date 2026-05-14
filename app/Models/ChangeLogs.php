<?php

namespace App\Models;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChangeLogs extends Model
{
    use HasFactory, Blameable;

    protected $table = 'change_logs';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'version_id',
        'type',
        'title',
        'description',
        'created_by',
        'updated_by',
    ];

    /**
     * Cast attributes
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Available change types (centralized & reusable)
     */
    public const TYPES = [
        'added',
        'modified',
        'fixed',
        'removed',
        'security',
    ];

    /**
     * Scope: filter by version
     */
    public function scopeVersion($query, string $versionId)
    {
        return $query->where('version_id', $versionId);
    }

    /**
     * ChangeLog belongs to a Version
     */
    public function version()
    {
        return $this->belongsTo(Versions::class, 'version_id', 'version_id');
    }

    /**
     * Scope: filter by change type
     */
    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
