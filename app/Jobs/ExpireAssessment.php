<?php

namespace App\Jobs;

use App\Models\Assessment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExpireAssessment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Assessment $assessment)
    {}

    public function handle(): void
    {
        $assessment = $this->assessment;
        $assessment->status = 2;
        $assessment->save();

        //dispatch expire event
        \App\Events\ExpireAssessment::dispatch($assessment);
    }
}
