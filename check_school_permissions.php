<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CHECKING SCHOOL PERMISSIONS ===\n\n";

// Get all schools
$schools = \App\Models\School::all();

foreach ($schools as $school) {
    echo "School: {$school->name} (ID: {$school->id})\n";
    echo "Database: {$school->database_name}\n";
    
    if (!$school->database_name) {
        echo "  No database name set for this school\n";
        echo "  ---\n";
        continue;
    }
    
    try {
        // Set the database connection to the school database
        \Illuminate\Support\Facades\Config::set('database.connections.school.database', $school->database_name);
        \Illuminate\Support\Facades\DB::purge('school');
        \Illuminate\Support\Facades\DB::connection('school')->reconnect();
        \Illuminate\Support\Facades\DB::setDefaultConnection('school');
        
        // Check all roles in this school database
        echo "\n=== ROLES IN SCHOOL DATABASE ===\n";
        $schoolRoles = \Spatie\Permission\Models\Role::on('school')->get();
        
        foreach ($schoolRoles as $role) {
            echo "Role: {$role->name} (ID: {$role->id})\n";
            echo "  School ID: " . ($role->school_id ?: 'None') . "\n";
            echo "  Custom Role: " . ($role->custom_role ? 'Yes' : 'No') . "\n";
            echo "  Editable: " . ($role->editable ? 'Yes' : 'No') . "\n";
            
            $permissions = $role->permissions;
            echo "  Permissions ({$permissions->count()}):\n";
            
            if ($permissions->count() > 0) {
                // Group permissions by category
                $categorizedPermissions = [];
                foreach ($permissions as $permission) {
                    $parts = explode('-', $permission->name);
                    $category = $parts[0];
                    if (!isset($categorizedPermissions[$category])) {
                        $categorizedPermissions[$category] = [];
                    }
                    $categorizedPermissions[$category][] = $permission->name;
                }
                
                foreach ($categorizedPermissions as $category => $perms) {
                    echo "    {$category}: " . implode(', ', $perms) . "\n";
                }
            } else {
                echo "    No permissions assigned\n";
            }
            echo "  ---\n";
        }
        
        // Check users and their permissions
        echo "\n=== USERS AND THEIR PERMISSIONS ===\n";
        $users = \App\Models\User::on('school')->with('roles')->get();
        
        foreach ($users as $user) {
            echo "User: {$user->email} ({$user->first_name} {$user->last_name})\n";
            
            if ($user->roles->count() > 0) {
                foreach ($user->roles as $role) {
                    echo "  Role: {$role->name}\n";
                    
                    // Get all permissions for this user (including role permissions)
                    $allUserPermissions = $user->getAllPermissions();
                    echo "  Total Permissions: {$allUserPermissions->count()}\n";
                    
                    if ($allUserPermissions->count() > 0) {
                        // Show first 10 permissions
                        echo "  Sample Permissions:\n";
                        foreach ($allUserPermissions->take(10) as $permission) {
                            echo "    - {$permission->name}\n";
                        }
                        if ($allUserPermissions->count() > 10) {
                            echo "    ... and " . ($allUserPermissions->count() - 10) . " more\n";
                        }
                    }
                }
            } else {
                echo "  No roles assigned\n";
            }
            echo "  ---\n";
        }
        
    } catch (\Exception $e) {
        echo "  Error connecting to school database: " . $e->getMessage() . "\n";
    }
    
    echo "\n==========================================\n\n";
}

// Reset to main database
\Illuminate\Support\Facades\DB::setDefaultConnection('mysql');

echo "\n=== MAIN DATABASE PERMISSIONS ===\n";
echo "Total permissions in main DB: " . \Spatie\Permission\Models\Permission::count() . "\n";
echo "Total roles in main DB: " . \Spatie\Permission\Models\Role::count() . "\n";
