# Complete OTP Implementation - File & Code Locations

## 1. FILES WITH "OTP" IN NAME OR PATH

### Controllers
- **[app/Http/Controllers/Auth/OtpController.php](app/Http/Controllers/Auth/OtpController.php)** - Login OTP verification
  - `show()` [Lines 18-57] - Display OTP verification page
  - `verify()` [Lines 62-128] - Verify login OTP
  - `handleLoginSuccess()` [Lines 177-195] - Handle successful login
  - `handleRegistrationSuccess()` [Lines 200-209] - Handle successful registration after OTP
  - Helper methods: `maskEmail()`, `canResendOtp()`, `getResendWaitSeconds()`, `isAccountLocked()`, `lockAccount()`, `resend()`

- **[app/Http/Controllers/Auth/RegisterOtpController.php](app/Http/Controllers/Auth/RegisterOtpController.php)** - Registration OTP verification
  - `show()` [Lines 17-42] - Display registration OTP page
  - `verify()` [Lines 47-146] - Verify registration OTP
  - Supports both new (`otp_user_id`) and old (`register_otp_user_id`) session keys for backward compatibility
  - Helper methods: `isAccountLocked()`, `lockAccount()`, `createEmployeeRecord()`, `resend()`

### Views
- **[resources/views/emails/otp.blade.php](resources/views/emails/otp.blade.php)** - Email template for OTP
  - [Lines 6-131] - HTML email template with styled OTP display
  - Uses variables: `$purpose`, `$otp`
  - Displays OTP code in formatted box [Line 118]
  - Warning message about OTP security [Line 127]

- **[resources/views/auth/otp.blade.php](resources/views/auth/otp.blade.php)** - Login OTP verification form
  - [Lines 1-100+] - Interactive OTP input form
  - Features: OTP input fields, countdown timer, resend functionality
  - Uses variables: `$purpose`, `$email`, `$masked_email`, `$remaining_seconds`, `$attempts_remaining`, `$can_resend`, `$resend_wait_seconds`

- **[resources/views/auth/register-otp.blade.php](resources/views/auth/register-otp.blade.php)** - Registration OTP verification form
  - [Lines 615-878] - Registration with OTP verification
  - [Line 615] - Staff registration with OTP comment
  - [Line 682] - Agent registration (NO OTP) comment
  - [Line 846] - "Secure OTP verification" span
  - [Line 878] - Sweet alert message for OTP verification

### Migrations
- **[database/migrations/2026_01_27_230034_add_otp_fields_to_users.php](database/migrations/2026_01_27_230034_add_otp_fields_to_users.php)** - Initial OTP fields
  - [Line 16] - `login_otp` (string, nullable)
  - [Line 17] - `otp_expires_at` (timestamp, nullable)

- **[database/migrations/2026_03_10_154920_add_otp_tracking_fields_to_users.php](database/migrations/2026_03_10_154920_add_otp_tracking_fields_to_users.php)** - OTP tracking fields
  - [Line 13] - `otp_attempts` (integer, default 0) - Track failed OTP attempts
  - [Line 14] - `otp_last_sent_at` (timestamp, nullable) - Track when OTP was last sent (60-second cooldown)
  - [Line 15] - `last_otp_verified_at` (timestamp, nullable) - Track when OTP was verified
  - [Line 18] - `register_otp` (string, nullable) - Registration OTP (separate from login)
  - [Line 19] - `register_otp_expires_at` (timestamp, nullable) - Registration OTP expiry

- **[database/migrations/2026_03_19_163606_update_shipments_table.php](database/migrations/2026_03_19_163606_update_shipments_table.php)** - Shipment delivery OTP
  - [Line 76] - Comment: "Customer OTP for delivery"
  - [Line 77-78] - `delivery_otp` (string(6), nullable) - OTP for delivery verification
  - [Line 81-82] - `otp_verified_at` (timestamp, nullable) - Track when delivery OTP was verified

---

## 2. OTP ROUTES

### routes/auth.php
- **Login OTP Routes** [Lines 75-85]
  - [Line 78-79] - `GET /otp` → `OtpController@show` → route name: `otp.verify`
  - [Line 81-82] - `POST /otp` → `OtpController@verify` → route name: `otp.verify.post`
  - [Line 84-85] - `POST /otp/resend` → `OtpController@resend` → route name: `otp.resend`

- **Registration OTP Routes** [Lines 89-99]
  - [Line 92-93] - `GET /register-otp` → `RegisterOtpController@show` → route name: `register.otp`
  - [Line 95-96] - `POST /register-otp` → `RegisterOtpController@verify` → route name: `register.otp.verify`
  - [Line 98-99] - `POST /register-otp/resend` → `RegisterOtpController@resend` → route name: `register.otp.resend`

- **Imports** [Lines 63-64]
  - `use App\Http\Controllers\Auth\OtpController;`
  - `use App\Http\Controllers\Auth\RegisterOtpController;`

