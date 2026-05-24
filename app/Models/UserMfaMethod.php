<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMfaMethod extends Model
{
    public const METHOD_EMAIL = 'email';
    public const METHOD_TOTP = 'totp';
    public const METHOD_SMS = 'sms';

    protected $fillable = [
        'user_id',
        'method',
        'enabled',
        'preferred',
        'verified_at',
        'secret_encrypted',
        'destination_snapshot',
        'meta',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'preferred' => 'boolean',
        'verified_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isEmail(): bool
    {
        return $this->method === self::METHOD_EMAIL;
    }

    public function isTotp(): bool
    {
        return $this->method === self::METHOD_TOTP;
    }

    public function isSms(): bool
    {
        return $this->method === self::METHOD_SMS;
    }
}
