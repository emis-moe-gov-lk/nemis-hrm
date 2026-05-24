<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Blameable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Class TeacherTransferBoardRecommendation
 *
 * @property int $id
 * @property string $transfer_application_id
 * @property string|null $transfer_board_id
 * @property-read \App\Models\TeacherTransferApplication|null $application
 * @property-read \App\Models\TeacherTransferBoard|null $board
 * @property-read \App\Models\ZonalEducationOffice|null $selectedZone
 * @property-read \App\Models\Institution|null $selectedSchool
 */
class TeacherTransferBoardRecommendation extends Model
{
    use HasFactory, Blameable, LogsActivity;

    protected $table = 'teacher_transfer_board_recommendations';

    protected $primaryKey = 'id';

    protected $fillable = [
        'transfer_application_id',
        'transfer_board_id',
        'ttbr_list_id',
        'recommendation_remarks',
        'recommendation_status',
        'school_selection_type',
        'selected_zone_id',
        'selected_school_id',
        'transfer_effective_date',
        'rejection_reason',
        'created_by',
        'updated_by',
        'active_status',
    ];

    protected $casts = [
        'transfer_effective_date' => 'date',
        'active_status' => 'boolean',
    ];

    /**
     * Relationships
     */

    // Transfer Application
    public function application()
    {
        return $this->belongsTo(
            TeacherTransferApplication::class,
            'transfer_application_id',
            'transfer_application_id'
        );
    }

    // Recommendation Decision List
    public function recommendationList()
    {
        return $this->belongsTo(
            TeacherTransferBoardRecommendationList::class,
            'ttbr_list_id',
            'ttbr_list_id'
        );
    }

    public function board()
    {
        return $this->belongsTo(
            TeacherTransferBoard::class,
            'transfer_board_id',
            'board_id'
        );
    }

    public function selectedZone()
    {
        return $this->belongsTo(
            ZonalEducationOffice::class,
            'selected_zone_id',
            'workplace_id'
        );
    }

    public function selectedSchool()
    {
        return $this->belongsTo(
            Institution::class,
            'selected_school_id',
            'workplace_id'
        );
    }

    // Created By (People)
    public function creator()
    {
        return $this->belongsTo(People::class, 'created_by', 'people_id');
    }

    // Updated By (People)
    public function updater()
    {
        return $this->belongsTo(People::class, 'updated_by', 'people_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('teacher_transfer_board_recommendations')
            ->dontSubmitEmptyLogs();
    }
}
