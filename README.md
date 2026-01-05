# Sea Palace Booking REST API

A complete Laravel 11 REST API for Sea Palace share booking system with Sanctum authentication, booking management, payment processing, and admin dashboard.

**Compatible with PHP 8.1, 8.2, and 8.3**

---

## Table of Contents

1. [Quick Setup](#quick-setup)
2. [Environment Configuration](#environment-configuration)
3. [Test Credentials](#test-credentials)
4. [Complete File Structure](#complete-file-structure)
5. [Database Schema](#database-schema)
6. [API Endpoints](#api-endpoints)
7. [Authentication](#authentication)
8. [Request & Response Examples](#request--response-examples)
9. [Validation Rules](#validation-rules)
10. [Eloquent Models & Relationships](#eloquent-models--relationships)
11. [Enums Reference](#enums-reference)
12. [Configuration](#configuration)
13. [File Storage](#file-storage)
14. [Error Handling](#error-handling)
15. [Production Deployment](#production-deployment)

---

## Quick Setup

```bash
# 1. Clone repository
git clone https://github.com/Shad-Hassan/krishi-booking-laravel-api.git
cd krishi-booking-laravel-api

# 2. Install dependencies
composer install

# 3. Create MySQL database
mysql -u root -p -e "CREATE DATABASE sea_palace_booking;"

# 4. Update .env with your MySQL password (if needed)
# nano .env
# Change: DB_PASSWORD=your_password

# 5. Run migrations
php artisan migrate

# 6. Seed database (creates admin user + bank accounts)
php artisan db:seed

# 7. Create storage symlink for file uploads
php artisan storage:link

# 8. Start server
php artisan serve
```

**API Base URL:** `http://localhost:8000/api`

---

## Environment Configuration

The `.env` file is pre-configured and ready to use:

```env
APP_NAME="Sea Palace Booking API"
APP_ENV=local
APP_KEY=base64:XGmTyWhFx24E4/Zu4BK7F269xIgq6r+CY+6njgCaAeg=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sea_palace_booking
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync

# Booking Configuration
SHARE_PRICE=100000
INSTALLMENT_COUNT=12
CANCELLATION_FEE_PERCENTAGE=0.05
```

**Only change `DB_PASSWORD` if your MySQL requires authentication.**

---

## Test Credentials

After running `php artisan db:seed`:

| Role  | Email               | Phone       | Password    |
|-------|---------------------|-------------|-------------|
| Admin | admin@seapalace.com | 01700000000 | password123 |
| User  | user@example.com    | 01711111111 | password123 |

**Seeded Bank Accounts:**
- Sonali Bank Ltd. (BDT)
- Dutch-Bangla Bank Ltd. (BDT)
- BRAC Bank Ltd. (USD)

---

## Complete File Structure

```
krishi-booking-laravel-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php
│   │   │   └── Api/
│   │   │       ├── AuthController.php
│   │   │       ├── BookingController.php
│   │   │       ├── PaymentController.php
│   │   │       └── Admin/
│   │   │           ├── BankAccountController.php
│   │   │           ├── BookingController.php
│   │   │           ├── DashboardController.php
│   │   │           └── PaymentController.php
│   │   ├── Middleware/
│   │   │   └── AdminMiddleware.php
│   │   ├── Requests/
│   │   │   ├── BookingRequest.php
│   │   │   ├── CancelBookingRequest.php
│   │   │   ├── LoginRequest.php
│   │   │   ├── PaymentRequest.php
│   │   │   └── RegisterRequest.php
│   │   └── Resources/
│   │       ├── BankAccountResource.php
│   │       ├── BookingResource.php
│   │       ├── PaymentInstallmentResource.php
│   │       ├── PaymentResource.php
│   │       └── UserResource.php
│   ├── Models/
│   │   ├── BankAccount.php
│   │   ├── Booking.php
│   │   ├── Payment.php
│   │   ├── PaymentInstallment.php
│   │   └── User.php
│   └── Providers/
│       └── AppServiceProvider.php
├── bootstrap/
│   ├── app.php
│   ├── cache/
│   └── providers.php
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── booking.php              # Custom booking configuration
│   ├── cache.php
│   ├── cors.php
│   ├── database.php
│   ├── filesystems.php
│   ├── logging.php
│   ├── mail.php
│   ├── queue.php
│   ├── sanctum.php
│   ├── services.php
│   └── session.php
├── database/
│   ├── factories/
│   │   └── UserFactory.php
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2026_01_04_223111_create_personal_access_tokens_table.php
│   │   ├── 2026_01_05_000001_add_fields_to_users_table.php
│   │   ├── 2026_01_05_000002_create_bookings_table.php
│   │   ├── 2026_01_05_000003_create_payments_table.php
│   │   ├── 2026_01_05_000004_create_bank_accounts_table.php
│   │   └── 2026_01_05_000005_create_payment_installments_table.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── public/
│   ├── .htaccess
│   ├── favicon.ico
│   ├── index.php
│   └── robots.txt
├── routes/
│   ├── api.php                  # All API routes
│   ├── console.php
│   └── web.php
├── storage/
│   ├── app/
│   │   ├── private/
│   │   └── public/              # Uploaded files stored here
│   ├── framework/
│   │   ├── cache/
│   │   ├── sessions/
│   │   └── views/
│   └── logs/
├── .env                         # Environment configuration (MySQL ready)
├── .env.example
├── .gitignore
├── artisan
├── composer.json
├── composer.lock
└── README.md
```

---

## Database Schema

### Table: `users`

| Column            | Type                    | Constraints                |
|-------------------|-------------------------|----------------------------|
| id                | BIGINT UNSIGNED         | PRIMARY KEY, AUTO_INCREMENT|
| name              | VARCHAR(255)            | NOT NULL                   |
| email             | VARCHAR(255)            | NOT NULL, UNIQUE           |
| phone             | VARCHAR(20)             | NOT NULL, UNIQUE           |
| email_verified_at | TIMESTAMP               | NULLABLE                   |
| password          | VARCHAR(255)            | NOT NULL                   |
| role              | ENUM('user', 'admin')   | DEFAULT 'user'             |
| remember_token    | VARCHAR(100)            | NULLABLE                   |
| created_at        | TIMESTAMP               | NULLABLE                   |
| updated_at        | TIMESTAMP               | NULLABLE                   |

### Table: `bookings`

| Column              | Type                          | Constraints                          |
|---------------------|-------------------------------|--------------------------------------|
| id                  | BIGINT UNSIGNED               | PRIMARY KEY, AUTO_INCREMENT          |
| user_id             | BIGINT UNSIGNED               | FOREIGN KEY -> users.id, ON DELETE CASCADE |
| booking_reference   | VARCHAR(255)                  | UNIQUE (format: KSP-YYYY-XXXXXX)     |
| applicant_name_en   | VARCHAR(255)                  | NOT NULL                             |
| applicant_name_bn   | VARCHAR(255)                  | NULLABLE                             |
| father_name         | VARCHAR(255)                  | NOT NULL                             |
| mother_name         | VARCHAR(255)                  | NOT NULL                             |
| spouse_name         | VARCHAR(255)                  | NULLABLE                             |
| present_address     | JSON                          | NOT NULL                             |
| permanent_address   | JSON                          | NOT NULL                             |
| nationality         | VARCHAR(255)                  | DEFAULT 'Bangladeshi'                |
| date_of_birth       | DATE                          | NOT NULL                             |
| nid_passport        | VARCHAR(255)                  | NOT NULL                             |
| marriage_date       | DATE                          | NULLABLE                             |
| mobile_1            | VARCHAR(255)                  | NOT NULL                             |
| mobile_2            | VARCHAR(255)                  | NULLABLE                             |
| email               | VARCHAR(255)                  | NOT NULL                             |
| tin                 | VARCHAR(255)                  | NULLABLE                             |
| profession          | ENUM                          | private_service, govt_service, business, others |
| designation_address | VARCHAR(255)                  | NULLABLE                             |
| nominee_name        | VARCHAR(255)                  | NOT NULL                             |
| nominee_address     | JSON                          | NOT NULL                             |
| nominee_relation    | ENUM                          | spouse, son, daughter, father, mother, others |
| nominee_nid         | VARCHAR(255)                  | NULLABLE                             |
| nominee_dob         | DATE                          | NULLABLE                             |
| nominee_mobile_1    | VARCHAR(255)                  | NULLABLE                             |
| nominee_mobile_2    | VARCHAR(255)                  | NULLABLE                             |
| no_of_shares        | INT                           | NOT NULL                             |
| category_ownership  | VARCHAR(255)                  | NULLABLE                             |
| payment_mode        | ENUM                          | installment, at_a_time               |
| applicant_photo_path| VARCHAR(255)                  | NOT NULL                             |
| nominee_photo_path  | VARCHAR(255)                  | NULLABLE                             |
| signature_path      | VARCHAR(255)                  | NOT NULL                             |
| total_amount        | DECIMAL(12,2)                 | NOT NULL                             |
| paid_amount         | DECIMAL(12,2)                 | DEFAULT 0                            |
| status              | ENUM                          | pending, active, completed, cancelled, cancellation_requested |
| payment_status      | ENUM                          | unpaid, partial, paid                |
| next_due_date       | DATE                          | NULLABLE                             |
| agreed_to_terms     | BOOLEAN                       | DEFAULT FALSE                        |
| cancellation_reason | VARCHAR(255)                  | NULLABLE                             |
| refund_amount       | DECIMAL(12,2)                 | NULLABLE                             |
| created_at          | TIMESTAMP                     | NULLABLE                             |
| updated_at          | TIMESTAMP                     | NULLABLE                             |

**Index:** `(user_id, nid_passport)`

### Table: `payments`

| Column               | Type                       | Constraints                          |
|----------------------|----------------------------|--------------------------------------|
| id                   | BIGINT UNSIGNED            | PRIMARY KEY, AUTO_INCREMENT          |
| booking_id           | BIGINT UNSIGNED            | FOREIGN KEY -> bookings.id, ON DELETE CASCADE |
| user_id              | BIGINT UNSIGNED            | FOREIGN KEY -> users.id, ON DELETE CASCADE |
| amount               | DECIMAL(12,2)              | NOT NULL                             |
| payment_method       | ENUM                       | bank_transfer_swift, bank_transfer_local, cheque, pay_order |
| bank_name            | VARCHAR(255)               | NOT NULL                             |
| transaction_reference| VARCHAR(255)               | NOT NULL                             |
| payment_date         | DATE                       | NOT NULL                             |
| receipt_path         | VARCHAR(255)               | NOT NULL                             |
| status               | ENUM                       | pending, verified, rejected          |
| verified_by          | BIGINT UNSIGNED            | NULLABLE, FOREIGN KEY -> users.id, ON DELETE SET NULL |
| verified_at          | TIMESTAMP                  | NULLABLE                             |
| rejection_reason     | TEXT                       | NULLABLE                             |
| created_at           | TIMESTAMP                  | NULLABLE                             |
| updated_at           | TIMESTAMP                  | NULLABLE                             |

**Index:** `(booking_id, status)`

### Table: `bank_accounts`

| Column         | Type             | Constraints               |
|----------------|------------------|---------------------------|
| id             | BIGINT UNSIGNED  | PRIMARY KEY, AUTO_INCREMENT|
| bank_name      | VARCHAR(255)     | NOT NULL                  |
| branch_name    | VARCHAR(255)     | NOT NULL                  |
| account_name   | VARCHAR(255)     | NOT NULL                  |
| account_number | VARCHAR(255)     | NOT NULL                  |
| routing_number | VARCHAR(255)     | NULLABLE                  |
| swift_code     | VARCHAR(255)     | NULLABLE                  |
| currency       | VARCHAR(255)     | DEFAULT 'BDT'             |
| is_active      | BOOLEAN          | DEFAULT TRUE              |
| created_at     | TIMESTAMP        | NULLABLE                  |
| updated_at     | TIMESTAMP        | NULLABLE                  |

### Table: `payment_installments`

| Column            | Type             | Constraints                          |
|-------------------|------------------|--------------------------------------|
| id                | BIGINT UNSIGNED  | PRIMARY KEY, AUTO_INCREMENT          |
| booking_id        | BIGINT UNSIGNED  | FOREIGN KEY -> bookings.id, ON DELETE CASCADE |
| installment_number| INT              | NOT NULL                             |
| amount            | DECIMAL(12,2)    | NOT NULL                             |
| due_date          | DATE             | NOT NULL                             |
| status            | ENUM             | pending, paid, overdue               |
| created_at        | TIMESTAMP        | NULLABLE                             |
| updated_at        | TIMESTAMP        | NULLABLE                             |

**Unique Constraint:** `(booking_id, installment_number)`

### Table: `personal_access_tokens` (Sanctum)

| Column        | Type             | Constraints               |
|---------------|------------------|---------------------------|
| id            | BIGINT UNSIGNED  | PRIMARY KEY               |
| tokenable_type| VARCHAR(255)     | NOT NULL                  |
| tokenable_id  | BIGINT UNSIGNED  | NOT NULL                  |
| name          | VARCHAR(255)     | NOT NULL                  |
| token         | VARCHAR(64)      | UNIQUE                    |
| abilities     | TEXT             | NULLABLE                  |
| last_used_at  | TIMESTAMP        | NULLABLE                  |
| expires_at    | TIMESTAMP        | NULLABLE                  |
| created_at    | TIMESTAMP        | NULLABLE                  |
| updated_at    | TIMESTAMP        | NULLABLE                  |

### JSON Address Structure

```json
{
  "house_vill": "123 Main Street",
  "road_block": "Block A",
  "post": "Dhaka GPO",
  "thana": "Motijheel",
  "district": "Dhaka"
}
```

---

## API Endpoints

### Public Endpoints (No Authentication)

| Method | Endpoint           | Controller                    | Description              |
|--------|-------------------|-------------------------------|--------------------------|
| POST   | `/api/register`   | AuthController@register       | Register new user        |
| POST   | `/api/login`      | AuthController@login          | Login (email OR phone)   |
| GET    | `/api/bank-accounts` | PaymentController@bankAccounts | Get active bank accounts |

### Authenticated User Endpoints (Bearer Token Required)

| Method | Endpoint                          | Controller                      | Description                 |
|--------|----------------------------------|---------------------------------|-----------------------------|
| POST   | `/api/logout`                    | AuthController@logout           | Logout (invalidate token)   |
| GET    | `/api/user`                      | AuthController@user             | Get current user profile    |
| GET    | `/api/bookings`                  | BookingController@index         | List user's bookings        |
| POST   | `/api/bookings`                  | BookingController@store         | Create new booking          |
| GET    | `/api/bookings/{id}`             | BookingController@show          | Get booking details         |
| GET    | `/api/bookings/{id}/payments`    | BookingController@payments      | Get booking payments        |
| GET    | `/api/bookings/{id}/payment-summary` | BookingController@paymentSummary | Get payment summary     |
| POST   | `/api/bookings/{id}/cancel`      | BookingController@cancel        | Request cancellation        |
| POST   | `/api/payments`                  | PaymentController@store         | Submit payment receipt      |

### Admin Endpoints (Admin Bearer Token Required)

| Method | Endpoint                                    | Controller                           | Description              |
|--------|---------------------------------------------|--------------------------------------|--------------------------|
| GET    | `/api/admin/dashboard`                      | Admin\DashboardController@index      | Dashboard statistics     |
| GET    | `/api/admin/dashboard/recent-activity`      | Admin\DashboardController@recentActivity | Recent activity      |
| GET    | `/api/admin/bookings`                       | Admin\BookingController@index        | List all bookings        |
| GET    | `/api/admin/bookings/{id}`                  | Admin\BookingController@show         | Get any booking          |
| PUT    | `/api/admin/bookings/{id}/activate`         | Admin\BookingController@activate     | Activate booking         |
| PUT    | `/api/admin/bookings/{id}/process-cancellation` | Admin\BookingController@processCancellation | Process cancel |
| GET    | `/api/admin/payments`                       | Admin\PaymentController@index        | List all payments        |
| PUT    | `/api/admin/payments/{id}/verify`           | Admin\PaymentController@verify       | Verify payment           |
| PUT    | `/api/admin/payments/{id}/reject`           | Admin\PaymentController@reject       | Reject payment           |
| GET    | `/api/admin/bank-accounts`                  | Admin\BankAccountController@index    | List all bank accounts   |
| POST   | `/api/admin/bank-accounts`                  | Admin\BankAccountController@store    | Create bank account      |
| PUT    | `/api/admin/bank-accounts/{id}`             | Admin\BankAccountController@update   | Update bank account      |
| DELETE | `/api/admin/bank-accounts/{id}`             | Admin\BankAccountController@destroy  | Delete bank account      |
| PUT    | `/api/admin/bank-accounts/{id}/toggle-status` | Admin\BankAccountController@toggleStatus | Toggle active      |

---

## Authentication

This API uses **Laravel Sanctum** for token-based authentication.

### Headers for Authenticated Requests

```
Authorization: Bearer {your_token}
Accept: application/json
Content-Type: application/json
```

### Headers for File Uploads

```
Authorization: Bearer {your_token}
Accept: application/json
Content-Type: multipart/form-data
```

---

## Request & Response Examples

### 1. Register User

**Request:**
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "01712345678",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Response (201 Created):**
```json
{
  "message": "Registration successful",
  "user": {
    "id": 3,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "01712345678",
    "role": "user",
    "updated_at": "2026-01-05T12:00:00.000000Z",
    "created_at": "2026-01-05T12:00:00.000000Z"
  },
  "token": "3|laravel_sanctum_abc123xyz456..."
}
```

### 2. Login

**Request (with email):**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "identifier": "admin@seapalace.com",
    "password": "password123"
  }'
```

**Request (with phone):**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "identifier": "01700000000",
    "password": "password123"
  }'
```

**Response (200 OK):**
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@seapalace.com",
    "phone": "01700000000",
    "role": "admin",
    "email_verified_at": null,
    "created_at": "2026-01-05T10:00:00.000000Z",
    "updated_at": "2026-01-05T10:00:00.000000Z"
  },
  "token": "1|laravel_sanctum_xyz789abc..."
}
```

**Response (401 Unauthorized):**
```json
{
  "message": "Invalid credentials"
}
```

### 3. Logout

**Request:**
```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "message": "Logged out successfully"
}
```

### 4. Get Current User

**Request:**
```bash
curl -X GET http://localhost:8000/api/user \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@seapalace.com",
    "phone": "01700000000",
    "role": "admin",
    "email_verified_at": null,
    "created_at": "2026-01-05T10:00:00.000000Z",
    "updated_at": "2026-01-05T10:00:00.000000Z"
  }
}
```

### 5. Get Bank Accounts (Public)

**Request:**
```bash
curl -X GET http://localhost:8000/api/bank-accounts \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "bank_accounts": [
    {
      "id": 1,
      "bank_name": "Sonali Bank Ltd.",
      "branch_name": "Motijheel Branch",
      "account_name": "Sea Palace Properties Ltd.",
      "account_number": "0102010012345",
      "routing_number": "150270509",
      "swift_code": "BABORADHXXX",
      "currency": "BDT"
    },
    {
      "id": 2,
      "bank_name": "Dutch-Bangla Bank Ltd.",
      "branch_name": "Gulshan Branch",
      "account_name": "Sea Palace Properties Ltd.",
      "account_number": "1234567890123",
      "routing_number": "090261728",
      "swift_code": "DBBLBDDH",
      "currency": "BDT"
    }
  ]
}
```

### 6. Create Booking

**Request:**
```bash
curl -X POST http://localhost:8000/api/bookings \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json" \
  -F "applicant_name_en=John Doe" \
  -F "applicant_name_bn=জন ডো" \
  -F "father_name=Robert Doe" \
  -F "mother_name=Mary Doe" \
  -F "spouse_name=Jane Doe" \
  -F "present_address[house_vill]=123 Main Street" \
  -F "present_address[road_block]=Block A" \
  -F "present_address[post]=Dhaka GPO" \
  -F "present_address[thana]=Motijheel" \
  -F "present_address[district]=Dhaka" \
  -F "permanent_address[house_vill]=456 Village Road" \
  -F "permanent_address[road_block]=" \
  -F "permanent_address[post]=Comilla Sadar" \
  -F "permanent_address[thana]=Sadar" \
  -F "permanent_address[district]=Comilla" \
  -F "nationality=Bangladeshi" \
  -F "date_of_birth=1990-05-15" \
  -F "nid_passport=1234567890123" \
  -F "marriage_date=2015-06-20" \
  -F "mobile_1=01712345678" \
  -F "mobile_2=01812345678" \
  -F "email=john@example.com" \
  -F "tin=123456789012" \
  -F "profession=business" \
  -F "designation_address=CEO, ABC Company Ltd." \
  -F "nominee_name=Jane Doe" \
  -F "nominee_address[house_vill]=123 Main Street" \
  -F "nominee_address[road_block]=Block A" \
  -F "nominee_address[post]=Dhaka GPO" \
  -F "nominee_address[thana]=Motijheel" \
  -F "nominee_address[district]=Dhaka" \
  -F "nominee_relation=spouse" \
  -F "nominee_nid=9876543210123" \
  -F "nominee_dob=1992-08-10" \
  -F "nominee_mobile_1=01798765432" \
  -F "nominee_mobile_2=" \
  -F "no_of_shares=5" \
  -F "category_ownership=Individual" \
  -F "payment_mode=installment" \
  -F "applicant_photo=@/path/to/photo.jpg" \
  -F "nominee_photo=@/path/to/nominee_photo.jpg" \
  -F "signature=data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==" \
  -F "agreed_to_terms=1"
```

**Response (201 Created):**
```json
{
  "message": "Booking created successfully",
  "booking": {
    "id": 1,
    "user_id": 2,
    "booking_reference": "KSP-2026-000001",
    "applicant_name_en": "John Doe",
    "applicant_name_bn": "জন ডো",
    "father_name": "Robert Doe",
    "mother_name": "Mary Doe",
    "spouse_name": "Jane Doe",
    "present_address": {
      "house_vill": "123 Main Street",
      "road_block": "Block A",
      "post": "Dhaka GPO",
      "thana": "Motijheel",
      "district": "Dhaka"
    },
    "permanent_address": {
      "house_vill": "456 Village Road",
      "road_block": "",
      "post": "Comilla Sadar",
      "thana": "Sadar",
      "district": "Comilla"
    },
    "nationality": "Bangladeshi",
    "date_of_birth": "1990-05-15",
    "nid_passport": "1234567890123",
    "marriage_date": "2015-06-20",
    "mobile_1": "01712345678",
    "mobile_2": "01812345678",
    "email": "john@example.com",
    "tin": "123456789012",
    "profession": "business",
    "designation_address": "CEO, ABC Company Ltd.",
    "nominee_name": "Jane Doe",
    "nominee_address": {
      "house_vill": "123 Main Street",
      "road_block": "Block A",
      "post": "Dhaka GPO",
      "thana": "Motijheel",
      "district": "Dhaka"
    },
    "nominee_relation": "spouse",
    "nominee_nid": "9876543210123",
    "nominee_dob": "1992-08-10",
    "nominee_mobile_1": "01798765432",
    "nominee_mobile_2": null,
    "no_of_shares": 5,
    "category_ownership": "Individual",
    "payment_mode": "installment",
    "applicant_photo_path": "bookings/photos/abc123.jpg",
    "nominee_photo_path": "bookings/photos/xyz789.jpg",
    "signature_path": "bookings/signatures/sig456.png",
    "total_amount": "500000.00",
    "paid_amount": "0.00",
    "status": "pending",
    "payment_status": "unpaid",
    "next_due_date": "2026-02-05",
    "agreed_to_terms": true,
    "cancellation_reason": null,
    "refund_amount": null,
    "created_at": "2026-01-05T12:00:00.000000Z",
    "updated_at": "2026-01-05T12:00:00.000000Z",
    "installments": [
      {
        "id": 1,
        "booking_id": 1,
        "installment_number": 1,
        "amount": "41666.67",
        "due_date": "2026-02-05",
        "status": "pending"
      },
      {
        "id": 2,
        "booking_id": 1,
        "installment_number": 2,
        "amount": "41666.67",
        "due_date": "2026-03-05",
        "status": "pending"
      }
    ]
  }
}
```

### 7. List User's Bookings

**Request:**
```bash
curl -X GET "http://localhost:8000/api/bookings?per_page=10&page=1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "booking_reference": "KSP-2026-000001",
      "created_at": "2026-01-05T12:00:00.000000Z",
      "no_of_shares": 5,
      "total_amount": "500000.00",
      "paid_amount": "0.00",
      "due_amount": 500000,
      "status": "pending",
      "payment_status": "unpaid",
      "next_due_date": "2026-02-05"
    }
  ],
  "first_page_url": "http://localhost:8000/api/bookings?page=1",
  "from": 1,
  "last_page": 1,
  "last_page_url": "http://localhost:8000/api/bookings?page=1",
  "links": [...],
  "next_page_url": null,
  "path": "http://localhost:8000/api/bookings",
  "per_page": 10,
  "prev_page_url": null,
  "to": 1,
  "total": 1
}
```

### 8. Get Booking Details

**Request:**
```bash
curl -X GET http://localhost:8000/api/bookings/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "booking": {
    "id": 1,
    "booking_reference": "KSP-2026-000001",
    "applicant_name_en": "John Doe",
    "...": "all booking fields",
    "due_amount": 500000,
    "payments": [],
    "installments": [...]
  }
}
```

### 9. Get Payment Summary

**Request:**
```bash
curl -X GET http://localhost:8000/api/bookings/1/payment-summary \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "total_amount": "500000.00",
  "paid_amount": "0.00",
  "due_amount": 500000,
  "next_due_date": "2026-02-05",
  "payment_mode": "installment",
  "installments": [
    {
      "installment_number": 1,
      "amount": "41666.67",
      "due_date": "2026-02-05",
      "status": "pending"
    },
    {
      "installment_number": 2,
      "amount": "41666.67",
      "due_date": "2026-03-05",
      "status": "pending"
    }
  ]
}
```

### 10. Submit Payment

**Request:**
```bash
curl -X POST http://localhost:8000/api/payments \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json" \
  -F "booking_id=1" \
  -F "amount=50000" \
  -F "payment_method=bank_transfer_local" \
  -F "bank_name=Dutch-Bangla Bank Ltd." \
  -F "transaction_reference=TXN202601050001" \
  -F "payment_date=2026-01-05" \
  -F "receipt_file=@/path/to/receipt.pdf"
```

**Response (201 Created):**
```json
{
  "message": "Payment receipt submitted successfully",
  "payment": {
    "id": 1,
    "booking_id": 1,
    "user_id": 2,
    "amount": "50000.00",
    "payment_method": "bank_transfer_local",
    "bank_name": "Dutch-Bangla Bank Ltd.",
    "transaction_reference": "TXN202601050001",
    "payment_date": "2026-01-05",
    "receipt_path": "payments/receipts/receipt123.pdf",
    "status": "pending",
    "verified_by": null,
    "verified_at": null,
    "rejection_reason": null,
    "created_at": "2026-01-05T12:30:00.000000Z",
    "updated_at": "2026-01-05T12:30:00.000000Z"
  }
}
```

### 11. Request Cancellation

**Request:**
```bash
curl -X POST http://localhost:8000/api/bookings/1/cancel \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "cancellation_reason": "Financial difficulties"
  }'
