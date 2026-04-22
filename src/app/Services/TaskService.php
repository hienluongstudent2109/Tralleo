<?php

namespace App\Services;

use App\Models\Task;
use App\Events\TaskCreated;

class TaskService
{
    public function create(array $data, $user)
    {
        $task = Task::create([
            ...$data,
            'created_by' => $user->id
        ]);

        event(new TaskCreated($task));

        return $task;
    }
}
