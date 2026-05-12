@extends('layouts.admin')

@section('title', 'Payments')
@section('page-title', 'Payments')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Payments</h1>
    <div>
        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm me-2">All</a>
        <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" class="btn btn-outline-warning btn-sm me-2">Pending</a>
        <a href="{{ route('admin.payments.index', ['status' => 'approved']) }}" class="btn btn-outline-success btn-sm me-2">Approved</a>
        <a href="{{ route('admin.payments.index', ['status' => 'rejected']) }}" class="btn btn-outline-danger btn-sm">Rejected</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>Ebook</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $purchase->user->name ?? '-' }}</div>
                                <div class="text-muted small">{{ $purchase->user->email ?? '-' }}</div>
                            </td>
                            <td>{{ $purchase->ebook->title ?? '-' }}</td>
                            <td class="fw-semibold text-success">${{ number_format($purchase->amount, 2) }}</td>
                            <td>
                                @if($purchase->payment_status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($purchase->payment_status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                            <td>{{ $purchase->created_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    @if($purchase->payment_status === 'pending')
                                        <form action="{{ route('admin.payments.approve', $purchase) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                        </form>
                                        <form action="{{ route('admin.payments.reject', $purchase) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                        </form>
                                    @else
                                        <span class="text-muted small">No actions</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No payments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $purchases->withQueryString()->links() }}</div>
@endsection