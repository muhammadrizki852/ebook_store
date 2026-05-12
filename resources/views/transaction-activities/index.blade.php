@extends('layouts.admin')

@section('title', 'Transaction Activities')
@section('page-title', 'Transaction Activities')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <h1 class="h3 mb-0">Transaction Activities</h1>
    <a href="{{ route('admin.transaction-activities.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add New Activity
    </a>
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
                        <th>ID</th>
                        <th>User</th>
                        <th>Ebook</th>
                        <th>Activity Type</th>
                        <th>Amount</th>
                        <th>Created At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                        <tr>
                            <td>{{ $activity->id }}</td>
                            <td>{{ $activity->user->name }}</td>
                            <td>{{ $activity->ebook->title }}</td>
                            <td>{{ $activity->activity_type }}</td>
                            <td>{{ $activity->amount ? '$' . number_format($activity->amount, 2) : 'N/A' }}</td>
                            <td>{{ $activity->created_at->format('Y-m-d H:i') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.transaction-activities.show', $activity) }}" class="btn btn-sm btn-outline-secondary me-1">View</a>
                                <a href="{{ route('admin.transaction-activities.edit', $activity) }}" class="btn btn-sm btn-outline-primary me-1">Edit</a>
                                <form action="{{ route('admin.transaction-activities.destroy', $activity) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus aktivitas ini?')">Delete</button>
                                </form>
                            </td>
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