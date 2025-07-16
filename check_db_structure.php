<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== DATABASE TABLES ===\n";
$tables = DB::select('SHOW TABLES');
foreach ($tables as $table) {
    $tableName = array_values((array)$table)[0];
    echo "- $tableName\n";
}

echo "\n=== CHECKING SPECIFIC TABLES ===\n";
$checkTables = ['users', 'schools', 'class_teachers', 'class_sections', 'subject_teachers', 'students', 'attendances'];
foreach ($checkTables as $table) {
    if (Schema::hasTable($table)) {
        echo "✓ $table exists\n";
    } else {
        echo "✗ $table does not exist\n";
    }
}

echo "\n=== CHECKING CURRENT CONNECTION ===\n";
echo "Default connection: " . config('database.default') . "\n";
echo "Current connection: " . DB::connection()->getDatabaseName() . "\n";

// Check schools table
if (Schema::hasTable('schools')) {
    echo "\n=== SCHOOLS IN DATABASE ===\n";
    $schools = DB::table('schools')->get();
    foreach ($schools as $school) {
        echo "School ID: {$school->id}, Name: {$school->name}, Database: {$school->database_name}\n";
    }
}
