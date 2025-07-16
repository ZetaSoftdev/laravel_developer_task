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
use App\Models\ClassTeacher;
use App\Models\ClassSection;
use App\Models\SubjectTeacher;
use App\Models\School;

// Get the school from the main database
$school = School::on('mysql')->first();
if (!$school) {
    echo "No school found in the main database!\n";
    exit;
}

echo "=== SCHOOL INFO ===\n";
echo "School: {$school->name}\n";
echo "Database: {$school->database_name}\n";

// Switch to school database
Config::set('database.connections.school.database', $school->database_name);
DB::purge('school');
DB::connection('school')->reconnect();
DB::setDefaultConnection('school');

echo "\n=== SWITCHED TO SCHOOL DATABASE ===\n";
echo "Current database: " . DB::connection()->getDatabaseName() . "\n";

// Now check if the school tables exist
$checkTables = ['users', 'class_teachers', 'class_sections', 'subject_teachers', 'students', 'attendances'];
echo "\n=== CHECKING SCHOOL TABLES ===\n";
foreach ($checkTables as $table) {
    if (DB::getSchemaBuilder()->hasTable($table)) {
        echo "✓ $table exists\n";
    } else {
        echo "✗ $table does not exist\n";
    }
}

// Get teachers from the school database
$teachers = User::whereHas('roles', function($q) {
    $q->where('name', 'Teacher');
})->get();

echo "\n=== TEACHERS IN SCHOOL DATABASE ===\n";
foreach ($teachers as $teacher) {
    echo "Teacher ID: {$teacher->id}, Name: {$teacher->full_name}, Email: {$teacher->email}\n";
}

// Check class_teachers table
if (DB::getSchemaBuilder()->hasTable('class_teachers')) {
    echo "\n=== CLASS TEACHERS TABLE ===\n";
    $classTeachers = ClassTeacher::with('teacher', 'class_section.class', 'class_section.section')->get();
    foreach ($classTeachers as $ct) {
        echo "Class Teacher ID: {$ct->id}, Teacher: {$ct->teacher->full_name} (ID: {$ct->teacher_id}), Class: {$ct->class_section->class->name} {$ct->class_section->section->name}\n";
    }
}

// Check class_sections
if (DB::getSchemaBuilder()->hasTable('class_sections')) {
    echo "\n=== CLASS SECTIONS ===\n";
    $classSections = ClassSection::with('class', 'section', 'medium')->get();
    foreach ($classSections as $cs) {
        echo "Class Section ID: {$cs->id}, Class: {$cs->class->name} {$cs->section->name}, Medium: {$cs->medium->name}\n";
    }
    
    // Test the scope with a teacher
    if ($teachers->count() > 0) {
        $teacher = $teachers->first();
        echo "\n=== TESTING SCOPE WITH TEACHER: {$teacher->full_name} (ID: {$teacher->id}) ===\n";
        
        // Simulate session
        Session::put('school_database_name', $school->database_name);
        
        // Simulate login
        Auth::login($teacher);
        
        // Test the ClassTeacher scope
        $scopedSections = ClassSection::ClassTeacher()->with('class', 'section', 'medium')->get();
        echo "ClassTeacher scope returned " . $scopedSections->count() . " sections:\n";
        foreach ($scopedSections as $section) {
            echo "- {$section->class->name} {$section->section->name} (ID: {$section->id})\n";
        }
        
        // Test the manual query from the view method
        $teacherId = $teacher->id;
        $manualSections = ClassSection::where(function($query) use ($teacherId) {
            $query->whereHas('class_teachers', function($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })
            ->orWhereHas('subject_teachers', function($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            });
        })->with('class', 'section', 'medium')->get();
        
        echo "\nManual query returned " . $manualSections->count() . " sections:\n";
        foreach ($manualSections as $section) {
            echo "- {$section->class->name} {$section->section->name} (ID: {$section->id})\n";
        }
    }
}
