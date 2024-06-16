<?php

use App\Enums\AssessmentType;
use App\Enums\ReadingType;
use App\Helpers\Helper;
use App\Jobs\ActivateAssessment;
use App\Jobs\ExpireAssessment;
use App\Models\Assessment;
use App\Traits\WithModal;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithModal, WithFileUploads;

    public int $formId;

    public $type, $tag;
    public $expire_date, $start_date;
    public $start_time, $expire_time;
    public string $instructions = '';
    public $reading_type, $source;
    public $total_questions = 5, $total_marks = 10;

    public function mount(): void
    {
        $this->formId = random_int(1, 100);
    }

    public function updatedSource(): void
    {
        $this->validate([
            'source' => 'file|max:100240',
        ]);
    }

    private function validateData(): void
    {
        if ($this->type === AssessmentType::IDENTIFICATION->value) {
            $this->validate([
                'type' => ['required'],
                'total_questions' => ['required'],
                'total_marks' => ['required'],
            ]);
        } elseif ($this->type === AssessmentType::SUPPORT->value) {
            $this->validate([
                'type' => ['required'],
                'start_date' => ['required', 'date'],
                'start_time' => ['required'],
                'expire_date' => ['required', 'date'],
                'expire_time' => ['required'],
            ]);
        } else {
            $this->validate([
                'type' => ['required'],
            ]);
        }
    }

    public function save(): void
    {
        $this->validateData();
        $source = '';
        $mimeType = '';
        if (is_file($this->source)){
            $mimeType = $this->source->getMimeType();
        }

        if (Str::startsWith($mimeType, 'video/')) {
            $source = $this->source->store('public/files/videos');
        } elseif (Str::startsWith($mimeType, 'audio/')) {
            $source = $this->source->store('public/files/audios');
        }else{
            $source = $this->source;
        }

        $assessment = Assessment::create([
            'type' => $this->type,
            'tag' => $this->tag,
            'reading_type' => $this->reading_type,
            'source' => $source,
            'total_questions' => $this->total_questions,
            'total_marks' => $this->total_marks,
            'start_date' => $this->start_date ?? null,
            'start_time' => $this->start_time ?? null,
            'expire_date' => $this->expire_date ?? null,
            'expire_time' => $this->expire_time ?? null,
            'instructions' => $this->instructions ?? null,
            'created_by' => auth()->user()->username,
        ]);
        $this->reset('source');

        if ($this->type === AssessmentType::SUPPORT->value) {
            $activeDelay = Helper::calculateDelaySeconds($assessment->start_date, $assessment->start_time);
            $expireDelay = Helper::calculateDelaySeconds($assessment->expire_date, $assessment->expire_time);

            dispatch(new ActivateAssessment($assessment))->delay(now()->addRealSeconds($activeDelay));
            dispatch(new ExpireAssessment($assessment))->delay(now()->addRealSeconds($expireDelay));

        }

        $this->dispatch('assessment-created', Str::title($this->type));
        $this->dispatch('saved');
        $this->resetExcept('formId');
    }


}; ?>

