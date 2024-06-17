<?php

use Livewire\Volt\Component;

new class extends Component {
    public $stats;
    public function mount(): void
    {
        $this->stats = DB::selectOne("
        SELECT
            (SELECT COUNT(*) FROM students) AS all_students,
            (SELECT COUNT(*) FROM students WHERE level = 'higher') AS proficient_learners,
            (SELECT COUNT(*) FROM students WHERE level = 'lower') AS steady_learners,
            (SELECT COUNT(*) FROM assessments WHERE type = 'identification') AS identification_assessments,
            (SELECT COUNT(*) FROM assessments WHERE type = 'support') AS support_assessments"
        );
    }
}; ?>

<div>
    <div class="flex flex-col xl:flex-row space-y-4 xl:space-y-0 xl:space-x-4">
        <!-- start::Total stats -->
        <div class="w-full xl:w-full p-6 space-y-6 bg-white shadow-xl rounded-lg">
            <h4 class="text-xl font-semibold mb-4 capitalize">General stats</h4>
            <div class="grid grid-cols-2 gap-4 h-40">
                <div class="bg-green-300 bg-opacity-20 text-green-700 flex flex-col items-center justify-center rounded-lg">
                    <span class="text-4xl font-bold">{{ $stats->all_students }}</span>
                    <span>All Students</span>
                </div>
                <div class="bg-indigo-300 bg-opacity-20 text-indigo-700 flex flex-col items-center justify-center rounded-lg">
                    <span class="text-4xl font-bold">{{ $stats->proficient_learners }}</span>
                    <span>proficient learners</span>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-2 h-32">
                <div class="bg-yellow-300 bg-opacity-20 text-yellow-700 flex flex-col items-center justify-center rounded-lg">
                    <span class="text-3xl font-bold">{{ $stats->steady_learners }}</span>
                    <span>steady learners</span>
                </div>
                <div class="bg-blue-300 bg-opacity-20 text-blue-700 flex flex-col items-center justify-center rounded-lg">
                    <span class="text-3xl font-bold">{{ $stats->identification_assessments }}</span>
                    <span>Identification Assessment</span>
                </div>
                <div class="bg-red-300 bg-opacity-20 text-red-700 flex flex-col items-center justify-center rounded-lg">
                    <span class="text-3xl font-bold">{{ $stats->support_assessments  }}</span>
                    <span>Support Assessment</span>
                </div>
            </div>
        </div>
        <!-- end::Total stats -->
    </div>
</div>
