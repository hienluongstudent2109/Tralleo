<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Column;

class ProjectService
{
    public function listByWorkspace($workspaceId)
    {
        return Project::where('workspace_id', $workspaceId)
            ->latest()
            ->get();
    }

    public function create($data, $user)
    {
        $project = Project::create([
            ...$data,
            'created_by' => $user->id
        ]);

        // tạo default columns (quan trọng)
        $this->createDefaultColumns($project);

        return $project;
    }

    private function createDefaultColumns($project)
    {
        $columns = ['TODO', 'DOING', 'DONE'];

        foreach ($columns as $index => $name) {
            Column::create([
                'project_id' => $project->id,
                'name' => $name,
                'position' => $index
            ]);
        }
    }
}
