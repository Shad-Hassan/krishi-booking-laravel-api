# Sea Palace Booking REST API

A Laravel 12 REST API for Sea Palace share booking system with authentication, booking management, payment processing, and admin dashboard.

---

## 5-Minute Setup (MySQL Ready)

The `.env` file is already configured. Just update MySQL credentials and run:

```bash
# 1. Clone and install
git clone https://github.com/Shad-Hassan/krishi-booking-laravel-api.git
cd krishi-booking-laravel-api
composer install

# 2. Create MySQL database
mysql -u root -p -e "CREATE DATABASE sea_palace_booking;"

# 3. Update .env with your MySQL password (if needed)
# DB_PASSWORD=your_password

# 4. Run migrations and seed
php artisan migrate
php artisan db:seed

# 5. Create storage link
php artisan storage:link

# 6. Serve
php artisan serve
```

**API Base URL:** `http://localhost:8000/api`

---

## Pre-configured .env

The `.env` file is already set up with:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sea_palace_booking
DB_USERNAME=root
DB_PASSWORD=

SHARE_PRICE=100000
INSTALLMENT_COUNT=12
```

Just update `DB_PASSWORD` if your MySQL has a password.

---

## Test Credentials (After Seeding)

| Role  | Email                  | Phone        | Password     |
|-------|------------------------|--------------|--------------|
| Admin | admin@seapalace.com    | 01700000000  | password123  |
| User  | user@example.com       | 01711111111  | password123  |

---

## Eloquent Models

| Model | Table | Description |
|-------|-------|-------------|
| `App\Models\User` | users | Extended with phone, role |
| `App\Models\Booking` | bookings | Share bookings with applicant/nominee info |
| `App\Models\Payment` | payments | Payment receipts |
| `App\Models\BankAccount` | bank_accounts | Company bank accounts |
| `App\Models\PaymentInstallment` | payment_installments | Installment schedules |

### Relationships

```php
// User
$user->bookings;        // HasMany Booking
$user->payments;        // HasMany Payment

// Booking
$booking->user;         // BelongsTo User
$booking->payments;     // HasMany Payment
$booking->installments; // HasMany PaymentInstallment

// Payment
$payment->user;         // BelongsTo User
$payment->booking;      // BelongsTo Booking
$payment->verifier;     // BelongsTo User (admin who verified)
```

---

## Database Schema

### users (extended)
```sql
- id, name, email (unique), phone (unique), password
- role ENUM('user', 'admin') DEFAULT 'user'
- timestamps
```

### bookings
```sql
- id, user_id (FK), booking_reference (unique, format: KSP-YYYY-XXXXXX)
- applicant_name_en, applicant_name_bn, father_name, mother_name, spouse_name
- present_address (JSON), permanent_address (JSON)
- nationality, date_of_birth, nid_passport, marriage_date
- mobile_1, mobile_2, email, tin, profession, designation_address
- nominee_name, nominee_address (JSON), nominee_relation, nominee_nid, nominee_dob
- nominee_mobile_1, nominee_mobile_2
- no_of_shares, category_ownership, payment_mode ENUM('installment', 'at_a_time')
- applicant_photo_path, nominee_photo_path, signature_path
- total_amount, paid_amount, refund_amount
- status ENUM('pending', 'active', 'completed', 'cancelled', 'cancellation_requested')
- payment_status ENUM('unpaid', 'partial', 'paid')
- next_due_date, agreed_to_terms, cancellation_reason
- timestamps
```

### payments
```sql
- id, booking_id (FK), user_id (FK)
- amount, payment_method, bank_name, transaction_reference, payment_date
- receipt_path
- status ENUM('pending', 'verified', 'rejected')
- verified_by (FK users), verified_at, rejection_reason
- timestamps
```

### bank_accounts
```sql
- id, bank_name, branch_name, account_name, account_number
- routing_number, swift_code, currency, is_active
- timestamps
```

### payment_installments
```sql
- id, booking_id (FK), installment_number, amount, due_date
- status ENUM('pending', 'paid', 'overdue')
- timestamps
```

---

## API Endpoints

### Authentication (Public)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/register` | Register user |
| POST | `/api/login` | Login (email OR phone) |
| GET | `/api/bank-accounts` | Get bank accounts |