### routes/web.php
- **Imports** [Lines 36, 38]
  - [Line 36] - `use App\Http\Controllers\Auth\OtpController;`
  - [Line 38] - `use App\Http\Controllers\Auth\RegisterOtpController;`

- **Registration OTP Routes** [Lines 130-133]
  - [Line 131] - `GET /register-otp` → `RegisterOtpController@show` → route name: `register.otp`
  - [Line 132] - `POST /register-otp/verify` → `RegisterOtpController@verify` → route name: `register.otp.verify`
  - [Line 133] - `POST /register-otp/resend` → `RegisterOtpController@resend` → route name: `register.otp.resend`

- **Login OTP Routes** [Lines 135-138]
  - [Line 136] - `GET /otp-verify` → `OtpController@show` → route name: `otp.verify`
  - [Line 137] - `POST /otp-verify` → `OtpController@verify` → route name: `otp.verify.post`
  - [Line 138] - `POST /otp-resend` → `OtpController@resend` → route name: `otp.resend`

- **Test Route** [Line 112]
  - `"htmlContent" => "<h1>OTP: 123456</h1>"` - Test OTP display

---

## 3. OTP CONTROLLER METHODS

### AuthenticatedSessionController.php - Location-based OTP
File: [app/Http/Controllers/Auth/AuthenticatedSessionController.php](app/Http/Controllers/Auth/AuthenticatedSessionController.php)

- **Main Method** [Lines 29-128]
  - `store()` [Line 31] - "Handle login request with location-based OTP and role-based redirect"
  - [Line 85] - Admin check: "CHECK IF USER IS ADMIN - NO OTP REQUIRED"
  - [Line 87] - `Log::info('Admin login - bypassing OTP', [...])`

- **OTP Sending Logic** 
  - [Lines 104-108] - CASE A: Location OFF/DENIED → EMAIL OTP REQUIRED
    - Calls `sendLoginOtp()` with message about location access
  - [Lines 123-127] - CASE B: Outside office radius → EMAIL OTP REQUIRED
    - Calculates distance and sends OTP if outside allowed radius

- **sendLoginOtp() Method** [Lines 136-175]
  - Signature: `private function sendLoginOtp(User $user, string $message): RedirectResponse`
  - [Line 141] - Check `$user->otp_last_sent_at` for resend cooldown
  - [Line 148-149] - Generate 6-digit OTP: `sprintf("%06d", random_int(0, 999999))`
  - [Line 152] - Store in `$user->login_otp`
  - [Line 153] - Set `$user->otp_expires_at = now()->addMinutes(5)`
  - [Line 154] - Set `$user->otp_last_sent_at = now()`
  - [Line 155] - Reset `$user->otp_attempts = 0`
  - [Line 159-163] - Set session variables: `otp_user_id`, `otp_purpose`, `otp_email`, `otp_sent_at`, `otp_expires_at`
  - [Line 166] - Send OTP via email: `$this->sendOtpEmail($user, $otp, 'Login')`
  - [Line 175] - Redirect to `route('otp.verify')`

- **directLogin() Method** [Lines 179-198]
  - "Direct login without OTP - WITH ROLE-BASED REDIRECT"
  - [Lines 188-190] - Clear OTP data: `$user->login_otp = null`, `$user->otp_expires_at = null`
  - [Line 198] - Clear OTP session: `Session::forget(['otp_user_id', 'otp_purpose', ...])`

- **Helper Methods**
  - [Lines 312-320] - `canResendOtp(User $user): bool` - Check 60-second cooldown
  - [Lines 322-332] - `getResendWaitSeconds(User $user)` - Calculate remaining wait time
  - [Lines 337-339] - `sendOtpEmail(User $user, string $otp, string $purpose): void` - Send OTP email

---

## 4. OTP MODEL METHODS

### User.php
File: [app/Models/User.php](app/Models/User.php)

- **Fillable Attributes** [Lines 30-33]
  - [Line 31] - `'otp'`
  - [Line 32] - `'otp_expires_at'`
  - [Line 33] - `'otp_verified_at'`

- **Casts** [Lines 43, 49-50]
  - [Line 43] - `'otp'` - Hidden from array output
  - [Line 49] - `'otp_expires_at' => 'datetime'`
  - [Line 50] - `'otp_verified_at' => 'datetime'`

- **generateOtp() Method** [Lines 439-453]
  - Signature: `public function generateOtp($length = 6)`
  - [Line 443] - Initialize empty OTP string
  - [Lines 444-445] - Loop to generate random digits
  - [Line 448] - Store in `$this->otp`
  - [Line 449] - Set expiry: `$this->otp_expires_at = now()->addMinutes(10)`
  - [Line 450] - Reset verification: `$this->otp_verified_at = null`
  - [Line 453] - Return generated OTP

