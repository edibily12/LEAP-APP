<?php

use Livewire\Volt\Component;

new class extends Component {
    public $assessment;
    public $assessment_id;
    public bool $assessmentSubmitted = false;

    public function mount($id): void
    {
        $this->assessment_id = Crypt::decrypt($id);
        $this->assessment = \App\Models\Assessment::findOrFail($this->assessment_id);

        //check if user submitted the assessment
        $this->assessmentSubmitted = \App\Models\AssessmentReport::where('username', auth()->user()->username)
            ->where('assessment_id', $this->assessment_id)
            ->exists();
    }

}; ?>

<div>
    <div class="h-full bg-gray-200">
        <div class="bg-white p-4 rounded-lg shadow-xl py-8 mt-12">
            @if($assessmentSubmitted === false)
                <h4 class="text-4xl font-bold text-gray-800 tracking-widest uppercase text-center">
                    READ INSTRUCTIONS
                </h4>
                <p class="text-justify text-gray-700 text-lg mt-2">
                    {{ $assessment->instructions }}
                </p>

                <a wire:navigate href="{{ route('assessments.start', encrypt($assessment->id)) }}">
                    <x-buttons.primary class="mx-auto mt-2">
                        {{__('START NOW')}}
                    </x-buttons.primary>
                </a>
            @else
                <p class="text-justify text-gray-700 text-lg mt-2">
                    You have no any attempt.!
                </p>
            @endif

        </div>
    </div>
</div>
