<?php

use App\Traits\WithFilter;
use Livewire\Volt\Component;
use \Livewire\Attributes\On;

new class extends Component {
    use \Livewire\WithPagination;
    use WithFilter;

    protected $listeners = ['saved' => '$refresh'];

    public function mount()
    {
        auth()->user()->isTeacher() ? '' : abort(403, 'Not Authorized to Access This Page');
    }

    public function with(): array
    {
        return [
            'assessments' => \App\Models\Assessment::search($this->search)
                ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
                ->paginate($this->perPage),
        ];
    }

    public function deleteAssessment(\App\Models\Assessment $assessment): void
    {
        $assessment->delete();

        $this->dispatch('assessment-deleted');
        $this->dispatch('saved');
    }

    //listen activate assessment event
    #[On('echo:activate-assessment-channel,ActivateAssessment')]
    public function listenActiveAssessment($data)
    {
        $this->with();
    }
    //listen expire assessment event
    #[On('echo:assessment-expire-channel,ExpireAssessment')]
    public function listenExpireAssessment($data)
    {
        $this->with();
    }

}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('All Assessments') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <!-- start::Advance Table Filters -->
        <div x-data="{ filter: false }" class="bg-white rounded-lg px-8 py-6 overflow-x-scroll custom-scrollbar">

            {{-- add assessment --}}
            <livewire:assessments.create/>

            <div class="mt-8 mb-3 flex flex-col md:flex-row items-start md:items-center md:justify-between">
                <div class="flex items-center justify-center space-x-4">
                    <input
                            type="search"
                            wire:model.live="search"
                            placeholder="Search..."
                            class="w-48 lg:w-72 bg-gray-200 text-sm py-2 pl-4 rounded-lg focus:ring-0 focus:outline-none"
                    >
                </div>
                <div class="mt-4 md:mt-0">
                    <form>
                        <label>Order By:</label>
                        <select wire:model.live="orderBy" class="text-sm py-0.5 ml-1">
                            <option value="created_at">Date</option>
                            <option value="type">Type</option>
                            <option value="status">Status</option>
                        </select>
                        <select wire:model.live="orderAsc" class="text-sm py-0.5 ml-1">
                            <option value="0">Desc</option>
                            <option value="1">Asc</option>
                        </select>
                        <select wire:model.live="perPage" class="text-sm py-0.5 ml-1">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                        </select>
                    </form>
                </div>
            </div>

            <table class="w-full whitespace-nowrap mb-8">
                <thead class="bg-secondary text-gray-100 font-bold">
                <td class="py-2 pl-2">
                    #
                </td>
                <td class="py-2 pl-2">
                    Assessment Type
                </td>
                <td class="py-2 pl-2">
                    Status
                </td>
                <td class="py-2 pl-2">
                    Total Questions
                </td>
                <td class="py-2 pl-2">
                    Total Marks
                </td>
                <td class="py-2 pl-2">
                    Start Date
                </td>
                <td class="py-2 pl-2">
                    Expire Date
                </td>
                <td class="py-2 pl-2"></td>
                </thead>
                <tbody class="text-sm">

                @if($assessments->count() > 0)
                    @php $sno = 1 @endphp
                    @foreach($assessments as $assessment)
                        <tr class="bg-gray-100 hover:bg-primary hover:bg-opacity-20 transition duration-200">
                            <td class="py-3 pl-2">
                                {{ $sno++ }}
                            </td>
                            <td class="py-3 pl-2 capitalize font-black {{ $assessment->type === \App\Enums\AssessmentType::IDENTIFICATION->value ? "text-yellow-600" :"text-red-600" }}">
                                {{ $assessment->type === \App\Enums\AssessmentType::IDENTIFICATION->value ? "IDENTIFICATION" : "SUPPORT" }}
                            </td>
                            <td class="py-3 pl-2 capitalize {{ $assessment->status === 0 ? "text-gray-600" : ($assessment->status === 1 ? "text-green-600" : "text-red-600") }}">
                                {{ $assessment->status === 0 ? "Pending" : ($assessment->status === 1 ? "Active" : "Expired") }}
                            </td>
                            <td class="py-3 pl-2">
                                {{ $assessment->total_questions }}
                            </td>
                            <td class="py-3 pl-2">
                                {{ $assessment->total_marks }}
                            </td>
                            <td class="py-3 pl-2">
                                {{ $assessment->start_date ? $assessment->start_date . ' - '. $assessment->start_time : "N/A"  }}
                            </td>
                            <td class="py-3 pl-2">
                                {{ $assessment->expire_date ? $assessment->expire_date . ' - '. $assessment->expire_time : "N/A"  }}
                            </td>

                            <td class="py-3 pl-2 flex items-center space-x-2">
                                <a title="View assessment" href="{{ route('assessments.view', encrypt($assessment->id)) }}" wire:navigate>
                                    <x-icon name="eye" class="text-xl text-blue-600 font-black"/>
                                </a>

                                @if($assessment->status !== 1)
                                    <a title="delete assessment" wire:click="deleteAssessment({{$assessment->id}})" wire:confirm.prompt="Are you sure? All related data will be lost! type YES|YES">
                                        <x-icon name="trash" class="text-xl text-red-600 font-black"/>
                                    </a>
                                @endif

                            </td>

                        </tr>
                    @endforeach
                @else
                    <tr class="bg-gray-100 hover:bg-gray-700 hover:bg-opacity-20 transition duration-200">
                        <td class="py-3 pl-2" colspan="8">
                            <p>No Assessment(s) found</p>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
            {{ $assessments->links(data: ['scrollTo' => false]) }}
        </div>
        <!-- end::Advance Table Filters -->
    </div>
</div>

@push('scripts')
    <script>
        Livewire.on('assessment-deleted', function () {
            swal({
                title: "Successfully",
                text: "Assessment deleted successfully.",
                icon: "success",
                button: "OK",
                timer: 3000
            });
        })
    </script>
@endpush