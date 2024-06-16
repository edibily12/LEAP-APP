<?php

namespace App\Jobs;

use App\Models\Assessment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ActivateAssessment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Assessment $assessment)
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $assessment = $this->assessment;
        $assessment->status = 1;
        $assessment->save();

        //fire an event
        \App\Events\ActivateAssessment::dispatch($assessment);
    }
}
