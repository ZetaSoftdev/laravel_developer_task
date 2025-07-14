<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ClassTeacher;
use App\Models\ClassSection;
use App\Models\SubjectTeacher;

// First let's check what users exist with Teacher role
$teachers = User::whereHas('roles', function($q) {
    $q->where('name', 'Teacher');
})->get();

echo "=== TEACHERS IN SYSTEM ===\n";
foreach ($teachers as $teacher) {
    echo "Teacher ID: {$teacher->id}, Name: {$teacher->full_name}, Email: {$teacher->email}\n";
}

// Let's check class_teachers table
echo "\n=== CLASS TEACHERS TABLE ===\n";
$classTeachers = ClassTeacher::with('teacher', 'class_section.class', 'class_section.section')->get();
foreach ($classTeachers as $ct) {
    echo "Class Teacher ID: {$ct->id}, Teacher: {$ct->teacher->full_name} (ID: {$ct->teacher_id}), Class: {$ct->class_section->class->name} {$ct->class_section->section->name}\n";
}

// Let's check subject_teachers table
echo "\n=== SUBJECT TEACHERS TABLE ===\n";
$subjectTeachers = SubjectTeacher::with('teacher', 'class_section.class', 'class_section.section')->get();
foreach ($subjectTeachers as $st) {
    echo "Subject Teacher ID: {$st->id}, Teacher: {$st->teacher->full_name} (ID: {$st->teacher_id}), Class: {$st->class_section->class->name} {$st->class_section->section->name}\n";
}

// Let's check class_sections
echo "\n=== CLASS SECTIONS ===\n";
$classSections = ClassSection::with('class', 'section', 'medium')->get();
foreach ($classSections as $cs) {
    echo "Class Section ID: {$cs->id}, Class: {$cs->class->name} {$cs->section->name}, Medium: {$cs->medium->name}\n";
}

// Let's test the scope with a specific teacher
if ($teachers->count() > 0) {
    $teacher = $teachers->first();
    echo "\n=== TESTING SCOPE WITH TEACHER: {$teacher->full_name} (ID: {$teacher->id}) ===\n";
    
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
