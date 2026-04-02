<?php

namespace App\Policies;

use App\Enums\WorkspaceRole;
use App\Models\User;
use App\Models\Workspace;

class WorkspacePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function view(User $user, Workspace $workspace): bool
    {
        return $workspace->roleFor($user) !== null;
    }

    public function addMember(User $user, Workspace $workspace): bool
    {
        $role = $workspace->roleFor($user);

        return $role !== null && in_array($role, [
            WorkspaceRole::Owner->value,
            WorkspaceRole::Admin->value,
        ], true);
    }
}
