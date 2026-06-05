<?php

namespace App\Events;

use App\Models\Task;
use App\Models\TaskMessageReaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskMessageReactionAdded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public TaskMessageReaction $reaction;
    public Task $task;

    public function __construct(TaskMessageReaction $reaction, Task $task)
    {
        $this->reaction = $reaction;
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
        return 'reaction.added';
    }

    public function broadcastWith(): array
    {
        return [
            'task_message_id' => $this->reaction->task_message_id,
            'user_id' => $this->reaction->user_id,
            'emoji' => $this->reaction->emoji,
        ];
    }
}
