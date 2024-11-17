<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat Role
        $admin = Role::create(['name' => 'admin']);
        $santri = Role::create(['name' => 'santri']); 

        // Membuat Permission
        $manage = Permission::create(['name' => 'manage']); 
        $view = Permission::create(['name' => 'view']); 

        // Memberikan Permission ke Role
        $admin->givePermissionTo([$manage, $view]); 
        $santri->givePermissionTo($view); 
    }
}
