<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Share Price
    |--------------------------------------------------------------------------
    |
    | The price per share in BDT (Bangladeshi Taka).
    |
    */
    'share_price' => env('SHARE_PRICE', 100000),

    /*
    |--------------------------------------------------------------------------
    | Installment Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for installment payments.
    |
    */
    'installment_count' => env('INSTALLMENT_COUNT', 12),

    /*
    |--------------------------------------------------------------------------
    | Cancellation Fee
    |--------------------------------------------------------------------------
    |
    | The cancellation fee percentage (5% = 0.05).
    |
    */
    'cancellation_fee_percentage' => env('CANCELLATION_FEE_PERCENTAGE', 0.05),

    /*
    |--------------------------------------------------------------------------
    | File Upload Limits
    |--------------------------------------------------------------------------
    |
    | Maximum file sizes in KB.
    |
    */
    'max_photo_size' => env('MAX_PHOTO_SIZE', 2048), // 2MB
    'max_receipt_size' => env('MAX_RECEIPT_SIZE', 5120), // 5MB

    /*
    |--------------------------------------------------------------------------
    | Booking Reference Prefix
    |--------------------------------------------------------------------------
    |
    | The prefix for booking reference numbers.
    |
    */
    'booking_reference_prefix' => env('BOOKING_REFERENCE_PREFIX', 'KSP'),
];
