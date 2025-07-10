<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\School;
use App\Models\Subscription;
use App\Models\SubscriptionFeature;
use App\Models\ZoomSetting;
use App\Services\SchoolDataService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FixZoomVisibilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->command->info('Starting Zoom visibility fix...');

        // 1. Make sure Zoom feature exists and is enabled
        $zoomFeature = Feature::firstOrCreate(
            ['name' => 'Zoom Online Classes'],
            [
                'is_default' => 1, // Set as default
                'status' => 1,
                'required_vps' => 0
            ]
        );

        // 2. Create Zoom permissions if they don't exist
        $permissions = [
            'zoom-settings',
            'zoom-class-list',
            'zoom-class-create',
            'zoom-class-edit',
            'zoom-class-delete',
            'zoom-attendance',
        ];

        // Create permissions in main database
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 3. Get all schools
        $schools = School::all();
        
        foreach ($schools as $school) {
            $this->command->info("Processing school: {$school->name}");

            try {
                // 4. Assign Zoom feature to school's subscription
                $subscription = Subscription::where('school_id', $school->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($subscription) {
                    SubscriptionFeature::firstOrCreate([
                        'subscription_id' => $subscription->id,
                        'feature_id' => $zoomFeature->id
                    ]);
                }

                // 5. Switch to school database and run migrations
                Config::set('database.connections.school.database', $school->database_name);
                DB::purge('school');
                DB::connection('school')->reconnect();
                DB::setDefaultConnection('school');

                // Run Zoom migrations
                Artisan::call('migrate', [
                    '--path' => 'database/migrations/2025_07_10_223815_create_zoom_settings_table.php',
                    '--database' => 'school',
                    '--force' => true
                ]);
                
                Artisan::call('migrate', [
                    '--path' => 'database/migrations/2025_07_10_223834_create_zoom_online_classes_table.php',
                    '--database' => 'school',
                    '--force' => true
                ]);
                
                Artisan::call('migrate', [
                    '--path' => 'database/migrations/2025_07_10_223846_create_zoom_attendances_table.php',
                    '--database' => 'school',
                    '--force' => true
                ]);

                // 6. Create default Zoom settings if not exists
                ZoomSetting::firstOrCreate(
                    ['school_id' => $school->id],
                    [
                        'is_active' => 1
                    ]
                );

                // Create permissions in school database
                foreach ($permissions as $permission) {
                    Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
                }

                // 7. Update roles and permissions
                $adminRole = Role::where('name', 'School Admin')
                    ->where('school_id', $school->id)
                    ->first();

                $teacherRole = Role::where('name', 'Teacher')
                    ->where('school_id', $school->id)
                    ->first();

                if ($adminRole) {
                    foreach ($permissions as $permission) {
                        $adminRole->givePermissionTo($permission);
                    }
                    $this->command->info("Updated School Admin permissions for school: {$school->name}");
                }

                if ($teacherRole) {
                    $teacherPermissions = [
                        'zoom-class-list',
                        'zoom-class-create',
                        'zoom-class-edit',
                        'zoom-class-delete',
                        'zoom-attendance',
                    ];
                    foreach ($teacherPermissions as $permission) {
                        $teacherRole->givePermissionTo($permission);
                    }
                    $this->command->info("Updated Teacher permissions for school: {$school->name}");
                }
            } catch (\Exception $e) {
                $this->command->error("Error processing school {$school->name}: " . $e->getMessage());
                continue;
            }
        }

        // Switch back to main database
        DB::setDefaultConnection('mysql');
        Config::set('database.connections.mysql.database', env('DB_DATABASE'));
        DB::purge('mysql');
        DB::connection('mysql')->reconnect();

        $this->command->info('Zoom visibility fix completed successfully!');
    }
} 