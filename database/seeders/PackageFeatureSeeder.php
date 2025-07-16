<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\PackageFeature;
use Illuminate\Database\Seeder;

class PackageFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $features = Feature::get();
        $package_features = array();
        foreach ($features as $key => $feature) {
            $package_features[] = [
                'package_id' => 1,
                'feature_id' => $feature->id
            ];
        }
        PackageFeature::upsert($package_features,['package_id','feature_id'],['package_id','feature_id']);
    }
}
