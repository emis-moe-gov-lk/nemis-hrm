<?php

namespace App\Livewire\TransferModule\Teacher;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TeacherTransferApplication;
use App\Models\TeacherTransferScoreRouteDistance;
use App\Models\TeacherTransferPolicy;
use App\Support\Transfer\TransferAccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TeacherTransferRequestExport;
use Throwable;

class TeacherTransferRequest extends Component
{
    use WithPagination;

    public $filterPolicy = '';
    public $filterCategory = '';
    public $filterSubCategory = '';
    public $filterZone = '';
    public $perPage = 10;
    public $deleteApplicationId = null;
    public $deleteApplicationLabel = '';
    public $deletePassword = '';
    public $showDeleteModal = false;

    public function updatedFilterPolicy()
    {
        $this->filterCategory = '';
        $this->filterSubCategory = '';
        $this->resetPage();
    }

    public function updatedFilterSubCategory()
    {
        $this->filterCategory = '';

        $this->resetPage();
    }

    public function updatedFilterZone()
    {
        $this->resetPage();
    }

    public function updatedPerPage($value): void
    {
        $this->perPage = in_array((int) $value, [10, 25, 50, 100], true) ? (int) $value : 10;
        $this->resetPage();
    }

    public function exportExcel()
    {
        return Excel::download(
            new TeacherTransferRequestExport($this->filterPolicy, $this->filterCategory, $this->filterSubCategory, $this->filterZone),
            'Transfer-Requests-' . date('Y-m-d') . '.xlsx'
        );
    }

    public function confirmDeleteApplication(string $transferApplicationId): void
    {
        if (!$this->canDeleteRequests()) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'You are not allowed to delete transfer requests.']);

            return;
        }

        $application = $this->applicationsQuery()
            ->where('transfer_application_id', $transferApplicationId)
            ->first();

        if (!$application) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Transfer request not found or outside your scope.']);

            return;
        }

        $this->deleteApplicationId = $application->transfer_application_id;
        $this->deleteApplicationLabel = $application->transfer_application_id;
        $this->deletePassword = '';
        $this->resetErrorBag('deletePassword');
        $this->showDeleteModal = true;
    }

    public function verifyPasswordAndDelete(): void
    {
        $this->validate([
            'deletePassword' => ['required'],
        ], [
            'deletePassword.required' => __('Please enter your current password.'),
        ]);

        $user = Auth::user();

        if (!$user || !Hash::check($this->deletePassword, $user->password)) {
            $this->addError('deletePassword', __('The password does not match your current password.'));

            return;
        }

        if (!$this->deleteApplicationId) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'No transfer request selected for deletion.']);
            $this->resetDeleteConfirmation();

            return;
        }

        $application = $this->applicationsQuery()
            ->where('transfer_application_id', $this->deleteApplicationId)
            ->first();

        if (!$application) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Transfer request not found or outside your scope.']);
            $this->resetDeleteConfirmation();

            return;
        }

        try {
            DB::transaction(function () use ($application) {
                $application->preferences()->delete();
                $application->achievements()->delete();
                $application->recommendations()->delete();
                $application->boardRecommendations()->delete();
                $application->appeals()->delete();

                TeacherTransferScoreRouteDistance::where(
                    'transfer_application_id',
                    $application->transfer_application_id
                )->delete();

                $application->delete();
            });

            $this->resetPage();
            $this->resetDeleteConfirmation();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Transfer request deleted successfully.']);
        } catch (Throwable $e) {
            report($e);

            $this->dispatch('notify', ['type' => 'error', 'message' => 'Unable to delete this transfer request.']);
        }
    }

    public function cancelDeleteConfirmation(): void
    {
        $this->resetDeleteConfirmation();
    }

    public function render()
    {
        $policies = TransferAccess::applyPolicyViewScope(
            TeacherTransferPolicy::active(),
            Auth::user()
        )
            ->orderByDesc('policy_year')
            ->get();

        if ($this->filterPolicy && !$policies->contains('policy_id', $this->filterPolicy)) {
            $this->filterPolicy = '';
            $this->filterCategory = '';
            $this->filterSubCategory = '';
        }

        $transferSubCategories = \App\Models\TeacherTransferSubCategory::active()
            ->orderBy('display_order')
            ->get();

        $transferCategories = \App\Models\TeacherTransferCategory::scopedListQuery(
            $this->filterPolicy,
            null,
            null,
            $this->filterSubCategory ?: null
        )->with('transferSubCategory')->get();

        $zones = \App\Models\ZonalEducationOffice::active()->get();
        if (Auth::check()) {
            $applications = $this->applicationsQuery()
                ->with(['policy', 'targetProvince', 'reason', 'employee.title', 'category.transferSubCategory', 'transferSubCategory'])
                ->orderByDesc('created_at')
                ->paginate($this->perPage);
        } else {
            $applications = TeacherTransferApplication::whereIn('id', [])->paginate($this->perPage);
        }

        return view('livewire.transfer-module.teacher.teacher-transfer-request', [
            'applications' => $applications,
            'policies' => $policies,
            'transferSubCategories' => $transferSubCategories,
            'transferCategories' => $transferCategories,
            'zones' => $zones,
            'canDeleteRequests' => $this->canDeleteRequests(),
        ]);
    }

    public function statusBadge($status)
    {
        return match ($status) {
            'draft' => ['color' => 'slate', 'label' => 'Draft'],
            'submitted' => ['color' => 'indigo', 'label' => 'Submitted'],
            'processing' => ['color' => 'amber', 'label' => 'Processing'],
            'approved' => ['color' => 'emerald', 'label' => 'Approved'],
            'rejected' => ['color' => 'rose', 'label' => 'Not Recomended'],
            default => ['color' => 'slate', 'label' => ucfirst($status)],
        };
    }

    private function applicationsQuery()
    {
        $user = Auth::user();
        $query = TeacherTransferApplication::query();

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        // If not super admin, filter by workplace hierarchy
        if (!$user->hasRole('super admin')) {
            $workplace = $user->workplace;
            if ($workplace) {
                $allowedWorkplaceIds = $workplace->getAllChildWorkplaces();
                $query->whereIn('current_workplace', $allowedWorkplaceIds);
            } else {
                // Fallback if no workplace is found
                $query->where('employee_id', $user->people_id);
            }
        }

        if ($this->filterPolicy) {
            $query->where('policy_id', $this->filterPolicy);
        }

        if ($this->filterCategory) {
            $query->where('transfer_category', $this->filterCategory);
        }

        if ($this->filterSubCategory) {
            $query->where('transfer_sub_category_id', $this->filterSubCategory);
        }

        if ($this->filterZone) {
            $query->where(function ($q) {
                $q->where('current_workplace', $this->filterZone)
                    ->orWhereHas('currentWorkplace.institution', function ($instQ) {
                        $instQ->where('zeo_wp_id', $this->filterZone);
                    });
            });
        }

        return $query;
    }

    private function canDeleteRequests(): bool
    {
        return TransferAccess::canManagePolicies(Auth::user());
    }

    private function resetDeleteConfirmation(): void
    {
        $this->showDeleteModal = false;
        $this->deleteApplicationId = null;
        $this->deleteApplicationLabel = '';
        $this->deletePassword = '';
        $this->resetErrorBag('deletePassword');
    }
}
