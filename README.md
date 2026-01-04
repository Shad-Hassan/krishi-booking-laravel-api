# Sea Palace Booking REST API

A Laravel 12 REST API for Sea Palace share booking system with authentication, booking management, payment processing, and admin dashboard.

## Quick Setup (For Laravel Developers)

```bash
# 1. Clone and install
git clone https://github.com/Shad-Hassan/krishi-booking-laravel-api.git
cd krishi-booking-laravel-api
composer install

# 2. Configure environment
cp .env.example .env
php artisan key:generate

# 3. Set MySQL in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sea_palace_booking
DB_USERNAME=your_username
DB_PASSWORD=your_password

# 4. Run migrations and seed
php artisan migrate
php artisan db:seed

# 5. Create storage link for file uploads
php artisan storage:link

# 6. Serve
php artisan serve
```

API will be available at `http://localhost:8000/api`

---

## Test Credentials (After Seeding)

| Role  | Email                  | Phone        | Password     |
|-------|------------------------|--------------|--------------|
| Admin | admin@seapalace.com    | 01700000000  | password123  |
| User  | user@example.com       | 01711111111  | password123  |

---

## Database Schema

### Tables Created

1. **users** - Extended with `phone` (unique) and `role` (user/admin)
2. **bookings** - All applicant, nominee, share info with JSON address fields
3. **payments** - Payment receipts with verification status
4. **bank_accounts** - Company bank account details
5. **payment_installments** - Installment schedule for bookings
6. **personal_access_tokens** - Sanctum tokens

---

## API Endpoints

### Authentication

| Method | Endpoint        | Auth | Description                          |
|--------|-----------------|------|--------------------------------------|
| POST   | `/api/register` | No   | Register new user                    |
| POST   | `/api/login`    | No   | Login with email OR phone + password |
| POST   | `/api/logout`   | Yes  | Logout (invalidate token)            |
| GET    | `/api/user`     | Yes  | Get current user profile             |

### Bookings (User)

| Method | Endpoint                           | Auth | Description                    |
|--------|------------------------------------|------|--------------------------------|
| GET    | `/api/bookings`                    | Yes  | List user's bookings (paginated) |
| POST   | `/api/bookings`                    | Yes  | Create new booking             |
| GET    | `/api/bookings/{id}`               | Yes  | Get booking details            |
| GET    | `/api/bookings/{id}/payments`      | Yes  | Get payment history            |
| GET    | `/api/bookings/{id}/payment-summary` | Yes | Get payment summary with installments |
| POST   | `/api/bookings/{id}/cancel`        | Yes  | Request booking cancellation   |

### Payments (User)

| Method | Endpoint            | Auth | Description                |
|--------|---------------------|------|----------------------------|
| POST   | `/api/payments`     | Yes  | Submit payment receipt     |
| GET    | `/api/bank-accounts`| No   | Get company bank accounts  |

### Admin Endpoints

| Method | Endpoint                                    | Auth  | Description                    |
|--------|---------------------------------------------|-------|--------------------------------|
| GET    | `/api/admin/dashboard`                      | Admin | Dashboard statistics           |
| GET    | `/api/admin/dashboard/recent-activity`      | Admin | Recent bookings & payments     |
| GET    | `/api/admin/bookings`                       | Admin | List all bookings (filterable) |
| GET    | `/api/admin/bookings/{id}`                  | Admin | Get any booking details        |
| PUT    | `/api/admin/bookings/{id}/activate`         | Admin | Activate pending booking       |
| PUT    | `/api/admin/bookings/{id}/process-cancellation` | Admin | Process cancellation (5% fee) |
| GET    | `/api/admin/payments`                       | Admin | List all payments              |
| PUT    | `/api/admin/payments/{id}/verify`           | Admin | Verify payment                 |
| PUT    | `/api/admin/payments/{id}/reject`           | Admin | Reject payment (with reason)   |
| GET    | `/api/admin/bank-accounts`                  | Admin | List all bank accounts         |
| POST   | `/api/admin/bank-accounts`                  | Admin | Create bank account            |
| PUT    | `/api/admin/bank-accounts/{id}`             | Admin | Update bank account            |
| DELETE | `/api/admin/bank-accounts/{id}`             | Admin | Delete bank account            |
| PUT    | `/api/admin/bank-accounts/{id}/toggle-status` | Admin | Toggle active status         |

---

## API Usage Examples

### 1. Register User

```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "01712345678",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Response:**
```json
{
  "message": "Registration successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "01712345678",
    "role": "user"
  },
  "token": "1|abc123xyz..."
}
```

### 2. Login (Email or Phone)

```bash
# Login with email
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "identifier": "john@example.com",
    "password": "password123"
  }'

# Login with phone
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "identifier": "01712345678",
    "password": "password123"
  }'
```

### 3. Create Booking (multipart/form-data)

```bash
curl -X POST http://localhost:8000/api/bookings \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "applicant_name_en=John Doe" \
  -F "father_name=Father Name" \
  -F "mother_name=Mother Name" \
  -F "present_address[house_vill]=123 Main St" \
  -F "present_address[post]=Dhaka GPO" \
  -F "present_address[thana]=Motijheel" \
  -F "present_address[district]=Dhaka" \
  -F "permanent_address[house_vill]=123 Main St" \
  -F "permanent_address[post]=Dhaka GPO" \
  -F "permanent_address[thana]=Motijheel" \
  -F "permanent_address[district]=Dhaka" \
  -F "date_of_birth=1990-01-15" \
  -F "nid_passport=1234567890123" \
  -F "mobile_1=01712345678" \
  -F "email=john@example.com" \
  -F "profession=business" \
  -F "nominee_name=Jane Doe" \
  -F "nominee_address[house_vill]=456 Other St" \
  -F "nominee_address[post]=Dhaka GPO" \
  -F "nominee_address[thana]=Gulshan" \
  -F "nominee_address[district]=Dhaka" \
  -F "nominee_relation=spouse" \
  -F "no_of_shares=5" \
  -F "payment_mode=installment" \
  -F "applicant_photo=@/path/to/photo.jpg" \
  -F "signature=data:image/png;base64,iVBORw0KGgo..." \
  -F "agreed_to_terms=1"
