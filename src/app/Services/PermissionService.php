<?php

namespace App\Services;

class PermissionService
{
    public function hasPermission($user, $workspaceId, $permission)
    {
        return \DB::table('workspace_user_roles as wur')
            ->join('role_permissions as rp', 'wur.role_id', '=', 'rp.role_id')
            ->join('permissions as p', 'rp.permission_id', '=', 'p.id')
            ->where('wur.user_id', $user->id)
            ->where('wur.workspace_id', $workspaceId)
            ->where('p.name', $permission)
            ->exists();
    }
}
