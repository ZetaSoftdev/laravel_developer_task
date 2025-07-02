@extends('layouts.master')

@section('title')
    {{ __('Manual Payment Verification') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Manual Payment Verification') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ __('Manual Payments') }}</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('User') }}</th>
                                        <th>{{ __('Transaction ID') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Submitted At') }}</th>
                                        <th>{{ __('Receipt') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pending_payments as $payment)
                                        <tr>
                                            <td>{{ $payment->user->email }}</td>
                                            <td>{{ $payment->transaction_id }}</td>
                                            <td>${{ number_format($payment->amount, 2) }}</td>
                                            <td>
                                                @if($payment->status == 'pending')
                                                    <span class="badge badge-warning">{{ __('Pending') }}</span>
                                                @elseif($payment->status == 'approved')
                                                    <span class="badge badge-success">{{ __('Approved') }}</span>
                                                @elseif($payment->status == 'rejected')
                                                    <span class="badge badge-danger">{{ __('Rejected') }}</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ ucfirst($payment->status ?? 'Unknown') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $payment->created_at->format('d-m-Y H:i:s') }}</td>
                                            <td>
                                                @if($payment->payment_receipt)
                                                    <a href="{{ asset('storage/' . $payment->payment_receipt) }}" target="_blank" class="btn btn-sm btn-info">{{ __('View Receipt') }}</a>
                                                @else
                                                    <span class="text-muted">{{ __('No receipt') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($payment->status == 'pending')
                                                    <form action="{{ route('admin.manual-payments.update', $payment->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="btn btn-sm btn-success">{{ __('Approve') }}</button>
                                                    </form>
                                                    <form action="{{ route('admin.manual-payments.update', $payment->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="submit" class="btn btn-sm btn-danger">{{ __('Reject') }}</button>
                                                    </form>
                                                @else
                                                    <span class="text-muted">{{ __('No actions available') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">{{ __('No pending payments found.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
