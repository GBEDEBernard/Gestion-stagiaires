<?php

namespace App\Events;

use App\Models\Task;
use App\Models\TaskMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskMessageCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public TaskMessage $message;
    public Task $task;

    public function __construct(TaskMessage $message, Task $task)
    {
        $this->message = $message;
        $this->task = $task;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("task.{$this->task->id}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'task_id' => $this->task->id,
            'user_id' => $this->message->user_id,
            'body' => $this->message->body,
            'type' => $this->message->type,
            'parent_id' => $this->message->parent_id,
            'created_at' => $this->message->created_at,
        ];
    }
}
