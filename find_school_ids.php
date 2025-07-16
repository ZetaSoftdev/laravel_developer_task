<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== FINDING ALL SCHOOL IDs ===\n\n";

// Get all schools from main database
$schools = \App\Models\School::all();

echo "Total schools found: {$schools->count()}\n\n";

foreach ($schools as $school) {
    echo "School ID: {$school->id}\n";
    echo "Name: {$school->name}\n";
    echo "Admin ID: {$school->admin_id}\n";
    echo "Database: {$school->database_name}\n";
    echo "Status: " . ($school->status ? 'Active' : 'Inactive') . "\n";
    echo "Type: {$school->type}\n";
    echo "Domain: " . ($school->domain ?: 'None') . "\n";
    echo "Code: " . ($school->code ?: 'None') . "\n";
    echo "Created: {$school->created_at}\n";
    
    // Get admin user details
    if ($school->admin_id) {
        $admin = \App\Models\User::find($school->admin_id);
        if ($admin) {
            echo "Admin: {$admin->email} ({$admin->first_name} {$admin->last_name})\n";
        } else {
            echo "Admin: User not found (ID: {$school->admin_id})\n";
        }
    } else {
        echo "Admin: Not assigned\n";
    }
    
    echo "---\n";
}

// Also check if there are any schools in school-specific databases
echo "\n=== CHECKING SCHOOL-SPECIFIC DATABASES ===\n";

foreach ($schools as $school) {
    if ($school->database_name) {
        try {
            // Set connection to school database
            \Illuminate\Support\Facades\Config::set('database.connections.school.database', $school->database_name);
            \Illuminate\Support\Facades\DB::purge('school');
            \Illuminate\Support\Facades\DB::connection('school')->reconnect();
            \Illuminate\Support\Facades\DB::setDefaultConnection('school');
            
            // Check if school exists in its own database
            $schoolInOwnDb = \App\Models\School::on('school')->find($school->id);
            
            echo "School {$school->id} ({$school->name}):\n";
            if ($schoolInOwnDb) {
                echo "  ✅ Exists in own database (ID: {$schoolInOwnDb->id})\n";
                echo "  Database Name: {$schoolInOwnDb->database_name}\n";
            } else {
                echo "  ❌ Does not exist in own database\n";
            }
            
        } catch (\Exception $e) {
            echo "School {$school->id}: ❌ Error accessing database - {$e->getMessage()}\n";
        }
    } else {
        echo "School {$school->id}: ❌ No database name set\n";
    }
}

// Reset to main database
\Illuminate\Support\Facades\DB::setDefaultConnection('mysql');
