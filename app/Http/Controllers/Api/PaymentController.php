<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Models\BankAccount;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    /**
     * Submit a payment receipt.
     */
    public function store(PaymentRequest $request): JsonResponse
    {
        // Upload receipt file
        $receiptPath = $request->file('receipt_file')
            ->store('payments/receipts', 'public');

        $payment = Payment::create([
            'booking_id' => $request->booking_id,
            'user_id' => auth()->id(),
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'bank_name' => $request->bank_name,
            'transaction_reference' => $request->transaction_reference,
            'payment_date' => $request->payment_date,
            'receipt_path' => $receiptPath,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Payment receipt submitted successfully',
            'payment' => $payment,
        ], 201);
    }

    /**
     * Get bank account details (public endpoint).
     */
    public function bankAccounts(): JsonResponse
    {
        $bankAccounts = BankAccount::active()
            ->select([
                'id',
                'bank_name',
                'branch_name',
                'account_name',
                'account_number',
                'routing_number',
                'swift_code',
                'currency',
            ])
            ->get();

        return response()->json([
            'bank_accounts' => $bankAccounts,
        ]);
    }
}
