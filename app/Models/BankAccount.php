<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_name',
        'branch_name',
        'account_name',
        'account_number',
        'routing_number',
        'swift_code',
        'currency',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope for active bank accounts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