```

**Response (200 OK):**
```json
{
  "message": "Cancellation request submitted successfully",
  "booking": {
    "id": 1,
    "booking_reference": "KSP-2026-000001",
    "status": "cancellation_requested",
    "paid_amount": "50000.00",
    "cancellation_fee": 2500,
    "refund_amount": 47500
  }
}
```

### 12. Admin: Get Dashboard Stats

**Request:**
```bash
curl -X GET http://localhost:8000/api/admin/dashboard \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "total_bookings": 25,
  "pending_bookings": 5,
  "active_bookings": 18,
  "completed_bookings": 1,
  "cancelled_bookings": 0,
  "pending_cancellations": 1,
  "total_revenue": "5000000.00",
  "pending_payments": 3,
  "pending_payment_amount": "150000.00",
  "total_shares_sold": 150,
  "bookings_by_status": {
    "pending": 5,
    "active": 18,
    "completed": 1,
    "cancelled": 0,
    "cancellation_requested": 1
  },
  "payment_summary": {
    "unpaid": 5,
    "partial": 15,
    "paid": 5
  }
}
```

### 13. Admin: List All Bookings with Filters

**Request:**
```bash
curl -X GET "http://localhost:8000/api/admin/bookings?status=active&payment_status=partial&search=KSP-2026&date_from=2026-01-01&date_to=2026-12-31&per_page=15" \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Accept: application/json"
```

**Query Parameters:**
- `status`: pending, active, completed, cancelled, cancellation_requested
- `payment_status`: unpaid, partial, paid
- `search`: booking reference, applicant name, or phone
- `date_from`: YYYY-MM-DD
- `date_to`: YYYY-MM-DD
- `per_page`: items per page (default: 15)
- `page`: page number

### 14. Admin: Verify Payment

**Request:**
```bash
curl -X PUT http://localhost:8000/api/admin/payments/1/verify \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "message": "Payment verified successfully",
  "payment": {
    "id": 1,
    "booking_id": 1,
    "amount": "50000.00",
    "status": "verified",
    "verified_by": 1,
    "verified_at": "2026-01-05T14:00:00.000000Z",
    "booking": {
      "id": 1,
      "paid_amount": "50000.00",
      "payment_status": "partial"
    }
  }
}
```

### 15. Admin: Reject Payment

**Request:**
```bash
curl -X PUT http://localhost:8000/api/admin/payments/1/reject \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "rejection_reason": "Receipt is unclear and transaction reference does not match bank records"
  }'
