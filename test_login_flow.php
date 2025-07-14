<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\School;

// Get the school from the main database
$school = School::on('mysql')->first();
if (!$school) {
    echo "No school found in the main database!\n";
    exit;
}

echo "=== SCHOOL LOGIN TEST ===\n";
echo "School: {$school->name}\n";
echo "School Code: {$school->code}\n";
echo "Database: {$school->database_name}\n";

// Simulate the login process
echo "\n=== SIMULATING LOGIN PROCESS ===\n";

// Step 1: Set the dynamic database connection (from LoginController)
Config::set('database.connections.school.database', $school->database_name);
DB::purge('school');
DB::connection('school')->reconnect();
DB::setDefaultConnection('school');

echo "✓ Switched to school database: " . DB::connection()->getDatabaseName() . "\n";

// Step 2: Get a teacher from the school database
$teacher = User::whereHas('roles', function($q) {
    $q->where('name', 'Teacher');
})->first();

if (!$teacher) {
    echo "✗ No teacher found in the school database!\n";
    exit;
}

echo "✓ Found teacher: {$teacher->full_name} (ID: {$teacher->id})\n";

// Step 3: Simulate the session setup (from LoginController)
Session::put('school_database_name', $school->database_name);
Auth::login($teacher);

echo "✓ Logged in teacher and set session\n";

// Step 4: Test the attendance controller logic
echo "\n=== TESTING ATTENDANCE CONTROLLER LOGIC ===\n";

// Test the index method logic
$indexClassSections = \App\Models\ClassSection::ClassTeacher()->with('class', 'class.stream', 'section', 'medium')->get();
echo "Index method (ClassTeacher scope): " . $indexClassSections->count() . " sections\n";

// Test the view method logic
if (Auth::user()->hasRole('Teacher')) {
    $teacherId = Auth::user()->id;
    $viewClassSections = \App\Models\ClassSection::where(function($query) use ($teacherId) {
        $query->whereHas('class_teachers', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })
        ->orWhereHas('subject_teachers', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        });
    })->with('class', 'class.stream', 'section', 'medium')->get();
    
    echo "View method (manual query): " . $viewClassSections->count() . " sections\n";
    
    foreach ($viewClassSections as $section) {
        echo "- Section ID: {$section->id}, Name: {$section->full_name}\n";
    }
} else {
    echo "✗ User is not a teacher\n";
}

echo "\n=== TESTING COMPLETE ===\n";
echo "The attendance view should show " . $viewClassSections->count() . " class sections in the dropdown.\n";
echo "Teacher can add attendance for these sections.\n";
