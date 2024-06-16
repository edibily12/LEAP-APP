<?php

use App\Models\Permission;
use Livewire\Volt\Component;

new class extends Component {
    use \App\Traits\WithModal;

    public int $formId;
    #[\Livewire\Attributes\Rule('required')]
    public $name;

    public function mount(): void
    {
        $this->formId = random_int(1, 100);
    }

    public function save(): void
    {
        $this->validate();
        $actions = [
            'viewAny', 'view', 'create', 'update', 'delete', 'forceDelete', 'restore',
        ];

        $resources = [
            \Str::lower($this->name)
        ];

        $total = collect($resources)->crossJoin($actions)->map(function ($set) {
            return implode('.', $set);
        })->each(function ($permission) {
            Permission::create(['name' => $permission]);
        });

        $totalPermissionsAdded = $total->count();


        $this->dispatch('permission-created', $totalPermissionsAdded);
        $this->dispatch('saved');
        $this->resetExcept('formId');
    }

}; ?>

<div>
    <x-buttons.primary wire:click="openDialogModal">
        <x-icon name="plus" class="mr-2" />
        {{__('ADD PERMISSION')}}
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
                <div class="grid grid-cols-1 gap-4">
                    <div class="flex flex-col my-4">
                        <x-label for="password" value="{{ __('Permission Name') }}"/>
                        <x-input
                                class="py-4"
                                type="text"
                                placeholder="Name..."
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
        Livewire.on('permission-created', function (total) {
            swal({
                title: "Successfully",
                text: "Permission created successfully.\nTotal: "+total,
                icon: "success",
                button: "OK"
            });
        })
    </script>
@endpush