### User Endpoints (Auth Required)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/logout` | Logout |
| GET | `/api/user` | Get profile |
| GET | `/api/bookings` | List my bookings |
| POST | `/api/bookings` | Create booking |
| GET | `/api/bookings/{id}` | Booking details |
| GET | `/api/bookings/{id}/payments` | Payment history |
| GET | `/api/bookings/{id}/payment-summary` | Payment summary |
| POST | `/api/bookings/{id}/cancel` | Request cancellation |
| POST | `/api/payments` | Submit payment |

### Admin Endpoints (Admin Auth Required)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/dashboard` | Stats |
| GET | `/api/admin/dashboard/recent-activity` | Recent activity |
| GET | `/api/admin/bookings` | All bookings |
| GET | `/api/admin/bookings/{id}` | Booking details |
| PUT | `/api/admin/bookings/{id}/activate` | Activate booking |
| PUT | `/api/admin/bookings/{id}/process-cancellation` | Process cancel |
| GET | `/api/admin/payments` | All payments |
| PUT | `/api/admin/payments/{id}/verify` | Verify payment |
| PUT | `/api/admin/payments/{id}/reject` | Reject payment |
| GET | `/api/admin/bank-accounts` | List bank accounts |
| POST | `/api/admin/bank-accounts` | Create bank account |
| PUT | `/api/admin/bank-accounts/{id}` | Update bank account |
| DELETE | `/api/admin/bank-accounts/{id}` | Delete bank account |

---

## Quick API Test

```bash
# Register
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@test.com","phone":"01799999999","password":"password123","password_confirmation":"password123"}'

# Login (returns token)
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"identifier":"admin@seapalace.com","password":"password123"}'

# Use token for authenticated requests
curl -X GET http://localhost:8000/api/user \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── AuthController.php
│   │       ├── BookingController.php
│   │       ├── PaymentController.php
│   │       └── Admin/
│   │           ├── BookingController.php
│   │           ├── PaymentController.php
│   │           ├── DashboardController.php
│   │           └── BankAccountController.php
│   ├── Middleware/
│   │   └── AdminMiddleware.php
│   ├── Requests/
│   │   ├── RegisterRequest.php
│   │   ├── LoginRequest.php
│   │   ├── BookingRequest.php
│   │   ├── PaymentRequest.php
│   │   └── CancelBookingRequest.php
│   └── Resources/
│       ├── UserResource.php
│       ├── BookingResource.php
│       ├── PaymentResource.php
│       └── ...
└── Models/
    ├── User.php
    ├── Booking.php
    ├── Payment.php
    ├── BankAccount.php
    └── PaymentInstallment.php

config/
└── booking.php          # Share price, installment config

database/
├── migrations/          # All table schemas
└── seeders/
    └── DatabaseSeeder.php  # Admin user + bank accounts
```

---

## Enums

### profession
`private_service`, `govt_service`, `business`, `others`

### payment_mode
`installment`, `at_a_time`

### nominee_relation
`spouse`, `son`, `daughter`, `father`, `mother`, `others`

### payment_method
`bank_transfer_swift`, `bank_transfer_local`, `cheque`, `pay_order`

### booking.status
`pending`, `active`, `completed`, `cancelled`, `cancellation_requested`

### booking.payment_status
`unpaid`, `partial`, `paid`

### payment.status
`pending`, `verified`, `rejected`

---

## Production Deployment

```bash
# Update .env
APP_ENV=production
APP_DEBUG=false
DB_PASSWORD=secure_password

# Run
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan db:seed
php artisan storage:link
php artisan config:cache
php artisan route:cache

# Set permissions
chmod -R 775 storage bootstrap/cache
```

Point web server to `public/` directory.

---

## Tech Stack

- **Laravel 12** - PHP Framework
- **Laravel Sanctum** - API Token Authentication
- **Eloquent ORM** - Database layer
- **MySQL** - Database
- **PHP 8.2+**

---

## License

MIT
