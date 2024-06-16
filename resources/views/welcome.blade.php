<x-guest-layout>
    <nav class="bg-white border-gray-200 dark:bg-gray-900">
        <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl p-4">
            <a href="https://flowbite.com" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="https://flowbite.com/docs/images/logo.svg" class="h-8" alt="Flowbite Logo" />
                <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">LEAP-APP</span>
            </a>
            <div class="flex items-center space-x-4 rtl:space-x-reverse">
                <x-buttons.success data-drawer-target="register" data-drawer-show="register" aria-controls="register">
                    <x-icon name="user-plus" />Register
                </x-buttons.success>

                <x-buttons.primary data-drawer-target="login2" data-drawer-show="login2" data-drawer-placement="right" aria-controls="login2"><x-icon name="arrow-right-end-on-rectangle"/>Login
                </x-buttons.primary>
            </div>
        </div>
    </nav>

    <section class="bg-center h-[90vh] bg-no-repeat bg-[url('https://flowbite.s3.amazonaws.com/docs/jumbotron/conference.jpg')] bg-gray-700 bg-blend-multiply">
        <div class="px-4 mx-auto max-w-screen-xl text-center py-24 lg:py-56">
            <h1 class="mb-4 text-4xl font-extrabold tracking-tight leading-none text-white md:text-5xl lg:text-6xl">We invest in the world’s potential</h1>
            <p class="mb-8 text-lg font-normal text-gray-300 lg:text-xl sm:px-16 lg:px-48">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus autem, eius esse est quam repudiandae soluta totam. Alias aspernatur distinctio ex fugiat illo iste, nesciunt obcaecati odit, sequi sint, vero!
            </p>
            <div class="flex flex-col space-y-4 gap-4 sm:flex-row sm:justify-center sm:space-y-0">
                <x-buttons.success data-drawer-target="register" data-drawer-show="register" aria-controls="register">
                    <x-icon name="user-plus" />Register
                </x-buttons.success>

                <x-buttons.primary data-drawer-target="login2" data-drawer-show="login2" data-drawer-placement="right" aria-controls="login2"><x-icon name="arrow-right-end-on-rectangle"/>Login
                </x-buttons.primary>
            </div>
        </div>
    </section>


    <!-- register component -->
    <div id="register" class="fixed top-0 left-0 z-40 h-screen p-4 overflow-y-auto transition-transform -translate-x-full bg-white w-80 dark:bg-gray-800" tabindex="-1" aria-labelledby="drawer-contact-label">
        <h5 id="drawer-label" class="inline-flex items-center mb-6 text-base font-semibold text-gray-500 uppercase dark:text-gray-400"><svg class="w-4 h-4 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 16">
                <path d="m10.036 8.278 9.258-7.79A1.979 1.979 0 0 0 18 0H2A1.987 1.987 0 0 0 .641.541l9.395 7.737Z"/>
                <path d="M11.241 9.817c-.36.275-.801.425-1.255.427-.428 0-.845-.138-1.187-.395L0 2.6V14a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2.5l-8.759 7.317Z"/>
            </svg>Register Now</h5>
        <button type="button" data-drawer-hide="register" aria-controls="drawer-contact" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 absolute top-2.5 end-2.5 inline-flex items-center justify-center dark:hover:bg-gray-600 dark:hover:text-white" >
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
            <span class="sr-only">Close menu</span>
        </button>
        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="flex gap-4 mt-4">
                <div>
                    <x-auth-label for="name" value="{{ __('Name') }}" />
                    <x-auth-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                </div>
                <div>
                    <x-auth-label for="email" value="{{ __('Email') }}" />
                    <x-auth-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                </div>
            </div>

            <div class="mt-4">
                <x-auth-label for="dob" value="{{ __('Date of Birth') }}" />
                <x-auth-input id="dob" class="block mt-1 w-full" type="date" name="dob" :value="old('dob')" required autofocus autocomplete="dob" />
            </div>

            <div class="mt-4">
                <x-auth-label for="gender" value="{{ __('Gender') }}" />

                <select name="gender" id="gender" class="block mt-1 w-full dark:bg-gray-900 border border-gray-700">
                    <option value="">--select gender--</option>
                    <option value="male">MALE</option>
                    <option value="female">FEMALE</option>
                </select>
            </div>

            <div class="flex gap-4 mt-4">
                <div>
                    <x-auth-label for="location" value="{{ __('Location') }}" />
                    <x-auth-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location')" required autofocus autocomplete="location" />
                </div>
                <div>
                    <x-auth-label for="language" value="{{ __('Language') }}" />
                    <x-auth-input id="language" class="block mt-1 w-full" type="text" name="language" :value="old('language')" required autocomplete="language" />
                </div>
            </div>

            <div class="flex gap-4 mt-4">
                <div>
                    <x-auth-label for="password" value="{{ __('Password') }}" />
                    <x-auth-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                </div>
                <div>
                    <x-auth-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                    <x-auth-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                </div>
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-auth-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-auth-label>
                </div>
            @endif

            <div class="block w-full mt-4">
                <x-buttons.success class="w-full">
                    <x-icon name="user" />
                    {{ __('Register') }}
                </x-buttons.success>
            </div>
        </form>
    </div>

    <!-- login component -->
    <div id="login2" class="fixed top-0 right-0 z-40 h-screen p-4 overflow-y-auto transition-transform translate-x-full bg-white w-80 dark:bg-gray-800" tabindex="-1" aria-labelledby="drawer-right-label">
        <h5 id="drawer-right-label" class="inline-flex items-center mb-4 text-base font-semibold text-gray-500 dark:text-gray-400"><svg class="w-4 h-4 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>Login</h5>
        <button type="button" data-drawer-hide="login2" aria-controls="login2" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 absolute top-2.5 end-2.5 inline-flex items-center justify-center dark:hover:bg-gray-600 dark:hover:text-white" >
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
            <span class="sr-only">Close menu</span>
        </button>
        <x-validation-errors class="mb-4" />

        @session('status')
        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ $value }}
        </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-auth-label for="email" value="{{ __('Email') }}" />
                <x-auth-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-auth-label for="password" value="{{ __('Password') }}" />
                <x-auth-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="block w-full mt-4">
                <x-buttons.primary class="w-full my-2">
                    {{ __('Log in') }}
                </x-buttons.primary>
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>
        </form>
    </div>


</x-guest-layout>
