<div>

    <aside
            :class="menuOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'"
            class="fixed z-30 inset-y-0 left-0 w-64 transition duration-300 bg-secondary overflow-y-auto lg:translate-x-0 lg:inset-0 custom-scrollbar"
            x-cloak
    >
        <!-- start::Logo -->
        <div class="flex gap-2 items-center justify-center bg-black bg-opacity-30 h-16">

            <!-- Logo -->
            <div class="shrink-0 flex items-center">
                <a href="{{ route('dashboard') }}" wire:navigate="dashboard">
                    <x-application-mark class="block h-7 w-auto" />
                </a>
            </div>

            <h1 class="text-gray-100 text-lg font-bold uppercase tracking-widest">
                {{ config('app.name') }}
            </h1>
        </div>
        <!-- end::Logo -->

        <!-- start::Navigation -->
        <nav class="py-10 custom-scrollbar">
            <!-- start::Menu link -->
            <a
                    x-data="{ linkHover: false }"
                    @mouseover = "linkHover = true"
                    @mouseleave = "linkHover = false"
                    href="{{ route('dashboard') }}"
                    class="flex items-center text-gray-400 px-6 py-3 cursor-pointer hover:bg-black hover:bg-opacity-30 transition duration-200 {{ request()->routeIs('dashboard') ? 'bg-gray-700' : '' }}"
                    wire:navigate
            >
                <x-icon name="home" />
                <span
                        class="ml-3 transition duration-200"
                        :class="linkHover ? 'text-gray-100' : ''"
                >
                            Dashboard
                        </span>
            </a>
            <!-- end::Menu link -->

            @if(auth()->user()->isAdmin())
                <p class="text-xs text-gray-600 mt-10 mb-2 px-6 uppercase">Admin</p>

                <!-- start::Menu link -->
                <div
                        x-data="{ linkHover: false, linkActive: false }"
                >
                    <div
                            @mouseover = "linkHover = true"
                            @mouseleave = "linkHover = false"
                            @click = "linkActive = !linkActive"
                            class="flex items-center justify-between text-gray-400 hover:text-gray-100 px-6 py-3 cursor-pointer hover:bg-black hover:bg-opacity-30 transition duration-200"
                            :class=" linkActive ? 'bg-black bg-opacity-30 text-gray-100' : ''"
                    >
                        <div class="flex items-center">
                            <x-icon name="user-group" />
                            <span class="ml-3">Manage Users</span>
                        </div>
                        <svg class="w-3 h-3 transition duration-300" :class="linkActive ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                    <!-- start::Submenu -->
                    <ul
                            x-show="linkActive"
                            x-cloak
                            x-collapse.duration.300ms
                            class="text-gray-400"
                    >
                        <!-- start::Submenu link -->
                        <li class="pl-10 pr-6 py-2 cursor-pointer hover:bg-black hover:bg-opacity-30 transition duration-200 hover:text-gray-100 {{ request()->routeIs('roles.index') ? 'bg-gray-700' : '' }}">
                            <a
                                    wire:navigate
                                    href="{{ route('roles.index') }}"
                                    class="flex items-center"
                            >
                                <span class="mr-2 text-sm">&bull;</span>
                                <span class="overflow-ellipsis">Roles</span>
                            </a>
                        </li>
                        <!-- end::Submenu link -->

                        <!-- start::Submenu link -->
                        <li class="pl-10 pr-6 py-2 cursor-pointer hover:bg-black hover:bg-opacity-30 transition duration-200 hover:text-gray-100 {{ request()->routeIs('permissions.index') ? 'bg-gray-700' : '' }}">
                            <a
                                    wire:navigate
                                    href="{{ route('permissions.index') }}"
                                    class="flex items-center"
                            >
                                <span class="mr-2 text-sm">&bull;</span>
                                <span class="overflow-ellipsis">Permissions</span>
                            </a>
                        </li>
                        <!-- end::Submenu link -->

                        <!-- start::Submenu link -->
                        <li class="pl-10 pr-6 py-2 cursor-pointer hover:bg-black hover:bg-opacity-30 transition duration-200 hover:text-gray-100 {{ request()->routeIs('users.index') ? 'bg-gray-700' : '' }}">
                            <a
                                    href="{{ route('users.index') }}"
                                    class="flex items-center"
                                    wire:navigate
                            >
                                <span class="mr-2 text-sm">
{{--                                      <x-tabler-math-greater class="w-3 h-3" /> --}}
                                </span>
                                <span class="overflow-ellipsis">Users</span>
                            </a>
                        </li>
                        <!-- end::Submenu link -->
                    </ul>
                    <!-- end::Submenu -->
                </div>
                <!-- end::Menu link -->

                <!-- start::Menu link -->
                <a
                        x-data="{ linkHover: false }"
                        @mouseover = "linkHover = true"
                        @mouseleave = "linkHover = false"
                        href="./pages/messages.html"
                        class="flex items-center text-gray-400 px-6 py-3 cursor-pointer hover:bg-black hover:bg-opacity-30 transition duration-200"
                >
                    <x-icon name="chat-bubble-oval-left-ellipsis" />
                    <span
                            class="ml-3 transition duration-200"
                            :class="linkHover ? 'text-gray-100' : ''"
                    >
                            Messages
                        </span>
                </a>
                <!-- end::Menu link -->
            @endif

            @if(auth()->user()->isTeacher())
                <p class="text-xs text-gray-600 mt-10 mb-2 px-6 uppercase">Teacher</p>

                <!-- start::Menu link -->
                <a
                        wire:navigate
                        x-data="{ linkHover: false }"
                        @mouseover = "linkHover = true"
                        @mouseleave = "linkHover = false"
                        href="{{ route('students.index') }}"
                        class="flex items-center text-gray-400 px-6 py-3 cursor-pointer hover:bg-black hover:bg-opacity-30 transition duration-200 {{ request()->routeIs('students.index') ? 'bg-gray-700' : '' }}"
                >
{{--                     <x-phosphor-student-duotone class="w-6 h-6" /> --}}
                    <span
                            class="ml-3 transition duration-200"
                            class="linkHover ? 'text-gray-100' : ''"
                    >
                            Students
                        </span>
                </a>
                <!-- end::Menu link -->

                <!-- start::Menu link -->
                <a
                        wire:navigate
                        x-data="{ linkHover: false }"
                        @mouseover = "linkHover = true"
                        @mouseleave = "linkHover = false"
                        href="{{ route('assessments.index') }}"
                        class="flex items-center text-gray-400 px-6 py-3 cursor-pointer hover:bg-black hover:bg-opacity-30 transition duration-200 {{ request()->routeIs('assessments.index') ? 'bg-gray-700' : '' }}"
                >
{{--                     <x-gmdi-assessment-o class="w-6 h-6" /> --}}
                    <span
                            class="ml-3 transition duration-200"
                            :class="linkHover ? 'text-gray-100' : ''"
                    >
                        Assessments
                    </span>
                </a>
                <!-- end::Menu link -->

            @endif

        </nav>
        <!-- end::Navigation -->
    </aside>

</div>