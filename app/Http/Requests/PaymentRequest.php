<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'booking_id' => [
                'required',
                'integer',
                Rule::exists('bookings', 'id')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                }),
            ],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', Rule::in(['bank_transfer_swift', 'bank_transfer_local', 'cheque', 'pay_order'])],
            'bank_name' => ['required', 'string', 'max:255'],
            'transaction_reference' => ['required', 'string', 'max:255'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'receipt_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // max 5MB
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'booking_id.exists' => 'The selected booking does not exist or does not belong to you.',
            'amount.min' => 'Payment amount must be greater than 0.',
            'receipt_file.max' => 'Receipt file must not exceed 5MB.',
            'receipt_file.mimes' => 'Receipt file must be a PDF, JPG, or PNG file.',
            'payment_date.before_or_equal' => 'Payment date cannot be in the future.',
        ];
    }
}
