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
    public $nbManche;

    /**
     * Create a new event instance.
     */
    public function __construct($groupToken, $nbManche)
    {
        $this->groupToken = $groupToken;
        $this->nbManche = $nbManche;
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
            "groupToken" => $this->groupToken,
            "nbManche" => $this->nbManche
        ];
    }

    public function broadcastAs()
    {
        return 'startGame';
    }
}
