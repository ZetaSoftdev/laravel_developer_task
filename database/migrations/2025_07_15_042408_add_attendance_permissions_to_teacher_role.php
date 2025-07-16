<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Define attendance permissions
        $attendancePermissions = [
            'attendance-list',
            'attendance-create', 
            'attendance-edit',
            'class-teacher'
        ];

        // Create permissions if they don't exist
        foreach ($attendancePermissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web'
            ]);
        }

        // Assign permissions to Teacher role
        $teacherRole = Role::where('name', 'Teacher')->first();
        if ($teacherRole) {
            $teacherRole->givePermissionTo($attendancePermissions);
        }

        // Also ensure School Admin has these permissions
        $schoolAdminRole = Role::where('name', 'School Admin')->first();
        if ($schoolAdminRole) {
            $schoolAdminRole->givePermissionTo($attendancePermissions);
        }

        // Ensure Super Admin has these permissions
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($attendancePermissions);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove attendance permissions from Teacher role
        $teacherRole = Role::where('name', 'Teacher')->first();
        if ($teacherRole) {
            $teacherRole->revokePermissionTo([
                'attendance-list',
                'attendance-create', 
                'attendance-edit',
                'class-teacher'
            ]);
        }
    }
};
