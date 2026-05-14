<?php

namespace App\Livewire\Offices\Zeo\Profile;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ZonalEducationOffice;
use App\Models\EmployerCurrentAppointment;

class ZeoStaff extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $officeId;
    public $workplaceId;

    public function mount($id)
    {
        $this->officeId = $id;

        $office = ZonalEducationOffice::find($this->officeId);

        if (!$office) {
            abort(404, 'Zonal Education Office not found');
        }

        $this->workplaceId = $office->workplace_id;
    }

    public function render()
    {
        $staffList = EmployerCurrentAppointment::with([
            'employee.title',
            'service',
            'rank',
            'position',
        ])
            ->where('workplace_id', $this->workplaceId)
            ->orderBy('appoint_date', 'asc')
            ->paginate(10);

        return view('livewire.offices.zeo.profile.zeo-staff', [
            'officeId'  => $this->officeId,
            'staffList' => $staffList,
        ]);
    }
}
