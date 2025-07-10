<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;

class AddZoomFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Add Zoom Online Classes feature
        Feature::firstOrCreate(
            ['name' => 'Zoom Online Classes'],
            [
                'is_default' => 0,
                'status' => 1,
                'required_vps' => 0
            ]
        );

        $this->command->info('Zoom Online Classes feature added successfully!');
    }
} 