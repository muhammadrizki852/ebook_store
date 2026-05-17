@extends('layouts.admin')

@section('title', 'Payments')
@section('page-title', 'Payments')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Payments</h1>
    <span class="badge bg-success-subtle text-success border border-success-subtle">Auto Approved</span>
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
                        <th>Proof / Notes</th>
                        <th>Status</th>
                        <th>Date</th>
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
                            <td class="fw-semibold text-success">Rp {{ number_format($purchase->amount ?? $purchase->ebook?->price ?? 0, 0, ',', '.') }}</td>
                            <td>
                                @if($purchase->payment_proof)
                                    <a href="{{ asset('storage/' . $purchase->payment_proof) }}" target="_blank" class="btn btn-sm btn-outline-primary mb-1">
                                        <i class="bi bi-receipt me-1"></i>View Proof
                                    </a>
                                @else
                                    <div class="text-muted small mb-1">No proof uploaded</div>
                                @endif
                                @if($purchase->notes)
                                    <div class="small text-muted">{{ Str::limit($purchase->notes, 70) }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-success">Approved</span>
                            </td>
                            <td>{{ $purchase->created_at->format('M d, Y') }}</td>
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
