@extends('layouts.admin')

@section('title', 'Transaction Activity Details')
@section('page-title', 'Transaction Activity Details')

@section('content')
<div class="card shadow-sm rounded-4">
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-6">
                <h5>ID</h5>
                <p class="mb-0">{{ $transactionActivity->id }}</p>
            </div>
            <div class="col-md-6">
                <h5>User</h5>
                <p class="mb-0">{{ $transactionActivity->user->name }}</p>
            </div>
            <div class="col-md-6">
                <h5>Ebook</h5>
                <p class="mb-0">{{ $transactionActivity->ebook->title }}</p>
            </div>
            <div class="col-md-6">
                <h5>Activity Type</h5>
                <p class="mb-0">{{ $transactionActivity->activity_type }}</p>
            </div>
            <div class="col-md-6">
                <h5>Description</h5>
                <p class="mb-0">{{ $transactionActivity->description ?: 'N/A' }}</p>
            </div>
            <div class="col-md-6">
                <h5>Amount</h5>
                <p class="mb-0">{{ $transactionActivity->amount ? '$' . number_format($transactionActivity->amount, 2) : 'N/A' }}</p>
            </div>
            <div class="col-md-6">
                <h5>Created At</h5>
                <p class="mb-0">{{ $transactionActivity->created_at->format('Y-m-d H:i:s') }}</p>
            </div>
            <div class="col-md-6">
                <h5>Updated At</h5>
                <p class="mb-0">{{ $transactionActivity->updated_at->format('Y-m-d H:i:s') }}</p>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-end gap-2">
            <a href="{{ route('admin.transaction-activities.index') }}" class="btn btn-outline-secondary">Back to List</a>
            <a href="{{ route('admin.transaction-activities.edit', $transactionActivity) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>
</div>
@endsection