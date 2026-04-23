<?php

namespace App\Events;

use App\Models\Leave;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Queue\SerializesModels;

class LeaveRejected
{
    use Dispatchable, SerializesModels;

    public function __construct(public Leave $leave)
    {
    }

    /**
     * Get the channels the event should broadcast on
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
