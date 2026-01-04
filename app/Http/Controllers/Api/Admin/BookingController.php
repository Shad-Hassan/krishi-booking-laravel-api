<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Get all bookings with filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Booking::with(['user:id,name,email,phone']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by booking reference, user name, or phone
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                    ->orWhere('applicant_name_en', 'like', "%{$search}%")
                    ->orWhere('mobile_1', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($bookings);
    }

    /**
     * Get a single booking details.
     */
    public function show(int $id): JsonResponse
    {
        $booking = Booking::with(['user:id,name,email,phone', 'payments', 'installments'])
            ->findOrFail($id);

        $booking->due_amount = $booking->total_amount - $booking->paid_amount;

        return response()->json([
            'booking' => $booking,
        ]);
    }

    /**
     * Process cancellation request.
     */
    public function processCancellation(int $id): JsonResponse
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'cancellation_requested') {
            return response()->json([
                'message' => 'This booking does not have a pending cancellation request.',
            ], 422);
        }

        // Calculate refund (5% cancellation fee)
        $cancellationFee = $booking->paid_amount * 0.05;
        $refundAmount = $booking->paid_amount - $cancellationFee;

        $booking->update([
            'status' => 'cancelled',
            'refund_amount' => $refundAmount,
        ]);

        return response()->json([
            'message' => 'Cancellation processed successfully',
            'booking' => [
                'id' => $booking->id,
                'booking_reference' => $booking->booking_reference,
                'status' => $booking->status,
                'paid_amount' => $booking->paid_amount,
                'cancellation_fee' => $cancellationFee,
                'refund_amount' => $refundAmount,
            ],
        ]);
    }

    /**
     * Activate a pending booking.
     */
    public function activate(int $id): JsonResponse
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending bookings can be activated.',
            ], 422);
        }

        $booking->update([
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'Booking activated successfully',
            'booking' => $booking,
        ]);
    }
}
