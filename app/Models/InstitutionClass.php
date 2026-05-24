<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InstitutionClass extends Model
{
    use HasFactory;

    protected $fillable = ['institution_grade_id', 'class_name', 'medium_id'];

    public function grade()
    {
        return $this->belongsTo(InstitutionGrade::class, 'institution_grade_id');
    }

    public function medium()
    {
        return $this->belongsTo(MediumOfInstruction::class, 'medium_id', 'medium_id');
    }

    public function admissions()
    {
        return $this->hasMany(InstitutionStudentAdmission::class);
    }
}
