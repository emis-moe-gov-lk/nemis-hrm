<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\Blameable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use App\Helpers\NicHelper;

/**
 * Class User
 *
 * @property string|null $people_id
 * @property-read \App\Models\People|null $people
 * @property-read \App\Models\EmployerCurrentAppointment|null $currentAppointment
 * @property-read \App\Models\Workplaces|null $workplace
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, LogsActivity, Blameable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nic',
        'nic_hash',
        'people_id',
        'name',
        'email',
        'contact',
        'password',
        'profile_picture',
        'remember_token',
        'active_status',
        'mfa_enabled',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nic' => 'encrypted', // Auto-encrypt/decrypt
            'name' => 'encrypted',
            //'email' => 'encrypted',
            //'contact' => 'encrypted',
            'email_verified_at' => 'datetime',
            'contact_verified_at' => 'datetime',
            'password' => 'hashed',
            'mfa_enabled' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($user) {
            // Normalize NIC
            if ($user->isDirty('nic')) {
                $cleanNic = NicHelper::normalize($user->nic);
                $user->nic = $cleanNic;
                $user->nic_hash = NicHelper::hash($cleanNic);
            }
        });

        static::saved(function ($user) {

            // Sync People only if these fields changed
            if (! $user->wasChanged(['nic', 'nic_hash', 'contact', 'email'])) {
                return;
            }

            $person = \App\Models\People::where('people_id', $user->people_id)->first();

            if ($person) {

                // Fill new data
                $person->fill([
                    'nic'       => $user->nic,
                    'nic_hash'  => $user->nic_hash,
                    'phone'     => $user->contact ?? $person->phone,
                    'email'     => $user->email ?? $person->email,
                ]);

                // Only save if actual changes
                if ($person->isDirty()) {
                    $person->saveQuietly();  // ← prevents recursive loop
                }
            }
        });
    }



    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Relationship: A user belongs to a person
     */
    public function people(): BelongsTo
    {
        return $this->belongsTo(People::class, 'people_id', 'people_id');
    }

    public function mfaMethods(): HasMany
    {
        return $this->hasMany(UserMfaMethod::class);
    }

    public function preferredMfaMethod(): HasOne
    {
        return $this->hasOne(UserMfaMethod::class)->where('preferred', true);
    }

    public function recoveryCodes(): HasMany
    {
        return $this->hasMany(UserMfaRecoveryCode::class);
    }

    public function trustedDevices(): HasMany
    {
        return $this->hasMany(TrustedDevice::class);
    }

    /**
     * Relationship: A user has one current appointment (through their person)
     */
    public function currentAppointment(): HasOne
    {
        return $this->hasOne(EmployerCurrentAppointment::class, 'employee_id', 'people_id');
    }

    /**
     * Relationship: A user’s workplace (through their current appointment)
     */
    public function workplace(): HasOneThrough
    {
        return $this->hasOneThrough(
            Workplaces::class,
            EmployerCurrentAppointment::class,
            'employee_id',   // FK on EmployerCurrentAppointment table
            'workplace_id',  // FK on Workplaces table
            'people_id',     // local key on Users table
            'workplace_id'   // local key on EmployerCurrentAppointment table
        );
    }

    public function officeLevel(): HasOneThrough
    {
        return $this->hasOneThrough(
            OfficeLevel::class,
            EmployerCurrentAppointment::class,
            'employee_id',     // FK on EmployerCurrentAppointment table
            'office_level_id', // FK on OfficeLevel table
            'people_id',       // local key on Users table
            'office_level_id'  // local key on EmployerCurrentAppointment table
        );
    }

    /**
     * Quick accessor to get full workplace details dynamically
     */
    public function getFullWorkplaceAttribute()
    {
        return $this->workplace?->office();
    }

    public function getWorkplaceNameAttribute()
    {
        return $this->workplace?->office_name ?? 'N/A';
    }

    public function getWorkplaceIdAttribute()
    {
        return $this->workplace?->workplace_id ?? null;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('users')
            ->dontSubmitEmptyLogs();
    }
}
