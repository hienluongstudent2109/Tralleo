<?php

namespace App\Http\Middleware;

use App\Services\PermissionService;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle($request, Closure $next, $permission)
    {
        $user = $request->user();
        $workspaceId = $request->route('workspace_id');

        if (!app(PermissionService::class)
            ->hasPermission($user, $workspaceId, $permission)) {
            throw new AuthorizationException('You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
