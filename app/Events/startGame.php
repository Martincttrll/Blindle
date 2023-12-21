<?php

namespace App\Events;

use App\Http\Controllers\GameController;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class startGame implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $groupToken;

    /**
     * Create a new event instance.
     */
    public function __construct($groupToken)
    {
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
            "groupToken" => $this->groupToken
        ];
    }

    public function broadcastAs()
    {
        return 'startGame';
    }
}
