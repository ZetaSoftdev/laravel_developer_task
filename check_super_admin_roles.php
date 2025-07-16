<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ALL SUPER ADMIN ROLES ===\n";
$superAdminRoles = \Spatie\Permission\Models\Role::where('name', 'Super Admin')->get();

foreach ($superAdminRoles as $role) {
    echo "Super Admin Role ID: {$role->id}\n";
    echo "  Guard: {$role->guard_name}\n";
    echo "  School ID: " . ($role->school_id ?: 'None') . "\n";
    echo "  Custom Role: " . ($role->custom_role ?: 'No') . "\n";
    echo "  Editable: " . ($role->editable ?: 'No') . "\n";
    
    $permissions = $role->permissions;
    echo "  Permissions Count: {$permissions->count()}\n";
    if ($permissions->count() > 0) {
        echo "  First few permissions:\n";
        foreach ($permissions->take(5) as $permission) {
            echo "    - {$permission->name}\n";
        }
        if ($permissions->count() > 5) {
            echo "    ... and " . ($permissions->count() - 5) . " more\n";
        }
    }
    echo "  ---\n";
}

echo "\n=== CURRENT USER ROLE ASSIGNMENT ===\n";
$user = \App\Models\User::find(1);
if ($user) {
    echo "User: {$user->email}\n";
    echo "Current roles:\n";
    foreach ($user->roles as $role) {
        echo "  - {$role->name} (ID: {$role->id})\n";
    }
}
