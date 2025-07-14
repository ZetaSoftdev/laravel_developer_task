<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CHECKING MISSING PERMISSIONS FOR SCHOOL FEATURES ===\n\n";

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

// Expected permissions for common school features
$expectedPermissions = [
    // Assignment Management
    'assignment-list', 'assignment-create', 'assignment-edit', 'assignment-delete',
    
    // Lesson Management  
    'lesson-list', 'lesson-create', 'lesson-edit', 'lesson-delete',
    'topic-list', 'topic-create', 'topic-edit', 'topic-delete',
    
    // Exam Management
    'exam-list', 'exam-create', 'exam-edit', 'exam-delete',
    'exam-timetable-list', 'exam-timetable-create', 'exam-timetable-edit', 'exam-timetable-delete',
    'exam-upload-marks', 'exam-result', 'exam-result-edit',
    
    // Student Management
    'student-list', 'student-create', 'student-edit', 'student-delete',
    'student-reset-password', 'student-change-password',
    
    // Class Management
    'class-list', 'class-create', 'class-edit', 'class-delete',
    'class-section-list', 'class-section-create', 'class-section-edit', 'class-section-delete',
    
    // Teacher Management
    'teacher-list', 'teacher-create', 'teacher-edit', 'teacher-delete',
    
    // Timetable Management
    'timetable-list', 'timetable-create', 'timetable-edit', 'timetable-delete',
    
    // Attendance Management
    'attendance-list', 'attendance-create', 'attendance-edit', 'attendance-delete',
    
    // Fee Management
    'fees-list', 'fees-create', 'fees-edit', 'fees-delete',
    'fees-paid', 'fees-config',
    
    // Announcement Management
    'announcement-list', 'announcement-create', 'announcement-edit', 'announcement-delete',
    
    // Holiday Management
    'holiday-list', 'holiday-create', 'holiday-edit', 'holiday-delete',
    
    // Online Exam Management
    'online-exam-list', 'online-exam-create', 'online-exam-edit', 'online-exam-delete',
    'online-exam-questions-list', 'online-exam-questions-create', 'online-exam-questions-edit', 'online-exam-questions-delete',
    
    // School Settings
    'school-setting-manage', 'school-web-settings',
    
    // Zoom Features
    'zoom-settings', 'zoom-class-list', 'zoom-class-create', 'zoom-class-edit', 'zoom-class-delete', 'zoom-attendance'
];

// Get School Admin role
$schoolAdminRole = \Spatie\Permission\Models\Role::on('school')
    ->where('name', 'School Admin')
    ->where('school_id', $school->id)
    ->first();

if (!$schoolAdminRole) {
    echo "School Admin role not found!\n";
    exit;
}

echo "School Admin Role ID: {$schoolAdminRole->id}\n";

// Get current permissions
$currentPermissions = $schoolAdminRole->permissions->pluck('name')->toArray();
echo "Current permissions count: " . count($currentPermissions) . "\n\n";

// Check for missing permissions
$missingPermissions = array_diff($expectedPermissions, $currentPermissions);

if (count($missingPermissions) > 0) {
    echo "=== MISSING PERMISSIONS ===\n";
    foreach ($missingPermissions as $permission) {
        echo "❌ {$permission}\n";
    }
    echo "\nTotal missing: " . count($missingPermissions) . "\n\n";
} else {
    echo "✅ All expected permissions are assigned!\n\n";
}

// Check specific features that might be missing
echo "=== CHECKING SPECIFIC FEATURE PERMISSIONS ===\n";

$featureCategories = [
    'Assignment' => ['assignment-list', 'assignment-create', 'assignment-edit', 'assignment-delete'],
    'Lesson' => ['lesson-list', 'lesson-create', 'lesson-edit', 'lesson-delete'],
    'Exam' => ['exam-list', 'exam-create', 'exam-edit', 'exam-delete'],
    'Online Exam' => ['online-exam-list', 'online-exam-create', 'online-exam-edit', 'online-exam-delete'],
    'Zoom' => ['zoom-settings', 'zoom-class-list', 'zoom-class-create', 'zoom-class-edit', 'zoom-class-delete'],
    'Fees' => ['fees-list', 'fees-create', 'fees-edit', 'fees-delete'],
];

foreach ($featureCategories as $feature => $permissions) {
    $hasAll = true;
    $missing = [];
    
    foreach ($permissions as $permission) {
        if (!in_array($permission, $currentPermissions)) {
            $hasAll = false;
            $missing[] = $permission;
        }
    }
    
    if ($hasAll) {
        echo "✅ {$feature}: All permissions available\n";
    } else {
        echo "❌ {$feature}: Missing " . implode(', ', $missing) . "\n";
    }
}

// Reset to main database
\Illuminate\Support\Facades\DB::setDefaultConnection('mysql');
