<?php

namespace App\Services;

use App\Models\Column;

class BoardService
{
    public function getBoard($projectId)
    {
        return Column::with(['tasks' => function ($q) {
            $q->orderBy('created_at');
        }])
        ->where('project_id', $projectId)
        ->orderBy('position')
        ->get();
    }
}
