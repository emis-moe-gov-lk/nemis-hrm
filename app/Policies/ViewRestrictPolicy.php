<?php

namespace App\Policies;

use App\Models\User;
use App\Models\People;

class ViewRestrictPolicy
{
    public function viewRestrict(User $user, People $people): bool
    {
        // 1. A user can always safely view their own profile
        if ($user->people_id === $people->people_id) {
            return true;
        }

        // 2. To strictly prevent IDOR lateral scaling, ordinary users cannot view OTHER profiles.
        // They must hold the explicit administrative permission to view lateral/child profiles.
        if (!$user->hasPermissionTo('teacher.profile.general.view')) {
            return false;
        }

        // 3. Prevent 500 crashes if they input an ID that has no valid appointment.
        if (!$people->currentAppointment || !$people->currentAppointment->workplace_id) {
            return false;
        }

        // 4. Finally, verify the target is strictly within the user's jurisdictional boundary.
        if (!$user->workplace) {
            return false;
        }

        $allowedWorkplaceIds = $user->workplace->getAllChildWorkplaces();

        return in_array($people->currentAppointment->workplace_id, $allowedWorkplaceIds);
    }
}
