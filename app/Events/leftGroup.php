<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class leftGroup implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $groupToken;
    /**
     * Create a new event instance.
     */
    public function __construct($userId, $groupToken)
    {
        $this->userId = $userId;
        $this->groupToken = $groupToken;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('Group.' . $this->groupToken),
        ];
    }

    public function broadcastWith()
    {
        return [
            'user' => User::where('id', $this->userId)->first(),
        ];
    }

    public function broadcastAs()
    {
        return 'leftGroup';
    }
}
