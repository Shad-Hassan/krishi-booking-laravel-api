<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankAccountResource extends JsonResource
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
            'bank_name' => $this->bank_name,
            'branch_name' => $this->branch_name,
            'account_name' => $this->account_name,
            'account_number' => $this->account_number,
            'routing_number' => $this->routing_number,
            'swift_code' => $this->swift_code,
            'currency' => $this->currency,
            'is_active' => $this->is_active,
        ];
    }
}
