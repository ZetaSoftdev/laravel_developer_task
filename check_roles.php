<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== USER ROLES CHECK ===\n\n";

// Check all users and their roles
$users = \App\Models\User::all();
foreach ($users as $user) {
    echo "User ID: {$user->id} - {$user->email} ({$user->first_name} {$user->last_name})\n";
    
    $roles = $user->roles;
    if ($roles->count() > 0) {
        echo "  Roles: ";
        foreach ($roles as $role) {
            echo "{$role->name} (ID: {$role->id}) ";
        }
        echo "\n";
    } else {
        echo "  NO ROLES ASSIGNED!\n";
    }
    
    echo "  School ID: " . ($user->school_id ?: 'None') . "\n";
    echo "  ---\n";
}

echo "\n=== ALL AVAILABLE ROLES ===\n";
$allRoles = \Spatie\Permission\Models\Role::all();
foreach ($allRoles as $role) {
    echo "Role ID: {$role->id} - {$role->name}\n";
    echo "  Guard: {$role->guard_name}\n";
    echo "  School ID: " . ($role->school_id ?: 'None') . "\n";
    echo "  ---\n";
}

echo "\n=== PERMISSIONS FOR SUPER ADMIN ROLE ===\n";
$superAdminRole = \Spatie\Permission\Models\Role::where('name', 'Super Admin')->first();
if ($superAdminRole) {
    echo "Super Admin Role ID: {$superAdminRole->id}\n";
    $permissions = $superAdminRole->permissions;
    if ($permissions->count() > 0) {
        echo "Permissions:\n";
        foreach ($permissions as $permission) {
            echo "  - {$permission->name}\n";
        }
    } else {
        echo "NO PERMISSIONS ASSIGNED TO SUPER ADMIN ROLE!\n";
    }
} else {
    echo "SUPER ADMIN ROLE NOT FOUND!\n";
}

echo "\n=== ALL PERMISSIONS IN SYSTEM ===\n";
$allPermissions = \Spatie\Permission\Models\Permission::all();
echo "Total permissions: {$allPermissions->count()}\n";
foreach ($allPermissions as $permission) {
    echo "- {$permission->name}\n";
}

echo "\n=== MODEL HAS ROLES CHECK ===\n";
$modelHasRoles = \DB::table('model_has_roles')->get();
foreach ($modelHasRoles as $relation) {
    echo "Model: {$relation->model_type} ID: {$relation->model_id} -> Role ID: {$relation->role_id}\n";
}