```

### 4. Submit Payment

```bash
curl -X POST http://localhost:8000/api/payments \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "booking_id=1" \
  -F "amount=50000" \
  -F "payment_method=bank_transfer_local" \
  -F "bank_name=Dutch-Bangla Bank" \
  -F "transaction_reference=TXN123456" \
  -F "payment_date=2026-01-05" \
  -F "receipt_file=@/path/to/receipt.pdf"
```

### 5. Admin: Verify Payment

```bash
curl -X PUT http://localhost:8000/api/admin/payments/1/verify \
  -H "Authorization: Bearer ADMIN_TOKEN"
```

### 6. Admin: Get Dashboard Stats

```bash
curl -X GET http://localhost:8000/api/admin/dashboard \
  -H "Authorization: Bearer ADMIN_TOKEN"
```

**Response:**
```json
{
  "total_bookings": 25,
  "active_bookings": 18,
  "pending_bookings": 5,
  "total_revenue": 5000000,
  "pending_payments": 3,
  "pending_cancellations": 1,
  "total_shares_sold": 150
}
```

---

## Request/Response Headers

### All Authenticated Requests

```
Authorization: Bearer {token}
Accept: application/json
```

### Form Submissions with Files

```
Authorization: Bearer {token}
Content-Type: multipart/form-data
Accept: application/json
```

---

## Validation Rules Summary

### Registration
- `name`: required, max 255
- `email`: required, unique, valid email
- `phone`: required, unique, max 20
- `password`: required, min 6, confirmed

### Booking
- `applicant_photo`: required, image, max 2MB
- `nominee_photo`: optional, image, max 2MB
- `signature`: required, base64 string
- `no_of_shares`: required, integer, min 1
- `nid_passport`: required, unique per user
- `profession`: enum (private_service, govt_service, business, others)
- `payment_mode`: enum (installment, at_a_time)
- `nominee_relation`: enum (spouse, son, daughter, father, mother, others)

### Payment
- `receipt_file`: required, file (pdf/jpg/png), max 5MB
- `payment_method`: enum (bank_transfer_swift, bank_transfer_local, cheque, pay_order)
- `amount`: required, numeric, min 0.01

---

## Configuration

Edit `config/booking.php` to customize:

```php
return [
    'share_price' => env('SHARE_PRICE', 100000),           // BDT per share
    'installment_count' => env('INSTALLMENT_COUNT', 12),   // Number of installments
    'cancellation_fee_percentage' => 0.05,                  // 5% cancellation fee
];
```

---

## File Storage

Uploaded files are stored in:
- Applicant photos: `storage/app/public/bookings/photos/`
- Nominee photos: `storage/app/public/bookings/photos/`
- Signatures: `storage/app/public/bookings/signatures/`
- Payment receipts: `storage/app/public/payments/receipts/`

Access via URL: `http://your-domain.com/storage/bookings/photos/filename.jpg`

---

## Status Enums

### Booking Status
- `pending` - Awaiting activation
- `active` - Active booking
- `completed` - Fully paid
- `cancelled` - Cancelled
- `cancellation_requested` - Awaiting cancellation processing

### Payment Status (Booking)
- `unpaid` - No payments made
- `partial` - Partially paid
- `paid` - Fully paid

### Payment Receipt Status
- `pending` - Awaiting verification
- `verified` - Payment verified by admin
- `rejected` - Payment rejected (with reason)

---

## Admin Filtering

### GET /api/admin/bookings

Query parameters:
- `status`: pending, active, completed, cancelled, cancellation_requested
- `payment_status`: unpaid, partial, paid
- `date_from`: YYYY-MM-DD
- `date_to`: YYYY-MM-DD
- `search`: booking reference, name, or phone
- `per_page`: items per page (default: 15)

Example:
```
GET /api/admin/bookings?status=active&payment_status=partial&search=KSP-2026
```

---

## Booking Reference Format

Auto-generated: `KSP-YYYY-XXXXXX`

Example: `KSP-2026-000001`, `KSP-2026-000002`

---

## Error Responses

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email has already been taken."],
    "phone": ["The phone has already been taken."]
  }
}
```

```json
{
  "message": "Unauthenticated."
}
```

```json
{
  "message": "Unauthorized. Admin access required."
}
```

---

## Production Deployment Checklist

1. Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`
2. Configure MySQL credentials
3. Run `php artisan migrate --force`
4. Run `php artisan db:seed` (or manually create admin)
5. Run `php artisan storage:link`
6. Run `php artisan config:cache`
7. Run `php artisan route:cache`
8. Set proper file permissions for `storage/` and `bootstrap/cache/`
9. Configure web server (Apache/Nginx) to point to `public/` directory

---

## Tech Stack

- Laravel 12
- Laravel Sanctum (API Authentication)
- MySQL (recommended)
- PHP 8.2+

---

## License

MIT License