```

**Response (200 OK):**
```json
{
  "message": "Payment rejected",
  "payment": {
    "id": 1,
    "status": "rejected",
    "rejection_reason": "Receipt is unclear and transaction reference does not match bank records",
    "verified_by": 1,
    "verified_at": "2026-01-05T14:00:00.000000Z"
  }
}
```

### 16. Admin: Activate Booking

**Request:**
```bash
curl -X PUT http://localhost:8000/api/admin/bookings/1/activate \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "message": "Booking activated successfully",
  "booking": {
    "id": 1,
    "status": "active"
  }
}
```

### 17. Admin: Process Cancellation

**Request:**
```bash
curl -X PUT http://localhost:8000/api/admin/bookings/1/process-cancellation \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Accept: application/json"
```

**Response (200 OK):**
```json
{
  "message": "Cancellation processed successfully",
  "booking": {
    "id": 1,
    "booking_reference": "KSP-2026-000001",
    "status": "cancelled",
    "paid_amount": "50000.00",
    "cancellation_fee": 2500,
    "refund_amount": 47500
  }
}
```

### 18. Admin: Create Bank Account

**Request:**
```bash
curl -X POST http://localhost:8000/api/admin/bank-accounts \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "bank_name": "City Bank Ltd.",
    "branch_name": "Banani Branch",
    "account_name": "Sea Palace Properties Ltd.",
    "account_number": "5678901234567",
    "routing_number": "225261234",
    "swift_code": "CIABORADHXXX",
    "currency": "BDT",
    "is_active": true
  }'
