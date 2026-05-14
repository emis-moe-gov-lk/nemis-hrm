<?php

namespace App\Livewire\Offices\Zeo\Profile;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ZonalEducationOffice;
use App\Models\EmployerCurrentAppointment;
use App\Models\Institution;

class ZeoPrincipalsList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $officeId;
    public $institutionIds = [];

    public function mount($id)
    {
        $this->officeId = $id;

        $office = ZonalEducationOffice::find($this->officeId);

        if (!$office) {
            abort(404, 'Zonal Education Office not found');
        }

        $this->institutionIds = Institution::where('zeo_wp_id', $office->workplace_id)
            ->pluck('workplace_id')
            ->toArray();
    }

    public function render()
    {
        $principalsList = EmployerCurrentAppointment::with([
            'employee.title',
            'service',
            'rank',
            'position',
        ])
            ->whereIn('workplace_id', $this->institutionIds)
            ->where('position_id', 'POS002')
            ->orderBy('appoint_date', 'asc')
            ->paginate(50);

        return view('livewire.offices.zeo.profile.zeo-principals-list', [
            'principalsList' => $principalsList,
        ]);
    }
}
