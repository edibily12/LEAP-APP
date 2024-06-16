<?php

use App\Traits\WithFilter;
use Livewire\Volt\Component;

new class extends Component {
    use \Livewire\WithPagination;
    use WithFilter;

    protected $listeners = ['saved' => '$refresh'];

    public function mount()
    {
        auth()->user()->isAdmin() ? '' : abort(403, 'Not Authorized to Access This Page') ;
    }

    public function with(): array
    {
        return [
            'permissions' => \App\Models\Permission::search($this->search)
                ->with(['roles'])
                ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
                ->paginate($this->perPage),
        ];
    }

    public function deletePermission(\App\Models\Permission $permission): void
    {
        $permission->delete();

        $this->dispatch('permission-deleted');
        $this->dispatch('saved');
    }

}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('All Permissions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <!-- start::Advance Table Filters -->
        <div x-data="{ filter: false }" class="bg-white rounded-lg px-8 py-6 overflow-x-scroll custom-scrollbar">

            {{-- add permission --}}
            <livewire:permissions.create/>

            <div class="mt-8 mb-3 flex flex-col md:flex-row items-start md:items-center md:justify-between">
                <div class="flex items-center justify-center space-x-4">
                    <input
                            type="search"
                            wire:model.live="search"
                            placeholder="Search..."
                            class="w-48 lg:w-72 bg-gray-200 text-sm py-2 pl-4 rounded-lg focus:ring-0 focus:outline-none"
                    >
                </div>
                <div class="mt-4 md:mt-0">
                    <form>
                        <label>Order By:</label>
                        <select wire:model.live="orderBy" class="text-sm py-0.5 ml-1">
                            <option value="created_at">Date</option>
                            <option value="name">Name</option>
                        </select>
                        <select wire:model.live="orderAsc" class="text-sm py-0.5 ml-1">
                            <option value="0">Desc</option>
                            <option value="1">Asc</option>
                        </select>
                        <select wire:model.live="perPage" class="text-sm py-0.5 ml-1">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                        </select>
                    </form>
                </div>
            </div>

            <table class="w-full whitespace-nowrap mb-8">
                <thead class="bg-secondary text-gray-100 font-bold">
                <td class="py-2 pl-2">
                    #
                </td>
                <td class="py-2 pl-2">
                    Permission Name
                </td>
                <td class="py-2 pl-2">
                    Roles Attached
                </td>
                <td class="py-2 pl-2"></td>
                </thead>
                <tbody class="text-sm">
                @if($permissions->count() > 0)
                    @php $sno = 1 @endphp
                    @foreach($permissions as $permission)
                        <tr class="bg-gray-100 hover:bg-primary hover:bg-opacity-20 transition duration-200">
                            <td class="py-3 pl-2">
                                {{ $sno++ }}
                            </td>
                            <td class="py-3 pl-2 capitalize">
                                {{ $permission->name }}
                            </td>
                            <td class="py-3 pl-2">
                                @if($permission->roles->count() > 0)
                                    @foreach($permission->roles as $role)
                                        <li>{{$role->name}}</li>
                                    @endforeach
                                @else
                                    {{ 'N/A' }}
                                @endif
                            </td>

                            <td class="py-3 pl-2 flex items-center space-x-2">
                                <a title="Delete role" href="#" wire:click="deletePermission({{$permission->id}})"
                                   wire:confirm.prompt="Are you sure? All related data will be removed. type YES|YES">
                                    <x-icon name="trash" class="text-xl text-red-600 font-black"/>
                                </a>
                            </td>

                        </tr>
                    @endforeach
                @else
                    <tr class="bg-gray-100 hover:bg-gray-700 hover:bg-opacity-20 transition duration-200">
                        <td class="py-3 pl-2" colspan="6">
                            <p>No Permission(s) found</p>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
            {{ $permissions->links(data: ['scrollTo' => false]) }}
        </div>
        <!-- end::Advance Table Filters -->
    </div>
</div>


@push('scripts')
    <script>
        Livewire.on('permission-deleted', function (type) {
            swal({
                title: "Successfully",
                text: "Permission deleted successfully.",
                icon: "success",
                button: "OK",
                timer: 3000,
            });
        })
    </script>
@endpush
