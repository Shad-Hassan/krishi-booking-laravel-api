<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'user_id',
        'amount',
        'payment_method',
        'bank_name',
        'transaction_reference',
        'payment_date',
        'receipt_path',
        'status',
        'verified_by',
        'verified_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Get the booking that owns the payment.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user that owns the payment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who verified the payment.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Mark payment as verified.
     */
    public function verify(int $adminId): void
    {
        $this->update([
            'status' => 'verified',
            'verified_by' => $adminId,
            'verified_at' => now(),
        ]);

        // Update booking paid amount
        $booking = $this->booking;
        $booking->paid_amount += $this->amount;
        $booking->updatePaymentStatus();
    }

    /**
     * Mark payment as rejected.
     */
    public function reject(int $adminId, string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'verified_by' => $adminId,
            'verified_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }
}
