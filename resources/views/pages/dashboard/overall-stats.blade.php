<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div>
    <div class="flex flex-col xl:flex-row space-y-4 xl:space-y-0 xl:space-x-4">
        <!-- start::Stats by category -->
        <div class="w-full xl:w-1/3 p-6 space-y-6 bg-white rounded-lg shadow-xl capitalize">
            <h4 class="text-center text-xl font-semibold mb-4">Project completion</h4>
            <section
                    x-data="statsByCategory"
                    class="space-y-6"
            >
                <div class="flex items-center justify-center">
                    <template x-for="item in items">
                        <button x-text="item.name"
                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 mx-1 rounded-lg transition duration-200"
                                :class="(currentItem.name == item.name) && 'bg-gray-300'"
                                @click="currentItem = item"></button>
                    </template>
                </div>

                <p class="text-center text-xl font-semibold text-gray-700" x-text="`${currentItem.name}`"></p>

                <div class="flex items-center justify-center" x-data="{ circumference: 2 * 22 / 7 * 80 }">
                    <svg class="-rotate-90 w-48 h-48">
                        <circle cx="96" cy="96" r="80" stroke="currentColor" stroke-width="20" fill="transparent"
                                class="text-gray-300" />

                        <circle cx="96" cy="96" r="80" stroke="currentColor" stroke-width="20" fill="transparent"
                                :stroke-dasharray="circumference"
                                :stroke-dashoffset="circumference - currentItem.percent / 100 * circumference"
                                class="text-green-500" />
                    </svg>
                    <span class="absolute text-3xl font-bold text-green-500" x-text="`${currentItem.percent}%`"></span>
                </div>
            </section>
        </div>
        <!-- end::Stats by category -->

        <!-- start::Project overview stats -->
        <div
                x-data="productOverviewStats"
                class="w-full xl:w-1/3 p-6 space-y-6 bg-white shadow-xl rounded-lg"
        >
            <h4 class="text-xl font-semibold mb-4 capitalize">Project overview</h4>
            <section class="space-y-6">
                <div class="flex items-center justify-center" x-data="{ circumference: 2 * 22 / 7 * 110 }">
                    <svg class="rotate-90 w-64 h-64">
                        <circle cx="128" cy="128" r="110" stroke="currentColor" stroke-width="7" fill="transparent"
                                class="text-gray-300" />

                        <circle cx="128" cy="128" r="110" stroke="currentColor" stroke-width="20" fill="transparent"
                                :stroke-dasharray="circumference"
                                :stroke-dashoffset="circumference - Math.round((project.completed / (project.completed + project.in_progress)) * 100) / 100 * circumference"
                                class="text-green-500" />
                    </svg>
                    <span class="absolute text-5xl font-bold text-green-500" x-text="`${Math.round((project.completed / (project.completed + project.in_progress)) * 100)}%`"></span>
                </div>
            </section>
            <div class="grid grid-cols-2 border-t-2 border-gray-300 pt-4">
                <div class="flex flex-col items-center justify-center border-r-2 border-gray-300">
                    <span class="text-sm text-gray-600">Completed</span>
                    <span class="text-3xl font-bold text-gray-800" x-text="`${project.completed}`"></span>
                </div>
                <div class="flex flex-col items-center justify-center">
                    <span class="text-sm text-gray-600">In Progress</span>
                    <span class="text-3xl font-bold text-gray-800" x-text="`${project.in_progress}`"></span>
                </div>
            </div>
        </div>
        <!-- end::Project overview stats -->

        <!-- start::Total stats -->
        <div class="w-full xl:w-1/3 p-6 space-y-6 bg-white shadow-xl rounded-lg">
            <h4 class="text-xl font-semibold mb-4 capitalize">Order stats</h4>
            <div class="grid grid-cols-2 gap-4 h-40">
                <div class="bg-green-300 bg-opacity-20 text-green-700 flex flex-col items-center justify-center rounded-lg">
                    <span class="text-4xl font-bold">184</span>
                    <span>Completed</span>
                </div>
                <div class="bg-indigo-300 bg-opacity-20 text-indigo-700 flex flex-col items-center justify-center rounded-lg">
                    <span class="text-4xl font-bold">54</span>
                    <span>Shipped</span>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-2 h-32">
                <div class="bg-yellow-300 bg-opacity-20 text-yellow-700 flex flex-col items-center justify-center rounded-lg">
                    <span class="text-3xl font-bold">9</span>
                    <span>Pending</span>
                </div>
                <div class="bg-blue-300 bg-opacity-20 text-blue-700 flex flex-col items-center justify-center rounded-lg">
                    <span class="text-3xl font-bold">24</span>
                    <span>Refunded</span>
                </div>
                <div class="bg-red-300 bg-opacity-20 text-red-700 flex flex-col items-center justify-center rounded-lg">
                    <span class="text-3xl font-bold">37</span>
                    <span>Canceled</span>
                </div>
            </div>
        </div>
        <!-- end::Total stats -->
    </div>
</div>

@push('scripts')
    <script>
        // Stats by category
        document.addEventListener('alpine:init', () => {
            Alpine.data('statsByCategory', () => ({
                items: [{
                    'name': 'Project 1',
                    'percent': '71',
                },
                    {
                        'name': 'Project 2',
                        'percent': '63',
                    },
                    {
                        'name': 'Project 3',
                        'percent': '92',
                    },
                    {
                        'name': 'Project 4',
                        'percent': '84',
                    },
                ],
                currentItem: {
                    'name': 'Project 1',
                    'percent': '71',
                }
            }));
        });

        // Project overview stats
        document.addEventListener('alpine:init', () => {
            Alpine.data('productOverviewStats', () => ({
                project: {
                    'completed': 149,
                    'in_progress': 42,
                }
            }));
        });
    </script>
@endpush
