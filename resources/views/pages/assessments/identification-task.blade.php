<?php

use App\Models\Answer;
use App\Models\Question;
use Livewire\Volt\Component;
use \App\Models\Assessment;
use \App\Enums\AssessmentType;
use \App\Models\IdentificationAttempt;
use \App\Models\Student;

new class extends Component {

    protected $listeners = ['submitAssessment', 'saved' => '$refresh'];

    public int $assessment_id;
    public $assessment;
    public int $totalQuestions;
    public $questions;
    public $selectedAnswers = [];
    public $remainingTime;
    public int $student_id;

    public function mount($id): void
    {
        auth()->user()->isStudent() ? '' : abort(419);
        $this->assessment_id = Crypt::decrypt($id);
        $this->assessment = \App\Models\Assessment::findOrFail($this->assessment_id);

        $this->student_id = auth()->user()->student->id;
        $this->questions = Question::with(['answers'])
            ->where('assessment_id', $this->assessment_id)
            ->inRandomOrder()
            ->get();

        $this->totalQuestions = count($this->questions);

        $this->remainingTime = ($this->totalQuestions * 60)/2;
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
        \App\Models\IdentificationAttempt::create([
            'student_id' => $this->student_id,
            'assessment_id' => $this->assessment_id,
            'total_questions' => $this->totalQuestions,
            'total_marks' => $this->assessment->total_marks,
            'scored_marks' => $totalMarks
        ]);

        //update level of student
        $total_assessment = Assessment::where('type', AssessmentType::IDENTIFICATION->value)->count();

        $attempts = IdentificationAttempt::where('student_id', $this->student_id)->get();

        $total_marks = $attempts->sum('total_marks');
        $totalScoredMarks = $attempts->sum('scored_marks');
        $average = (50/100)*$total_marks;

        if ($attempts->count() === $total_assessment * 3 && $totalScoredMarks < $average) {
            Student::findOrFail($this->student_id)->update([
                'level' => \App\Enums\StudentLevel::LOWER->value
            ]);
        }

        $this->dispatch('assessment_submitted', $totalMarks);

    }

}; ?>

<div>
    <h4 class="text-3xl font-bold mb-6 text-gray-800 tracking-widest uppercase text-center"
        x-data="{ remainingTime: {{ $remainingTime }}, formatTime: formatTime, countdownTimer: null, startCountdown: function() { this.countdownTimer = setInterval(() => { if (this.remainingTime > 0) { this.remainingTime--; } else { clearInterval(this.countdownTimer); Livewire.dispatch('submitAssessment'); } }, 1000); } }"
    >
        TIME: <span x-text="formatTime(remainingTime)" x-init="startCountdown"></span>
    </h4>

    <form wire:submit="submitAssessment">
        <div class="w-full xl:w-full grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-4">
            @if($questions->count() > 0)
                @foreach($questions as $question)
                    <div class="flex flex-col items-center justify-center space-y-3">
                        <div>
                            @if($question->file_type === 'image')
                                <img class="w-28" src="{{ Storage::url($question->file) }}" alt="Icon Name">
                            @elseif($question->file_type === 'video')
                                <video width="320" height="240" controls>
                                    <source src="{{ Storage::url($question->file) }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            @endif
                        </div>
                        <div class="flex flex-col items-center justify-center space-y-1">
                            <p>{{ $question->description }}</p>
                            <div class="flex items-center space-x-1">
                                <select class="w-full border py-0.5"
                                        wire:model="selectedAnswers.{{ $question->id }}">
                                    <option value="">--Select One--</option>
                                    @foreach($question->answers as $answer)
                                        <option value="{{ $answer->id }}">{{ $answer->description }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p>No record(s) found</p>
            @endif


        </div>
        <hr>

        <x-buttons.success class="mx-auto my-6 px-8">
            <x-icon name="check-circle" class="mr-2"/>
            {{ __('SUBMIT') }}
        </x-buttons.success>
    </form>
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
        window.addEventListener('blur', function () {
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