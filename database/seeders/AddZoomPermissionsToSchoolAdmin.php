<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AddZoomPermissionsToSchoolAdmin extends Seeder
{
    public function run()
    {
        $role = Role::where('name', 'School Admin')->first();
        
        if ($role) {
            $permissions = [
                'zoom-class-list',
                'zoom-settings',
                'zoom-class-create',
                'zoom-class-edit',
                'zoom-class-delete',
                'zoom-attendance'
            ];
            
            foreach ($permissions as $permission) {
                $perm = Permission::where('name', $permission)->first();
                if ($perm && !$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            }
        }
    }
} 