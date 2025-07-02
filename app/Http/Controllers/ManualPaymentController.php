<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\ResponseService;
use Throwable;

class ManualPaymentController extends Controller
{
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        \Log::info('ManualPaymentController store request:', $request->all());
        
        // First validate without file to avoid upload issues initially
        $request->validate([
            'payment_transaction_id' => 'required|integer',
            'transaction_id' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        try {
            // Ensure we're using the MySQL connection for payment transactions
            DB::setDefaultConnection('mysql');
            $transaction = PaymentTransaction::on('mysql')->findOrFail($request->payment_transaction_id);
            \Log::info('Found transaction:', ['id' => $transaction->id, 'status' => $transaction->status ?? $transaction->payment_status]);

            // Handle file upload with better error handling
            $receiptPath = null;
            if ($request->hasFile('receipt') && $request->file('receipt')->isValid()) {
                try {
                    $file = $request->file('receipt');
                    
                    // Validate file size and type manually
                    $allowedMimes = ['jpeg', 'jpg', 'png', 'gif', 'svg', 'pdf'];
                    $fileExtension = strtolower($file->getClientOriginalExtension());
                    $fileSize = $file->getSize();
                    
                    if (!in_array($fileExtension, $allowedMimes)) {
                        return redirect()->back()->with('error', 'Invalid file type. Please upload JPEG, PNG, GIF, SVG, or PDF files only.');
                    }
                    
                    if ($fileSize > 5242880) { // 5MB in bytes
                        return redirect()->back()->with('error', 'File size is too large. Maximum size allowed is 5MB.');
                    }
                    
                    // Create directory if it doesn't exist
                    $uploadPath = storage_path('app/public/receipts');
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    
                    $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                    
                    // Try to move file directly to avoid temp file issues
                    $fullPath = $uploadPath . DIRECTORY_SEPARATOR . $fileName;
                    if ($file->move($uploadPath, $fileName)) {
                        $receiptPath = 'receipts/' . $fileName;
                    } else {
                        throw new \Exception('Failed to move uploaded file');
                    }
                    
                } catch (\Exception $uploadException) {
                    \Log::error('File upload error: ' . $uploadException->getMessage());
                    // Continue without file upload - don't return error
                    \Log::info('Continuing payment submission without receipt due to upload error');
                }
            } elseif ($request->hasFile('receipt')) {
                \Log::warning('Invalid file uploaded, continuing without receipt');
            }
            // Allow submission without receipt if upload fails

            // Save metadata
            $metadata = [
                'name' => $request->name,
                'phone' => $request->phone,
                'payment_date' => $request->date,
                'bank_transaction_id' => $request->transaction_id,
                'submitted_at' => now(),
                'user_id' => Auth::user()->id,
                'school_id' => Auth::user()->school_id,
                'receipt_uploaded' => $receiptPath ? true : false,
            ];

            $transaction->update([
                'transaction_id' => $request->transaction_id,
                'payment_receipt' => $receiptPath,
                'metadata' => json_encode($metadata),
                'status' => 'pending', // Explicitly set to pending for admin review
            ]);

            return redirect()->route('subscriptions.history')->with('success', 'Payment details submitted successfully. Please wait for admin approval.');

        } catch (Throwable $e) {
            \Log::error('ManualPaymentController store error: ' . $e->getMessage());
            ResponseService::logErrorResponse($e, "ManualPaymentController -> store method");
            return redirect()->back()->with('error', 'An error occurred while submitting your payment details. Please try again.');
        }
    }
}
