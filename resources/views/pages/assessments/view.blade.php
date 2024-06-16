<?php

use App\Models\Question;
use Livewire\Volt\Component;

new class extends Component {
    use \Livewire\WithPagination;
    use \App\Traits\WithFilter;

    public int $assessment_id;
    public \App\Models\Assessment $assessment;
    public bool $isShuffled;

    protected $listeners = ['saved' => '$refresh'];

    public function mount($id): void
    {
        auth()->user()->isTeacher() ? '' : abort(419);
        $this->assessment_id = Crypt::decrypt($id);

        $this->assessment = \App\Models\Assessment::findOrFail($this->assessment_id);
        $this->isShuffled = $this->assessment->shuffled;
    }


    public function with(): array
    {
        return [
            'questions' => Question::with(['answers'])
                ->where('assessment_id', $this->assessment_id)
                ->orderByDesc('created_at')
                ->paginate($this->perPage)
        ];
    }

    public function deleteQuestion($id): void
    {
        $question = Question::findOrFail($id);
        $question->delete();
        $this->dispatch('question-deleted');
    }

    public function unShuffleQuestions(\App\Models\Assessment $assessment): void
    {
        \App\Events\UnshuffleQuestions::dispatch($assessment);
        $this->dispatch('unshuffled');
        $this->dispatch('saved');
    }

    #[\Livewire\Attributes\On('echo:un-shuffle-questions,UnshuffleQuestions')]
    public function listenUnShuffledEvent($data): void
    {
        $id = encrypt($data['assessment']['id']);
        $this->mount($id);
    }

    #[\Livewire\Attributes\On('echo:shuffle-question,ShuffleQuestionsEvent')]
    public function listenShuffledEvent($data): void
    {
        $this->isShuffled = $data['shuffled'];
    }

}; ?>

<div>
    @use('\Illuminate\Support\Facades\Storage')
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            <h2 class="font-semibold capitalize text-xl text-gray-800 leading-tight">
                Questions for: {{ $assessment->type. ' assessment.' }}
            </h2>
        </h2>
    </x-slot>

    <div class="h-full bg-gray-200">
        <div class="bg-white p-4 rounded-lg shadow-xl py-8 mt-12">

            <div class="flex justify-between">
                @if(!$isShuffled)
                    <livewire:questions.create :assessment_id="$assessment_id"/>
                    @if($assessment->questions()->count() >0)
                        <livewire:questions.shuffle-questions :assessment="$assessment_id"/>
                    @endif
                @else
                    <x-buttons.secondary wire:click="unShuffleQuestions({{ $assessment->id }})">
                        {{__('UN SHUFFLE')}}
                    </x-buttons.secondary>
                @endif
            </div>

            <div class="space-y-12 px-2 xl:px-16 mt-12">
                @if(count($questions) > 0)
                    @php $sno = 1 @endphp
                    @foreach($questions as $question)
                        <div class="flex justify-between">
                            <div class="mt-1 flex">
                                <div>
                                    <div class="flex items-center h-16 border-l-4 border-primary">
                                        <span class="text-4xl text-primary px-4">Q.</span>
                                    </div>
                                    <div class="flex items-center h-16 border-l-4 border-gray-400">
                                        <span class="text-4xl text-gray-400 px-4">A.</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex items-center h-16">
                                    <span class="text-lg text-primary font-bold">
                                        {{ $question->description }} <sup>({{ $question->marks. ' Marks' }})</sup>
                                    </span>
                                    </div>
                                    <div class="flex items-center py-2">
                                        <div class="flex-1">
                                            <ul class="mt-2 text-gray-700">
                                                @foreach($question->answers as $answer)
                                                    <li class="flex border-y py-2">
                                                <span class="font-bold w-24">
                                                    <input {{ $answer->status ? 'checked' : 'disabled' }}  type="radio"
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
                            @if($assessment->type === \App\Enums\AssessmentType::IDENTIFICATION->value)
                                <div>
                                    @if($question->file_type === 'image')
                                        <img class="h-32" src="{{ Storage::url($question->file) }}">
                                    @elseif($question->file_type === 'video')
                                        <video width="320" height="240" controls>
                                            <source src="{{ Storage::url($question->file) }}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <a href="#" wire:click="deleteQuestion({{$question->id}})"
                           wire:confirm.prompt="Are you sure? type YES|YES" class="text-red-600 font-bold">Delete
                            Question</a>
                    @endforeach
                @else
                    <p>No Question(s) found!.</p>
                @endif
                <div class="mt-12">
                    {{ $questions->links(data: ['scrollTo' => false]) }}
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
    <script>
        Livewire.on('question-deleted', function () {
            swal({
                title: "Success!",
                text: "Question deleted successfully. ",
                icon: "success",
                button: "OK",
            })
        });

        Livewire.on('unshuffled', function () {
            swal({
                title: "Success!",
                text: "Question unshuffled successfully. ",
                icon: "success",
                button: "OK",
            })
        });
    </script>
@endpush


