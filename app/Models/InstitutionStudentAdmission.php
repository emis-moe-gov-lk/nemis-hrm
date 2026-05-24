<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstitutionStudentAdmission extends Model
{
    use HasFactory;

    protected $fillable = ['institution_class_id', 'academic_year', 'male_count', 'female_count'];

    public function class()
    {
        return $this->belongsTo(InstitutionClass::class, 'institution_class_id');
    }
}
