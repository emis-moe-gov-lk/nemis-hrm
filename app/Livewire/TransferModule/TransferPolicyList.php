<?php

namespace App\Livewire\TransferModule;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Models\TransferPolicy as TransferPolicyModel;
use App\Models\Service;
use App\Models\TeacherTransferApplication;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Flux;

class TransferPolicyList extends Component
{
    use WithPagination;

    public $search = '';
    public $yearFilter = '';
    public $serviceFilter = '';
    public $confirmingPolicyId = null;
    public $confirmingAction = null;
    public $adminPassword = '';
    public $showPasswordModal = false;
    public $selectedPolicyId = null;
    public $showCategoriesDrawer = false;

    protected $updatesQueryString = [
        'search' => ['except' => ''],
        'yearFilter' => ['except' => ''],
        'serviceFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleStatus($policyId)
    {
        $policy = TransferPolicyModel::where('policy_id', $policyId)->first();
        if ($policy && $this->checkAuthority($policy)) {
            $policy->active_status = !$policy->active_status;
            $policy->save();
            session()->flash('success', 'Policy status updated successfully.');
        } else {
            session()->flash('error', __('Unauthorized action.'));
        }
    }

    public function toggleLock($policyId)
    {
        $policy = TransferPolicyModel::where('policy_id', $policyId)->first();
        if ($policy && $this->checkAuthority($policy)) {
            $policy->is_locked = !$policy->is_locked;
            $policy->save();
            session()->flash('success', $policy->is_locked ? __('Policy locked successfully.') : __('Policy unlocked successfully.'));
        } else {
            session()->flash('error', __('Unauthorized action.'));
        }
    }

    public function requestActionConfirmation($action, $policyId)
    {
        $policy = TransferPolicyModel::where('policy_id', $policyId)->first();
        if (!$policy || !$this->checkAuthority($policy)) {
            session()->flash('error', __('Unauthorized action.'));
            return;
        }

        $this->confirmingAction = $action;
        $this->confirmingPolicyId = $policyId;
        $this->adminPassword = '';
        $this->resetErrorBag();
        
        $this->showPasswordModal = true;
    }

    public function verifyPasswordAndExecute()
    {
        $this->validate([
            'adminPassword' => 'required',
        ]);

        if (!Hash::check($this->adminPassword, Auth::user()->password)) {
            $this->addError('adminPassword', __('Incorrect password.'));
            return;
        }

        $action = $this->confirmingAction;
        $policyId = $this->confirmingPolicyId;

        if ($action === 'toggleStatus') {
            $this->toggleStatus($policyId);
        } elseif ($action === 'toggleLock') {
            $this->toggleLock($policyId);
        } elseif ($action === 'deletePolicy') {
            $this->deletePolicy($policyId);
        }

        $this->showPasswordModal = false;
        $this->confirmingAction = null;
        $this->confirmingPolicyId = null;
        $this->adminPassword = '';
    }

    public function showCategories($policyId)
    {
        $this->selectedPolicyId = $policyId;
        $this->showCategoriesDrawer = true;
    }

    #[Computed]
    public function viewingPolicy()
    {
        if (!$this->selectedPolicyId) return null;
        return TransferPolicyModel::where('policy_id', $this->selectedPolicyId)->first();
    }

    #[Computed]
    public function categories()
    {
        $policy = $this->viewingPolicy();
        if (!$policy) return collect();

        $categories = $policy->categoriesQuery()
            ->with('officeLevel')
            ->orderBy('office_level_id')
            ->orderBy('transfer_category_name')
            ->get();

        if (!Auth::user()->hasRole('super admin')) {
            $userOfficeLevel = Auth::user()->workplace?->office_level_id;
            $categories = $categories->where('office_level_id', $userOfficeLevel);
        }

        return $categories;
    }

    public function deletePolicy($policyId)
    {
        $policy = TransferPolicyModel::where('policy_id', $policyId)->first();
        
        if (!$policy || !$this->checkAuthority($policy)) {
            session()->flash('error', __('Unauthorized action.'));
            return;
        }

        // Check if there are any applications using this policy
        $hasApplications = TeacherTransferApplication::where('policy_id', $policy->policy_id)->exists();
        
        if ($hasApplications) {
            session()->flash('error', __('Cannot delete policy because it is associated with existing transfer applications.'));
            return;
        }

        if (!$policy->is_locked) {
            $policy->delete();
            session()->flash('success', 'Policy deleted successfully.');
        } else {
            session()->flash('error', 'Cannot delete a locked policy.');
        }
    }

    protected function checkAuthority($policy)
    {
        if (Auth::user()->hasRole('super admin')) {
            return true;
        }

        return $policy->transfer_authority === Auth::user()->workplace_id;
    }

    public function render()
    {
        $query = TransferPolicyModel::with(['service', 'authority'])
            ->when(!Auth::user()->hasRole('super admin'), function ($q) {
                $q->where('transfer_authority', Auth::user()->workplace_id);
            })
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('circular_number', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->yearFilter, function ($q) {
                $q->where('policy_year', $this->yearFilter);
            })
            ->when($this->serviceFilter, function ($q) {
                $q->where('service_id', $this->serviceFilter);
            })
            ->orderBy('policy_year', 'desc')
            ->orderBy('created_at', 'desc');

        $serviceOptions = Service::active()->get();
        $yearOptions = TransferPolicyModel::select('policy_year')->distinct()->orderBy('policy_year', 'desc')->pluck('policy_year');

        return view('livewire.transfer-module.transfer-policy-list', [
            'policies' => $query->paginate(10),
            'serviceOptions' => $serviceOptions,
            'yearOptions' => $yearOptions,
        ]);
    }
}
