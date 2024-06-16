<?php

use App\Helpers\Helper;
use App\Models\Assessment;
use App\Models\Question;
use Livewire\Volt\Component;

new class extends Component {
    public $currentStep = 1, $totalSteps = 2;
    public $assessmentId;

    public int $assessment_id;
    public $assessment;
    public int $totalQuestions;
    public $questions;
    public $selectedAnswers = [];
    public $remainingTime;
    public $student;
    public bool $assessmentSubmitted = false, $assessmentStarted = false;

    protected $listeners = ['submitAssessment', 'saved' => '$refresh'];

    public function mount($id): void
    {
        $this->assessment_id = Crypt::decrypt($id);
        $this->assessment = Assessment::findOrFail($this->assessment_id);

        $this->student = auth()->user()->student;
//        $this->questions = $this->student->questions()
        $this->questions = Question::with(['answers'])
            ->where('assessment_id', $this->assessment_id)
            ->get();

        $this->totalQuestions = count($this->questions);
        $this->remainingTime = $this->totalQuestions * 60;
    }

    public function submitAssessment(): void
    {
        $totalMarks = 0;

        foreach ($this->selectedAnswers as $questionId => $selectedAnswerId) {
            // Retrieve the question from the database
            $question = Question::with('answers')->find($questionId);

            // Ensure the question exists
            if ($question) {
                $selectedAnswer = \App\Models\Answer::find($selectedAnswerId);

                if ($selectedAnswer && $selectedAnswer->question_id == $questionId) {
                    if ($selectedAnswer->status) {
                        $totalMarks += $question->marks;
                    }
                }
            }
        }

        //insert score
        \App\Models\AssessmentReport::updateOrCreate([
            'student_id' => auth()->user()->student->id,
            'assessment_id' => $this->assessment_id,
            'total_questions' => $this->totalQuestions,
            'total_marks' => $this->assessment->total_marks,
            'scored_marks' => $totalMarks
        ]);

        $this->dispatch('assessment_submitted', $totalMarks);

    }

    public function increaseStep(): void
    {
        $this->currentStep = Helper::increaseStep($this->currentStep, $this->totalSteps);
    }

    public function decreaseStep(): void
    {
        $this->currentStep = Helper::decreaseStep($this->currentStep);
    }

    public function getStepQuestions()
    {
        $questionsPerStep = ceil($this->totalQuestions / $this->totalSteps);
        $offset = ($this->currentStep - 1) * $questionsPerStep;
        return $this->questions->slice($offset, $questionsPerStep);
    }

    public function getCurrentStepAnswers(): array
    {
        $currentStepQuestions = $this->getStepQuestions();
        $currentStepAnswers = [];

        foreach ($currentStepQuestions as $question) {
            if (isset($this->selectedAnswers[$question->id])) {
                $currentStepAnswers[$question->id] = $this->selectedAnswers[$question->id];
            }
        }

        return $currentStepAnswers;
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="mb-2 text-lg font-semibold text-gray-900">Instructions.</h2>
        <ul class="max-w-full space-y-1 text-gray-500 list-inside">
            <li class="flex items-center">
                <svg class="w-3.5 h-3.5 me-2 text-red-500 flex-shrink-0" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                Do not refresh this page, to do so assessment will be submitted automatically
            </li>
            <li class="flex items-center">
                <svg class="w-3.5 h-3.5 me-2 text-red-500 flex-shrink-0" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                Go out this page 3 times will cause the assessment to be submitted automatically
            </li>
            <li class="flex items-center">
                <svg class="w-3.5 h-3.5 me-2 text-red-500 flex-shrink-0" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                After the given time, assessment to be submitted automatically
            </li>
        </ul>

        <h4 class="text-3xl font-bold my-4 text-gray-800 tracking-widest uppercase text-center"
            x-data="{ remainingTime: {{ $remainingTime }}, formatTime: formatTime, countdownTimer: null, startCountdown: function() { this.countdownTimer = setInterval(() => { if (this.remainingTime > 0) { this.remainingTime--; } else { clearInterval(this.countdownTimer); Livewire.dispatch('submitAssessment'); } }, 1000); } }"
        >
            TIME: <span x-text="formatTime(remainingTime)" x-init="startCountdown"></span>
        </h4>
    </x-slot>

    <div class="py-8">
        <div class="flex flex-col xl:flex-row space-y-8 xl:space-y-0 xl:space-x-8">
            <!-- left column -->
            <div class="w-full xl:w-1/6 h-full bg-white text-gray-800 rounded-lg p-8">
                <div class="flex justify-between mb-2">
                    <p class="text-primary font-bold text-lg">Attempts</p>
                </div>
                <ol class="space-y-4 w-full">
                    @if($currentStep === 1)
                        <li>
                            <div class="w-full p-4 text-blue-700 bg-blue-100 border border-blue-300 rounded-lg "
                                 role="alert">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-medium">{{ $currentStep }}. Attempt {{ $currentStep }}</h3>
                                    <svg class="rtl:rotate-180 w-4 h-4" aria-hidden="true"
                                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                    </svg>
                                </div>
                            </div>
                        </li>
                    @endif
                    @if($currentStep === 2)
                        <li>
                            <div class="w-full p-4 text-blue-700 bg-blue-100 border border-blue-300 rounded-lg "
                                 role="alert">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-medium">{{ $currentStep }}. Attempt {{ $currentStep }}</h3>
                                    <svg class="rtl:rotate-180 w-4 h-4" aria-hidden="true"
                                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                    </svg>
                                </div>
                            </div>
                        </li>
                    @endif
                </ol>
            </div>

            <!-- right column -->
            <div class="w-full xl:w-full bg-white rounded-lg p-4">
                <!-- STEP QUESTIONS -->
                @if($currentStep <= $totalSteps)
                    <ol class="space-y-4 text-gray-700 list-none list-inside">
                        @php $sno = 1 @endphp
                        @foreach($this->getStepQuestions() as $question)
                            <li class="mb-4">
                                <p class="font-black">
                                    {{ $question->description }} <sup>({{ $question->marks. ' Marks' }})</sup>
                                </p>

                                <ul class="w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg">
                                    @foreach($question->answers as $answer)
                                        <li class="w-full border-b border-gray-200 rounded-t-lg">
                                            <div class="flex items-center ps-3">
                                                <input id="list-radio-license-{{ $answer->id }}"
                                                       wire:model="selectedAnswers.{{ $question->id }}"
                                                       value="{{ $answer->id }}"
                                                       type="radio"
                                                       class="w-4 h-4 text-blue-600 bg-gray-300 border-gray-300 focus:ring-blue-500 focus:ring-2">
                                                <label for="list-radio-license-{{ $answer->id }}"
                                                       class="w-full py-3 ms-2 text-sm font-medium text-gray-700">
                                                    {{ $answer->description }}
                                                </label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ol>

                    <!-- HANDLE BUTTONS -->
                    <div class="flex items-end justify-end gap-1">
                        @if($currentStep > 1)
                            <div class="">
                                <x-buttons.secondary class="py-3 px-6 bg-gray-400" wire:click.prevent="decreaseStep">
                                    <x-icon name="arrow-left"/>
                                    <span>{{ __('BACK') }}</span>
                                </x-buttons.secondary>
                            </div>
                        @endif

                        @if($currentStep < $totalSteps)
                            <div class="">
                                <x-buttons.primary class="py-3 px-6" wire:click.prevent="increaseStep">
                                    <x-icon name="arrow-right"/>
                                    <span>{{ __('NEXT') }}</span>
                                </x-buttons.primary>
                            </div>
                        @endif

                        @if($currentStep === $totalSteps)
                            <x-buttons.success wire:click="submitAssessment" wire:loading.attr="disabled"
                                               class="ml-1 py-3 px-6">
                                <x-icon name="check-circle"/>
                                <span>{{ __('SUBMIT') }}</span>
                            </x-buttons.success>
                        @endif
                    </div>
                @else
                    <p>No Question(s) found!.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>

        function formatTime(time) {
            var minutes = Math.floor(time / 60);
            var seconds = time % 60;
            return ('0' + minutes).slice(-2) + ':' + ('0' + seconds).slice(-2);
        }

        //auto submit if user switch tabs more than 3 attempts
        // let attempts = 0;
        //
        // // Detect tab switch or window blur event
        // window.addEventListener('blur', function() {
        //     attempts++;
        //     if (attempts > 3) {
        //         Livewire.dispatch('submitAssessment');
        //     }
        // });

        // document.addEventListener('DOMContentLoaded', function () {
        //     Livewire.dispatch('submitAssessment');
        // })

        Livewire.on('assessment_submitted', function (totalMarks) {

            swal({
                title: "Success!",
                text: "Submitted successfully. Total Marks: " + totalMarks,
                icon: "success",
                button: "OK",
                dangerMode: false,
                closeOnClickOutside: false,
                closeOnEsc: false,
            })
                .then((willDelete) => {
                    if (willDelete) {
                        window.location.href = "{{ route('dashboard') }}";
                    }
                });

        });

    </script>
@endpush



