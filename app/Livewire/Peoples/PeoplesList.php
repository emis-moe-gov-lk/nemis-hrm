<?php

namespace App\Livewire\Peoples;

use App\Helpers\NicHelper;
use App\Models\People;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class PeoplesList extends Component
{
    use WithPagination;

    public string $query = '';

    /** 'all' | 'with' | 'without' */
    public string $employmentFilter = 'all';

    public Collection|array $results = [];

    public function updatedEmploymentFilter(): void
    {
        $this->resetPage();
    }

    public function search(): void
    {
        $this->resetPage();

        $raw = trim($this->query);

        if ($raw === '' || strlen($raw) < 3) {
            $this->results = [];
            return;
        }

        $peopleQuery = $this->peopleBaseQuery()->limit(10);

        if (NicHelper::isValid($raw)) {
            $normalized = NicHelper::normalize($raw);
            $peopleQuery->where('nic_hash', NicHelper::hash($normalized));
        } else {
            $peopleQuery->where(function ($q) use ($raw) {
                $q->where('people_id', 'like', "%{$raw}%")
                    ->orWhere('phone', 'like', "%{$raw}%")
                    ->orWhere('email', 'like', "%{$raw}%")
                    ->orWhere('full_name', 'like', "%{$raw}%")
                    ->orWhere('name_with_initials', 'like', "%{$raw}%");
            });
        }

        $this->results = $peopleQuery->get();
    }

    public function clearSearch(): void
    {
        $this->query = '';
        $this->results = [];
        $this->resetPage();
    }

    public function render(): View
    {
        $employees = $this->peopleBaseQuery()
            ->paginate(20);

        return view('livewire.peoples.peoples-list', compact('employees'));
    }

    private function peopleBaseQuery()
    {
        $query = People::query()
            ->orderByDesc('id');

        if ($this->employmentFilter === 'with') {
            $query->whereHas('myAppointments');
        } elseif ($this->employmentFilter === 'without') {
            $query->whereDoesntHave('myAppointments')
                  ->whereDoesntHave('currentAppointment');
        }

        return $query;
    }
}
