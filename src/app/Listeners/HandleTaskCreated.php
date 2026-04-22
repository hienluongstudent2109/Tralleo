<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use App\Models\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleTaskCreated implements ShouldQueue
{
    public function handle(TaskCreated $event)
    {
        $task = $event->task;

        ActivityLog::create([
            'workspace_id' => $task->project->workspace_id,
            'user_id' => $task->created_by,
            'action' => 'task_created',
            'entity_type' => 'task',
            'entity_id' => $task->id,
            'metadata' => json_encode([
                'title' => $task->title
            ])
        ]);
    }
}
