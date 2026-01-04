<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'booking_reference',
        'applicant_name_en',
        'applicant_name_bn',
        'father_name',
        'mother_name',
        'spouse_name',
        'present_address',
        'permanent_address',
        'nationality',
        'date_of_birth',
        'nid_passport',
        'marriage_date',
        'mobile_1',
        'mobile_2',
        'email',
        'tin',
        'profession',
        'designation_address',
        'nominee_name',
        'nominee_address',
        'nominee_relation',
        'nominee_nid',
        'nominee_dob',
        'nominee_mobile_1',
        'nominee_mobile_2',
        'no_of_shares',
        'category_ownership',
        'payment_mode',
        'applicant_photo_path',
        'nominee_photo_path',
        'signature_path',
        'total_amount',
        'paid_amount',
        'status',
        'payment_status',
        'next_due_date',
        'agreed_to_terms',
        'cancellation_reason',
        'refund_amount',
    ];

    protected function casts(): array
    {
        return [
            'present_address' => 'array',
            'permanent_address' => 'array',
            'nominee_address' => 'array',
            'date_of_birth' => 'date',
            'marriage_date' => 'date',
            'nominee_dob' => 'date',
            'next_due_date' => 'date',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'refund_amount' => 'decimal:2',
            'agreed_to_terms' => 'boolean',
        ];
    }

    /**
     * Generate a unique booking reference.
     */
    public static function generateBookingReference(): string
    {
        $year = date('Y');
        $lastBooking = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastBooking) {
            $lastNumber = (int) substr($lastBooking->booking_reference, -6);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('KSP-%s-%06d', $year, $newNumber);
    }

    /**
     * Calculate the due amount.
     */
    public function getDueAmountAttribute(): float
    {
        return $this->total_amount - $this->paid_amount;
    }

    /**
     * Get the user that owns the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the payments for the booking.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the installments for the booking.
     */
    public function installments(): HasMany
    {
        return $this->hasMany(PaymentInstallment::class);
    }

    /**
     * Update payment status based on paid amount.
     */
    public function updatePaymentStatus(): void
    {
        if ($this->paid_amount <= 0) {
            $this->payment_status = 'unpaid';
        } elseif ($this->paid_amount >= $this->total_amount) {
            $this->payment_status = 'paid';
        } else {
            $this->payment_status = 'partial';
        }
        $this->save();
    }

    /**
     * Scope to filter by authenticated user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
