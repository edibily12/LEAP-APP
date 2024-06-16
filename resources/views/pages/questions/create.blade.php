<?php

use App\Traits\WithModal;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;

new class extends Component {
    use WithModal, \Livewire\WithFileUploads;

    public int $formId, $assessment_id;
    public string $assessment_type;

    public $answers = [];
    public $correctAnswerIndex;

    #[Rule('required|min:3|max:255')]
    public string $question;

    #[Rule('required|numeric|between:1,5')]
    public int $marks;

    #[Rule('nullable|max:50480|mimetypes:image/jpeg,image/png,video/mp4,video/quicktime,audio/mp3,audio/wav')]
    public $file, $file_type;

    public function mount($assessment_id): void
    {
        $this->formId = random_int(1, 100);
        $this->$assessment_id = $assessment_id;
        $this->assessment_type = \App\Models\Assessment::findOrFail($assessment_id)->type;

        $this->answers = [
            ['answer' => '', 'correct' => null]
        ];
    }

    public function updatedFile($value): void
    {
        if ($value) {
            $this->file_type = strstr($value->getMimeType(), '/', true);
        } else {
            $this->file_type = null;
        }
    }

    public function addAnswer(): void
    {
        $this->answers[] = ['answer' => '', 'correct' => null];
    }

    public function validateAnswer($index): void
    {
        $this->validateOnly("answers.{$index}.answer", [
            "answers.{$index}.answer" => 'required|min:2|max:255',
        ]);

        $this->resetErrorBag('correctAnswerIndex');
    }

    public function save(): void
    {
        $this->validate([
            'question' => 'required|min:3|max:255',
            'file' => 'nullable|max:20480|mimetypes:image/jpeg,image/png,video/mp4,video/quicktime',
            'marks' => 'required|numeric|between:1,5',
            'answers.*.answer' => 'required|min:2|max:255',
        ]);

        if ($this->correctAnswerIndex == null) {
            $this->dispatch('qn_error');
            return;
        }

        $file_path = '';
        if ($this->file_type === 'image') {
            $file_path = $this->file ? $this->file->store('public/images/icons') : '';
        }


        if ($this->file_type === 'video') {
            $file_path = $this->file ? $this->file->store('public/files/images') : '';
        }

        if ($this->file_type === 'audio') {
            $file_path = $this->file ? $this->file->store('public/files/audios') : '';
        }

        $qn = \App\Models\Question::create([
            'assessment_id' => $this->assessment_id,
            'description' => $this->question,
            'marks' => $this->marks,
            'file' => $file_path ?? null,
            'file_type' => $this->file_type ?? null
        ]);

        foreach ($this->answers as $index => $item) {
            $qn->answers()->create([
                'description' => $item['answer'],
                'status' => $index == $this->correctAnswerIndex ? 1 : 0,
            ]);
        }

        $this->dispatch('saved');
        $this->resetExcept('assessment_id');
        $this->mount($this->assessment_id);
        $this->dispatch('qn-added');

    }
}; ?>

