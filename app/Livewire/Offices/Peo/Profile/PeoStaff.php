<?php

namespace App\Livewire\Offices\Peo\Profile;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EmployerCurrentAppointment;
use App\Models\ProvincialEducationOffice;

class PeoStaff extends Component
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
        $office = ProvincialEducationOffice::find($this->officeId);

        $staffList = collect();

        if ($office?->workplace_id) {
            $staffList = EmployerCurrentAppointment::with([
                'employee',
                'position',
                'service',
                'rank',
                //'teacher', // eager-load teacher (avoid N+1)
            ])
                ->where('workplace_id', $office->workplace_id)
                ->orderByDesc('appoint_date')
                ->paginate(10);
        }

        return view('livewire.offices.peo.profile.peo-staff', [
            'officeId'  => $this->officeId,
            'staffList' => $staffList,
        ]);
    }
}
