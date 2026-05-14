<?php

namespace App\Livewire\Offices\Peo\Profile;

use Livewire\WithPagination;
use App\Models\EmployerCurrentAppointment;
use App\Models\ProvincialEducationOffice;
use App\Models\ZonalEducationOffice;
use Livewire\Component;

class PeoZeoList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public int|string $officeId;

    public function mount($id): void
    {
        $this->officeId = $id;

        // Validate PMOE
        if (!ProvincialEducationOffice::whereKey($this->officeId)->exists()) {
            abort(404, 'Provincial Education Office not found');
        }
    }

    public function render()
    {
        $zonalServiceCounts = collect();

        $office = ProvincialEducationOffice::select('workplace_id')
            ->find($this->officeId);


        if ($office?->workplace_id) {
            $zonalServiceCounts = ZonalEducationOffice::employeeCountByServiceGroupedByZeoUsingWorkplaces(
                $office->workplace_id
            );
        }

        return view('livewire.offices.peo.profile.peo-zeo-list', [
            'officeId' => $this->officeId,
            'zonalServiceCounts' => $zonalServiceCounts,
        ]);
    }
}
