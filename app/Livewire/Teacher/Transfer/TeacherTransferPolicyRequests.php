<?php

namespace App\Livewire\Teacher\Transfer;

use App\Models\TeacherTransferApplication;
use App\Models\TeacherTransferPolicy;
use App\Support\Transfer\TransferAccess;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TeacherTransferPolicyRequests extends Component
{
    public TeacherTransferPolicy $policy;

    public function mount(string $policyId): void
    {
        abort_unless(TransferAccess::canViewTeacherSelfService(Auth::user()), 403);

        $this->policy = TeacherTransferPolicy::with(['authority', 'steps.officeLevel'])
            ->where('policy_id', $policyId)
            ->firstOrFail();

        abort_unless(
            TransferAccess::canViewPolicy(Auth::user(), $this->policy),
            403,
            'You are not allowed to view this transfer policy.'
        );
    }

    public function render()
    {
        $applications = $this->applicationsQuery()
            ->with(['policy', 'category.transferSubCategory', 'transferSubCategory'])
            ->orderByDesc('created_at')
            ->get();

        $editableApplication = $applications->first(fn (TeacherTransferApplication $application) => $application->is_editable);
        $latestApplication = $applications->first();

        return view('livewire.teacher.transfer.teacher-transfer-policy-requests', [
            'applications' => $applications,
            'editableApplication' => $editableApplication,
            'latestApplication' => $latestApplication,
            'canStartNewApplication' => $this->canStartNewApplication($applications),
            'isApplicationWindowOpen' => $this->isApplicationWindowOpen(),
        ]);
    }

    public function statusBadge(string $status): array
    {
        return match ($status) {
            'draft' => ['color' => 'slate', 'label' => 'Draft', 'bg' => 'bg-slate-50 dark:bg-slate-500/10', 'text' => 'text-slate-600', 'border' => 'border-slate-200 dark:border-slate-500/20', 'ring' => 'ring-slate-500/5'],
            'submitted' => ['color' => 'indigo', 'label' => 'Submitted', 'bg' => 'bg-indigo-50 dark:bg-indigo-500/10', 'text' => 'text-indigo-600', 'border' => 'border-indigo-200 dark:border-indigo-500/20', 'ring' => 'ring-indigo-500/5'],
            'processing' => ['color' => 'amber', 'label' => 'Processing', 'bg' => 'bg-amber-50 dark:bg-amber-500/10', 'text' => 'text-amber-600', 'border' => 'border-amber-200 dark:border-amber-500/20', 'ring' => 'ring-amber-500/5'],
            'approved' => ['color' => 'emerald', 'label' => 'Approved', 'bg' => 'bg-emerald-50 dark:bg-emerald-500/10', 'text' => 'text-emerald-600', 'border' => 'border-emerald-200 dark:border-emerald-500/20', 'ring' => 'ring-emerald-500/5'],
            'rejected' => ['color' => 'rose', 'label' => 'Not Recomended', 'bg' => 'bg-rose-50 dark:bg-rose-500/10', 'text' => 'text-rose-600', 'border' => 'border-rose-200 dark:border-rose-500/20', 'ring' => 'ring-rose-500/5'],
            default => ['color' => 'slate', 'label' => ucfirst($status), 'bg' => 'bg-slate-50 dark:bg-slate-500/10', 'text' => 'text-slate-600', 'border' => 'border-slate-200 dark:border-slate-500/20', 'ring' => 'ring-slate-500/5'],
        };
    }

    public function deleteApplication(string $transferApplicationId): void
    {
        $application = $this->applicationsQuery()
            ->where('transfer_application_id', $transferApplicationId)
            ->where('status', 'draft')
            ->first();

        if (!$application) {
            return;
        }

        $application->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Draft application deleted successfully.']);
    }

    private function applicationsQuery()
    {
        return TeacherTransferApplication::query()
            ->where('employee_id', Auth::user()->people_id)
            ->where('policy_id', $this->policy->policy_id);
    }

    private function canStartNewApplication(Collection $applications): bool
    {
        if (!$this->isApplicationWindowOpen()) {
            return false;
        }

        return !$applications->contains(
            fn (TeacherTransferApplication $application) => in_array($application->status, ['draft', 'submitted', 'processing', 'approved'], true)
        );
    }

    private function isApplicationWindowOpen(): bool
    {
        return TransferAccess::canStartPolicyApplication(Auth::user(), $this->policy);
    }
}