```

**Response (201 Created):**
```json
{
  "message": "Bank account created successfully",
  "bank_account": {
    "id": 4,
    "bank_name": "City Bank Ltd.",
    "branch_name": "Banani Branch",
    "account_name": "Sea Palace Properties Ltd.",
    "account_number": "5678901234567",
    "routing_number": "225261234",
    "swift_code": "CIABORADHXXX",
    "currency": "BDT",
    "is_active": true,
    "created_at": "2026-01-05T15:00:00.000000Z",
    "updated_at": "2026-01-05T15:00:00.000000Z"
  }
}
```

---

## Validation Rules

### Registration (`POST /api/register`)

| Field                  | Rules                              |
|------------------------|------------------------------------|
| name                   | required, string, max:255          |
| email                  | required, string, email, max:255, unique:users |
| phone                  | required, string, max:20, unique:users |
| password               | required, string, min:6, confirmed |
| password_confirmation  | required (must match password)     |

### Login (`POST /api/login`)

| Field      | Rules              |
|------------|--------------------|
| identifier | required, string   |
| password   | required, string   |

### Create Booking (`POST /api/bookings`)

| Field                      | Rules                                                    |
|----------------------------|----------------------------------------------------------|
| applicant_name_en          | required, string, max:255                                |
| applicant_name_bn          | nullable, string, max:255                                |
| father_name                | required, string, max:255                                |
| mother_name                | required, string, max:255                                |
| spouse_name                | nullable, string, max:255                                |
| present_address            | required, array                                          |
| present_address.house_vill | required, string, max:255                                |
| present_address.road_block | nullable, string, max:255                                |
| present_address.post       | required, string, max:255                                |
| present_address.thana      | required, string, max:255                                |
| present_address.district   | required, string, max:255                                |
| permanent_address          | required, array (same structure as present_address)      |
| nationality                | nullable, string, max:100                                |
| date_of_birth              | required, date, before:today                             |
| nid_passport               | required, string, max:50, unique per user                |
| marriage_date              | nullable, date, before_or_equal:today                    |
| mobile_1                   | required, string, max:20                                 |
| mobile_2                   | nullable, string, max:20                                 |
| email                      | required, email, max:255                                 |
| tin                        | nullable, string, max:50                                 |
| profession                 | required, in:private_service,govt_service,business,others|
| designation_address        | nullable, string, max:500                                |
| nominee_name               | required, string, max:255                                |
| nominee_address            | required, array (same structure as present_address)      |
| nominee_relation           | required, in:spouse,son,daughter,father,mother,others    |
| nominee_nid                | nullable, string, max:50                                 |
| nominee_dob                | nullable, date, before:today                             |
| nominee_mobile_1           | nullable, string, max:20                                 |
| nominee_mobile_2           | nullable, string, max:20                                 |
| no_of_shares               | required, integer, min:1                                 |
| category_ownership         | nullable, string, max:255                                |
| payment_mode               | required, in:installment,at_a_time                       |
| applicant_photo            | required, image, max:2048 (2MB)                          |
| nominee_photo              | nullable, image, max:2048 (2MB)                          |
| signature                  | required, string (base64 encoded image)                  |
| agreed_to_terms            | required, accepted (must be true)                        |

### Submit Payment (`POST /api/payments`)

| Field                 | Rules                                                    |
|-----------------------|----------------------------------------------------------|
| booking_id            | required, integer, exists:bookings (must belong to user) |
| amount                | required, numeric, min:0.01                              |
| payment_method        | required, in:bank_transfer_swift,bank_transfer_local,cheque,pay_order |
| bank_name             | required, string, max:255                                |
| transaction_reference | required, string, max:255                                |
| payment_date          | required, date, before_or_equal:today                    |
| receipt_file          | required, file, mimes:pdf,jpg,jpeg,png, max:5120 (5MB)   |

### Cancel Booking (`POST /api/bookings/{id}/cancel`)

| Field               | Rules                      |
|---------------------|----------------------------|
| cancellation_reason | nullable, string, max:1000 |

### Reject Payment (`PUT /api/admin/payments/{id}/reject`)

| Field            | Rules                      |
|------------------|----------------------------|
| rejection_reason | required, string, max:1000 |

### Create/Update Bank Account

| Field          | Rules                                |
|----------------|--------------------------------------|
| bank_name      | required (create) / sometimes, string, max:255 |
| branch_name    | required (create) / sometimes, string, max:255 |
| account_name   | required (create) / sometimes, string, max:255 |
| account_number | required (create) / sometimes, string, max:50  |
| routing_number | nullable, string, max:50             |
| swift_code     | nullable, string, max:20             |
| currency       | nullable, string, max:10             |
| is_active      | nullable, boolean                    |

---

## Eloquent Models & Relationships

### User Model (`App\Models\User`)

```php
// Attributes
$user->id;
$user->name;
$user->email;
$user->phone;
$user->password;        // Hashed
$user->role;            // 'user' or 'admin'
$user->email_verified_at;
$user->created_at;
$user->updated_at;

