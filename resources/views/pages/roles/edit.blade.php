<?php

use Livewire\Volt\Component;

new class extends Component {
    use \App\Traits\WithModal;


    #[\Livewire\Attributes\Rule('required|min:5|max:100')]
    public $name = '';
    public $role, $formId;

    public function mount(\App\Models\Role $role): void
    {
        $this->role = $role;
        $this->formId = random_int(1,100);
        $this->fill(
            $this->role->only(
                'name'
            )
        );
    }

    public function save(): void
    {
        $this->validate();
        $this->role->update([
            'name' => \Illuminate\Support\Str::lower($this->name)
        ]);

        $this->dispatch('role-updated', $this->name);
        $this->dispatch('saved');
        $this->reset();

    }


}; ?>

<div>

    @php
        $disabled = $errors->any()  ? true : false;
    @endphp

    <a title="edit role" href="#" wire:click="openDialogModal">
        <x-icon name="pencil-square" class="text-xl text-yellow-600 font-black"/>
    </a>

    {{--     modal  --}}
    <x-dialog-modal maxWidth="3xl" wire:model="openModal">
        <x-slot name="title">
            {{ __('EDIT ROLE') }}
        </x-slot>
        <x-slot name="content">
            <form wire:submit="save" id="add-{{ $this->formId }}">
                <div class="grid grid-cols-1 gap-4">
                    <div class="flex flex-col my-4">
                        <x-label for="password" value="{{ __('Role Name') }}"/>
                        <x-input
                                class="py-4"
                                type="text"
                                placeholder="Role name..."
                                wire:model.live="name"
                        />
                        <x-input-error for="name"/>
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
        Livewire.on('role-updated', function (name) {
            swal({
                title: "Successfully",
                text: "Role updated successfully.\nName: "+name,
                icon: "success",
                button: "OK"
            });
        })
    </script>
@endpush
