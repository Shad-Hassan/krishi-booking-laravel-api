<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'booking_reference' => $this->booking_reference,
            'applicant_name_en' => $this->applicant_name_en,
            'applicant_name_bn' => $this->applicant_name_bn,
            'father_name' => $this->father_name,
            'mother_name' => $this->mother_name,
            'spouse_name' => $this->spouse_name,
            'present_address' => $this->present_address,
            'permanent_address' => $this->permanent_address,
            'nationality' => $this->nationality,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'nid_passport' => $this->nid_passport,
            'marriage_date' => $this->marriage_date?->format('Y-m-d'),
            'mobile_1' => $this->mobile_1,
            'mobile_2' => $this->mobile_2,
            'email' => $this->email,
            'tin' => $this->tin,
            'profession' => $this->profession,
            'designation_address' => $this->designation_address,
            'nominee_name' => $this->nominee_name,
            'nominee_address' => $this->nominee_address,
            'nominee_relation' => $this->nominee_relation,
            'nominee_nid' => $this->nominee_nid,
            'nominee_dob' => $this->nominee_dob?->format('Y-m-d'),
            'nominee_mobile_1' => $this->nominee_mobile_1,
            'nominee_mobile_2' => $this->nominee_mobile_2,
            'no_of_shares' => $this->no_of_shares,
            'category_ownership' => $this->category_ownership,
            'payment_mode' => $this->payment_mode,
            'applicant_photo_url' => $this->applicant_photo_path
                ? asset('storage/' . $this->applicant_photo_path)
                : null,
            'nominee_photo_url' => $this->nominee_photo_path
                ? asset('storage/' . $this->nominee_photo_path)
                : null,
            'signature_url' => $this->signature_path
                ? asset('storage/' . $this->signature_path)
                : null,
            'total_amount' => $this->total_amount,
            'paid_amount' => $this->paid_amount,
            'due_amount' => $this->total_amount - $this->paid_amount,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'next_due_date' => $this->next_due_date?->format('Y-m-d'),
            'cancellation_reason' => $this->cancellation_reason,
            'refund_amount' => $this->refund_amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'installments' => PaymentInstallmentResource::collection($this->whenLoaded('installments')),
        ];
    }
}
