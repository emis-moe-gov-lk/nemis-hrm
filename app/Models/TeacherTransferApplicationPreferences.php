<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeacherTransferApplicationPreferences extends Model
{
    use HasFactory;

    protected $table = 'teacher_transfer_application_preferences';

    protected $fillable = [
        'transfer_application_id',
        'preference_order',
        'zeo_wp_id',
        'ins_wp_id',
        'distance',
    ];

    protected $casts = [
        'preference_order' => 'integer',
        'distance' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function application()
    {
        return $this->belongsTo(
            TeacherTransferApplication::class,
            'transfer_application_id',
            'transfer_application_id'
        );
    }

    public function zonalOffice()
    {
        return $this->belongsTo(
            Workplaces::class,
            'zeo_wp_id',
            'workplace_id'
        );
    }

    public function institution()
    {
        return $this->belongsTo(
            Workplaces::class,
            'ins_wp_id',
            'workplace_id'
        );
    }
}
