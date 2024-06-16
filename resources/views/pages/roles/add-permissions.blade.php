<?php

use Livewire\Volt\Component;

new class extends Component {
    use \App\Traits\WithModal;

    public int $formId;

    public $role, $permissions = [];

    #[\Livewire\Attributes\Rule('required|array')]
    public $permission = [];

    public function mount(\App\Models\Role $role): void
    {
        $this->formId = random_int(1, 100);
        $this->permissions = \App\Models\Permission::orderBy('name')->get();
        $this->role = $role;
    }

    public function save(): void
    {
        $this->validate();
        $this->role->permissions()->sync($this->permission, $detaching = false);
        $permissionAdded = count($this->permission);
        
        
        $this->dispatch('permissions-added', $permissionAdded);
        $this->dispatch('saved');
        $this->resetExcept('formId');
    }

}; ?>

<div>
    <a title="Edit Role" href="#" wire:click="openDialogModal">
        <x-icon name="plus" class="text-xl text-green-600 font-black"/>
    </a>

    @php
        $disabled = $errors->any()  ? true : false;
    @endphp

    {{--     modal  --}}
    <x-dialog-modal maxWidth="3xl" wire:model="openModal">
        <x-slot name="title">
            {{ __('ADD PERMISSIONS TO ROLE') }}
        </x-slot>
        <x-slot name="content">
            <form wire:submit="save" id="add-{{ $this->formId }}">
                <div class="grid grid-cols-1 gap-8">
                    <div class="flex flex-col my-4">
                        <x-label for="password" value="{{ __('Select Permission') }}" />
                        <select
                                multiple
                                wire:model.live="permission"
                                class="block mt-1 w-full h-[500px] capitalize border-gray-300 rounded-md"
                        >
                            <option value="">--Select Permission--</option>
                            @foreach($permissions as $permission)
                                <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="permission" />
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
        Livewire.on('permissions-added', function (totalPermissions) {
            swal({
                title: "Successfully",
                text: "Permissions added successfully.\nTotal Added: "+totalPermissions,
                icon: "success",
                button: "OK"
            });
        })
    </script>
@endpush
