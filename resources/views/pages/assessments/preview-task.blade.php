<?php

use App\Models\Question;
use Livewire\Volt\Component;

new class extends Component {

    public int $assessment_id;
    public $assessment;

    public function mount($id): void
    {
        auth()->user()->isStudent() ? '' : abort(419);
        $this->assessment_id = Crypt::decrypt($id);
        $this->assessment = \App\Models\Assessment::findOrFail($this->assessment_id);
    }


}; ?>

<div>`
    <div class="text-center">


        <section class="bg-white h-[100vh] dark:bg-[url('https://flowbite.s3.amazonaws.com/docs/jumbotron/hero-pattern-dark.svg')]">
            <div class="py-8 px-4 mx-auto max-w-screen-xl text-center lg:py-16 z-10 relative">
                <h1 class="mb-4 text-4xl font-extrabold tracking-tight leading-none text-gray-900 md:text-5xl lg:text-6xl dark:text-white">Read Instructions</h1>
                <p class="mb-8 text-lg font-normal text-gray-500 lg:text-xl sm:px-16 lg:px-48 dark:text-gray-200">{{ $assessment->instructions }}</p>
                <form class="w-full max-w-md mx-auto">
                    <div class="relative">

                        <a wire:navigate href="{{ route('assessments.read', encrypt($assessment->id)) }}" class="inline-flex items-center justify-center px-5 py-3 text-base font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-900">
                            START NOW
                            <svg class="w-3.5 h-3.5 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                            </svg>
                        </a>
                    </div>
                </form>
            </div>
            <div class="bg-gradient-to-b from-blue-50 to-transparent dark:from-blue-900 w-full h-full absolute top-0 left-0 z-0"></div>
        </section>

    </div>
</div>
