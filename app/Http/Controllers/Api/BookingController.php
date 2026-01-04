<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Http\Requests\CancelBookingRequest;
use App\Models\Booking;
use App\Models\PaymentInstallment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    /**
     * Get all bookings for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $bookings = Booking::forUser(auth()->id())
            ->select([
                'id',
                'booking_reference',
                'created_at',
                'no_of_shares',
                'total_amount',
                'paid_amount',
                'status',
                'payment_status',
                'next_due_date',
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        // Add due_amount to each booking
        $bookings->getCollection()->transform(function ($booking) {
            $booking->due_amount = $booking->total_amount - $booking->paid_amount;
            return $booking;
        });

        return response()->json($bookings);
    }

    /**
     * Store a new booking.
     */
    public function store(BookingRequest $request): JsonResponse
    {
        // Handle file uploads
        $applicantPhotoPath = $request->file('applicant_photo')
            ->store('bookings/photos', 'public');

        $nomineePhotoPath = null;
        if ($request->hasFile('nominee_photo')) {
            $nomineePhotoPath = $request->file('nominee_photo')
                ->store('bookings/photos', 'public');
        }

        // Handle signature (base64)
        $signaturePath = $this->saveBase64Image(
            $request->signature,
            'bookings/signatures'
        );

        // Calculate total amount
        $sharePrice = config('booking.share_price', 100000);
        $totalAmount = $request->no_of_shares * $sharePrice;

        // Generate booking reference
        $bookingReference = Booking::generateBookingReference();

        // Create booking
        $booking = Booking::create([
            'user_id' => auth()->id(),
            'booking_reference' => $bookingReference,
            'applicant_name_en' => $request->applicant_name_en,
            'applicant_name_bn' => $request->applicant_name_bn,
            'father_name' => $request->father_name,
            'mother_name' => $request->mother_name,
            'spouse_name' => $request->spouse_name,
            'present_address' => $request->present_address,
            'permanent_address' => $request->permanent_address,
            'nationality' => $request->nationality ?? 'Bangladeshi',
            'date_of_birth' => $request->date_of_birth,
            'nid_passport' => $request->nid_passport,
            'marriage_date' => $request->marriage_date,
            'mobile_1' => $request->mobile_1,
            'mobile_2' => $request->mobile_2,
            'email' => $request->email,
            'tin' => $request->tin,
            'profession' => $request->profession,
            'designation_address' => $request->designation_address,
            'nominee_name' => $request->nominee_name,
            'nominee_address' => $request->nominee_address,
            'nominee_relation' => $request->nominee_relation,
            'nominee_nid' => $request->nominee_nid,
            'nominee_dob' => $request->nominee_dob,
            'nominee_mobile_1' => $request->nominee_mobile_1,
            'nominee_mobile_2' => $request->nominee_mobile_2,
            'no_of_shares' => $request->no_of_shares,
            'category_ownership' => $request->category_ownership,
            'payment_mode' => $request->payment_mode,
            'applicant_photo_path' => $applicantPhotoPath,
            'nominee_photo_path' => $nomineePhotoPath,
            'signature_path' => $signaturePath,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'agreed_to_terms' => true,
        ]);

        // If installment mode, create installment schedule
        if ($request->payment_mode === 'installment') {
            $this->createInstallmentSchedule($booking);
        }

        return response()->json([
            'message' => 'Booking created successfully',
            'booking' => $booking->load('installments'),
        ], 201);
    }

    /**
     * Get a single booking with details.
     */
    public function show(int $id): JsonResponse
    {
        $booking = Booking::forUser(auth()->id())
            ->with(['payments' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }, 'installments'])
            ->findOrFail($id);

        $booking->due_amount = $booking->total_amount - $booking->paid_amount;

        return response()->json([
            'booking' => $booking,
        ]);
    }

    /**
     * Get payments for a booking.
     */
    public function payments(int $id): JsonResponse
    {
        $booking = Booking::forUser(auth()->id())->findOrFail($id);

        $payments = $booking->payments()
            ->select([
                'id',
                'payment_date',
                'amount',
                'payment_method',
                'transaction_reference',
                'status',
                'rejection_reason',
                'created_at',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'payments' => $payments,
        ]);
    }

    /**
     * Get payment summary for a booking.
     */
    public function paymentSummary(int $id): JsonResponse
    {
        $booking = Booking::forUser(auth()->id())
            ->with('installments')
            ->findOrFail($id);

        $summary = [
            'total_amount' => $booking->total_amount,
            'paid_amount' => $booking->paid_amount,
            'due_amount' => $booking->total_amount - $booking->paid_amount,
            'next_due_date' => $booking->next_due_date,
            'payment_mode' => $booking->payment_mode,
            'installments' => $booking->installments->map(function ($installment) {
                return [
                    'installment_number' => $installment->installment_number,
                    'amount' => $installment->amount,
                    'due_date' => $installment->due_date,
                    'status' => $installment->status,
                ];
            }),
        ];

        return response()->json($summary);
    }

    /**
     * Request booking cancellation.
     */
    public function cancel(CancelBookingRequest $request, int $id): JsonResponse
    {
        $booking = Booking::forUser(auth()->id())->findOrFail($id);

        if ($booking->status !== 'active') {
            return response()->json([
                'message' => 'Only active bookings can be cancelled.',
            ], 422);
        }

        // Calculate refund (5% cancellation fee)
        $cancellationFee = $booking->paid_amount * 0.05;
        $refundAmount = $booking->paid_amount - $cancellationFee;

        $booking->update([
            'status' => 'cancellation_requested',
            'cancellation_reason' => $request->cancellation_reason,
            'refund_amount' => $refundAmount,
        ]);

        return response()->json([
            'message' => 'Cancellation request submitted successfully',
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
     * Save base64 encoded image to storage.
     */
    private function saveBase64Image(string $base64String, string $directory): string
    {
        // Remove data URL prefix if present
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $matches)) {
            $extension = $matches[1];
            $base64String = substr($base64String, strpos($base64String, ',') + 1);
        } else {
            $extension = 'png';
        }

        $imageData = base64_decode($base64String);
        $fileName = uniqid() . '.' . $extension;
        $filePath = $directory . '/' . $fileName;

        Storage::disk('public')->put($filePath, $imageData);

        return $filePath;
    }

    /**
     * Create installment schedule for a booking.
     */
    private function createInstallmentSchedule(Booking $booking): void
    {
        $installmentCount = config('booking.installment_count', 12);
        $installmentAmount = $booking->total_amount / $installmentCount;
        $startDate = Carbon::now();

        for ($i = 1; $i <= $installmentCount; $i++) {
            PaymentInstallment::create([
                'booking_id' => $booking->id,
                'installment_number' => $i,
                'amount' => $installmentAmount,
                'due_date' => $startDate->copy()->addMonths($i),
                'status' => 'pending',
            ]);
        }

        // Set first due date
        $booking->update([
            'next_due_date' => $startDate->copy()->addMonth(),
        ]);
    }
}
