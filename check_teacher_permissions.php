<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get Teacher role
$teacherRole = Role::where('name', 'Teacher')->first();

if ($teacherRole) {
    echo "Teacher Role Permissions:\n";
    echo "========================\n";
    
    $permissions = $teacherRole->permissions->pluck('name')->toArray();
    
    // Filter attendance and class-teacher related permissions
    $relevantPermissions = array_filter($permissions, function($p) {
        return str_contains($p, 'attendance') || str_contains($p, 'class-teacher');
    });
    
    if (empty($relevantPermissions)) {
        echo "No attendance or class-teacher permissions found for Teacher role.\n";
    } else {
        foreach ($relevantPermissions as $permission) {
            echo "- " . $permission . "\n";
        }
    }
    
    echo "\nAll Teacher Permissions:\n";
    echo "========================\n";
    foreach ($permissions as $permission) {
        echo "- " . $permission . "\n";
    }
} else {
    echo "Teacher role not found.\n";
}

// Check if permissions exist in the system
echo "\n\nSystem Permissions (attendance/class-teacher):\n";
echo "==============================================\n";

$systemPermissions = Permission::where('name', 'like', '%attendance%')
    ->orWhere('name', 'like', '%class-teacher%')
    ->pluck('name')
    ->toArray();

if (empty($systemPermissions)) {
    echo "No attendance or class-teacher permissions exist in the system.\n";
} else {
    foreach ($systemPermissions as $permission) {
        echo "- " . $permission . "\n";
    }
}
