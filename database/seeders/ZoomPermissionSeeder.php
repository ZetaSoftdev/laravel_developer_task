<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ZoomPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Zoom permissions
        $permissions = [
            'zoom-settings',
            'zoom-class-list',
            'zoom-class-create',
            'zoom-class-edit',
            'zoom-class-delete',
            'zoom-attendance',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permissions to roles
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $adminRole = Role::where('name', 'School Admin')->first();
        $teacherRole = Role::where('name', 'Teacher')->first();

        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
        }

        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        if ($teacherRole) {
            $teacherRole->givePermissionTo([
                'zoom-class-list',
                'zoom-class-create',
                'zoom-class-edit',
                'zoom-class-delete',
                'zoom-attendance',
            ]);
        }

        $this->command->info('Zoom permissions created and assigned successfully!');
    }
}