- **verifyOtp() Method** [Lines 457-475]
  - Signature: `public function verifyOtp($otp)`
  - [Line 461] - Check if OTP exists and not expired
  - [Line 465] - Check if OTP is expired
  - [Line 469] - Compare OTP value
  - [Line 473] - Set `$this->otp_verified_at = now()`
  - [Line 474] - Clear OTP: `$this->otp = null`
  - [Line 475] - Clear expiry: `$this->otp_expires_at = null`

---

## 5. DATABASE SCHEMA SUMMARY

### Users Table - OTP Fields
```
Column Name                    | Type          | Default | Nullable | Purpose
-------------------------------|---------------|---------|----------|---------------------------
login_otp                      | string        | -       | YES      | Login OTP code
otp_expires_at                 | timestamp     | -       | YES      | Login OTP expiry
otp_attempts                   | integer       | 0       | NO       | Failed OTP attempt counter
otp_last_sent_at               | timestamp     | -       | YES      | Last OTP send time (60s cooldown)
last_otp_verified_at           | timestamp     | -       | YES      | Last successful OTP verification
register_otp                   | string        | -       | YES      | Registration OTP code
register_otp_expires_at        | timestamp     | -       | YES      | Registration OTP expiry
```

### Shipments Table - Delivery OTP Fields
```
Column Name                    | Type          | Default | Nullable | Purpose
-------------------------------|---------------|---------|----------|---------------------------
delivery_otp                   | string(6)     | -       | YES      | Customer delivery OTP
otp_verified_at                | timestamp     | -       | YES      | Delivery OTP verification time
```

---

## 6. OTP SECURITY & CONSTRAINTS

### Expiry & Timeout
- **Login OTP**: 5 minutes expiry [AuthenticatedSessionController.php:153]
- **Registration OTP**: 5 minutes expiry (based on migrations)
- **Model OTP**: 10 minutes expiry [User.php:449]

### Rate Limiting
- **Max Attempts**: 5 failed attempts before account lock [OtpController.php:107, RegisterOtpController.php:92]
- **Resend Cooldown**: 60 seconds between resend requests [AuthenticatedSessionController.php:320]
- **Account Lock Duration**: 15 minutes [OtpController.php:119, RegisterOtpController.php:104]

### Session Management
- **Session Keys Used**:
  - `otp_user_id` - User ID for OTP verification
  - `otp_purpose` - OTP purpose ('login' or 'registration')
  - `otp_email` - User email for display
  - `otp_sent_at` - Timestamp of OTP send (for 10-minute session validation)
  - `otp_expires_at` - OTP expiry timestamp
  - `register_otp_user_id` - Old session key (backward compatibility)

### Logging
- Invalid OTP attempts logged [OtpController.php:138-145]
- Admin login bypass logged [AuthenticatedSessionController.php:87]
- Account lock events logged [OtpController.php:113-117]
- OTP verification success logged [OtpController.php:152-156]

---

## 7. CONDITIONAL LOGIC FOR OTP

### When OTP is Required (AuthenticatedSessionController.php)
- [Line 85] - NOT an admin user
- [Line 104-108] - Location access denied/OFF
- [Line 123-127] - User outside office radius

### When OTP is NOT Required
- Admin users - Bypassed completely
- Users within office location radius with location access granted

---

## 8. VIEW COMPONENTS

### OTP Email Template [resources/views/emails/otp.blade.php]
- [Line 6] - Title: "ERP OTP Verification"
- [Line 41] - CSS class: `.otp-box`
- [Line 49] - CSS class: `.otp-code` (52px font size)
- [Line 106] - Heading: "🔐 ERP {{ $purpose }} OTP"
- [Line 114] - Message: "You requested an OTP for <strong>{{ $purpose }}</strong> verification"
- [Line 118] - OTP display: `{{ $otp }}`
- [Line 123] - Expiry notice: "This OTP will expire in 5 minutes"
- [Line 127] - Security warning: "Never share this OTP with anyone"
- [Line 131] - Fallback: "If you didn't request this OTP, please ignore this email"

### OTP Login Form [resources/views/auth/otp.blade.php]
- Two-panel layout design
- OTP input with 6-digit fields
- Countdown timer
- Resend button (conditionally enabled based on cooldown)
- Email masking for privacy
- Attempt counter display

### OTP Registration Form [resources/views/auth/register-otp.blade.php]
- Staff registration with OTP requirement [Line 615]
- Agent registration without OTP [Line 682]
- OTP verification badge [Line 846]
- Sweet Alert confirmation [Line 878]

---

## 9. INTEGRATION POINTS

- **Auth Flow**: Login → Location Check → OTP Required → Send Email → Verify OTP → Set Session → Redirect
- **Registration Flow**: Register → Send OTP Email → Verify OTP → Create Employee Record → Redirect to Login
- **Email Service**: Mailable class sends OTP via configured email driver
- **Session Management**: Uses Laravel's session facade for state management
- **Database**: OTP fields tracked on users table for audit and security