// Methods
$user->isAdmin();       // Returns boolean

// Relationships
$user->bookings;        // HasMany Booking
$user->payments;        // HasMany Payment

// Usage
$user->bookings()->where('status', 'active')->get();
$user->payments()->where('status', 'verified')->sum('amount');
```

### Booking Model (`App\Models\Booking`)

```php
// Attributes (partial list)
$booking->id;
$booking->user_id;
$booking->booking_reference;    // KSP-YYYY-XXXXXX
$booking->applicant_name_en;
$booking->present_address;      // Cast to array
$booking->permanent_address;    // Cast to array
$booking->nominee_address;      // Cast to array
$booking->date_of_birth;        // Cast to date
$booking->no_of_shares;
$booking->payment_mode;         // 'installment' or 'at_a_time'
$booking->total_amount;         // Cast to decimal
$booking->paid_amount;          // Cast to decimal
$booking->status;               // 'pending', 'active', etc.
$booking->payment_status;       // 'unpaid', 'partial', 'paid'
$booking->next_due_date;        // Cast to date

// Computed Attributes
$booking->due_amount;           // total_amount - paid_amount

// Methods
Booking::generateBookingReference();    // Static: returns 'KSP-2026-000001'
$booking->updatePaymentStatus();        // Updates payment_status based on paid_amount

