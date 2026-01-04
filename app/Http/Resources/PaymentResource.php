<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_id' => $this->booking_id,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'bank_name' => $this->bank_name,
            'transaction_reference' => $this->transaction_reference,
            'payment_date' => $this->payment_date?->format('Y-m-d'),
            'receipt_url' => $this->receipt_path
                ? asset('storage/' . $this->receipt_path)
                : null,
            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,
            'verified_at' => $this->verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'booking' => new BookingResource($this->whenLoaded('booking')),
            'user' => new UserResource($this->whenLoaded('user')),
            'verifier' => new UserResource($this->whenLoaded('verifier')),
        ];
    }
}
