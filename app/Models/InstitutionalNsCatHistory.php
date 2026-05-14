<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InstitutionalNsCatHistory extends Model
{
    use HasFactory;

    protected $table = 'institutional_ns_cat_histories';

    protected $primaryKey = 'id';

    protected $fillable = [
        'workplace_id',
        'ns_cat',
        'effective_year',
        'start_date',
        'end_date',
        'remarks',
    ];

    /**
     * Relationship with Institution
     */
    public function institution()
    {
        return $this->belongsTo(Institution::class, 'workplace_id', 'workplace_id');
    }
}
