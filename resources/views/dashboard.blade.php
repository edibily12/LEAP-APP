<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        @if(auth()->user()->isAdmin())
            <!-- start::Stats -->
            <livewire:dashboard.starts />
            <!-- end::Stats -->
        @endif

        @if(auth()->user()->isTeacher())

            <!-- start::Stats  -->
            <livewire:dashboard.overall-stats />
            <!-- end::Stats -->
        @endif

        @if(auth()->user()->isStudent())
            <!-- start::Activities -->
            <livewire:dashboard.activities />
            <!-- end::Activities -->
        @endif

    </div>
</x-app-layout>