<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LEAP') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
     <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.min.js"></script>

    <!-- Styles -->
    @livewireStyles
</head>
<body class="font-sans antialiased text-gray-800">
<x-banner />

<div x-data="{ menuOpen: false }" class="flex min-h-screen custom-scrollbar">
    <!-- start::Black overlay -->
    <div :class="menuOpen ? 'block' : 'hidden'" @click="menuOpen = false" class="fixed z-20 inset-0 bg-black opacity-50 transition-opacity lg:hidden"></div>
    <!-- end::Black overlay -->

    <!-- start::side bar-->
    <x-sidebar />
    <!-- end::side bar -->

    <div class="lg:pl-64 w-full flex flex-col" x-cloak>
        <!-- start::Topbar -->
        @livewire('navigation-menu')
        <!-- end::Topbar -->

        <!-- start:Page content -->
        <div class="h-full bg-gray-200 p-8">
            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="w-full mx-auto py-6 px-1 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
            <!-- end:Page content -->
        </div>
    </div>
</div>

@stack('modals')

@livewireScripts

@stack('scripts')
</body>
</html>