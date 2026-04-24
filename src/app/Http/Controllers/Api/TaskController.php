<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Services\TaskService;

class TaskController extends Controller
{
    public function store(Request $request, TaskService $service)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'column_id' => 'required|exists:columns,id',
            'title' => 'required|string'
        ]);

        $task = Task::create([
            ...$data,
            'created_by' => $request->user()->id
        ]);

        event(new \App\Events\TaskCreated($task));

        return response()->json($task);
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $data = $request->validate([
            'column_id' => 'required|exists:columns,id'
        ]);

        $task->update([
            'column_id' => $data['column_id']
        ]);

        event(new \App\Events\TaskUpdated($task));

        return response()->json($task);
    }
}
