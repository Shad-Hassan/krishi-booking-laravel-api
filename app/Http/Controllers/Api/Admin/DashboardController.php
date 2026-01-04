<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics.
     */
    public function index(): JsonResponse
    {
        $stats = [
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'active_bookings' => Booking::where('status', 'active')->count(),
            'completed_bookings' => Booking::where('status', 'completed')->count(),
            'cancelled_bookings' => Booking::where('status', 'cancelled')->count(),
            'pending_cancellations' => Booking::where('status', 'cancellation_requested')->count(),
            'total_revenue' => Payment::where('status', 'verified')->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'pending_payment_amount' => Payment::where('status', 'pending')->sum('amount'),
            'total_shares_sold' => Booking::whereIn('status', ['active', 'completed'])->sum('no_of_shares'),
            'bookings_by_status' => [
                'pending' => Booking::where('status', 'pending')->count(),
                'active' => Booking::where('status', 'active')->count(),
                'completed' => Booking::where('status', 'completed')->count(),
                'cancelled' => Booking::where('status', 'cancelled')->count(),
                'cancellation_requested' => Booking::where('status', 'cancellation_requested')->count(),
            ],
            'payment_summary' => [
                'unpaid' => Booking::where('payment_status', 'unpaid')->count(),
                'partial' => Booking::where('payment_status', 'partial')->count(),
                'paid' => Booking::where('payment_status', 'paid')->count(),
            ],
        ];

        return response()->json($stats);
    }

    /**
     * Get recent activity.
     */
    public function recentActivity(): JsonResponse
    {
        $recentBookings = Booking::with('user:id,name')
            ->select(['id', 'user_id', 'booking_reference', 'status', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentPayments = Payment::with(['user:id,name', 'booking:id,booking_reference'])
            ->select(['id', 'user_id', 'booking_id', 'amount', 'status', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'recent_bookings' => $recentBookings,
            'recent_payments' => $recentPayments,
        ]);
    }
}
