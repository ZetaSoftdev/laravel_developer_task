<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use App\Services\ResponseService;
use Throwable;

class ManualPaymentController extends Controller
{
    public function index()
    {
        $pending_payments = PaymentTransaction::where('payment_gateway', 'bank_transfer')
            ->whereNotNull('transaction_id') // Only show submitted payments
            ->with(['user', 'school'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.manual_payments.index', compact('pending_payments'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        try {
            $transaction = PaymentTransaction::findOrFail($id);
            
            if ($request->status == 'approved') {
                // Update transaction status and payment_status
                $transaction->update([
                    'status' => 'approved',
                    'payment_status' => 'succeed'
                ]);

                // Find and update related subscription bill if exists
                $subscriptionBill = $transaction->subscription_bill;
                if ($subscriptionBill) {
                    // Mark subscription bill as paid
                    $subscriptionBill->update(['payment_transaction_id' => $transaction->id]);
                    
                    // Activate subscription features if needed
                    $subscription = $subscriptionBill->subscription;
                    if ($subscription && $subscription->package) {
                        // Create subscription features
                        $subscription_features = [];
                        foreach ($subscription->package->package_feature as $feature) {
                            $subscription_features[] = [
                                'subscription_id' => $subscription->id,
                                'feature_id' => $feature->feature_id
                            ];
                        }
                        
                        if (!empty($subscription_features)) {
                            \App\Models\SubscriptionFeature::upsert(
                                $subscription_features, 
                                ['subscription_id', 'feature_id'], 
                                ['subscription_id', 'feature_id']
                            );
                        }
                    }
                }
                
                $message = 'Payment approved and subscription activated successfully.';
            } else {
                // Reject the payment
                $transaction->update([
                    'status' => 'rejected',
                    'payment_status' => 'failed'
                ]);
                
                // Optionally clean up pending subscription data
                $subscriptionBill = $transaction->subscription_bill;
                if ($subscriptionBill) {
                    $subscription = $subscriptionBill->subscription;
                    if ($subscription) {
                        // Remove subscription features
                        \App\Models\SubscriptionFeature::where('subscription_id', $subscription->id)->delete();
                        
                        // Optionally delete the subscription and bill for rejected payments
                        $subscriptionBill->delete();
                        $subscription->delete();
                    }
                }
                
                $message = 'Payment rejected and subscription cancelled.';
            }

            ResponseService::successResponse($message);
            return redirect()->route('admin.manual-payments.index')->with('success', $message);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Admin\ManualPaymentController -> update method");
            ResponseService::errorResponse();
            return redirect()->back()->with('error', 'An error occurred while updating the payment status.');
        }
    }
}
