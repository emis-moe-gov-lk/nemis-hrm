<?php

namespace App\Livewire\Offices\Zeo\Profile;

use App\Models\Institution;
use App\Models\ZonalEducationOffice;
use Livewire\Component;
use Livewire\WithPagination;

class ZeoInstitutionsList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $officeId;
    public $office;

    public function mount($id)
    {
        $this->officeId = $id;

        $this->office = ZonalEducationOffice::find($this->officeId);

        if (!$this->office) {
            abort(404, 'Zonal Education Office not found');
        }
    }

    public function render()
    {
        $institutions = Institution::where('zeo_wp_id', $this->office->workplace_id)
            ->withCount('staffList')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('livewire.offices.zeo.profile.zeo-institutions-list', [
            'institutionList' => $institutions,
        ]);
    }
}
