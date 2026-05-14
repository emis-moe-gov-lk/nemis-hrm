<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Blameable;

class InstitutionGroup extends Model
{
    use HasFactory, Blameable;

    protected $table = 'institution_groups';

    protected $fillable = [
        'group_code',
        'parent_office_id',
        'group_name',
        'group_description',
        'is_assigned',
        'created_by',
        'updated_by',
        'active_status',
    ];

    protected $casts = [
        'active_status' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // 🔹 Parent Office (Workplace)
    public function parentOffice()
    {
        return $this->belongsTo(
            Workplaces::class,
            'parent_office_id',
            'workplace_id'
        );
    }

    // 🔹 Assigned IS (People)
    public function assignedPerson()
    {
        return $this->belongsTo(
            People::class,
            'is_assigned',
            'people_id'
        );
    }

    // 🔹 Many-to-Many Institutions
    public function institutions()
    {
        return $this->belongsToMany(
            Institution::class,
            'institution_group_institutions',
            'group_code',
            'institution_id',
            'group_code',
            'workplace_id'
        )->withTimestamps();
    }
}
