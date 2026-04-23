# OTP Quick Reference - File Locations & Line Numbers

## All OTP Files - At a Glance

| File Path | Type | Key Lines | Purpose |
|-----------|------|-----------|---------|
| **app/Http/Controllers/Auth/OtpController.php** | Controller | 18-57, 62-128, 177-209 | Login & Registration OTP verification |
| **app/Http/Controllers/Auth/RegisterOtpController.php** | Controller | 17-42, 47-146 | Registration-specific OTP handling |
| **app/Http/Controllers/Auth/AuthenticatedSessionController.php** | Controller | 31-128, 136-175, 312-339 | Location-based OTP trigger logic |
| **app/Models/User.php** | Model | 30-50, 439-475 | OTP storage & verification methods |
| **resources/views/emails/otp.blade.php** | Email View | 6, 41, 49, 106-131 | Email template for OTP delivery |
| **resources/views/auth/otp.blade.php** | Form View | 1-100+ | Login OTP verification form |
| **resources/views/auth/register-otp.blade.php** | Form View | 615-878 | Registration OTP verification form |
| **database/migrations/2026_01_27_230034_add_otp_fields_to_users.php** | Migration | 16-17 | Initial OTP fields (login_otp, otp_expires_at) |
| **database/migrations/2026_03_10_154920_add_otp_tracking_fields_to_users.php** | Migration | 13-19 | OTP tracking fields (attempts, resend, register_otp) |
| **database/migrations/2026_03_19_163606_update_shipments_table.php** | Migration | 76-82 | Delivery OTP for shipments |
| **routes/auth.php** | Routes | 63-64, 75-99 | OTP route definitions |
| **routes/web.php** | Routes | 36, 38, 112, 130-138 | Web OTP routes & test routes |

---

## OTP Routes Summary

```
GET    /otp                    → OtpController@show         [auth.php:78-79]      (route: otp.verify)
POST   /otp                    → OtpController@verify       [auth.php:81-82]      (route: otp.verify.post)
POST   /otp/resend             → OtpController@resend       [auth.php:84-85]      (route: otp.resend)
GET    /register-otp           → RegisterOtpController@show [auth.php:92-93]      (route: register.otp)
POST   /register-otp           → RegisterOtpController@verify [auth.php:95-96]    (route: register.otp.verify)
POST   /register-otp/resend    → RegisterOtpController@resend [auth.php:98-99]    (route: register.otp.resend)
```

---

## OTP Database Columns

**users table:**
- `login_otp` (string, nullable) [migration:2026_01_27_230034 L16]
- `otp_expires_at` (timestamp, nullable) [migration:2026_01_27_230034 L17]
- `otp_attempts` (integer, default 0) [migration:2026_03_10_154920 L13]
- `otp_last_sent_at` (timestamp, nullable) [migration:2026_03_10_154920 L14]
- `last_otp_verified_at` (timestamp, nullable) [migration:2026_03_10_154920 L15]
- `register_otp` (string, nullable) [migration:2026_03_10_154920 L18]
- `register_otp_expires_at` (timestamp, nullable) [migration:2026_03_10_154920 L19]

**shipments table:**
- `delivery_otp` (string(6), nullable) [migration:2026_03_19_163606 L78]
- `otp_verified_at` (timestamp, nullable) [migration:2026_03_19_163606 L82]

---

## User Model OTP Methods

| Method | Location | Parameters | Purpose |
|--------|----------|-----------|---------|
| `generateOtp()` | User.php:441 | `$length = 6` | Generate 6-digit OTP, set 10-min expiry |
| `verifyOtp()` | User.php:459 | `$otp` | Verify OTP, check expiry, mark as verified |

---

## Key OTP Timeouts & Limits

- **OTP Expiry (Login)**: 5 minutes [AuthenticatedSessionController.php:153]
- **OTP Expiry (Registration)**: 5 minutes
- **OTP Expiry (Model)**: 10 minutes [User.php:449]
- **Max Failed Attempts**: 5 [OtpController.php:107]
- **Account Lock Duration**: 15 minutes
- **Resend Cooldown**: 60 seconds [AuthenticatedSessionController.php:320]
- **Session Validity**: 10 minutes [OtpController.php:33]

---

## OTP Flow Diagrams

### Login OTP Flow
1. User logs in
2. AuthenticatedSessionController@store [line 31]
3. Check if admin [line 85] → if yes, bypass OTP
4. Check location [lines 104-127] → if off/outside radius, send OTP
5. sendLoginOtp() [line 138]
   - Generate OTP [line 149]
   - Store in `login_otp` [line 152]
   - Set expiry [line 153]
   - Store session data [lines 159-163]
   - Send email [line 166]
6. Redirect to OtpController@show [line 175]
7. User enters OTP
8. OtpController@verify [line 62]
   - Verify OTP [lines 119-128]
   - handleLoginSuccess() [line 177]

### Registration OTP Flow
1. User registers
2. Send register OTP
3. RegisterOtpController@show [line 17]
4. User enters OTP
5. RegisterOtpController@verify [line 47]
   - Verify OTP [lines 116-125]
   - Activate account [lines 132-139]
   - Create employee record

---

## Important Session Keys

- `otp_user_id` - User ID undergoing OTP verification
- `otp_purpose` - Type of OTP ('login' or 'registration')
- `otp_email` - User email for display/confirmation
- `otp_sent_at` - Timestamp when OTP was sent
- `otp_expires_at` - Unix timestamp of OTP expiry
- `register_otp_user_id` - Legacy key (backward compatibility)

---

## OTP Security Checks

✓ Max 5 failed attempts before account lock  
✓ 60-second cooldown between resend requests  
✓ OTP expires after 5 minutes  
✓ Session expires after 10 minutes  
✓ Email OTP validation for location-based access  
✓ Rate limiting on account lockouts (15 min)  
✓ Logging of all OTP attempts  
✓ Masked email display in UI  
✓ Admin bypass (no OTP required)
