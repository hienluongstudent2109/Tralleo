<?php

namespace App\Services;

use App\Enums\WorkspaceRole;
use App\Models\User;
use App\Models\Workspace;
use App\Repositories\WorkspaceRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

class WorkspaceService
{
    public function __construct(
        private readonly WorkspaceRepository $workspaces,
    ) {}

    public function listFor(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return $this->workspaces->listForUser($user);
    }

    public function create(User $owner, string $name): Workspace
    {
        return $this->workspaces->createOwnedBy($owner, $name);
    }

    public function addMember(User $actor, Workspace $workspace, string $email, string $role): void
    {
        if (! in_array($role, WorkspaceRole::assignable(), true)) {
            throw ValidationException::withMessages([
                'role' => [__('Invalid role.')],
            ]);
        }

        if ($role === WorkspaceRole::Admin->value && $workspace->owner_id !== $actor->id) {
            throw new AuthorizationException(__('Only the workspace owner can assign the admin role.'));
        }

        $member = User::query()->where('email', $email)->first();
        if ($member === null) {
            throw ValidationException::withMessages([
                'email' => [__('No user found with this email.')],
            ]);
        }

        if ($this->workspaces->userIsMember($workspace, $member)) {
            throw ValidationException::withMessages([
                'email' => [__('This user is already a member of the workspace.')],
            ]);
        }

        $this->workspaces->attachMember($workspace, $member, $role);
    }
}
