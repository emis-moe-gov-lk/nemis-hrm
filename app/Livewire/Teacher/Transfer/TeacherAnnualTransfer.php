<?php

namespace App\Livewire\Teacher\Transfer;

use Livewire\Component;
use App\Models\TeacherTransferApplication;
use App\Models\TeacherTransferPolicy;
use App\Support\Transfer\TransferAccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;


class TeacherAnnualTransfer extends Component
{
    public function mount(): void
    {
        abort_unless(TransferAccess::canViewTeacherSelfService(Auth::user()) && Gate::allows('transfer.annual.view'), 403);
    }

    public function render()
    {
        $applications = TeacherTransferApplication::with([
            'policy',
            'category.transferSubCategory',
            'transferSubCategory',
        ])
            ->where('employee_id', Auth::user()->people_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $currentAnnualPolicy = TransferAccess::applyPolicyViewScope(
            TeacherTransferPolicy::active(),
            Auth::user()
        )
            ->where('transfer_type', 'annual')
            ->orderByDesc('policy_year')
            ->first();

        return view('livewire.teacher.transfer.teacher-annual-transfer', [
            'applications' => $applications,
            'currentCycleYear' => $currentAnnualPolicy?->policy_year ?? now()->year,
        ]);
    }

    public function statusBadge($status)
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

    public function deleteApplication($id)
    {
        $application = TeacherTransferApplication::where('transfer_application_id', $id)
            ->where('employee_id', Auth::user()->people_id)
            ->where('status', 'draft') // Safety check: only drafts can be deleted
            ->first();

        if ($application) {
            $application->delete();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Application deleted successfully.']);
        }
    }
}
