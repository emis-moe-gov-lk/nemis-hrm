<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class GradesList
 *
 * @property string $grade_id
 */
class GradesList extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'order', 'active_status'];
}
