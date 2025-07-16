<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CHECKING SCHOOL DATABASES FOR TEACHER ===\n\n";

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
        
        // Check for users in this school database
        $users = \App\Models\User::on('school')->get();
        echo "  Users in school database: {$users->count()}\n";
        
        foreach ($users as $user) {
            echo "    User ID: {$user->id} - {$user->email} ({$user->first_name} {$user->last_name})\n";
            
            $roles = $user->roles;
            if ($roles->count() > 0) {
                echo "      Roles: ";
                foreach ($roles as $role) {
                    echo "{$role->name} (ID: {$role->id}) ";
                }
                echo "\n";
            } else {
                echo "      NO ROLES ASSIGNED!\n";
            }
            
            // Check if this is a teacher
            if ($user->hasRole('Teacher')) {
                echo "      >>> THIS IS A TEACHER <<<\n";
                
                // Check teacher permissions
                $permissions = $user->getAllPermissions();
                echo "      Teacher permissions ({$permissions->count()}):\n";
                foreach ($permissions->take(10) as $permission) {
                    echo "        - {$permission->name}\n";
                }
                if ($permissions->count() > 10) {
                    echo "        ... and " . ($permissions->count() - 10) . " more\n";
                }
            }
        }
        
        // Check roles in this school database
        echo "  Roles in school database:\n";
        $schoolRoles = \Spatie\Permission\Models\Role::on('school')->get();
        foreach ($schoolRoles as $role) {
            echo "    - {$role->name} (ID: {$role->id}) - {$role->permissions->count()} permissions\n";
        }
        
    } catch (\Exception $e) {
        echo "  Error connecting to school database: " . $e->getMessage() . "\n";
    }
    
    echo "  ---\n";
}

// Reset to main database
\Illuminate\Support\Facades\DB::setDefaultConnection('mysql');
