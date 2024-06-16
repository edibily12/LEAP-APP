<?php

use App\Models\Answer;
use App\Models\Question;
use Livewire\Volt\Component;

new class extends Component {
    use \Livewire\WithPagination;

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
        $this->assessment = \App\Models\Assessment::findOrFail($this->assessment_id);

        $this->student = auth()->user()->student;
        $this->questions = $this->student->questions()
            ->with(['answers'])
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
                $selectedAnswer = Answer::find($selectedAnswerId);

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

}; ?>

<div>
    <style>
        .swal-overlay {
            background-color: rgba(43, 165, 137, 0.45);
        }
    </style>

    <div class="h-full bg-gray-200">
        <div class="bg-white p-4 rounded-lg shadow-xl py-8 mt-12">
            <h4 class="text-3xl font-bold my-4 text-gray-800 tracking-widest uppercase text-center"
                x-data="{ remainingTime: {{ $remainingTime }}, formatTime: formatTime, countdownTimer: null, startCountdown: function() { this.countdownTimer = setInterval(() => { if (this.remainingTime > 0) { this.remainingTime--; } else { clearInterval(this.countdownTimer); Livewire.dispatch('submitAssessment'); } }, 1000); } }"
            >
                TIME: <span x-text="formatTime(remainingTime)" x-init="startCountdown"></span>
            </h4>

            @if(count($questions) > 0)
                @php $sno = 1 @endphp
                @foreach($questions as $question)
                    <div
                            x-data="{ accordion1: false, accordion2: false, accordion3: false, accordion4: false, accordion5: false, accordion6: false}"
                            class="px-2 xl:px-16 mt-1 space-y-4"
                    >
                        <div class="px-4">
                            <button
                                    @click="
                                        accordion1 = !accordion1,
                                        accordion2 = false,
                                        accordion3 = false,
                                        accordion4 = false,
                                        accordion5 = false,
                                        accordion6 = false
                                        "
                                    class="w-full flex items-center text-left py-2 hover:text-primary"
                                    :class="accordion1 ? 'text-primary' : ''"
                            >
                                <span>{{ $sno++ }}.</span>
                                <span class="ml-2 text-lg" :class="accordion1 ? 'text-primary' : 'text-gray-800'">
                                        {{ $question->description }}
                                    <sup>({{ $question->marks. ' Marks' }})</sup>
                                </span>
                            </button>
                            <div x-show="accordion1" x-collapse.duration.500ms="" style="overflow: hidden; height: 0px;">
                                <div class="py-2 pl-8 text-gray-500">
                                    <div class="flex-1">
                                        <ul class="mt-2 text-gray-700">
                                            @foreach($question->answers as $answer)
                                                <li class="flex border-y py-2">
                                                        <span class="font-bold w-24">
                                                            <input
                                                                    wire:model="selectedAnswers.{{ $question->id }}"
                                                                    value="{{ $answer->id }}"
                                                                    type="radio"
                                                                    class="p-2 mx-1.5">
                                                        </span>
                                                    <span class="text-gray-700">
                                                        {{ $answer->description }}
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <hr class="my-2">
                <x-buttons.success wire:click="submitAssessment" wire:loading.attr="disabled" class="py-4 mx-4 xl:mx-20">
                    <x-icon name="check-circle" class="mr-1"/>
                    {{ __('SUBMIT') }}
                </x-buttons.success>
            @else
                <p>No Question(s) found!.</p>
            @endif
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
        let attempts = 0;

        // Detect tab switch or window blur event
        window.addEventListener('blur', function() {
            attempts++;
            if (attempts > 3) {
                Livewire.dispatch('submitAssessment');
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            Livewire.dispatch('submitAssessment');
        })

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



