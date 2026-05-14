<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeletedUsersArchive extends Model
{
    /**
     * Table name
     */
    protected $table = 'deleted_users_archive';

    /**
     * Primary key
     */
    protected $primaryKey = 'id';

    /**
     * Key type
     */
    protected $keyType = 'int';

    /**
     * Auto incrementing ID
     */
    public $incrementing = true;

    /**
     * Timestamps are explicitly defined in migration
     */
    public $timestamps = true;

    /**
     * Mass assignable attributes
     * (use guarded if you prefer)
     */
    protected $fillable = [
        'original_user_id',
        'people_id',
        'user_data',
        'related_data',
        'delete_reason',
        'delete_note',
        'deleted_by',
        'deleted_ip',
        'is_restored',
        'restored_at',
        'data_hash',
        'created_at',
        'updated_at',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'user_data'    => 'array',
        'related_data' => 'array',
        'is_restored'  => 'boolean',
        'restored_at'  => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    /* =====================================================
     | Relationships (Optional but recommended)
     |===================================================== */

    /**
     * Original user record (if still exists)
     */
    public function originalUser()
    {
        return $this->belongsTo(User::class, 'original_user_id');
    }

    /**
     * Deleted by user (depending on what you store)
     * If you store people_id in deleted_by, keep this.
     * If you store users.id, change foreign/local keys.
     */
    public function deletedByUser()
    {
        return $this->belongsTo(User::class, 'deleted_by', 'people_id');
    }

    /* =====================================================
     | Scopes
     |===================================================== */

    public function scopeNotRestored($query)
    {
        return $query->where('is_restored', false);
    }

    public function scopeRestored($query)
    {
        return $query->where('is_restored', true);
    }

    /* =====================================================
     | Helpers
     |===================================================== */

    public function markAsRestored(): void
    {
        $this->update([
            'is_restored' => true,
            'restored_at' => now(),
        ]);
    }
}