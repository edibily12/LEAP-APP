<?php

use Livewire\Volt\Component;

new class extends Component {
    use \App\Traits\WithModal;

    public $assessment;
    public bool $isShuffled = false;

    public $students;

    #[\Livewire\Attributes\Rule('required|numeric|between:1,50')]
    public int $questionsPerStudent = 5;

    public function mount(\App\Models\Assessment $assessment): void
    {
        $this->assessment = $assessment;
        $this->isShuffled = $assessment->shuffled;

        $this->students = \App\Models\Student::count();
    }

    public function with(): array
    {
        return [
            'totalQuestions' => \App\Models\Question::where('assessment_id', $this->assessment->id)->count(),
        ];
    }

    public function shuffleQuestions(): void
    {
        if ($this->assessment->shuffled) {
            $this->dispatch('shuffle-error');
            $this->resetExcept('assessment');
            return;
        }

        $questions = \App\Models\Question::where('assessment_id', $this->assessment->id)->get()
            ->shuffle();
        $students = \App\Models\Student::get();
        $questionPerStudent = $this->questionsPerStudent;

        foreach ($students as $student) {
            $studentQns = $questions->take($questionPerStudent);
            foreach ($studentQns as $qn) {
//                $student->questions()->syncWithoutDetaching($qn, ['assignment_id' => $this->assessment->id]);
                $student->questions()->attach($qn, ['assignment_id' => $this->assessment->id]);
            }

            $questions = $questions->merge($studentQns)->shuffle()->unique();
        }

        \App\Events\ShuffleQuestionsEvent::dispatch($this->assessment);

        $this->dispatch('shuffle-success');
        $this->resetExcept('assessment');
    }

}; ?>

<div>
    <x-buttons.success class="font-black sm:py-3" wire:click="openDialogModal">
        <x-icon name="share" class="mr-1"/>
        {{ __('SHUFFLE QNs') }}
    </x-buttons.success>

    {{--     modal  --}}
    <x-dialog-modal maxWidth="2xl" wire:model="openModal">
        <x-slot name="title">
            {{ __('SHUFFLE QUESTIONS') }}
        </x-slot>
        <x-slot name="content">
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col my-4">
                    <h2>Students:</h2>
                </div>

                <div class="flex flex-col my-4">
                    <h2>{{ $students }}</h2>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col my-4">
                    <h2>Questions:</h2>
                </div>

                <div class="flex flex-col my-4">
                    <h2>{{ $totalQuestions }}</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <div class="flex flex-col my-4">
                    <x-label for="password" value="{{ __('Total Questions Per Student') }}"/>
                    <x-input
                            type="number"
                            id="title"
                            placeholder="Example 10"
                            wire:model="questionsPerStudent"
                    />
                    <x-input-error for="questionsPerStudent"/>
                </div>
            </div>

        </x-slot>
        <x-slot name="footer">
            <div class="flex gap-2">
                <x-buttons.secondary wire:click="$toggle('openModal')">
                    <x-icon name="x-mark" class="mr-1"/>
                    {{ __('CANCEL') }}
                </x-buttons.secondary>

                <x-buttons.success wire:click="shuffleQuestions" wire:loading.attr="disabled">
                    <x-icon name="check-circle" class="mr-1"/>
                    {{ __('SHUFFLE') }}
                </x-buttons.success>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

@push('scripts')
    <script>

        Livewire.on('shuffle-success', function (e) {
            swal('Good Job', 'All questions are shared to all students', 'success')
        })

        Livewire.on('shuffle-error', function (e) {
            swal('Error', 'All questions for this assessment are already shared to all students', 'error')
        })
    </script>
@endpush
