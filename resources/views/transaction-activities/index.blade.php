@extends('layouts.admin')

@section('title', 'Transaction Activities')
@section('page-title', 'Transaction Activities')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <h1 class="h3 mb-0">Transaction Activities</h1>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card shadow-sm rounded-4">
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Actions</th>
                        <th>No</th>
                        <th>User</th>
                        <th>Ebook</th>
                        <th>Activity Type</th>
                        <th>Amount</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                        <tr>
                            <td>
                                <a href="{{ route('admin.transaction-activities.show', $activity) }}" class="btn btn-sm btn-outline-secondary me-1">View</a>
                            </td>
                            <td>{{ $activities->firstItem() + $loop->index }}</td>
                            <td>{{ $activity->user->name }}</td>
                            <td>{{ $activity->ebook->title }}</td>
                            <td>{{ $activity->activity_type }}</td>
                            <td>
                                @php($amount = $activity->amount ?? $activity->ebook?->price)
                                {{ $amount !== null ? 'Rp ' . number_format($amount, 0, ',', '.') : 'N/A' }}
                            </td>
                            <td>{{ $activity->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Belum ada data transaction activities.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $activities->links() }}
</div>
@endsection
