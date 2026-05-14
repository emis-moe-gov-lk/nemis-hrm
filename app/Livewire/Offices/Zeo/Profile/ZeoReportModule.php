<?php

namespace App\Livewire\Offices\Zeo\Profile;

use App\Models\ZonalEducationOffice;
use Livewire\Component;

class ZeoReportModule extends Component
{
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
        return view('livewire.offices.zeo.profile.zeo-report-module');
    }
}
