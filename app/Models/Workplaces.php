<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workplaces extends Model
{
    use HasFactory;

    protected $table = 'workplaces';

    protected $primaryKey = 'workplace_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'workplace_id',
        'office_level_id',
        'parent_workplace_id'
    ];

    /*
    |--------------------------------------------------------------------------
    | Basic Relationships
    |--------------------------------------------------------------------------
    */

    public function parent()
    {
        return $this->belongsTo(
            self::class,
            'parent_workplace_id',
            'workplace_id'
        );
    }

    public function children()
    {
        return $this->hasMany(
            self::class,
            'parent_workplace_id',
            'workplace_id'
        );
    }

    public function officeLevel()
    {
        return $this->belongsTo(OfficeLevel::class, 'office_level_id');
    }

    public function institutionGroups()
    {
        return $this->hasMany(
            InstitutionGroup::class,
            'parent_office_id',
            'workplace_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Office Type Relationships
    |--------------------------------------------------------------------------
    */

    public function ministry()
    {
        return $this->hasOne(MinistryOfEducationOffice::class, 'workplace_id', 'workplace_id');
    }

    public function provincialMinistry()
    {
        return $this->hasOne(ProvincialMinistryOfEducationOffice::class, 'workplace_id', 'workplace_id');
    }

    public function provincial()
    {
        return $this->hasOne(ProvincialEducationOffice::class, 'workplace_id', 'workplace_id');
    }

    public function zonal()
    {
        return $this->hasOne(ZonalEducationOffice::class, 'workplace_id', 'workplace_id');
    }

    public function divisional()
    {
        return $this->hasOne(DivisionalEducationOffice::class, 'workplace_id', 'workplace_id');
    }

    public function institution()
    {
        return $this->hasOne(Institution::class, 'workplace_id', 'workplace_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Optimized Office Resolver
    |--------------------------------------------------------------------------
    */

    public function office()
    {
        $relations = [
            'OLID001' => 'ministry',
            'OLID002' => 'provincialMinistry',
            'OLID003' => 'provincial',
            'OLID004' => 'zonal',
            'OLID005' => 'divisional',
            'OLID006' => 'institution',
        ];

        $relation = $relations[$this->office_level_id] ?? null;

        if (!$relation) {
            return null;
        }

        // Return already loaded relation if available
        if ($this->relationLoaded($relation)) {
            return $this->getRelation($relation);
        }

        return $this->$relation()->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getOfficeNameAttribute()
    {
        $office = $this->office();

        if (!$office) {
            return 'Unknown Office';
        }

        return $office->name
            ?? $office->short_name
            ?? 'Unnamed Office';
    }

    public function getAddressAttribute()
    {
        $office = $this->office();

        return $office->address ?? null;
    }

    /*
    |--------------------------------------------------------------------------
    | Hierarchy Helpers (Safe BFS)
    |--------------------------------------------------------------------------
    */

    public function getAllChildWorkplaces()
    {
        $visited = collect([$this->workplace_id]);
        $queue = collect([$this->workplace_id]);

        while ($queue->isNotEmpty()) {

            $children = self::whereIn('parent_workplace_id', $queue)
                ->pluck('workplace_id');

            $new = $children->diff($visited);

            if ($new->isEmpty()) {
                break;
            }

            $visited = $visited->merge($new);
            $queue = $new;
        }

        return $visited->values()->all();
    }

    public function getAllParentWorkplaces()
    {
        $ids = collect([$this->workplace_id]);
        $current = $this->parent_workplace_id;

        while ($current) {

            $parent = self::where('workplace_id', $current)->first();

            if (!$parent) {
                break;
            }

            // Prevent circular loop
            if ($ids->contains($parent->workplace_id)) {
                break;
            }

            $ids->push($parent->workplace_id);
            $current = $parent->parent_workplace_id;
        }

        return $ids->values()->all();
    }

    public function getWorkplaceHierarchy()
    {
        return collect($this->getAllParentWorkplaces())
            ->merge($this->getAllChildWorkplaces())
            ->unique()
            ->values()
            ->all();
    }
}
