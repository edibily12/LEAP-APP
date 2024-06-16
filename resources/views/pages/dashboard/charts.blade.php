<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div>
    <div class="w-full flex flex-col lg:flex-row items-center justify-between bg-white mb-16 px-2 py-4 rounded-lg shadow-lg">
        <div class="w-full lg:w-2/3">
            <h4 class="text-center text-xl font-semibold mb-4">Statistic 1</h4>
            <div>
                <canvas id="chart_1"></canvas>
            </div>
        </div>
        <div class="hidden lg:block lg:mx-2 xl:mx-10 2xl:mx-20"></div>
        <div class="w-full lg:w-1/3 mt-10 lg:mt-0">
            <h4 class="text-center text-xl font-semibold mb-4">Statistic 2</h4>
            <div>
                <canvas id="chart_2"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>

        // start::Chart 1
        const labels = [
            'January',
            'February',
            'Mart',
            'April',
            'May',
            'Jun',
            'July'
        ];

        const data_1 = {
            labels: labels,
            datasets: [{
                label: 'My First Dataset',
                data: [65, 59, 80, 81, 56, 55, 40],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(255, 205, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(201, 203, 207, 0.2)'
                ],
                borderColor: [
                    'rgb(255, 99, 132)',
                    'rgb(255, 159, 64)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(54, 162, 235)',
                    'rgb(153, 102, 255)',
                    'rgb(201, 203, 207)'
                ],
                borderWidth: 1
            }]
        };

        const config_1 = {
            type: 'bar',
            data: data_1,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            },
        };

        var chart_1 = new Chart(
            document.getElementById('chart_1'),
            config_1
        );

        // end::Chart 1

        // start::Chart 2
        const data_2 = {
            labels: [
                'Eating',
                'Drinking',
                'Sleeping',
                'Designing',
                'Coding',
                'Cycling',
                'Running'
            ],
            datasets: [{
                label: 'My First Dataset',
                data: [65, 59, 90, 81, 56, 55, 40],
                fill: true,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgb(255, 99, 132)',
                pointBackgroundColor: 'rgb(255, 99, 132)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgb(255, 99, 132)'
            }, {
                label: 'My Second Dataset',
                data: [28, 48, 40, 19, 96, 27, 100],
                fill: true,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgb(54, 162, 235)',
                pointBackgroundColor: 'rgb(54, 162, 235)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgb(54, 162, 235)'
            }]
        };

        const config_2 = {
            type: 'radar',
            data: data_2,
            options: {
                elements: {
                    line: {
                        borderWidth: 3
                    }
                }
            },
        };

        var chart_2 = new Chart(
            document.getElementById('chart_2'),
            config_2
        );
        // end::Chart 2
    </script>
@endpush