// Relationships
$booking->user;                 // BelongsTo User
$booking->payments;             // HasMany Payment
$booking->installments;         // HasMany PaymentInstallment

// Scopes
Booking::forUser($userId)->get();

// Usage
$booking = Booking::with(['payments', 'installments'])->find(1);
$booking->payments()->where('status', 'verified')->get();
```

### Payment Model (`App\Models\Payment`)

```php
// Attributes
$payment->id;
$payment->booking_id;
$payment->user_id;
$payment->amount;               // Cast to decimal
$payment->payment_method;
$payment->bank_name;
$payment->transaction_reference;
$payment->payment_date;         // Cast to date
$payment->receipt_path;
$payment->status;               // 'pending', 'verified', 'rejected'
$payment->verified_by;          // Admin user_id
$payment->verified_at;          // Cast to datetime
$payment->rejection_reason;

// Methods
$payment->verify($adminId);     // Marks as verified, updates booking
$payment->reject($adminId, $reason);  // Marks as rejected

// Relationships
$payment->booking;              // BelongsTo Booking
$payment->user;                 // BelongsTo User
$payment->verifier;             // BelongsTo User (admin)

// Usage
$payment->verify(auth()->id());
$payment->booking->paid_amount; // Updated after verify()
```

### BankAccount Model (`App\Models\BankAccount`)

```php
// Attributes
$bankAccount->id;
$bankAccount->bank_name;
$bankAccount->branch_name;
$bankAccount->account_name;
$bankAccount->account_number;
$bankAccount->routing_number;
$bankAccount->swift_code;
$bankAccount->currency;         // Default: 'BDT'
$bankAccount->is_active;        // Cast to boolean

