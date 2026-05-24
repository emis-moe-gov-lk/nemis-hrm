<?php

namespace App\Livewire\TransferModule\Teacher;

use Livewire\Component;
use App\Models\TeacherTransferAppeals;
use App\Models\TeacherTransferBoard;
use App\Models\TeacherTransferPolicy;
use App\Models\TeacherTransferPolicyStep;
use App\Support\Transfer\TransferAccess;

class TeacherTransferPolicyView extends Component
{
    public $policy;
    public bool $canManageActions = false;
    public bool $canDeletePolicy = false;
    public array $deleteDependencyCounts = [];

    public function mount($id)
    {
        $this->policy = TeacherTransferPolicy::with([
            'authority',
            'steps.officeLevel',
            'scoreRules.criterion',
            'facilityScoreRules.facility',
            'achievementLevelScores',
        ])
            ->where('policy_id', $id)
            ->firstOrFail();

        $this->policy->setRelation(
            'categories',
            $this->policy->categoriesQuery()
                ->with(['officeLevel', 'transferSubCategory'])
                ->orderBy('office_level_id')
                ->orderBy('transfer_category_name')
                ->get()
        );

        $this->canManageActions = TransferAccess::canManagePolicy(auth()->user(), $this->policy);
        $this->refreshDeleteState();

        abort_unless(TransferAccess::canViewPolicy(auth()->user(), $this->policy), 403);
    }

    protected function refreshDeleteState(): void
    {
        $this->deleteDependencyCounts = [
            'applications' => $this->policy->teacherApplication()->count(),
            'boards' => TeacherTransferBoard::query()
                ->where('policy_id', $this->policy->policy_id)
                ->count(),
            'appeals' => TeacherTransferAppeals::query()
                ->where('policy_id', $this->policy->policy_id)
                ->count(),
        ];

        $this->canDeletePolicy = array_sum($this->deleteDependencyCounts) === 0;
    }

    public function goBack()
    {
        $previousUrl = url()->previous();
        $currentUrl = request()->fullUrl();

        return redirect()->to(
            $previousUrl && $previousUrl !== $currentUrl
                ? $previousUrl
                : route('transfer.transfer-policies')
        );
    }

    public function deletePolicy()
    {
        abort_unless(TransferAccess::canManagePolicy(auth()->user(), $this->policy), 403);

        if ($this->policy->is_locked) {
            session()->flash('error', __('This policy is locked and cannot be deleted.'));
            return;
        }

        $this->refreshDeleteState();

        if (!$this->canDeletePolicy) {
            $parts = [];

            foreach ($this->deleteDependencyCounts as $label => $count) {
                if ($count > 0) {
                    $parts[] = "{$count} {$label}";
                }
            }

            session()->flash(
                'error',
                __('This policy cannot be deleted because it already has linked transfer records: :items.', [
                    'items' => implode(', ', $parts),
                ])
            );

            return;
        }

        try {
            $this->policy->delete();
            session()->flash('success', __('Transfer Policy deleted successfully.'));
            return redirect()->route('transfer.transfer-policies');
        } catch (\Throwable $e) {
            session()->flash('error', __('Failed to delete Transfer Policy.'));
        }
    }

    public function toggleLock()
    {
        abort_unless(TransferAccess::canManagePolicy(auth()->user(), $this->policy), 403);

        $this->policy->is_locked = !$this->policy->is_locked;
        $this->policy->save();
        session()->flash('success', $this->policy->is_locked ? __('Policy locked successfully.') : __('Policy unlocked successfully.'));
    }

    public function render()
    {
        return view('livewire.transfer-module.teacher.teacher-transfer-policy-view');
    }
}
