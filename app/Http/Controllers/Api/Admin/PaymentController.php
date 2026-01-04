<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentInstallment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Get all payments with filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Payment::with([
            'booking:id,booking_reference',
            'user:id,name,email,phone',
        ]);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $payments = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($payments);
    }

    /**
     * Verify a payment.
     */
    public function verify(int $id): JsonResponse
    {
        $payment = Payment::with('booking')->findOrFail($id);

        if ($payment->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending payments can be verified.',
            ], 422);
        }

        $payment->verify(auth()->id());

        // Update installment status if applicable
        $this->updateInstallmentStatus($payment);

        return response()->json([
            'message' => 'Payment verified successfully',
            'payment' => $payment->fresh(['booking']),
        ]);
    }

    /**
     * Reject a payment.
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $payment = Payment::findOrFail($id);

        if ($payment->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending payments can be rejected.',
            ], 422);
        }

        $payment->reject(auth()->id(), $request->rejection_reason);

        return response()->json([
            'message' => 'Payment rejected',
            'payment' => $payment->fresh(),
        ]);
    }

    /**
     * Update installment status after payment verification.
     */
    private function updateInstallmentStatus(Payment $payment): void
    {
        $booking = $payment->booking;

        if ($booking->payment_mode !== 'installment') {
            return;
        }

        // Find pending installments and mark as paid based on amount
        $pendingInstallments = PaymentInstallment::where('booking_id', $booking->id)
            ->where('status', 'pending')
            ->orderBy('installment_number')
            ->get();

        $remainingAmount = $payment->amount;

        foreach ($pendingInstallments as $installment) {
            if ($remainingAmount >= $installment->amount) {
                $installment->markAsPaid();
                $remainingAmount -= $installment->amount;
            } else {
                break;
            }
        }

        // Update next due date
        $nextInstallment = PaymentInstallment::where('booking_id', $booking->id)
            ->where('status', 'pending')
            ->orderBy('installment_number')
            ->first();

        if ($nextInstallment) {
            $booking->update([
                'next_due_date' => $nextInstallment->due_date,
            ]);
        } else {
            $booking->update([
                'next_due_date' => null,
            ]);
        }
    }
}
