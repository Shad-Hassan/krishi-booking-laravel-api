-- Sea Palace Booking - Seed Data Backup
-- Generated: 2026-01-15
-- Run this after migrations if seeding fails: mysql -u root -p sea_palace_booking < database/seed_backup.sql

SET FOREIGN_KEY_CHECKS=0;

-- Clear existing data
TRUNCATE TABLE `payment_installments`;
TRUNCATE TABLE `payments`;
TRUNCATE TABLE `bookings`;
TRUNCATE TABLE `bank_accounts`;
DELETE FROM `users`;

SET FOREIGN_KEY_CHECKS=1;

-- Users (password: vanga.com for both)
INSERT INTO `users` (`id`, `name`, `email`, `phone`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admiral Vanga', 'tahmid@vanga.com', '01700000000', NULL, '$2y$12$K8Rz27mbVZP.x0DWKpHi7eRwI1dKdg2O9OJl.BrLIiFjjvb6bEILC', 'admin', NULL, NOW(), NOW()),
(2, 'Shad Hassan', 'shad@vanga.com', '01952087074', NULL, '$2y$12$EuMbzxRs11bdh.r/rh2JMuWHUdOMhGSX27114H3wec3L1hGNafnM2', 'user', NULL, NOW(), NOW());

-- Bank Accounts
INSERT INTO `bank_accounts` (`id`, `bank_name`, `branch_name`, `account_name`, `account_number`, `routing_number`, `swift_code`, `currency`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Dutch Bangla Bank PLC', 'Mirpur Circle -10', 'KHeCOM Ltd', '1641100049503', '090263136', NULL, 'BDT', 1, NOW(), NOW()),
(2, 'Rupali Bank PLC', 'Pallabi Branch, Dhaka', 'KGeCom Ltd', '0471020002109', '185263588', NULL, 'BDT', 1, NOW(), NOW());

-- Bookings
INSERT INTO `bookings` (`id`, `user_id`, `booking_reference`, `applicant_name_en`, `applicant_name_bn`, `father_name`, `mother_name`, `spouse_name`, `present_address`, `permanent_address`, `nationality`, `date_of_birth`, `nid_passport`, `marriage_date`, `mobile_1`, `mobile_2`, `email`, `tin`, `profession`, `designation_address`, `nominee_name`, `nominee_address`, `nominee_relation`, `nominee_nid`, `nominee_dob`, `nominee_mobile_1`, `nominee_mobile_2`, `no_of_shares`, `category_ownership`, `payment_mode`, `applicant_photo_path`, `nominee_photo_path`, `signature_path`, `total_amount`, `paid_amount`, `status`, `payment_status`, `next_due_date`, `agreed_to_terms`, `cancellation_reason`, `refund_amount`, `created_at`, `updated_at`) VALUES
-- Booking 1: Shad Hassan, 2 shares, installment, pending
(1, 2, 'KSP-2026-000001', 'Shad Hassan', 'শাদ হাসান', 'Mohammad Hassan', 'Fatima Hassan', NULL,
'{"post": "Mirpur", "thana": "Mirpur", "district": "Dhaka", "house_vill": "House 45, Road 12", "road_block": "Block C"}',
'{"post": "Karimpur", "thana": "Companiganj", "district": "Sylhet", "house_vill": "Village Karimpur"}',
'Bangladeshi', '1995-03-15', '1995123456789', NULL, '01952087074', NULL, 'shad@vanga.com', '123456789012', 'private_service',
'Software Engineer, Vanga Tech Ltd, Dhaka', 'Mohammad Hassan',
'{"post": "Karimpur", "thana": "Companiganj", "district": "Sylhet", "house_vill": "Village Karimpur"}',
'father', '1960987654321', '1960-05-20', '01712345678', NULL, 2, 'Individual', 'installment',
'bookings/photos/seed_applicant_1.jpg', 'bookings/photos/seed_nominee_1.jpg', 'bookings/signatures/seed_signature_1.png',
200000.00, 0.00, 'pending', 'unpaid', DATE_ADD(CURDATE(), INTERVAL 1 MONTH), 1, NULL, NULL, NOW(), NOW()),

-- Booking 2: Shad Hassan, 1 share, at_a_time, active with partial payment
(2, 2, 'KSP-2026-000002', 'Shad Hassan', 'শাদ হাসান', 'Mohammad Hassan', 'Fatima Hassan', NULL,
'{"post": "Mirpur", "thana": "Mirpur", "district": "Dhaka", "house_vill": "House 45, Road 12", "road_block": "Block C"}',
'{"post": "Karimpur", "thana": "Companiganj", "district": "Sylhet", "house_vill": "Village Karimpur"}',
'Bangladeshi', '1995-03-15', '1995123456789', NULL, '01952087074', NULL, 'shad@vanga.com', '123456789012', 'private_service',
'Software Engineer, Vanga Tech Ltd, Dhaka', 'Fatima Hassan',
'{"post": "Karimpur", "thana": "Companiganj", "district": "Sylhet", "house_vill": "Village Karimpur"}',
'mother', '1965123456789', '1965-08-10', '01812345678', NULL, 1, 'Individual', 'at_a_time',
'bookings/photos/seed_applicant_2.jpg', 'bookings/photos/seed_nominee_2.jpg', 'bookings/signatures/seed_signature_2.png',
100000.00, 50000.00, 'active', 'partial', DATE_ADD(CURDATE(), INTERVAL 2 WEEK), 1, NULL, NULL, NOW(), NOW()),

-- Booking 3: Karim Ahmed, 3 shares, installment, active with partial payment
(3, 2, 'KSP-2026-000003', 'Karim Ahmed', 'করিম আহমেদ', 'Abdul Ahmed', 'Rashida Begum', 'Salma Ahmed',
'{"post": "Gulshan", "thana": "Gulshan", "district": "Dhaka", "house_vill": "House 78, Road 5", "road_block": "Block A"}',
'{"post": "Rampur", "thana": "Sadar", "district": "Chittagong", "house_vill": "Village Rampur"}',
'Bangladeshi', '1980-07-22', '1980567890123', '2008-12-15', '01819876543', '01919876543', 'karim.ahmed@example.com', '987654321098', 'business',
'Managing Director, Ahmed Traders, Chittagong', 'Salma Ahmed',
'{"post": "Gulshan", "thana": "Gulshan", "district": "Dhaka", "house_vill": "House 78, Road 5", "road_block": "Block A"}',
'spouse', '1985678901234', '1985-04-18', '01712345679', NULL, 3, 'Individual', 'installment',
'bookings/photos/seed_applicant_3.jpg', 'bookings/photos/seed_nominee_3.jpg', 'bookings/signatures/seed_signature_3.png',
300000.00, 75000.00, 'active', 'partial', DATE_ADD(CURDATE(), INTERVAL 1 MONTH), 1, NULL, NULL, NOW(), NOW()),

-- Booking 4: Rahim Uddin, 1 share, at_a_time, completed
(4, 2, 'KSP-2026-000004', 'Rahim Uddin', 'রহিম উদ্দিন', 'Hafiz Uddin', 'Amina Khatun', 'Nasima Begum',
'{"post": "Banani", "thana": "Banani", "district": "Dhaka", "house_vill": "House 22, Road 8", "road_block": "Block D"}',
'{"post": "Sunamganj Sadar", "thana": "Sadar", "district": "Sunamganj", "house_vill": "Village Sunamganj"}',
'Bangladeshi', '1975-11-30', '1975234567890', '2002-06-10', '01711223344', NULL, 'rahim.uddin@example.com', '456789012345', 'govt_service',
'Deputy Secretary, Ministry of Finance, Dhaka', 'Nasima Begum',
'{"post": "Banani", "thana": "Banani", "district": "Dhaka", "house_vill": "House 22, Road 8", "road_block": "Block D"}',
'spouse', '1980345678901', '1980-02-25', '01811223344', NULL, 1, 'Individual', 'at_a_time',
'bookings/photos/seed_applicant_4.jpg', 'bookings/photos/seed_nominee_4.jpg', 'bookings/signatures/seed_signature_4.png',
100000.00, 100000.00, 'completed', 'paid', NULL, 1, NULL, NULL, NOW(), NOW());

-- Payment Installments for Booking 1 (12 installments, all pending)
INSERT INTO `payment_installments` (`id`, `booking_id`, `installment_number`, `amount`, `due_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 16666.67, DATE_ADD(CURDATE(), INTERVAL 1 MONTH), 'pending', NOW(), NOW()),
(2, 1, 2, 16666.67, DATE_ADD(CURDATE(), INTERVAL 2 MONTH), 'pending', NOW(), NOW()),
(3, 1, 3, 16666.67, DATE_ADD(CURDATE(), INTERVAL 3 MONTH), 'pending', NOW(), NOW()),
(4, 1, 4, 16666.67, DATE_ADD(CURDATE(), INTERVAL 4 MONTH), 'pending', NOW(), NOW()),
(5, 1, 5, 16666.67, DATE_ADD(CURDATE(), INTERVAL 5 MONTH), 'pending', NOW(), NOW()),
(6, 1, 6, 16666.67, DATE_ADD(CURDATE(), INTERVAL 6 MONTH), 'pending', NOW(), NOW()),
(7, 1, 7, 16666.67, DATE_ADD(CURDATE(), INTERVAL 7 MONTH), 'pending', NOW(), NOW()),
(8, 1, 8, 16666.67, DATE_ADD(CURDATE(), INTERVAL 8 MONTH), 'pending', NOW(), NOW()),
(9, 1, 9, 16666.67, DATE_ADD(CURDATE(), INTERVAL 9 MONTH), 'pending', NOW(), NOW()),
(10, 1, 10, 16666.67, DATE_ADD(CURDATE(), INTERVAL 10 MONTH), 'pending', NOW(), NOW()),
(11, 1, 11, 16666.67, DATE_ADD(CURDATE(), INTERVAL 11 MONTH), 'pending', NOW(), NOW()),
(12, 1, 12, 16666.67, DATE_ADD(CURDATE(), INTERVAL 12 MONTH), 'pending', NOW(), NOW()),

-- Payment Installments for Booking 3 (12 installments, first 3 paid)
(13, 3, 1, 25000.00, DATE_ADD(CURDATE(), INTERVAL 1 MONTH), 'paid', NOW(), NOW()),
(14, 3, 2, 25000.00, DATE_ADD(CURDATE(), INTERVAL 2 MONTH), 'paid', NOW(), NOW()),
(15, 3, 3, 25000.00, DATE_ADD(CURDATE(), INTERVAL 3 MONTH), 'paid', NOW(), NOW()),
(16, 3, 4, 25000.00, DATE_ADD(CURDATE(), INTERVAL 4 MONTH), 'pending', NOW(), NOW()),
(17, 3, 5, 25000.00, DATE_ADD(CURDATE(), INTERVAL 5 MONTH), 'pending', NOW(), NOW()),
(18, 3, 6, 25000.00, DATE_ADD(CURDATE(), INTERVAL 6 MONTH), 'pending', NOW(), NOW()),
(19, 3, 7, 25000.00, DATE_ADD(CURDATE(), INTERVAL 7 MONTH), 'pending', NOW(), NOW()),
(20, 3, 8, 25000.00, DATE_ADD(CURDATE(), INTERVAL 8 MONTH), 'pending', NOW(), NOW()),
(21, 3, 9, 25000.00, DATE_ADD(CURDATE(), INTERVAL 9 MONTH), 'pending', NOW(), NOW()),
(22, 3, 10, 25000.00, DATE_ADD(CURDATE(), INTERVAL 10 MONTH), 'pending', NOW(), NOW()),
(23, 3, 11, 25000.00, DATE_ADD(CURDATE(), INTERVAL 11 MONTH), 'pending', NOW(), NOW()),
(24, 3, 12, 25000.00, DATE_ADD(CURDATE(), INTERVAL 12 MONTH), 'pending', NOW(), NOW());
