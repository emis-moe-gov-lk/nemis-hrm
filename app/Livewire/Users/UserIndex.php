<?php

namespace App\Livewire\Users;

use Exception;
use App\Models\User;
use App\Models\Workplaces;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use App\Helpers\NicHelper;

class UserIndex extends Component
{
    use WithPagination;

    public $search = '';
    protected $updatesQueryString = ['search'];

    public function render()
    {
        $loggedUser = Auth::user();
        $users = User::query()->with(['roles', 'currentAppointment']);

        try {

            if ($loggedUser->hasRole('super admin')) {

                // SUPER ADMIN sees everything
                // No filtering needed

            } else {

                // Load logged user's appointment
                $loggedAppointment = $loggedUser->currentAppointment;

                if (!$loggedAppointment) {
                    // No appointment means user only sees themselves
                    $users = $users->where('id', $loggedUser->id);
                } else {

                    // Get user's workplace
                    $loggedWorkplace = Workplaces::where(
                        'workplace_id',
                        $loggedAppointment->workplace_id
                    )->first();

                    if (!$loggedWorkplace) {
                        $users = $users->where('id', $loggedUser->id);
                    } else {

                        // Get ALL workplaces under user's hierarchy (FAST BFS)
                        $allowedWorkplaceIds = $loggedWorkplace->getAllChildWorkplaces();

                        // Filter users by allowed workplaces
                        $users = $users->whereHas('currentAppointment', function ($q) use ($allowedWorkplaceIds) {
                            $q->whereIn('workplace_id', $allowedWorkplaceIds);
                        });
                    }
                }
            }

            // =========================
            // SEARCH (Optimized)
            // =========================
            if (!empty($this->search)) {

                $search = trim($this->search);

                // NIC search handling
                $isNic = NicHelper::isValid($search);

                if ($isNic) {
                    $normalizedNic = NicHelper::normalize($search);
                    $hashSearch = NicHelper::hash($normalizedNic);

                    $users->where('nic_hash', $hashSearch);
                }

                // Email + contact search (string search)
                $users->orWhere(function ($query) use ($search) {
                    $query->where('email', 'like', "%{$search}%")
                        ->orWhere('contact', 'like', "%{$search}%");
                });
            }


            // Final pagination
            $users = $users->paginate(20);
        } catch (Exception $e) {

            Log::error('UserIndex render error: ' . $e->getMessage());

            // Prevent UI crash
            $users = User::where('id', 0)->paginate(10);
        }

        return view('livewire.users.user-index', compact('users'));
    }

    // =========================
    // Delete user
    // =========================
    // public function deleteUser($userId)
    // {
    //     $user = User::findOrFail($userId);
    //     $user->delete();

    //     $this->dispatch('status-updated', [
    //         'message' => 'User deleted successfully!',
    //     ]);
    // }

    // =========================
    // Toggle status
    // =========================
    public function toggleStatus($userId)
    {
        $user = User::find($userId);

        if ($user) {
            $user->active_status = $user->active_status ? 0 : 1;
            $user->save();

            $this->dispatch('status-updated', [
                'message' => $user->active_status
                    ? 'User activated successfully!'
                    : 'User deactivated successfully!',
            ]);
        }
    }

    // =========================
    // Reset Password
    // =========================
    public function resetPassword($userId)
    {
        try {
            $user = User::findOrFail($userId);

            $newPassword = 'User@' . rand(1000, 9999);
            $user->password = Hash::make($newPassword);
            $user->save();

            Mail::to($user->email)->send(new ResetPasswordMail($newPassword));

            session()->flash('success', "Password reset successfully! PW: {$newPassword}");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'User not found.');
        } catch (Exception $e) {
            session()->flash('error', 'Something went wrong.');
        }
    }
}
