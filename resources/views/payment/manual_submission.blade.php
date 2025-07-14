@extends('layouts.master')

@section('title')
    {{ __('Manual Payment Submission') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Manual Payment Submission') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ __('Bank Details') }}</h4>
                        <p>{{ __('Please make the payment to the following bank account and submit the details below.') }}</p>
                        @if($bank_details)
                            @php
                                $bankInfo = json_decode($bank_details->secret_key, true);
                            @endphp
                            @if($bankInfo)
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li><strong>{{ __('Bank Name') }}:</strong> {{ $bankInfo['bank_name'] ?? 'Not configured' }}</li>
                                            <li><strong>{{ __('Account Name') }}:</strong> {{ $bankInfo['account_name'] ?? 'Not configured' }}</li>
                                            <li><strong>{{ __('Account Number') }}:</strong> {{ $bankInfo['account_number'] ?? 'Not configured' }}</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li><strong>{{ __('Routing Number') }}:</strong> {{ $bankInfo['routing_number'] ?? 'Not configured' }}</li>
                                            <li><strong>{{ __('SWIFT Code') }}:</strong> {{ $bankInfo['swift_code'] ?? 'Not configured' }}</li>
                                            <li><strong>{{ __('Branch') }}:</strong> {{ $bankInfo['branch_name'] ?? 'Not configured' }}</li>
                                        </ul>
                                    </div>
                                </div>
                                @if(isset($bankInfo['instructions']))
                                    <div class="alert alert-info">
                                        <strong>{{ __('Instructions') }}:</strong> {{ $bankInfo['instructions'] }}
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-warning">
                                    {{ __('Bank details format is invalid. Please contact administrator.') }}
                                </div>
                            @endif
                        @else
                            <div class="alert alert-warning">
                                {{ __('Bank details are not configured. Please contact administrator.') }}
                            </div>
                        @endif

                        <hr>

                        <h4 class="card-title mt-4">{{ __('Submit Payment Details') }}</h4>
                        
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if(isset($transaction) && $transaction)
                            <div class="alert alert-info">
                                <strong>Transaction Details:</strong><br>
                                Transaction ID: {{ $transaction->id ?? 'Not set' }}<br>
                                Amount: {{ $transaction->amount ?? 'Not set' }}<br>
                                Order ID: {{ $transaction->order_id ?? 'Not set' }}
                            </div>
                        @else
                            <div class="alert alert-danger">
                                <strong>Error:</strong> No transaction data available. Please go back and try again.
                            </div>
                        @endif

                        <form action="{{ route('manual-payment.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="payment_transaction_id" value="{{ isset($transaction) && $transaction ? $transaction->id : '' }}">
                            <div class="form-group">
                                <label for="transaction_id">{{ __('Bank Transaction ID') }}</label>
                                <input type="text" class="form-control" id="transaction_id" name="transaction_id" placeholder="{{ __('Enter the transaction ID from your bank receipt') }}" required>
                                <small class="form-text text-muted">{{ __('This is the transaction ID provided by your bank after making the payment') }}</small>
                            </div>
                            <div class="form-group">
                                <label for="name">{{ __('Your Name') }}</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">{{ __('Phone Number') }}</label>
                                <input type="text" class="form-control" id="phone" name="phone" required>
                            </div>
                            <div class="form-group">
                                <label for="amount">{{ __('Amount') }}</label>
                                <input type="text" class="form-control" id="amount" name="amount" value="{{ isset($transaction) && $transaction ? $transaction->amount : '0' }}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="date">{{ __('Payment Date') }}</label>
                                <input type="date" class="form-control" id="date" name="date" required>
                            </div>
                            <div class="form-group">
                                <label for="receipt">{{ __('Payment Receipt') }} <span class="text-muted">({{ __('Optional if upload fails') }})</span></label>
                                <input type="file" class="form-control" id="receipt" name="receipt" accept=".jpg,.jpeg,.png,.gif,.svg,.pdf">
                                <small class="form-text text-muted">
                                    {{ __('Upload your payment receipt (JPEG, PNG, GIF, SVG, or PDF). Max size: 5MB.') }}<br>
                                    {{ __('If upload fails, you can submit without receipt and contact admin separately.') }}
                                </small>
                            </div>
                            <button type="submit" class="btn btn-theme">{{ __('Submit') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
