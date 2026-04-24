<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProjectService;
use App\Models\Project;

class ProjectController extends Controller
{
    protected $service;

    public function __construct(ProjectService $service)
    {
        $this->service = $service;
    }

    // GET /projects?workspace_id=
    public function index(Request $request)
    {
        $workspaceId = $request->query('workspace_id');

        return $this->service->listByWorkspace($workspaceId);
    }

    // POST /projects
    public function store(Request $request)
    {
        $data = $request->validate([
            'workspace_id' => 'required|exists:workspaces,id',
            'name' => 'required|string',
            'description' => 'nullable|string'
        ]);

        $project = $this->service->create($data, $request->user());

        return response()->json($project);
    }

    // GET /projects/{id}
    public function show($id)
    {
        return Project::findOrFail($id);
    }

    // DELETE /projects/{id}
    public function destroy($id)
    {
        $project = Project::findOrFail($id);

        $project->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
