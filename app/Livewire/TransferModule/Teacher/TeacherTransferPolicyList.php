<?php

namespace App\Livewire\TransferModule\Teacher;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Models\TeacherTransferPolicy as TeacherTransferPolicyModel;
use App\Models\Service;
use App\Models\TeacherTransferApplication;
use App\Support\Transfer\TransferAccess;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Flux;

class TeacherTransferPolicyList extends Component
{
    use WithPagination;

    public $search = '';
    public $yearFilter = '';
    public $confirmingPolicyId = null;
    public $confirmingAction = null;
    public $adminPassword = '';
    public $showPasswordModal = false;
    public $selectedPolicyId = null;
    public $showCategoriesDrawer = false;

    protected $updatesQueryString = [
        'search' => ['except' => ''],
        'yearFilter' => ['except' => ''],
    ];

    public function mount()
    {
        abort_unless(TransferAccess::canViewPolicies(Auth::user()), 403);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleStatus($policyId)
    {
        $policy = TeacherTransferPolicyModel::where('policy_id', $policyId)->first();
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
        $policy = TeacherTransferPolicyModel::where('policy_id', $policyId)->first();
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
        $policy = TeacherTransferPolicyModel::where('policy_id', $policyId)->first();
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
        return TeacherTransferPolicyModel::where('policy_id', $this->selectedPolicyId)->first();
    }

    #[Computed]
    public function categories()
    {
        $policy = $this->viewingPolicy();
        if (!$policy) return collect();

        $categories = $policy->categoriesQuery()
            ->with(['officeLevel', 'transferSubCategory'])
            ->orderBy('office_level_id')
            ->orderBy('transfer_category_name')
            ->get();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->hasRole('super admin') && !TransferAccess::isSltsEmployee($user)) {
            $userOfficeLevel = $user->workplace?->office_level_id;
            $categories = $categories->where('office_level_id', $userOfficeLevel);
        }

        return $categories;
    }

    public function deletePolicy($policyId)
    {
        $policy = TeacherTransferPolicyModel::where('policy_id', $policyId)->first();
        
        if (!$policy || !$this->checkAuthority($policy)) {
            session()->flash('error', __('Unauthorized action.'));
            return;
        }

        /** @var TeacherTransferPolicyModel $policy */
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

    protected function checkAuthority(TeacherTransferPolicyModel $policy): bool
    {
        return TransferAccess::canManagePolicy(Auth::user(), $policy);
    }

    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = TeacherTransferPolicyModel::with(['authority'])
            ->when(!$user->hasRole('super admin'), function ($q) use ($user) {
                if (TransferAccess::isSltsEmployee($user)) {
                    // Teachers see active policies within their parent authority hierarchy
                    $q->active();
                    if ($user->workplace) {
                        $q->whereIn('transfer_authority', $user->workplace->getAllParentWorkplaces());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                } else {
                    // Admins see policies where their workplace is the transfer authority
                    $q->where('transfer_authority', $user->workplace_id);
                }
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
            ->orderBy('policy_year', 'desc')
            ->orderBy('created_at', 'desc');

        $yearOptions = TeacherTransferPolicyModel::select('policy_year')->distinct()->orderBy('policy_year', 'desc')->pluck('policy_year');

        return view('livewire.transfer-module.teacher.teacher-transfer-policy-list', [
            'policies' => $query->paginate(10),
            'yearOptions' => $yearOptions,
        ]);
    }
}