// Scopes
BankAccount::active()->get();   // Only active accounts

// Usage
$accounts = BankAccount::active()->get();
```

### PaymentInstallment Model (`App\Models\PaymentInstallment`)

```php
// Attributes
$installment->id;
$installment->booking_id;
$installment->installment_number;
$installment->amount;           // Cast to decimal
$installment->due_date;         // Cast to date
$installment->status;           // 'pending', 'paid', 'overdue'

// Methods
$installment->markAsPaid();

// Relationships
$installment->booking;          // BelongsTo Booking

// Scopes
PaymentInstallment::pending()->get();
PaymentInstallment::overdue()->get();

// Usage
$booking->installments()->pending()->orderBy('installment_number')->first();
```

---

## Enums Reference

### User Role
| Value   | Description           |
|---------|-----------------------|
| `user`  | Regular user (default)|
| `admin` | Administrator         |

### Profession
| Value             | Description      |
|-------------------|------------------|
| `private_service` | Private Service  |
| `govt_service`    | Government Service |
| `business`        | Business         |
| `others`          | Others           |

### Payment Mode
| Value        | Description         |
|--------------|---------------------|
| `installment`| Pay in installments |
| `at_a_time`  | Pay full amount     |

### Nominee Relation
| Value      | Description |
|------------|-------------|
| `spouse`   | Spouse      |
| `son`      | Son         |
| `daughter` | Daughter    |
| `father`   | Father      |
| `mother`   | Mother      |
| `others`   | Others      |

### Payment Method
| Value                 | Description              |
|-----------------------|--------------------------|
| `bank_transfer_swift` | SWIFT Bank Transfer      |
| `bank_transfer_local` | Local Bank Transfer      |
| `cheque`              | Cheque                   |
| `pay_order`           | Pay Order                |

### Booking Status
| Value                   | Description                    |
|-------------------------|--------------------------------|
| `pending`               | Awaiting activation            |
| `active`                | Active booking                 |
| `completed`             | Fully paid and completed       |
| `cancelled`             | Cancelled                      |
| `cancellation_requested`| Cancellation pending approval  |

### Payment Status (Booking)
| Value     | Description              |
|-----------|--------------------------|
| `unpaid`  | No payments made         |
| `partial` | Partially paid           |
| `paid`    | Fully paid               |

### Payment Receipt Status
| Value      | Description               |
|------------|---------------------------|
| `pending`  | Awaiting admin verification |
| `verified` | Payment verified by admin |
| `rejected` | Payment rejected by admin |

### Installment Status
| Value     | Description              |
|-----------|--------------------------|
| `pending` | Not yet paid             |
| `paid`    | Paid                     |
| `overdue` | Past due date, not paid  |

---

## Configuration

### `config/booking.php`

```php
<?php

