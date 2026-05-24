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
        $query = EmployerCurrentAppointment::with([
            'employee.title',
            'service',
            'rank',
            'position',
            'workplace'
        ])
        ->select('employer_current_appointments.*')
        ->leftJoin('employer_appointments', 'employer_appointments.appointment_id', '=', 'employer_current_appointments.appointment_id')
        ->leftJoin('services', 'services.service_id', '=', 'employer_appointments.service_id')
        ->whereIn('employer_current_appointments.workplace_id', $this->institutionIds)
        ->where('employer_current_appointments.position_id', 'POS002');

        $principalsList = $query
            ->orderByRaw('COALESCE(services.rank, 9999) ASC')
            ->orderBy('employer_current_appointments.appoint_date', 'asc')
            ->paginate(50);

        return view('livewire.offices.zeo.profile.zeo-principals-list', [
            'principalsList' => $principalsList,
            'officeId' => $this->officeId,
        ]);
    }
}
