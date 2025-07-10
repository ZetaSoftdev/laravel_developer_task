<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Package;
use App\Models\PaymentTransaction;
use App\Models\School;
use App\Models\Subscription;
use App\Models\SubscriptionBill;
use App\Models\SubscriptionFeature;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AssignFullMaxPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $schoolId = $this->command->ask('Enter the School ID to assign the FULL MAX package to:');
        $school = School::find($schoolId);

        if (!$school) {
            $this->command->error('School not found!');
            return;
        }

        $fullMaxPackage = Package::where('name', 'FULL MAX')->first();

        if (!$fullMaxPackage) {
            $this->command->error('FULL MAX package not found!');
            return;
        }

        // Deactivate existing subscriptions for the school by setting end_date to now
        Subscription::where('school_id', $school->id)->update(['end_date' => Carbon::now()]);

        // Create a new subscription
        $subscription = Subscription::create([
            'school_id'     => $school->id,
            'package_id'    => $fullMaxPackage->id,
            'start_date'    => Carbon::now(),
            'end_date'      => Carbon::now()->addDays($fullMaxPackage->days),
            'billing_cycle' => $fullMaxPackage->days,
            'package_type'  => $fullMaxPackage->type,
        ]);

        $this->command->info('Created new subscription for ' . $school->name);

        // If package is prepaid, create a bill and a successful transaction
        if ($fullMaxPackage->type == 0) { // 0 for Prepaid
            $this->command->info('Package is Prepaid. Creating subscription bill and transaction...');
            $bill = SubscriptionBill::create([
                'subscription_id' => $subscription->id,
                'amount'          => $fullMaxPackage->charges ?? 0,
                'total_student'   => $fullMaxPackage->no_of_students,
                'total_staff'     => $fullMaxPackage->no_of_staffs,
                'due_date'        => Carbon::now(),
                'school_id'       => $subscription->school_id,
            ]);

            $transaction = PaymentTransaction::create([
                'user_id' => $school->admin_id,
                'amount' => $bill->amount,
                'payment_gateway' => 'Cash', // Or 'Manual'
                'payment_status' => 'succeed',
                'school_id' => $subscription->school_id,
            ]);

            $bill->payment_transaction_id = $transaction->id;
            $bill->save();
            $this->command->info('Bill and successful transaction created.');
        }


        // Assign all features to the subscription
        $features = Feature::all();
        foreach ($features as $feature) {
            SubscriptionFeature::create([
                'subscription_id' => $subscription->id,
                'feature_id'      => $feature->id,
            ]);
        }

        $this->command->info('Assigned all features to the new subscription.');
        $this->command->info('Package "FULL MAX" assigned to school "' . $school->name . '" successfully!');
    }
} 