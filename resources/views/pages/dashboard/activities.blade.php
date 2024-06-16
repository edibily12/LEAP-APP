<?php

use App\Enums\AssessmentType;
use App\Enums\StudentLevel;
use App\Models\Assessment;
use App\Models\Question;
use App\Traits\WithFilter;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
    use \Livewire\WithPagination;
    use WithFilter;

    public string $student_level;

    protected $listeners = ['saved' => '$refresh'];

    public function mount(): void
    {
        auth()->user()->isStudent() ? '' : abort(419);
        $this->student_level = auth()->user()->student->level;
    }

    public function with(): array
    {
        return [
            'assessments' => Assessment::search($this->search)
                ->where('type', AssessmentType::SUPPORT->value)
                ->whereNot('status', '2')
                ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
                ->paginate($this->perPage),

            'identification_assessments' => Assessment::search($this->search)
                ->where('type', AssessmentType::IDENTIFICATION->value)
                ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
                ->paginate($this->perPage),

            'total_identification_assessments' => Assessment::search($this->search)
                ->where('type', AssessmentType::IDENTIFICATION->value)
                ->count(),

            'assessments_taken' => auth()->user()->student->reports,
            'identification_taken' => auth()->user()->student->identification_attempts,
        ];
    }

    //listen activate assessment event
    #[On('echo:activate-assessment-channel,ActivateAssessment')]
    public function listenActiveAssessment($data): void
    {
        $this->with();
    }

    //listen expire assessment event
    #[On('echo:assessment-expire-channel,ExpireAssessment')]
    public function listenExpireAssessment($data): void
    {
        $this->with();
    }

}; ?>

