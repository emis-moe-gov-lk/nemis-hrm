<?php

namespace App\Livewire\NationalSchool;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Institution;
use App\Models\Authority;
use App\Models\ProvincialEducationOffice;
use App\Models\ZonalEducationOffice;
use App\Models\DivisionalEducationOffice;

class SchoolList extends Component
{
    use WithPagination;

    /* =========================
     | Dropdown Options
     ========================= */
    public $authorityOption = [];
    public $provinceOption  = [];
    public $zoneOption      = [];
    public $divisionOption  = [];
    public $statusOption    = [];

    /* =========================
     | Filters
     ========================= */
    public $query     = '';
    public $authority = 'AUID01';
    public $province  = '';
    public $zone      = '';
    public $division  = '';
    public $status    = '';

    /* =========================
     | Query String Sync
     ========================= */
    protected $updatesQueryString = [
        'query'     => ['except' => ''],
        'authority' => ['except' => ''],
        'province'  => ['except' => ''],
        'zone'      => ['except' => ''],
        'division'  => ['except' => ''],
        'status'    => ['except' => ''],
        'page'      => ['except' => 1],
    ];

    /* =========================
     | Mount
     ========================= */
    public function mount(): void
    {
        $this->authorityOption = Authority::active()->get();
        $this->provinceOption  = ProvincialEducationOffice::active()->get();

        // Status dropdown
        $this->statusOption = [
            (object) ['id' => '1', 'name' => 'Active'],
            (object) ['id' => '0', 'name' => 'Closed'],
        ];
    }

    /* =========================
     | Dependent Dropdowns
     ========================= */
    public function updatedAuthority(): void
    {
        $this->reset(['province', 'zone', 'division']);
        $this->provinceOption = ProvincialEducationOffice::active()->get();
        $this->zoneOption     = [];
        $this->divisionOption = [];
        $this->resetPage();
    }

    public function updatedProvince($value): void
    {
        $this->reset(['zone', 'division']);

        $this->zoneOption = ZonalEducationOffice::active()
            ->where('peo_wp_id', $value)
            ->get();

        $this->divisionOption = [];
        $this->resetPage();
    }

    public function updatedZone($value): void
    {
        $this->reset(['division']);

        $this->divisionOption = DivisionalEducationOffice::active()
            ->where('zeo_wp_id', $value)
            ->get();

        $this->resetPage();
    }

    /* =========================
     | Reset Pagination on Filter Change
     ========================= */
    public function updated($property): void
    {
        if (in_array($property, [
            'query',
            'authority',
            'province',
            'zone',
            'division',
            'status',
        ])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $institutions = Institution::query()
            ->with([
                'zonalEducationOffice',
                'divisionalEducationOffice',
            ])

            // Search
            ->when(filled($this->query), function ($q) {
                $search = trim($this->query);
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('census_no', 'like', "%{$search}%");
                });
            })

            // Authority
            ->when(
                filled($this->authority),
                fn($q) => $q->where('authority_id', $this->authority)
            )

            // Province (via ZEO)
            ->when(filled($this->province), function ($q) {
                $q->whereHas(
                    'zonalEducationOffice',
                    fn($z) => $z->where('peo_wp_id', $this->province)
                );
            })

            // Zone
            ->when(
                filled($this->zone),
                fn($q) => $q->where('zeo_wp_id', $this->zone)
            )

            // Division
            ->when(
                filled($this->division),
                fn($q) => $q->where('deo_wp_id', $this->division)
            )

            // Status filter
            ->when(
                filled($this->status),
                fn($q) => $q->where('active_status', $this->status)
            )
            ->paginate(50);

        return view('livewire.national-school.school-list', [
            'institutions' => $institutions,
        ]);
    }
}
