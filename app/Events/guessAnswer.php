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

class guessAnswer
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $groupToken;
    public $returnArray;

    /**
     * Create a new event instance.
     */
    public function __construct($userId, $groupToken, $returnArray)
    {
        $this->userId = $userId;
        $this->groupToken = $groupToken;
        $this->returnArray = $returnArray;
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
            'datas' => $this->returnArray
        ];
    }

    public function broadcastAs()
    {
        return 'guessAnswer';
    }
}
