<?php

use App\Enums\RoleName;
use App\Models\Role;
use Livewire\Volt\Component;
use \Illuminate\Support\Facades\Hash;

new class extends Component {
    use \App\Traits\WithModal;

    public int $formId;
    #[\Livewire\Attributes\Rule('required')]
    public $name, $email, $type, $phone, $role;

    public function mount(): void
    {
        $this->formId = random_int(1, 100);
    }

    public function save(): void
    {

        $this->validate();
        $user = \App\Models\User::create([
            'name' => $this->name,
            'email' => $this->email,
            'type' => $this->type,
            'phone' => $this->phone,
            'password' => bcrypt('password')
        ]);

        if ($this->role === \App\Enums\RoleName::STUDENT->value) {
            $user->student()->create([
                'gender' => 'male',
            ]);
        }

        $ROLE = \Illuminate\Support\Str::lower($this->role);
        $user->roles()->sync(Role::where('name', $ROLE)->first());


        $this->dispatch('user-created', ['name' => $user->name, 'username' => $user->username]);
        $this->dispatch('saved');
        $this->resetExcept('formId');
    }

}; ?>

<div>
    <x-buttons.primary wire:click="openDialogModal">
        <x-icon name="plus" class="mr-2"/>
        {{__('ADD USER')}}
    </x-buttons.primary>

    @php
        $disabled = $errors->any()  ? true : false;
    @endphp

    {{--     modal  --}}
    <x-dialog-modal maxWidth="5xl" wire:model="openModal">
        <x-slot name="title">
            {{ __('ADD NEW USER') }}
        </x-slot>
        <x-slot name="content">
            <form wire:submit="save" id="add-{{ $this->formId }}">
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col my-4">
                        <x-label for="password" value="{{ __('Full Name') }}"/>
                        <x-input
                                class="py-2"
                                type="text"
                                placeholder="Name..."
                                wire:model.live="name"
                        />
                        <x-input-error for="name"/>
                    </div>

                    <div class="flex flex-col my-4">
                        <x-label for="email" value="{{ __('Email') }}"/>
                        <x-input
                                class="py-2"
                                type="email"
                                placeholder="Email..."
                                wire:model.live="email"
                        />
                        <x-input-error for="email"/>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col my-4">
                        <x-label for="password" value="{{ __('User Type') }}"/>
                        <select class="block mt-1 w-full capitalize border-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm py-2"
                                wire:model.live="type">
                            <option value="">--Select Option--</option>
                            @foreach(\App\Enums\UserType::cases() as $type)
                                <option value="{{ $type->value }}">{{ $type->value }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="type"/>
                    </div>

                    <div class="flex flex-col my-4">
                        <x-label for="password" value="{{ __('Phone Number(optional)') }}"/>
                        <x-input
                                class="py-2"
                                type="number"
                                placeholder="0712345467"
                                wire:model.live="phone"
                        />
                        <x-input-error for="phone"/>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div class="flex flex-col my-4">
                        <x-label for="password" value="{{ __('Role') }}"/>
                        <select class="block mt-1 w-full border-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm py-2"
                                wire:model.live="role">
                            <option value="">--Select Option--</option>
                            @foreach(\App\Enums\RoleName::cases() as $type)
                                <option value="{{ $type->value }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="role"/>
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <div class="flex gap-2">
                <x-buttons.secondary wire:click="$toggle('openModal')">
                    <x-icon name="x-mark" class="mr-1"/>
                    {{ __('CANCEL') }}
                </x-buttons.secondary>

                <x-buttons.success wire:target="save" wire:loading.attr="disabled" :disabled="$disabled"
                                   form="add-{{ $this->formId }}">
                    <x-icon name="check-circle" class="mr-1"/>
                    {{ __('SAVE') }}
                </x-buttons.success>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

@push('scripts')
    <script>
        Livewire.on('user-created', function (user) {
            swal({
                title: "Successfully",
                text: "User created successfully.\nName: " + user[0].name + " \nUsername: " + user[0].username,
                icon: "success",
                button: "OK"
            });
        })
    </script>
@endpush
