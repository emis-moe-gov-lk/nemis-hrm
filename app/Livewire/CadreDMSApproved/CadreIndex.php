<?php

namespace App\Livewire\CadreDMSApproved;

use Livewire\Component;
use App\Models\Authority;
use App\Models\Workplaces;
use App\Models\Institution;
use Livewire\WithPagination;
use App\Models\CadreCirculars;
use App\Models\ZonalEducationOffice;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use App\Models\DivisionalEducationOffice;
use App\Models\ProvincialEducationOffice;

class CadreIndex extends Component
{
    use WithPagination;

    /* =========================
     | Data from user/workplace
     ========================= */
    public $user;
    public $workplace;
    public array $allowedWorkplaceIds = [];

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
    public $authority = '';
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
        // Load logged-in user with workplace
        $this->user = Auth::user()->load('workplace');
        $this->workplace = $this->user->workplace;

        $this->activeCircular = CadreCirculars::active()->first();

        if (! $this->workplace && ! $this->isSuperAdmin()) {
            abort(403, 'You do not have a registered workplace.');
        }

        $this->allowedWorkplaceIds = $this->isSuperAdmin()
            ? Workplaces::query()->pluck('workplace_id')->all()
            : $this->workplace->getWorkplaceHierarchy();

        // Dropdowns
        $this->authorityOption = Authority::active()->get();

        $this->provinceOption  = ProvincialEducationOffice::active()
            ->whereIn('workplace_id', $this->allowedWorkplaceIds)
            ->get();

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

        $this->provinceOption = ProvincialEducationOffice::active()
            ->whereIn('workplace_id', $this->allowedWorkplaceIds)
            ->get();

        $this->zoneOption     = [];
        $this->divisionOption = [];
        $this->resetPage();
    }

    public function updatedProvince($value): void
    {
        $this->reset(['zone', 'division']);

        $this->zoneOption = ZonalEducationOffice::active()
            ->where('peo_wp_id', $value)
            ->whereIn('workplace_id', $this->allowedWorkplaceIds)
            ->get();

        $this->divisionOption = [];
        $this->resetPage();
    }

    public function updatedZone($value): void
    {
        $this->reset(['division']);

        $this->divisionOption = DivisionalEducationOffice::active()
            ->where('zeo_wp_id', $value)
            ->whereIn('workplace_id', $this->allowedWorkplaceIds)
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
        ], true)) {
            $this->resetPage();
        }
    }

    public $activeCircular;

    protected function isSuperAdmin(): bool
    {
        return $this->user?->hasAnyRole(['super admin', 'superadmin']) ?? false;
    }

    /* =========================
     | Render
     ========================= */
    public function render()
    {
        if (! $this->activeCircular) {
            $institutions = new LengthAwarePaginator([], 0, 50);

            return view('livewire.cadre-d-m-s-approved.cadre-index', compact('institutions'));
        }

        $circularId = $this->activeCircular->circular_id;

        $institutions = Institution::query()
            ->with([
                'zonalEducationOffice',
                'divisionalEducationOffice',
            ])

            // add summed cadre columns
            ->withSum(['cadreApproved as approved_teacher_cadre' => function ($q) use ($circularId) {
                $q->where('circular_id', $circularId)
                ->whereHas('subject', function ($sub) {
                    $sub->where('type', 1); // Subject = teacher
                });
            }], 'approved_posts')

            ->withSum(['cadreApproved as approved_non_principal_cadre' => function ($q) use ($circularId) {
                $q->where('circular_id', $circularId)
                ->whereHas('subject', function ($sub) {
                    $sub->where('type', 2); // Designation = principal
                });
            }], 'approved_posts')

            ->withSum(['cadreApproved as approved_other_cadre' => function ($q) use ($circularId) {
                $q->where('circular_id', $circularId)
                ->whereHas('subject', function ($sub) {
                    $sub->where('type', 3); // Other
                });
            }], 'approved_posts')

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

            // Workplace hierarchy restriction
            ->whereIn('workplace_id', $this->allowedWorkplaceIds)
            ->paginate(50);

        return view('livewire.cadre-d-m-s-approved.cadre-index', compact('institutions'));
    }

}
