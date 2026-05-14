<?php

namespace App\Models;

use App\Traits\Blameable;
use App\Models\ChangeLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Versions extends Model
{
    use HasFactory, Blameable;

    protected $table = 'versions';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'version_id',
        'version',
        'release_date',
        'title',
        'description',
        'is_latest',
        'created_by',
        'updated_by',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'release_date' => 'date',
        'is_latest'    => 'boolean',
    ];

    /**
     * Scope: Latest version
     */
    public function scopeLatestVersion($query)
    {
        return $query->where('is_latest', true);
    }

    /**
     * Auto-generate version_id (optional)
     * Example: VER0001
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->version_id)) {
                $lastId = static::max('id') + 1;
                $model->version_id = 'VER' . str_pad($lastId, 4, '0', STR_PAD_LEFT);
            }
        });
    }


    public function changeLogs()
    {
        return $this->hasMany(ChangeLogs::class, 'version_id', 'version_id');
    }
}
