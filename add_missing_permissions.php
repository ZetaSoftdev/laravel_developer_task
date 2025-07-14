<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ADDING MISSING PERMISSIONS TO SCHOOL ADMIN ===\n\n";

// Connect to SSCA school database
$school = \App\Models\School::where('name', 'SSCA')->first();

if (!$school) {
    echo "SSCA school not found!\n";
    exit;
}

echo "School: {$school->name}\n";
echo "Database: {$school->database_name}\n\n";

// Set connection to school database
\Illuminate\Support\Facades\Config::set('database.connections.school.database', $school->database_name);
\Illuminate\Support\Facades\DB::purge('school');
\Illuminate\Support\Facades\DB::connection('school')->reconnect();
\Illuminate\Support\Facades\DB::setDefaultConnection('school');

// Missing permissions that need to be added
$missingPermissions = [
    // Assignment Management
    'assignment-list', 'assignment-create', 'assignment-edit', 'assignment-delete',
    
    // Lesson Management
    'lesson-list', 'lesson-create', 'lesson-edit', 'lesson-delete',
    'topic-list', 'topic-create', 'topic-edit', 'topic-delete',
    
    // Attendance Management (missing ones)
    'attendance-create', 'attendance-edit', 'attendance-delete',
    
    // Exam Management (missing ones)
    'exam-timetable-edit', 'exam-upload-marks',
    
    // Zoom Features
    'zoom-settings', 'zoom-class-list', 'zoom-class-create', 'zoom-class-edit', 'zoom-class-delete', 'zoom-attendance'
];

echo "Permissions to add: " . count($missingPermissions) . "\n\n";

// First, create the permissions if they don't exist
echo "=== CREATING PERMISSIONS ===\n";
foreach ($missingPermissions as $permissionName) {
    $permission = \Spatie\Permission\Models\Permission::on('school')
        ->where('name', $permissionName)
        ->first();
    
    if (!$permission) {
        \Spatie\Permission\Models\Permission::create([
            'name' => $permissionName,
            'guard_name' => 'web'
        ]);
        echo "✅ Created permission: {$permissionName}\n";
    } else {
        echo "📋 Permission already exists: {$permissionName}\n";
    }
}

// Get School Admin role
$schoolAdminRole = \Spatie\Permission\Models\Role::on('school')
    ->where('name', 'School Admin')
    ->where('school_id', $school->id)
    ->first();

if (!$schoolAdminRole) {
    echo "❌ School Admin role not found!\n";
    exit;
}

echo "\n=== ASSIGNING PERMISSIONS TO SCHOOL ADMIN ===\n";
echo "School Admin Role ID: {$schoolAdminRole->id}\n";

// Add missing permissions to School Admin role
foreach ($missingPermissions as $permissionName) {
    try {
        if (!$schoolAdminRole->hasPermissionTo($permissionName)) {
            $schoolAdminRole->givePermissionTo($permissionName);
            echo "✅ Added permission: {$permissionName}\n";
        } else {
            echo "📋 Already has permission: {$permissionName}\n";
        }
    } catch (\Exception $e) {
        echo "❌ Error adding {$permissionName}: " . $e->getMessage() . "\n";
    }
}

// Verify the changes
echo "\n=== VERIFICATION ===\n";
$schoolAdminRole = $schoolAdminRole->fresh(); // Refresh the role
$newPermissionCount = $schoolAdminRole->permissions->count();
echo "School Admin now has {$newPermissionCount} permissions\n";

// Check if all missing permissions are now added
$currentPermissions = $schoolAdminRole->permissions->pluck('name')->toArray();
$stillMissing = array_diff($missingPermissions, $currentPermissions);

if (count($stillMissing) == 0) {
    echo "✅ All permissions successfully added!\n";
} else {
    echo "❌ Still missing " . count($stillMissing) . " permissions:\n";
    foreach ($stillMissing as $missing) {
        echo "  - {$missing}\n";
    }
}

// Reset to main database
\Illuminate\Support\Facades\DB::setDefaultConnection('mysql');

echo "\n=== COMPLETED ===\n";
echo "Please refresh your school dashboard to see the new features!\n";
