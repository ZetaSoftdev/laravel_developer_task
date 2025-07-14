<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\School;
use App\Models\Subscription;
use App\Models\SubscriptionFeature;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssignZoomFeatureToSchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find the Zoom feature
        $zoomFeature = Feature::where('name', 'Zoom Online Classes')->first();
        
        if (!$zoomFeature) {
            $this->command->error('Zoom Online Classes feature not found. Please run AddZoomFeatureSeeder first.');
            return;
        }
        
        // Get all schools
        $schools = School::all();
        
        if ($schools->isEmpty()) {
            $this->command->error('No schools found in the database.');
            return;
        }
        
        $this->command->info('Assigning Zoom Online Classes feature to schools...');
        
        foreach ($schools as $school) {
            // Get the school's subscription
            $subscription = Subscription::where('school_id', $school->id)
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($subscription) {
                // Check if the feature is already assigned
                $exists = SubscriptionFeature::where('subscription_id', $subscription->id)
                    ->where('feature_id', $zoomFeature->id)
                    ->exists();
                
                if (!$exists) {
                    // Add the feature to the subscription
                    SubscriptionFeature::create([
                        'subscription_id' => $subscription->id,
                        'feature_id' => $zoomFeature->id
                    ]);
                    
                    $this->command->info("Zoom feature assigned to school: {$school->name}");
                }
            } else {
                $this->command->warn("School {$school->name} has no subscription. Skipping...");
            }
        }
        
        $this->command->info('Zoom Online Classes feature assignment completed!');
    }
} 