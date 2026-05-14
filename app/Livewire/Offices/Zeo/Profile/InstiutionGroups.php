<?php

namespace App\Livewire\Offices\Zeo\Profile;

use App\Models\InstitutionGroup;
use App\Models\ZonalEducationOffice;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class InstiutionGroups extends Component
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

    private function getTeacherCountsByGroup(): array
    {
        return DB::table('institution_group_institutions as igi')
            ->join('institution_groups as ig', 'ig.group_code', '=', 'igi.group_code')
            ->leftJoin('employer_current_appointments as eca', function ($join) {
                $join->on('eca.workplace_id', '=', 'igi.institution_id')
                    ->where('eca.position_id', '=', 'POS001');
            })
            ->where('ig.parent_office_id', $this->workplaceId)
            ->select(
                'igi.group_code',
                DB::raw('COUNT(DISTINCT eca.employee_id) as total_teachers')
            )
            ->groupBy('igi.group_code')
            ->pluck('total_teachers', 'igi.group_code')
            ->map(fn($count) => (int) $count)
            ->toArray();
    }

    private function abbreviateInstitutionName(string $name): string
    {
        $clean = preg_replace('/[^A-Za-z0-9\s]/', ' ', $name);
        $parts = preg_split('/\s+/', trim((string) $clean));
        $parts = array_values(array_filter($parts));

        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
        }

        if (count($parts) === 1) {
            return strtoupper(substr($parts[0], 0, 2));
        }

        return '--';
    }

    public function render()
    {
        $teacherCounts = $this->getTeacherCountsByGroup();

        $groups = InstitutionGroup::query()
            ->where('parent_office_id', $this->workplaceId)
            ->with(['assignedPerson:people_id,name_with_initials'])
            ->with(['institutions:workplace_id,name'])
            ->withCount(['institutions as total_schools'])
            ->latest()
            ->get()
            ->map(function ($group) use ($teacherCounts) {
                $labels = $group->institutions
                    ->pluck('name')
                    ->filter()
                    ->map(fn($name) => $this->abbreviateInstitutionName($name))
                    ->unique()
                    ->values();

                return [
                    'group_code' => $group->group_code,
                    'group_name' => $group->group_name,
                    'description' => $group->group_description,
                    'officer_name' => $group->assignedPerson?->name_with_initials ?? 'Not assigned',
                    'total_schools' => (int) $group->total_schools,
                    'total_teachers' => $teacherCounts[$group->group_code] ?? 0,
                    'institution_short_labels' => $labels->take(2)->all(),
                    'remaining_institution_count' => max($labels->count() - 2, 0),
                ];
            });

        return view('livewire.offices.zeo.profile.instiution-groups', [
            'groups' => $groups,
        ]);
    }
}
