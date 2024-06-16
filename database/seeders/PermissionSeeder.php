<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $actions = [
            'viewAny', 'view', 'create', 'update', 'delete', 'forceDelete', 'restore',
        ];

        $resources = [
            'permission', 'role', 'user', 'staff', 'student'
        ];

        DB::table('permissions')->truncate();
        collect($resources)->crossJoin($actions)->map(function ($set){
            return implode('.', $set);
        })->each(function ($permission){
            Permission::create(['name' => $permission]);
        });
    }
}
