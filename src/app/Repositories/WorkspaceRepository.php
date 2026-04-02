<?php

namespace App\Repositories;

use App\Enums\WorkspaceRole;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class WorkspaceRepository
{
    public function listForUser(User $user): Collection
    {
        return $user->workspaces()
            ->orderBy('workspaces.name')
            ->get();
    }

    public function createOwnedBy(User $owner, string $name): Workspace
    {
        return DB::transaction(function () use ($owner, $name) {
            $workspace = Workspace::query()->create([
                'name' => $name,
                'owner_id' => $owner->id,
            ]);

            $workspace->users()->attach($owner->id, [
                'role' => WorkspaceRole::Owner->value,
            ]);

            return $workspace;
        });
    }

    public function userIsMember(Workspace $workspace, User $user): bool
    {
        return $workspace->users()->where('users.id', $user->id)->exists();
    }

    public function attachMember(Workspace $workspace, User $member, string $role): void
    {
        $workspace->users()->attach($member->id, [
            'role' => $role,
        ]);
    }
}