return [
    // Price per share in BDT
    'share_price' => env('SHARE_PRICE', 100000),

    // Number of installments for installment payment mode
    'installment_count' => env('INSTALLMENT_COUNT', 12),

    // Cancellation fee percentage (5% = 0.05)
    'cancellation_fee_percentage' => env('CANCELLATION_FEE_PERCENTAGE', 0.05),

    // Maximum file sizes in KB
    'max_photo_size' => env('MAX_PHOTO_SIZE', 2048),      // 2MB
    'max_receipt_size' => env('MAX_RECEIPT_SIZE', 5120),  // 5MB

    // Booking reference prefix
    'booking_reference_prefix' => env('BOOKING_REFERENCE_PREFIX', 'KSP'),
];
```

### Usage in Code

```php
$sharePrice = config('booking.share_price');           // 100000
$installmentCount = config('booking.installment_count'); // 12
$cancellationFee = config('booking.cancellation_fee_percentage'); // 0.05
```

---

## File Storage

### Storage Locations

| File Type        | Storage Path                          | Public URL                                    |
|------------------|---------------------------------------|-----------------------------------------------|
| Applicant Photos | `storage/app/public/bookings/photos/` | `http://domain.com/storage/bookings/photos/`  |
| Nominee Photos   | `storage/app/public/bookings/photos/` | `http://domain.com/storage/bookings/photos/`  |
| Signatures       | `storage/app/public/bookings/signatures/` | `http://domain.com/storage/bookings/signatures/` |
| Payment Receipts | `storage/app/public/payments/receipts/` | `http://domain.com/storage/payments/receipts/` |

### Storage Link

Run this command to create the symbolic link:

```bash
php artisan storage:link
```

This creates: `public/storage` -> `storage/app/public`

### File Size Limits

| File Type       | Max Size |
|-----------------|----------|
| Applicant Photo | 2 MB     |
| Nominee Photo   | 2 MB     |
| Payment Receipt | 5 MB     |

### Accepted Formats

| File Type       | Formats              |
|-----------------|----------------------|
| Photos          | jpg, jpeg, png, gif  |
| Signatures      | Base64 encoded image |
| Receipts        | pdf, jpg, jpeg, png  |

---

## Error Handling

### Validation Errors (422 Unprocessable Entity)

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "The email has already been taken."
    ],
    "phone": [
      "The phone has already been taken."
    ],
    "password": [
      "The password must be at least 6 characters."
    ]
  }
}
```

### Authentication Errors (401 Unauthorized)

```json
{
  "message": "Unauthenticated."
}
```

### Authorization Errors (403 Forbidden)

```json
{
  "message": "Unauthorized. Admin access required."
}
```

### Not Found Errors (404 Not Found)

```json
{
  "message": "No query results for model [App\\Models\\Booking] 999"
}
```

### Business Logic Errors (422 Unprocessable Entity)

```json
{
  "message": "Only active bookings can be cancelled."
}
```

```json
{
  "message": "Only pending payments can be verified."
}
```

---

## Production Deployment

### 1. Update Environment

```bash
# Edit .env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_HOST=your-mysql-host
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_secure_password
```

### 2. Install Dependencies

```bash
composer install --optimize-autoloader --no-dev
```

### 3. Run Migrations

```bash
php artisan migrate --force
```

### 4. Seed Database (Optional)

```bash
php artisan db:seed --force
```

### 5. Create Storage Link

```bash
php artisan storage:link
```

### 6. Cache Configuration

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 7. Set Permissions

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 8. Web Server Configuration

**Nginx Example:**

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/krishi-booking-laravel-api/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**Apache (.htaccess already included in `public/`):**

Point DocumentRoot to the `public/` directory.

---

## Tech Stack

| Component      | Technology                |
|----------------|---------------------------|
| Framework      | Laravel 11                |
| PHP Version    | 8.1+ (8.1, 8.2, 8.3)      |
| Authentication | Laravel Sanctum           |
| ORM            | Eloquent                  |
| Database       | MySQL 5.7+ / 8.0+         |
| File Storage   | Laravel Filesystem (local)|

### cPanel Compatibility

This API is fully compatible with shared hosting environments like cPanel that support:
- PHP 8.1 or higher
- MySQL 5.7 or higher
- Composer

---

## License

MIT License

---

## Support

For issues and feature requests, please open an issue on GitHub.
