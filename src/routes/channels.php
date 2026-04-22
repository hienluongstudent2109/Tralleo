<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('workspace.{workspaceId}', function ($user, $workspaceId) {
    return \DB::table('workspace_users')
        ->where('workspace_id', $workspaceId)
        ->where('user_id', $user->id)
        ->exists();
});
