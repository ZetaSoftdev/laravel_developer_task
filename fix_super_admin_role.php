<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== FIXING SUPER ADMIN ROLE ASSIGNMENT ===\n";

// Get the user
$user = \App\Models\User::find(1);
if (!$user) {
    echo "User not found!\n";
    exit;
}

echo "Current user: {$user->email}\n";

// Get the correct Super Admin role (the one with system permissions)
$correctSuperAdminRole = \Spatie\Permission\Models\Role::where('name', 'Super Admin')
    ->where('custom_role', 0)
    ->where('editable', 0)
    ->first();

if (!$correctSuperAdminRole) {
    echo "Correct Super Admin role not found!\n";
    exit;
}

echo "Correct Super Admin role ID: {$correctSuperAdminRole->id}\n";
echo "Permissions count: {$correctSuperAdminRole->permissions->count()}\n";

// Remove all current roles and assign the correct one
$user->syncRoles([$correctSuperAdminRole->id]);

echo "Successfully reassigned user to correct Super Admin role!\n";

// Verify the change
echo "\n=== VERIFICATION ===\n";
$user = \App\Models\User::find(1); // Refresh user
echo "User now has roles:\n";
foreach ($user->roles as $role) {
    echo "  - {$role->name} (ID: {$role->id}) - {$role->permissions->count()} permissions\n";
}

// Clean up - remove the old duplicate Super Admin role
echo "\n=== CLEANING UP DUPLICATE ROLE ===\n";
$duplicateRole = \Spatie\Permission\Models\Role::where('name', 'Super Admin')
    ->where('custom_role', 1)
    ->where('editable', 1)
    ->first();

if ($duplicateRole) {
    echo "Removing duplicate Super Admin role ID: {$duplicateRole->id}\n";
    $duplicateRole->delete();
    echo "Duplicate role removed successfully!\n";
} else {
    echo "No duplicate role found.\n";
}
