<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InstitutionalFacilityHistory extends Model
{
    use HasFactory;

    protected $table = 'institutional_facility_histories';

    protected $fillable = [
        'workplace_id',
        'facilities_id',
        'effective_year',
        'start_date',
        'end_date',
        'remarks',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    /**
     * Relationship: Institution
     */
    public function institution()
    {
        return $this->belongsTo(Institution::class, 'workplace_id', 'workplace_id');
    }

    /**
     * Relationship: Facility
     */
    public function facility()
    {
        return $this->belongsTo(InstitutionalFacility::class, 'facilities_id', 'id');
    }
}
