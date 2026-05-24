<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class InstitutionGrade
 *
 * @property string $grade_id
 */
class InstitutionGrade extends Model
{
    use HasFactory;

    protected $fillable = ['institution_id', 'grade_id', 'academic_year', 'order'];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function gradeList()
    {
        return $this->belongsTo(GradesList::class, 'grade_id');
    }

    public function classes()
    {
        return $this->hasMany(InstitutionClass::class);
    }
}
