<?php

namespace App\Livewire\Offices\Institutions;

use App\Models\Institution;
use App\Models\Workplaces;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class InstitutionsList extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Load logged-in user with workplace
        $user = Auth::user()->load('workplace');

        $workplace = $user->workplace;

        if (!$workplace) {
            abort(403, 'You do not have a registered workplace.');
        }

        // Step 1: Get all workplaces under logged user's hierarchy
        $allowedWorkplaceIds = $workplace->getAllChildWorkplaces();

        // Step 2: Load institutions (schools) matching those workplaces
        $query = Institution::whereIn('workplace_id', $allowedWorkplaceIds)->active();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('census_no', 'like', '%' . $this->search . '%');
            });
        }

        $institutions = $query->paginate(20);

        return view(
            'livewire.offices.institutions.institutions-list',
            compact('institutions')
        );
    }
}
