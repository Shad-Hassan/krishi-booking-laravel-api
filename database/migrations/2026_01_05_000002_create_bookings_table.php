<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('booking_reference')->unique();

            // Applicant Information
            $table->string('applicant_name_en');
            $table->string('applicant_name_bn')->nullable();
            $table->string('father_name');
            $table->string('mother_name');
            $table->string('spouse_name')->nullable();
            $table->json('present_address');
            $table->json('permanent_address');
            $table->string('nationality')->default('Bangladeshi');
            $table->date('date_of_birth');
            $table->string('nid_passport');
            $table->date('marriage_date')->nullable();
            $table->string('mobile_1');
            $table->string('mobile_2')->nullable();
            $table->string('email');
            $table->string('tin')->nullable();
            $table->enum('profession', ['private_service', 'govt_service', 'business', 'others']);
            $table->string('designation_address')->nullable();

            // Nominee Information
            $table->string('nominee_name');
            $table->json('nominee_address');
            $table->enum('nominee_relation', ['spouse', 'son', 'daughter', 'father', 'mother', 'others']);
            $table->string('nominee_nid')->nullable();
            $table->date('nominee_dob')->nullable();
            $table->string('nominee_mobile_1')->nullable();
            $table->string('nominee_mobile_2')->nullable();

            // Share Information
            $table->integer('no_of_shares');
            $table->string('category_ownership')->nullable();
            $table->enum('payment_mode', ['installment', 'at_a_time']);

            // File Uploads
            $table->string('applicant_photo_path');
            $table->string('nominee_photo_path')->nullable();
            $table->string('signature_path');

            // Financial Information
            $table->decimal('total_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);

            // Status
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled', 'cancellation_requested'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->date('next_due_date')->nullable();

            // Terms
            $table->boolean('agreed_to_terms')->default(false);

            // Cancellation
            $table->string('cancellation_reason')->nullable();
            $table->decimal('refund_amount', 12, 2)->nullable();

            $table->timestamps();

            // Index for faster lookups
            $table->index(['user_id', 'nid_passport']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
