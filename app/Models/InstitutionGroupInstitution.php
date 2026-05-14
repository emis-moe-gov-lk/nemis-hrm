<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Blameable;

class InstitutionGroupInstitution extends Model
{
    use HasFactory, Blameable;

    protected $table = 'institution_group_institutions';

    protected $fillable = [
        'group_code',
        'institution_id',
        'created_by',
        'updated_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function group()
    {
        return $this->belongsTo(
            InstitutionGroup::class,
            'group_code',
            'group_code'
        );
    }

    public function institution()
    {
        return $this->belongsTo(
            Institution::class,
            'institution_id',
            'workplace_id'
        );
    }
}
