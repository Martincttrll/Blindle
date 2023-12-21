<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class throwRandomSong implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $groupToken;
    public $song;

    /**
     * Create a new event instance.
     */
    public function __construct($groupToken, $song)
    {
        $this->groupToken = $groupToken;
        $this->song = $song;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('Group.' . $this->groupToken),
        ];
    }

    public function broadcastWith()
    {
        return [$this->song];
    }

    public function broadcastAs()
    {
        return 'throwRandomSong';
    }
}
