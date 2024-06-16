<?php

namespace App\Events;

use App\Models\Assessment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Array_;

class ShuffleQuestionsEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public bool $shuffled;

    public function __construct(Assessment $assessment)
    {
        $assessment->shuffled = true;
        $assessment->save();

        $this->shuffled = $assessment->shuffled;

    }


    public function broadcastOn(): Channel
    {
        return new Channel('shuffle-question');;
    }
}
