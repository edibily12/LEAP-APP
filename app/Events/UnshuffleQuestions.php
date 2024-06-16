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

class UnshuffleQuestions implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Assessment $assessment)
    {
        DB::table('question_student')
            ->where('assignment_id', $assessment->id)
            ->delete();

        $assessment->update([
            'shuffled' => false
        ]);
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('un-shuffle-questions'),
        ];
    }
}
