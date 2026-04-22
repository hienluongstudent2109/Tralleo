<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TaskService;

class TaskController extends Controller
{
    public function store(Request $request, TaskService $service)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'column_id' => 'required|exists:columns,id',
            'title' => 'required|string',
        ]);

        $task = $service->create($data, $request->user());

        return response()->json($task);
    }
}
