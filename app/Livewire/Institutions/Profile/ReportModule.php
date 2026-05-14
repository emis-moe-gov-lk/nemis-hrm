<?php

namespace App\Livewire\Institutions\Profile;

use Livewire\Component;
use App\Models\Institution;

class ReportModule extends Component
{
    public $id;
    public $institution;

    public function mount($id)
    {
        $this->institution = Institution::find($id);
    }
    public function render()
    {
        return view('livewire.institutions.profile.report-module');
    }
}
