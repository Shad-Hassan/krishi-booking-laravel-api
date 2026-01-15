<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\Booking;
use App\Models\PaymentInstallment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admiral Vanga',
            'email' => 'tahmid@vanga.com',
            'phone' => '01700000000',
            'password' => Hash::make('vanga.com'),
            'role' => 'admin',
        ]);

        // Create user - Shad Hassan
        $shadUser = User::create([
            'name' => 'Shad Hassan',
            'email' => 'shad@vanga.com',
            'phone' => '01952087074',
            'password' => Hash::make('vanga.com'),
            'role' => 'user',
        ]);

        // Create sample bank accounts
        BankAccount::create([
            'bank_name' => 'Dutch Bangla Bank PLC',
            'branch_name' => 'Mirpur Circle -10',
            'account_name' => 'KHeCOM Ltd',
            'account_number' => '1641100049503',
            'routing_number' => '090263136',
            'swift_code' => null,
            'currency' => 'BDT',
            'is_active' => true,
        ]);

        BankAccount::create([
            'bank_name' => 'Rupali Bank PLC',
            'branch_name' => 'Pallabi Branch, Dhaka',
            'account_name' => 'KGeCom Ltd',
            'account_number' => '0471020002109',
            'routing_number' => '185263588',
            'swift_code' => null,
            'currency' => 'BDT',
            'is_active' => true,
        ]);

        // Create sample bookings
        $sharePrice = config('booking.share_price', 100000);
        $installmentCount = config('booking.installment_count', 12);

        // Booking 1 - Shad Hassan (2 shares, installment, pending)
        $booking1 = Booking::create([
            'user_id' => $shadUser->id,
            'booking_reference' => 'KSP-2026-000001',
            'applicant_name_en' => 'Shad Hassan',
            'applicant_name_bn' => 'শাদ হাসান',
            'father_name' => 'Mohammad Hassan',
            'mother_name' => 'Fatima Hassan',
            'spouse_name' => null,
            'present_address' => [
                'house_vill' => 'House 45, Road 12',
                'road_block' => 'Block C',
                'post' => 'Mirpur',
                'thana' => 'Mirpur',
                'district' => 'Dhaka',
            ],
            'permanent_address' => [
                'house_vill' => 'Village Karimpur',
                'post' => 'Karimpur',
                'thana' => 'Companiganj',
                'district' => 'Sylhet',
            ],
            'nationality' => 'Bangladeshi',
            'date_of_birth' => '1995-03-15',
            'nid_passport' => '1995123456789',
            'marriage_date' => null,
            'mobile_1' => '01952087074',
            'mobile_2' => null,
            'email' => 'shad@vanga.com',
            'tin' => '123456789012',
            'profession' => 'private_service',
            'designation_address' => 'Software Engineer, Vanga Tech Ltd, Dhaka',
            'nominee_name' => 'Mohammad Hassan',
            'nominee_address' => [
                'house_vill' => 'Village Karimpur',
                'post' => 'Karimpur',
                'thana' => 'Companiganj',
                'district' => 'Sylhet',
            ],
            'nominee_relation' => 'father',
            'nominee_nid' => '1960987654321',
            'nominee_dob' => '1960-05-20',
            'nominee_mobile_1' => '01712345678',
            'nominee_mobile_2' => null,
            'no_of_shares' => 2,
            'category_ownership' => 'Individual',
            'payment_mode' => 'installment',
            'applicant_photo_path' => 'bookings/photos/seed_applicant_1.jpg',
            'nominee_photo_path' => 'bookings/photos/seed_nominee_1.jpg',
            'signature_path' => 'bookings/signatures/seed_signature_1.png',
            'total_amount' => $sharePrice * 2,
            'paid_amount' => 0,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'next_due_date' => now()->addMonth(),
            'agreed_to_terms' => true,
        ]);

        // Create installments for booking 1
        $installmentAmount = ($sharePrice * 2) / $installmentCount;
        for ($i = 1; $i <= $installmentCount; $i++) {
            PaymentInstallment::create([
                'booking_id' => $booking1->id,
                'installment_number' => $i,
                'amount' => $installmentAmount,
                'due_date' => now()->addMonths($i),
                'status' => 'pending',
            ]);
        }

        // Booking 2 - Shad Hassan (1 share, at_a_time, active with partial payment)
        $booking2 = Booking::create([
            'user_id' => $shadUser->id,
            'booking_reference' => 'KSP-2026-000002',
            'applicant_name_en' => 'Shad Hassan',
            'applicant_name_bn' => 'শাদ হাসান',
            'father_name' => 'Mohammad Hassan',
            'mother_name' => 'Fatima Hassan',
            'spouse_name' => null,
            'present_address' => [
                'house_vill' => 'House 45, Road 12',
                'road_block' => 'Block C',
                'post' => 'Mirpur',
                'thana' => 'Mirpur',
                'district' => 'Dhaka',
            ],
            'permanent_address' => [
                'house_vill' => 'Village Karimpur',
                'post' => 'Karimpur',
                'thana' => 'Companiganj',
                'district' => 'Sylhet',
            ],
            'nationality' => 'Bangladeshi',
            'date_of_birth' => '1995-03-15',
            'nid_passport' => '1995123456789',
            'marriage_date' => null,
            'mobile_1' => '01952087074',
            'mobile_2' => null,
            'email' => 'shad@vanga.com',
            'tin' => '123456789012',
            'profession' => 'private_service',
            'designation_address' => 'Software Engineer, Vanga Tech Ltd, Dhaka',
            'nominee_name' => 'Fatima Hassan',
            'nominee_address' => [
                'house_vill' => 'Village Karimpur',
                'post' => 'Karimpur',
                'thana' => 'Companiganj',
                'district' => 'Sylhet',
            ],
            'nominee_relation' => 'mother',
            'nominee_nid' => '1965123456789',
            'nominee_dob' => '1965-08-10',
            'nominee_mobile_1' => '01812345678',
            'nominee_mobile_2' => null,
            'no_of_shares' => 1,
            'category_ownership' => 'Individual',
            'payment_mode' => 'at_a_time',
            'applicant_photo_path' => 'bookings/photos/seed_applicant_2.jpg',
            'nominee_photo_path' => 'bookings/photos/seed_nominee_2.jpg',
            'signature_path' => 'bookings/signatures/seed_signature_2.png',
            'total_amount' => $sharePrice,
            'paid_amount' => 50000,
            'status' => 'active',
            'payment_status' => 'partial',
            'next_due_date' => now()->addWeeks(2),
            'agreed_to_terms' => true,
        ]);

        // Booking 3 - Another sample booking (3 shares, installment, active)
        $booking3 = Booking::create([
            'user_id' => $shadUser->id,
            'booking_reference' => 'KSP-2026-000003',
            'applicant_name_en' => 'Karim Ahmed',
            'applicant_name_bn' => 'করিম আহমেদ',
            'father_name' => 'Abdul Ahmed',
            'mother_name' => 'Rashida Begum',
            'spouse_name' => 'Salma Ahmed',
            'present_address' => [
                'house_vill' => 'House 78, Road 5',
                'road_block' => 'Block A',
                'post' => 'Gulshan',
                'thana' => 'Gulshan',
                'district' => 'Dhaka',
            ],
            'permanent_address' => [
                'house_vill' => 'Village Rampur',
                'post' => 'Rampur',
                'thana' => 'Sadar',
                'district' => 'Chittagong',
            ],
            'nationality' => 'Bangladeshi',
            'date_of_birth' => '1980-07-22',
            'nid_passport' => '1980567890123',
            'marriage_date' => '2008-12-15',
            'mobile_1' => '01819876543',
            'mobile_2' => '01919876543',
            'email' => 'karim.ahmed@example.com',
            'tin' => '987654321098',
            'profession' => 'business',
            'designation_address' => 'Managing Director, Ahmed Traders, Chittagong',
            'nominee_name' => 'Salma Ahmed',
            'nominee_address' => [
                'house_vill' => 'House 78, Road 5',
                'road_block' => 'Block A',
                'post' => 'Gulshan',
                'thana' => 'Gulshan',
                'district' => 'Dhaka',
            ],
            'nominee_relation' => 'spouse',
            'nominee_nid' => '1985678901234',
            'nominee_dob' => '1985-04-18',
            'nominee_mobile_1' => '01712345679',
            'nominee_mobile_2' => null,
            'no_of_shares' => 3,
            'category_ownership' => 'Individual',
            'payment_mode' => 'installment',
            'applicant_photo_path' => 'bookings/photos/seed_applicant_3.jpg',
            'nominee_photo_path' => 'bookings/photos/seed_nominee_3.jpg',
            'signature_path' => 'bookings/signatures/seed_signature_3.png',
            'total_amount' => $sharePrice * 3,
            'paid_amount' => 75000,
            'status' => 'active',
            'payment_status' => 'partial',
            'next_due_date' => now()->addMonth(),
            'agreed_to_terms' => true,
        ]);

        // Create installments for booking 3
        $installmentAmount3 = ($sharePrice * 3) / $installmentCount;
        for ($i = 1; $i <= $installmentCount; $i++) {
            PaymentInstallment::create([
                'booking_id' => $booking3->id,
                'installment_number' => $i,
                'amount' => $installmentAmount3,
                'due_date' => now()->addMonths($i),
                'status' => $i <= 3 ? 'paid' : 'pending',
            ]);
        }

        // Booking 4 - Completed booking (1 share, fully paid)
        Booking::create([
            'user_id' => $shadUser->id,
            'booking_reference' => 'KSP-2026-000004',
            'applicant_name_en' => 'Rahim Uddin',
            'applicant_name_bn' => 'রহিম উদ্দিন',
            'father_name' => 'Hafiz Uddin',
            'mother_name' => 'Amina Khatun',
            'spouse_name' => 'Nasima Begum',
            'present_address' => [
                'house_vill' => 'House 22, Road 8',
                'road_block' => 'Block D',
                'post' => 'Banani',
                'thana' => 'Banani',
                'district' => 'Dhaka',
            ],
            'permanent_address' => [
                'house_vill' => 'Village Sunamganj',
                'post' => 'Sunamganj Sadar',
                'thana' => 'Sadar',
                'district' => 'Sunamganj',
            ],
            'nationality' => 'Bangladeshi',
            'date_of_birth' => '1975-11-30',
            'nid_passport' => '1975234567890',
            'marriage_date' => '2002-06-10',
            'mobile_1' => '01711223344',
            'mobile_2' => null,
            'email' => 'rahim.uddin@example.com',
            'tin' => '456789012345',
            'profession' => 'govt_service',
            'designation_address' => 'Deputy Secretary, Ministry of Finance, Dhaka',
            'nominee_name' => 'Nasima Begum',
            'nominee_address' => [
                'house_vill' => 'House 22, Road 8',
                'road_block' => 'Block D',
                'post' => 'Banani',
                'thana' => 'Banani',
                'district' => 'Dhaka',
            ],
            'nominee_relation' => 'spouse',
            'nominee_nid' => '1980345678901',
            'nominee_dob' => '1980-02-25',
            'nominee_mobile_1' => '01811223344',
            'nominee_mobile_2' => null,
            'no_of_shares' => 1,
            'category_ownership' => 'Individual',
            'payment_mode' => 'at_a_time',
            'applicant_photo_path' => 'bookings/photos/seed_applicant_4.jpg',
            'nominee_photo_path' => 'bookings/photos/seed_nominee_4.jpg',
            'signature_path' => 'bookings/signatures/seed_signature_4.png',
            'total_amount' => $sharePrice,
            'paid_amount' => $sharePrice,
            'status' => 'completed',
            'payment_status' => 'paid',
            'next_due_date' => null,
            'agreed_to_terms' => true,
        ]);
    }
}
