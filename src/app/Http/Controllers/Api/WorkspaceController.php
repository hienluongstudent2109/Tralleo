<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AddWorkspaceMemberRequest;
use App\Http\Requests\Api\StoreWorkspaceRequest;
use App\Http\Resources\WorkspaceResource;
use App\Models\Workspace;
use App\Services\WorkspaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WorkspaceController extends Controller
{
    public function __construct(
        private readonly WorkspaceService $workspaces,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Workspace::class);

        return WorkspaceResource::collection(
            $this->workspaces->listFor($request->user()),
        );
    }

    public function store(StoreWorkspaceRequest $request): JsonResponse
    {
        $this->authorize('create', Workspace::class);

        $workspace = $this->workspaces->create(
            $request->user(),
            $request->validated('name'),
        );

        $withPivot = $request->user()
            ->workspaces()
            ->where('workspaces.id', $workspace->id)
            ->first();

        return response()->json([
            'data' => new WorkspaceResource($withPivot),
        ], 201);
    }

    public function addMember(AddWorkspaceMemberRequest $request, Workspace $workspace): JsonResponse
    {
        $this->authorize('addMember', $workspace);

        $this->workspaces->addMember(
            $request->user(),
            $workspace,
            $request->validated('email'),
            $request->validated('role'),
        );

        return response()->json(null, 204);
    }
}