<div>
    @php
        $disabled = $errors->any() || empty($this->question) || empty($this->marks) ? true : false;
    @endphp

    <x-buttons.primary class="font-black sm:py-3" wire:click="openDialogModal">
        <x-icon name="plus"/>
        {{__('ADD QUESTION')}}
    </x-buttons.primary>

    {{--     modal  --}}
    <x-dialog-modal maxWidth="5xl" wire:model="openModal">
        <x-slot name="title">
            {{ __('ADD NEW QUESTION') }}
        </x-slot>
        <x-slot name="content">
            <form wire:submit="save" id="add-{{ $this->formId }}">
                <div class="grid grid-cols-1">
                    <div class="flex flex-col my-4">
                        <x-label for="password" value="{{ __('Question') }}"/>
                        <x-input
                                type="text"
                                class="w-full"
                                placeholder="Enter question..."
                                wire:model.blur="question"
                        />
                        <x-input-error for="question"/>
                    </div>
                </div>

                <div class="grid grid-cols-1">
                    <div class="flex flex-col my-4">
                        <x-label for="password" value="{{ __('Marks') }}"/>
                        <x-input
                                type="number"
                                class="w-full"
                                placeholder="Enter Marks..."
                                wire:model.blur="marks"
                        />
                        <x-input-error for="marks"/>
                    </div>
                </div>

                @if($assessment_type === \App\Enums\AssessmentType::IDENTIFICATION->value)
                    <div class="grid grid-cols-1">
                        <div class="flex flex-col my-4">
                            <x-label for="file" value="{{ __('Upload File') }}"/>
                            <input
                                   class="block py-2 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none placeholder-gray-400"
                                   aria-describedby="file_input_help" id="file_input"
                                   type="file" wire:model.blur="file">
                            <p class="mt-1 text-sm text-gray-600">PNG, JPG (MAX. 1MB).</p>

                            <x-input-error for="file"/>
                        </div>

                        @if ($file && $file_type === 'image')
                            <img class="w-32" src="{{ $file->temporaryUrl() }}">
                        @elseif ($file && $file_type === 'video')
                            <video width="320" height="240" controls>
                                <source src="{{ $file->temporaryUrl() }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @elseif ($file && $file_type === 'audio')
                            <audio controls>
                                <source src="{{ $file->temporaryUrl() }}" type="{{ $file->getMimeType() }}">
                                Your browser does not support the audio tag.
                            </audio>
                        @endif
                    </div>
                @endif

                <div class="grid grid-cols-1">
                    <table class="w-full my-4 text-sm text-center rtl:text-right text-gray-500">
                        <thead class="text-xs text-gray-300 uppercase bg-gray-700">
                        <tr>
                            <th scope="col" class="px-1">
                                #
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Answer
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Correct?
                            </th>
                        </tr>
                        </thead>

                        <tbody>
                        @php $sno = 'A' @endphp
                        @foreach($answers as $index => $answer)
                            <tr class="bg-white border-b border-gray-600">
                                <td class="w-4">
                                    {{ $sno++ }}
                                </td>
                                <td class="px-6 py-2 w-[100%] font-medium text-gray-900 whitespace-nowrap ">
                                    <x-input
                                            type="text"
                                            class="w-full mx-8"
                                            placeholder="Enter answer..."
                                            wire:model.live="answers.{{$index}}.answer"
                                            wire:change="validateAnswer({{ $index }})"
                                    />
                                    <x-input-error for="answers.{{$index+1}}.answer"/>
                                </td>
                                <td class="px-8">
                                    <div class="flex">
                                        <input type="radio"
                                               wire:model="correctAnswerIndex"
                                               value="{{ $index }}"
                                               class="w-6 h-6 text-blue-600 bg-gray-100 border-gray-300 rounded-full focus:ring-blue-500 focus:ring-2 mx-auto">
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <div class="flex gap-2">
                <x-buttons.danger wire:click="$toggle('openModal')">
                    <x-icon name="x-mark" class="mr-1"/>
                    {{ __('CANCEL') }}
                </x-buttons.danger>

                @if(is_array($answers) && count($answers) < 4)
                    <x-buttons.secondary wire:click="addAnswer">
                        <x-icon name="plus" class="mr-1"/>
                        {{ __('ADD ANSWER') }}
                    </x-buttons.secondary>
                @endif

                @if((count($answers) == 2 || count($answers) == 4))
                    <x-buttons.success wire:target="save" wire:loading.attr="disabled" :disabled="$disabled"
                                       form="add-{{ $this->formId }}">
                        <x-icon name="check-circle" class="mr-1"/>
                        {{ __('SAVE') }}
                    </x-buttons.success>
                @endif

            </div>
        </x-slot>
    </x-dialog-modal>
</div>

@push('scripts')
    <script>
        Livewire.on('qn_error', function (e) {
            swal("Wrong", "Please select the correct answer before saving the question.", "error")
        })

        Livewire.on('qn-added', function (e) {
            swal("Good Job", "Question has been successfully saved!", "success")
        })
    </script>
@endpush