<div>

    <x-buttons.primary wire:click="openDialogModal">
        <x-icon name="plus" class="mr-2"/>
        {{__('ADD ASSESSMENT')}}
    </x-buttons.primary>

    {{--     modal  --}}
    <x-dialog-modal maxWidth="5xl" wire:model="openModal">
        <x-slot name="title">
            {{ __('CREATE NEW ASSESSMENT') }}
        </x-slot>
        <x-slot name="content">
            <form wire:submit="save" id="add-{{ $this->formId }}" x-data="{type: '', reading_type: ''}">
                <div class="grid grid-cols-1 gap-4">
                    <div class="flex flex-col my-4">
                        <x-label for="password" value="{{ __('Assessment Type') }}"/>
                        <select class="block mt-1 w-full capitalize border-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm py-2"
                                wire:model="type"
                                x-model="type"
                        >
                            <option value="">--Select Type--</option>
                            <option value="identification">IDENTIFICATION</option>
                            <option value="support">SUPPORT</option>
                        </select>
                        <x-input-error for="type"/>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4" x-show="type == 'identification' || type == 'support'">
                    <div class="flex flex-col my-4">
                        <x-label for="password" value="{{ __('Tag: optional') }}"/>
                        <x-input
                                type="text"
                                placeholder="Assessment tag"
                                wire:model="tag"
                        />
                        <x-input-error for="tag"/>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4" x-show="type == 'identification' || type == 'support'">
                    <div class="flex flex-col my-4">
                        <x-label for="password" value="{{ __('Total Questions') }}"/>
                        <x-input
                                type="number"
                                placeholder="Example 10"
                                wire:model="total_questions"
                        />
                        <x-input-error for="total_questions"/>
                    </div>

                    <div class="flex flex-col my-4">
                        <x-label for="password" value="{{ __('Total Marks') }}"/>
                        <x-input
                                type="number"
                                placeholder="Example 20"
                                wire:model="total_marks"
                        />
                        <x-input-error for="total_marks"/>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4" x-show="type == 'identification' || type == 'support'">
                    <div class="flex flex-col my-4">
                        <x-label for="password" value="{{ __('Reading Type') }}"/>
                        <select class="block mt-1 w-full capitalize border-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm py-2"
                                wire:model="reading_type"
                                x-model="reading_type"
                        >
                            <option value="">--Select Type--</option>
                            <option value="media">Media</option>
                            <option value="paragraph">Paragraph</option>
                        </select>
                        <x-input-error for="reading_type"/>
                    </div>
                </div>

                <div class="grid grid-cols-1" x-show="reading_type == 'paragraph'">
                    <div class="flex flex-col my-4">
                        <x-label for="password" value="{{ __('Paragraph(optional)') }}"/>
                        <textarea
                                rows="10"
                                placeholder="Write instructions..."
                                wire:model="source"
                        ></textarea>
                        <x-input-error for="source"/>
                    </div>
                </div>

                <div class="grid grid-cols-1" x-show="reading_type == 'media'">
                    <div class="flex flex-col my-4">
                        <x-label for="password" value="{{ __('Choose file') }}"/>
                        <x-input
                                type="file"
                                wire:model="source"
                                class="py-2"
                        />
                        <x-input-error for="source"/>
                    </div>
                </div>


                <div class="grid grid-cols-2 gap-4" x-show="type == 'support'">
                    <div class="flex flex-col my-4">
                        <x-label value="{{ __('Start Date') }}"/>
                        <x-input
                                type="date"
                                wire:model="start_date"
                        />
                        <x-input-error for="start_date"/>
                    </div>

                    <div class="flex flex-col my-4">
                        <x-label value="{{ __('Start Time(in 24 Hrs)') }}"/>
                        <x-input
                                type="time"
                                wire:model="start_time"
                        />
                        <x-input-error for="start_time"/>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4" x-show="type == 'support'">
                    <div class="flex flex-col my-4">
                        <x-label value="{{ __('Expire Date') }}"/>
                        <x-input
                                type="date"
                                wire:model="expire_date"
                        />
                        <x-input-error for="expire_date"/>
                    </div>

                    <div class="flex flex-col my-4">
                        <x-label value="{{ __('Expire Time(in 24 Hrs)') }}"/>
                        <x-input
                                type="time"
                                wire:model="expire_time"
                        />
                        <x-input-error for="expire_time"/>
                    </div>
                </div>

                <div class="grid grid-cols-1" x-show="type == 'identification' || type == 'support'">
                    <div class="flex flex-col my-4">
                        <x-label for="password" value="{{ __('Instructions(optional)') }}"/>
                        <textarea
                                rows="10"
                                placeholder="Write instructions..."
                                wire:model="instructions"
                        ></textarea>
                        <x-input-error for="instructions"/>
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <div class="flex gap-2">
                <x-buttons.secondary wire:click="$toggle('openModal')">
                    <x-icon name="x-mark" class="mr-1"/>
                    {{ __('CANCEL') }}
                </x-buttons.secondary>

                <x-buttons.success wire:target="save" wire:loading.attr="disabled" form="add-{{ $this->formId }}">
                    <x-icon name="check-circle" class="mr-1"/>
                    {{ __('SAVE') }}
                </x-buttons.success>
            </div>
        </x-slot>
    </x-dialog-modal>

</div>

@push('scripts')
    <script>
        Livewire.on('assessment-created', function (type) {
            swal({
                title: "Successfully",
                text: "Assessment created successfully.\nType: " + type,
                icon: "success",
                button: "OK"
            });
        })
    </script>
@endpush