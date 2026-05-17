<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ebook_id',
        'payment_proof',
        'payment_status',
        'amount',
        'notes',
    ];

    protected static function booted(): void
    {
        static::creating(function (Purchase $purchase) {
            $purchase->payment_status = 'approved';
        });

        static::created(function (Purchase $purchase) {
            TransactionActivity::create([
                'purchase_id' => $purchase->id,
                'user_id' => $purchase->user_id,
                'ebook_id' => $purchase->ebook_id,
                'activity_type' => 'purchase',
                'description' => 'User purchased ebook.',
                'amount' => $purchase->amount,
            ]);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ebook()
    {
        return $this->belongsTo(Ebook::class);
    }

    public function isApproved(): bool
    {
        return $this->payment_status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }
}
