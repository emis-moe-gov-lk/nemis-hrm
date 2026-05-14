<?php

namespace App\Livewire\Offices\Deo\Profile;

use App\Models\DivisionalEducationOffice;
use Livewire\Component;

class DeoReportModule extends Component
{
    public $officeId;
    public $workplaceId;

    public function mount($id)
    {
        $this->officeId = $id;
        $office = DivisionalEducationOffice::find($this->officeId);

        if (!$office) {
            abort(404, 'Divisional Education Office not found');
        }

        $this->workplaceId = $office->workplace_id;
    }

    public function render()
    {
        return view('livewire.offices.deo.profile.deo-report-module');
    }
}