<div>
    @use('\App\Models\IdentificationAttempt')
    @use('\App\Models\AssessmentReport')
    @use('\Illuminate\Support\Facades\Storage')

    @if($student_level === StudentLevel::HIGHER->value)
        @if($identification_assessments->count() > 0)
            <div class="flex flex-col xl:flex-row space-y-4 mb-16 xl:space-y-0 xl:space-x-4">
                <!-- start::Schedule -->
                <div class="w-full xl:w-2/3 bg-white shadow-xl rounded-lg space-y-1">
                    <div class="flex justify-between">
                        <h4 class="text-xl font-semibold m-6 capitalize">Task</h4>

                        @php $total_assessments = Assessment::count() @endphp
                        @if(auth()->user()->student->identification_attempts->count() >= $total_assessments * 3)
                            <p class="text-sm text-green-600 font-semibold m-6 capitalize">
                                You are not slow leaner, make more practice
                            </p>
                        @endif
                    </div>
                    @foreach($identification_assessments as $assessment)
                        <!-- start::Task in calendar -->
                        <div class="flex">
                            <div class="w-32 flex flex-col items-center justify-center px-2 bg-blue-500 text-gray-100">
                            <span class="text-sm lg:text-base font-semibold">
                                {{ $assessment->total_questions. ' Qns' }}
                            </span>
                                <span class="text-xs lg:text-sm text-gray-200">
                                {{ $assessment->total_marks. ' Marks' }}
                            </span>
                            </div>
                            <div class="w-full flex justify-between p-4 bg-gray-100 hover:bg-gray-200 transition duration-200">
                                <div class="flex flex-col justify-center">
                                    <span class="xl:text-lg py-4">{{  $assessment->tag }}</span>
                                </div>

                                @php
                                    $assessmentTaken = AssessmentReport::where('student_id', auth()->user()->student->id)
                                            ->where('assessment_id', $assessment->id)
                                            ->first();
                                @endphp
                                @if($assessment->shuffled)
                                    @if($assessmentTaken)
                                        <div class="flex flex-col justify-center">
                                            <span class="flex xl:text-lg py-4 text-green-600">
                                                <x-icon class="mr-1" name="check-circle"/> DONE
                                            </span>
                                        </div>
                                    @else
                                        <div class="flex items-center">
                                            <a wire:navigate
                                               href="{{ route('assessments.preview', encrypt($assessment->id)) }}">
                                                <x-buttons.primary
                                                        class="text-xs lg:text-sm p-2 rounded-lg text-center">
                                                    {{__('TAKE')}}
                                                </x-buttons.primary>
                                            </a>
                                        </div>
                                    @endif
                                @else

                                @endif
                            </div>
                        </div>
                        <!-- end::Task in calendar -->
                    @endforeach
                </div>
                <!-- end::Schedule -->

                <!-- start::Activity -->
                <div class="w-full xl:w-1/3 bg-white rounded-lg shadow-xl px-4 overflow-y-hidden">
                    <h4 class="text-xl font-semibold p-6 capitalize">Remark</h4>
                    <div class="relative h-full px-8 pt-2">
                        <div class="absolute h-full border border-dashed border-opacity-20 border-secondary"></div>

                        <!-- start::Timeline item -->
                        @if($assessments_taken->count() > 0)
                            @php
                                $takenAttempts = 0;
                                $sum = 0.0;
                                $assessmentsTotalMarks = 0.0;
                            @endphp
                            @foreach($assessments_taken as $answered)
                                <div class="flex items-center w-full my-6 -ml-1.5">
                                    <div class="w-1/12">
                                        <div class="w-3.5 h-3.5 bg-primary rounded-full"></div>
                                    </div>
                                    <div class="w-11/12">
                                        <p class="text-sm capitalize text-gray-800 font-black">
                                            Assessment {{ ++$takenAttempts }}</p>
                                        <p class="text-sm">Submitted
                                            Date: {{ $answered->created_at->format('D d m Y H:i A') }}</p>
                                        <p class="text-sm">Scored Marks:
                                            {{ $answered->scored_marks.'/'.$answered->total_marks.' Marks' }}
                                        </p>
                                    </div>
                                </div>
                                @php

                                    $sum+=$answered->scored_marks;
                                    $assessmentsTotalMarks+=$answered->total_marks;
                                @endphp
                            @endforeach
                            @if($takenAttempts === $total_identification_assessments)
                                <div class="flex items-center w-full my-6 ml-8">
                                    <div class="w-11/12">
                                        @php $average = (50/100)*$assessmentsTotalMarks; @endphp
                                        <p class="text-sm capitalize text-gray-800 font-black">
                                            Summary
                                        </p>
                                        <p class="text-sm">
                                            Total Marks: {{ $assessmentsTotalMarks }}
                                        </p>
                                        <p class="text-sm">
                                            Scored Marks: {{ $sum . "/".$assessmentsTotalMarks }}
                                        </p>
                                        <p class="text-sm">
                                            Average Score: {{ $average }}
                                        </p>
                                        @if($sum >= $average)
                                            <p class="text-sm text-green-700 font-black capitalize">Remark: Pass</p>
                                        @else
                                            <p class="text-sm text-yellow-700 font-black capitalize">Remark: Keep
                                                Going</p>
                                        @endif
                                    </div>
                                </div>
                            @endif


                        @else
                            <div class="flex items-center w-full my-6 -ml-1.5">
                                <div class="w-1/12">
                                    <div class="w-3.5 h-3.5 bg-primary rounded-full"></div>
                                </div>
                                <div class="w-11/12">
                                    <p class="text-sm">You have no any attempt.!</p>
                                </div>
                            </div>
                        @endif
                        <!-- end::Timeline item -->

                    </div>
                </div>
                <!-- end::Activity -->
            </div>
        @else
            <div class="flex flex-col xl:flex-row space-y-4 mb-16 xl:space-y-0 xl:space-x-4">
                <!-- start::Schedule -->
                <div class="w-full bg-white shadow-xl rounded-lg space-y-1">
                    <h4 class="text-sm font-semibold m-6">
                        No assessment(s) to display right now, please wait.....
                    </h4>
                </div>
            </div>
        @endif
    @else
        @if($assessments->count() > 0)
            <div class="flex flex-col xl:flex-row space-y-4 mb-16 xl:space-y-0 xl:space-x-4">
                <!-- start::Schedule -->
                <div class="w-full xl:w-2/3 bg-white shadow-xl rounded-lg space-y-1">
                    <h4 class="text-xl font-semibold m-6 capitalize">Scheduled Assessment</h4>
                    @php $sno = 1 @endphp
                    @foreach($assessments as $assessment)
                        <!-- start::Task in calendar -->
                        <div class="flex">
                            <div class="w-32 flex flex-col items-center justify-center px-2 bg-blue-500 text-gray-100">
                                <span class="text-sm lg:text-base font-semibold">
                                @php
                                    $startDate = \Carbon\Carbon::parse($assessment->start_date)->format('d M');
                                    $startTime = \Carbon\Carbon::parse($assessment->start_time)->format('h:i A');
                                @endphp
                                    {{ $startDate }}
                            </span>
                                <span class="text-xs lg:text-sm text-gray-200">
                                    {{ $startTime }}
                                </span>
                            </div>
                            <div class="w-full flex justify-between p-4 bg-gray-100 hover:bg-gray-200 transition duration-200">
                                <div class="flex flex-col justify-center">
                                    <span class="xl:text-lg"></span>
                                    <span class="flex items-start">
                                        <x-icon name="information-circle" class="h-4 w-4 mr-2"/>
                                        <span class="text-xs lg:text-sm {{ $assessment->status === 0 ? "text-gray-600" : ($assessment->status === 1 ? "text-green-600" : "text-red-600") }}">
                                            {{ $assessment->status === 0 ? "Pending..." : ($assessment->status === 1 ? "Active" : "Expired") }}
                                        </span>
                                    </span>
                                </div>
                                <div class="flex items-center">
                                    @php
                                        $user = auth()->user();
                                        $reportExist = AssessmentReport::where('student_id', $user->student->id)->where('assessment_id', $assessment->id)->exists();
                                    @endphp
                                    @if($assessment->shuffled && !$reportExist && $assessment->status === 1 && $user->type === \App\Enums\UserType::STUDENT->value && $user->student->questions()->count() > 0)
                                        <a wire:navigate
                                           href="{{ route('assessments.instructions', encrypt($assessment->id)) }}">
                                            <x-buttons.primary class="text-xs lg:text-sm p-2 rounded-lg text-center">
                                                {{__('TAKE ASSESSMENT')}}
                                            </x-buttons.primary>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- end::Task in calendar -->
                    @endforeach
                </div>
                <!-- end::Schedule -->

                <!-- start::Activity -->
                <div class="w-full xl:w-1/3 bg-white rounded-lg shadow-xl px-4 overflow-y-hidden">
                    <h4 class="text-xl font-semibold p-6 capitalize">Activity</h4>
                    <div class="relative h-full px-8 pt-2">
                        <div class="absolute h-full border border-dashed border-opacity-20 border-secondary"></div>

                        <!-- start::Timeline item -->
                        @if($assessments_taken->count() > 0)
                            @foreach($assessments_taken as $answered)
                                <div class="flex items-center w-full my-6 -ml-1.5">
                                    <div class="w-1/12">
                                        <div class="w-3.5 h-3.5 bg-primary rounded-full"></div>
                                    </div>
                                    <div class="w-11/12">
                                        @php
                                            $startDate = \Carbon\Carbon::parse($answered->assessment->start_date)->format('D d-m-Y');
                                            $startTime = \Carbon\Carbon::parse($answered->assessment->start_time)->format('h:i A');

                                            $startDateTime = $startDate." ".$startTime;
                                            $average = (50*100)*$answered->total_marks;
                                        @endphp
                                        <p class="text-sm capitalize text-gray-800 font-black">Assessment
                                            type: {{ $answered->assessment->type. ' - '. $answered->total_marks.' Marks' }}</p>
                                        <p class="text-sm">Assessment Date: {{ $startDateTime }}</p>
                                        <p class="text-sm">Submitted
                                            Date: {{ $answered->created_at->format('D d m Y H:i A') }}</p>
                                        <p class="text-sm">Scored Marks: {{ $answered->scored_marks }}</p>
                                        @if($answered->scored_marks >= $average)
                                            <p class="text-sm text-green-700 font-black capitalize">Remark: Pass</p>
                                        @else
                                            <p class="text-sm text-yellow-700 font-black capitalize">Remark: Keep
                                                Going</p>
                                        @endif

                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="flex items-center w-full my-6 -ml-1.5">
                                <div class="w-1/12">
                                    <div class="w-3.5 h-3.5 bg-primary rounded-full"></div>
                                </div>
                                <div class="w-11/12">
                                    <p class="text-sm">You have no any attempt.!</p>
                                </div>
                            </div>
                        @endif
                        <!-- end::Timeline item -->

                    </div>
                </div>
                <!-- end::Activity -->
            </div>
        @else
            <div class="flex flex-col xl:flex-row space-y-4 mb-16 xl:space-y-0 xl:space-x-4">
                <!-- start::Schedule -->
                <div class="w-full bg-white shadow-xl rounded-lg space-y-1">
                    dfgdfgd
                </div>
            </div>
        @endif
    @endif

</div>

