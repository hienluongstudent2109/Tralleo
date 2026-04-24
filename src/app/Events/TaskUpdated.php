<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $task;

    public function __construct($task)
    {
        $this->task = $task->load('project');
    }

    public function broadcastOn()
    {
        return new PrivateChannel(
            'workspace.' . $this->task->project->workspace_id
        );
    }

    public function broadcastAs()
    {
        return 'task.updated';
    }
}
