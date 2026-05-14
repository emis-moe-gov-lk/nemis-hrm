<?php

namespace App\Livewire\TransferModule;

use Livewire\Component;
use App\Models\TransferPolicy;
use App\Models\TransferPolicyStep;

class TransferPolicyView extends Component
{
    public $policy;

    public function mount($id)
    {
        $this->policy = TransferPolicy::with([
            'service',
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
                ->with('officeLevel')
                ->orderBy('office_level_id')
                ->orderBy('transfer_category_name')
                ->get()
        );
    }

    public function deletePolicy()
    {
        if ($this->policy->is_locked) {
            session()->flash('error', __('This policy is locked and cannot be deleted.'));
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
        $this->policy->is_locked = !$this->policy->is_locked;
        $this->policy->save();
        session()->flash('success', $this->policy->is_locked ? __('Policy locked successfully.') : __('Policy unlocked successfully.'));
    }

    public function render()
    {
        return view('livewire.transfer-module.transfer-policy-view');
    }
}
