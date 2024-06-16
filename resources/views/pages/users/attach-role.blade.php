<?php

use Livewire\Volt\Component;

new class extends Component {
    use \App\Traits\WithModal;

    public int $formId;

    public $user, $roles = [];

    #[\Livewire\Attributes\Rule('required|array')]
    public $role = [];

    public function mount(\App\Models\User $user): void
    {
        $this->formId = random_int(1,100);
        $this->roles = \App\Models\Role::all();
        $this->user = $user;
    }

    public function save(): void
    {
        $this->validate();
        $this->user->roles()->sync($this->role, $detaching = false);
        $rolesAdded = count($this->role);


        $this->dispatch('role-attached', $rolesAdded);
        $this->dispatch('saved');
        $this->resetExcept('formId');
    }
}; ?>

<div>
    <a title="Attach Role" href="#" wire:click="openDialogModal">
        <x-icon name="plus" class="text-xl text-green-600 font-black"/>
    </a>

    @php
        $disabled = $errors->any()  ? true : false;
    @endphp

    {{--     modal  --}}
    <x-dialog-modal maxWidth="3xl" wire:model="openModal">
        <x-slot name="title">
            {{ __('ADD ROLE TO USER') }}
        </x-slot>
        <x-slot name="content">
            <form wire:submit="save" id="add-{{ $this->formId }}">
                <div class="grid grid-cols-1 gap-8">
                    <div class="flex flex-col my-4">
                        <x-label for="password" value="{{ __('Select Role') }}" />
                        <select
                                multiple
                                wire:model.live="role"
                                class="block mt-1 w-full h-[500px] capitalize border-gray-300 rounded-md"
                        >
                            <option value="">--Select Role--</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="role" />
                        @if(session()->has('message'))
                            <p class="text-red-600">{{ session('message') }}</p>
                        @endif
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
        Livewire.on('role-attached', function (totalRole) {
            swal({
                title: "Successfully",
                text: "Role added successfully.\nTotal Added: "+totalRole,
                icon: "success",
                button: "OK"
            });
        })
    </script>
@endpush
