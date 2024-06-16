<?php

use Livewire\Volt\Component;
use \App\Models\User;
use \App\Models\Student;

new class extends Component {

    public $student;

    public function mount($id): void
    {
        auth()->user()->isTeacher() ? '' : abort(403, 'Not Authorized to Access This Page');
        $id = Crypt::decrypt($id);

        $this->student = Student::with(['user'])
            ->where('user_id', $id)
            ->firstOrFail();
    }

    public function changeStudentLevel($id): void
    {
        $student = Student::findOrFail($id);
        $student->update([
            'level' => 'lower'
        ]);
    }

    public function with(): array
    {
        return [
            'assessments_taken' => $this->student->reports,
            'identification_taken' => $this->student->identification_attempts,
        ];
    }

}; ?>

<div>
    <div class="py-1">
        <!-- start:Page content -->
        <div class="h-full bg-gray-200">
            <div class="bg-white rounded-lg shadow-xl pb-8">
                <div class="w-full h-[250px]">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url('images/profile-background.jpg') }}"
                         class="w-full h-full rounded-tl-lg rounded-tr-lg">
                </div>
                <div class="flex flex-col items-center -mt-20">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url('images/profile.jpg') }}"
                         class="w-40 border-4 border-white rounded-full">
                    <div class="flex items-center space-x-2 mt-2">
                        <p class="text-2xl">{{ $student->user->name }}</p>
                        <span class="bg-blue-500 rounded-full p-1" title="Verified">
                            <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-100 h-2.5 w-2.5" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                                      d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                    </div>
                    <p class="text-gray-700">{{ $student->user->email }}</p>
                    <p class="text-sm text-gray-500">{{ $student->user->username }}</p>
                </div>
                <div class="flex-1 flex flex-col items-center lg:items-end justify-end px-8 mt-2">
                    @if($student->level === 'higher')
                        <div class="flex items-center space-x-4 mt-2">
                            <button wire:click="changeStudentLevel({{ $student->id }})"
                                    class="flex items-center bg-blue-600 hover:bg-blue-700 text-gray-100 px-4 py-2 rounded text-sm space-x-2 transition duration-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                     fill="currentColor">
                                    <path fill-rule="evenodd"
                                          d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z"
                                          clip-rule="evenodd"/>
                                </svg>
                                <span>Change Level</span>
                            </button>
                        </div>
                    @endif

                </div>
            </div>

            <div class="my-4 flex flex-col 2xl:flex-row space-y-4 2xl:space-y-0 2xl:space-x-4">
                <div class="w-full flex flex-col 2xl:w-1/3">
                    <div class="flex-1 bg-white rounded-lg shadow-xl p-8">
                        <h4 class="text-xl text-gray-900 font-bold">Personal Info</h4>
                        <ul class="mt-2 text-gray-700">
                            <li class="flex border-y py-2">
                                <span class="font-bold w-24">Full name:</span>
                                <span class="text-gray-700">{{ $student->user->name }}</span>
                            </li>
                            <li class="flex border-b py-2">
                                <span class="font-bold w-24">Birthday:</span>
                                <span class="text-gray-700">
                                    {{ \Carbon\Carbon::createFromFormat('Y-m-d', $student->dob)->format('d M Y') }}
                                </span>
                            </li>
                            <li class="flex border-b py-2">
                                <span class="font-bold w-24">Joined:</span>
                                <span class="text-gray-700">
                                    {{ $student->created_at->format('d M Y'). '-'. $student->created_at->diffForHumans() }}</span>
                            </li>
                            <li class="flex border-b py-2">
                                <span class="font-bold w-24">Mobile:</span>
                                <span class="text-gray-700">
                                    {{ $student->user->phone ?: 'N/A' }}
                                </span>
                            </li>
                            <li class="flex border-b py-2">
                                <span class="font-bold w-24">Email:</span>
                                <span class="text-gray-700">{{ $student->user->email }}</span>
                            </li>
                            <li class="flex border-b py-2">
                                <span class="font-bold w-24">Location:</span>
                                <span class="text-gray-700">{{ $student->location }}</span>
                            </li>
                            <li class="flex border-b py-2">
                                <span class="font-bold w-24">Languages:</span>
                                <span class="text-gray-700">{{ $student->Language }}</span>
                            </li>
                        </ul>
                    </div>

                    <div class="flex-1 bg-white rounded-lg shadow-xl mt-4 p-8">
                        <h4 class="text-xl text-gray-900 font-bold">Assessments done</h4>
                        <div class="relative px-4">
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
                                        <p class="text-sm">No results found!</p>
                                    </div>
                                </div>
                            @endif
                            <!-- end::Timeline item -->
                        </div>
                    </div>
                </div>

                <div class="flex flex-col w-full 2xl:w-2/3">
                    <div class="flex-1 bg-white rounded-lg shadow-xl p-8">
                        <h4 class="text-xl text-gray-900 font-bold">Level</h4>
                        <p class="mt-2 text-gray-800 uppercase">
                            {{ $student->level . 'level' }}
                        </p>
                    </div>
                    <div class="flex-1 bg-white rounded-lg shadow-xl mt-4 p-8">
                        <h4 class="text-xl text-gray-900 font-bold">Identification Attempts</h4>
                        <div class="mt-4">
                            <!-- start::Timeline item -->
                            @if($identification_taken->count() > 0)
                                @php $takenAttempts = 1 @endphp
                                @foreach($identification_taken as $answered)
                                    <div class="flex items-center w-full my-6 -ml-1.5">
                                        <div class="w-1/12">
                                            <div class="w-3.5 h-3.5 bg-primary rounded-full"></div>
                                        </div>
                                        <div class="w-11/12">
                                            @php
                                                $average = (50/100)*$answered->total_marks;
                                            @endphp
                                            <p class="text-sm capitalize text-gray-800 font-black">
                                                Attempt {{ $takenAttempts++ }}</p>
                                            <p class="text-sm">Submitted
                                                Date: {{ $answered->created_at->format('D d m Y H:i A') }}</p>
                                            <p class="text-sm">Scored Marks:
                                                {{ $answered->scored_marks.'/'.$answered->total_marks.' Marks' }}
                                            </p>
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
                </div>
            </div>
        </div>
        <!-- end:Page content -->
    </div>
</div>

@push('scripts')
    <script>

        const DATA_SET_VERTICAL_BAR_CHART_1 = [68.106, 26.762, 94.255, 72.021, 74.082, 64.923, 85.565, 32.432, 54.664, 87.654, 43.013, 91.443];

        const labels_vertical_bar_chart = ['January', 'February', 'Mart', 'April', 'May', 'Jun', 'July', 'August', 'September', 'October', 'November', 'December'];

        const dataVerticalBarChart = {
            labels: labels_vertical_bar_chart,
            datasets: [
                {
                    label: 'Revenue',
                    data: DATA_SET_VERTICAL_BAR_CHART_1,
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                }
            ]
        };
        const configVerticalBarChart = {
            type: 'bar',
            data: dataVerticalBarChart,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Last 12 Months'
                    }
                }
            },
        };

        var verticalBarChart = new Chart(
            document.getElementById('verticalBarChart'),
            configVerticalBarChart
        );
    </script>
@endpush
