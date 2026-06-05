<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskMessageRead implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Task $task;
    public int $userId;
    public int $messageId;
    public string $readAt;

    public function __construct(Task $task, int $userId, int $messageId, string $readAt)
    {
        $this->task = $task;
        $this->userId = $userId;
        $this->messageId = $messageId;
        $this->readAt = $readAt;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("task.{$this->task->id}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.read';
    }

    public function broadcastWith(): array
    {
        return [
            'task_id' => $this->task->id,
            'user_id' => $this->userId,
            'last_read_message_id' => $this->messageId,
            'read_at' => $this->readAt,
        ];
    }
}
