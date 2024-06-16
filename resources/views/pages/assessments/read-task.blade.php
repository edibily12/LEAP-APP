<?php

use App\Enums\ReadingType;
use App\Helpers\Helper;
use App\Models\Assessment;
use Livewire\Volt\Component;

new class extends Component {
    public $currentStep = 1, $totalSteps = 2;
    public $assessmentId, $assessment;

    public function mount($id): void
    {
        auth()->user()->isStudent() ? '' : abort(403, 'Not Authorized to Access This Page');
        $this->assessmentId = Crypt::decrypt($id);
        $this->currentStep = 1;
        $this->assessment = Assessment::findOrFail($this->assessmentId);
    }

    //increase step
    public function increaseStep(): void
    {
        $this->currentStep = Helper::increaseStep($this->currentStep, $this->totalSteps);
    }

    public function decreaseStep(): void
    {
        $this->currentStep = Helper::decreaseStep($this->currentStep);
    }


}; ?>

<div>
    @use('\Illuminate\Support\Facades\Storage')
    <x-slot name="header">
        <p class="text-gray-500">
            Track work across the enterprise through an open, collaborative platform. Link issues across Jira and ingest
            data from other software development tools, so your IT support and operations teams have richer contextual
            information to rapidly respond to requests, incidents, and changes.
        </p>
    </x-slot>

    <div class="flex flex-col py-12 xl:flex-row space-y-8 xl:space-y-0 xl:space-x-8">
        <!-- right column -->
        <div class="w-full xl:w-1/6 h-full bg-white text-gray-800 rounded-lg p-8">
            <div class="flex justify-between mb-2">
                <p class="text-primary font-bold text-lg">Status</p>
            </div>
            <ol class="space-y-4 w-full">
                <li>
                    <div class="w-full p-4 text-blue-700 bg-blue-100 border border-blue-300 rounded-lg "
                         role="alert">
                        <div class="flex items-center justify-between">
                            <h3 class="font-medium">{{ $assessment->reading_type }}</h3>
                            <svg class="rtl:rotate-180 w-4 h-4" aria-hidden="true"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                            </svg>
                        </div>
                    </div>
                </li>
            </ol>
        </div>

        <!-- right column -->
        <div class="w-full xl:w-full bg-white rounded-lg p-4">

            @if($assessment->reading_type === ReadingType::MEDIA->value)
                <video class="w-full h-auto max-w-full" controls>
                    <source src="{{ Storage::url($assessment->source) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            @else
                <p class="mb-3 text-gray-700">
                    Track work across the enterprise through an open, collaborative platform. Link issues across Jira and ingest data from other software development tools, so your IT support and operations teams have richer contextual information to rapidly respond to requests, incidents, and changes.
                </p>
                <p class="text-gray-700">
                    Deliver great service experiences fast - without the complexity of traditional ITSM solutions.Accelerate critical development work, eliminate toil, and deploy changes with ease, with a complete audit trail for every change.
                </p>

            @endif

            <hr class="my-2">

            <!-- HANDLE BUTTONS -->
            <div class="flex mt-4 items-end justify-end gap-1">
                <div class="">
                    <a class="" wire:navigate href="{{ route('assessments.answer', encrypt($assessmentId)) }}">
                        <x-buttons.success wire:loading.attr="disabled" class="ml-1 py-3 px-6">
                            <x-icon name="check-circle"/>
                            <span>{{ __('ACCEPT') }}</span>
                        </x-buttons.success>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
