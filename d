Binary file [35mpersonnelManagement.zip[m matches
[35mpersonnelManagement/.env.production.example[m[36m:[mDB_[1;31mPASSWORD[m=your_secure_database_[1;31mpassword[m  # Use strong [1;31mpassword[m
[35mpersonnelManagement/.env.production.example[m[36m:[mREDIS_[1;31mPASSWORD[m=your_redis_[1;31mpassword[m
[35mpersonnelManagement/.env.production.example[m[36m:[mMAIL_[1;31mPASSWORD[m=your_smtp_[1;31mpassword[m
[35mpersonnelManagement/.env.production.example[m[36m:[mTHROTTLE_[1;31mPASSWORD[m_RESET=3  # [1;31mPassword[m reset requests
[35mpersonnelManagement/.env.production.example[m[36m:[m# [1;31mPassword[m Reset Settings
[35mpersonnelManagement/.env.production.example[m[36m:[m[1;31mPASSWORD[m_RESET_EXPIRY=15  # Token expiry in minutes
[35mpersonnelManagement/.env.production.example[m[36m:[m[1;31mPASSWORD[m_RESET_MAX_PER_HOUR=3  # Max reset requests per email per hour
[35mpersonnelManagement/.env.production.example[m[36m:[m#     ✓ Strong database [1;31mpassword[m
[35mpersonnelManagement/.env.production.example[m[36m:[m#     ✓ Strong Redis [1;31mpassword[m (if applicable)
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[muse App\Mail\[1;31mPassword[mResetMail;
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[mclass Forgot[1;31mPassword[mController
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m    $recentRequests = DB::table('[1;31mpassword[m_resets')
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m        $message = 'If an account exists with this email, a [1;31mpassword[m reset link has been sent.';
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m        DB::table('[1;31mpassword[m_resets')->updateOrInsert(
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m        $resetUrl = url("/reset-[1;31mpassword[m?token={$token}&email=" . urlencode($request->email));
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m            Mail::to($request->email)->send(new [1;31mPassword[mResetMail($resetUrl));
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m            \Log::error('[1;31mPassword[m reset email error: ' . $e->getMessage());
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m    $genericMessage = 'If an account exists with this email, a [1;31mpassword[m reset link has been sent.';
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[mpublic function reset[1;31mPassword[m(Request $request)
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m    // ✅ SECURITY FIX: Enhanced [1;31mpassword[m validation
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m        '[1;31mpassword[m' => [
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m        '[1;31mpassword[m.regex' => '[1;31mPassword[m must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*#?&).'
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m    $resetRecord = DB::table('[1;31mpassword[m_resets')
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m            DB::table('[1;31mpassword[m_resets')->where('email', $request->email)->delete();
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m                'message' => 'This [1;31mpassword[m reset link is invalid or has expired.',
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m                'errors' => ['email' => ['This [1;31mpassword[m reset link is invalid or has expired.']]
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m        return back()->withErrors(['email' => 'This [1;31mpassword[m reset link is invalid or has expired.']);
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m                'message' => 'This [1;31mpassword[m reset link is invalid or has expired.',
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m                'errors' => ['email' => ['This [1;31mpassword[m reset link is invalid or has expired.']]
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m        return back()->withErrors(['email' => 'This [1;31mpassword[m reset link is invalid or has expired.']);
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m    // ✅ SECURITY FIX: Ensure new [1;31mpassword[m is different from old [1;31mpassword[m
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m    if (Hash::check($request->[1;31mpassword[m, $user->[1;31mpassword[m)) {
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m        $errorMessage = 'New [1;31mpassword[m must be different from your current [1;31mpassword[m.';
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m                'errors' => ['[1;31mpassword[m' => [$errorMessage]]
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m        return back()->withErrors(['[1;31mpassword[m' => $errorMessage]);
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m    $user->[1;31mpassword[m = Hash::make($request->[1;31mpassword[m);
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m    // ✅ SECURITY FIX: Delete ALL [1;31mpassword[m reset tokens for this email
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m    DB::table('[1;31mpassword[m_resets')->where('email', $request->email)->delete();
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m    // ✅ SECURITY FIX: Send email notification about [1;31mpassword[m change
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m        Mail::to($user->email)->send(new \App\Mail\[1;31mPassword[mChangedMail($user));
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m        \Log::error('[1;31mPassword[m changed notification email error: ' . $e->getMessage());
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m            'message' => 'Your [1;31mpassword[m has been reset successfully! A confirmation email has been sent.',
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m            'status' => 'Your [1;31mpassword[m has been reset!'
[35mpersonnelManagement/app/Http/Controllers/Auth/ForgotPasswordController.php[m[36m:[m    return redirect()->route('login')->with('status', 'Your [1;31mpassword[m has been reset successfully!');
[35mpersonnelManagement/app/Http/Controllers/Auth/LoginController.php[m[36m:[m            '[1;31mpassword[m' => ['required'],
[35mpersonnelManagement/app/Http/Controllers/Auth/RegisterController.php[m[36m:[m     * Standardized [1;31mpassword[m validation rules
[35mpersonnelManagement/app/Http/Controllers/Auth/RegisterController.php[m[36m:[m     * Used consistently across registration and [1;31mpassword[m changes
[35mpersonnelManagement/app/Http/Controllers/Auth/RegisterController.php[m[36m:[m    public static function get[1;31mPassword[mRules(bool $isConfirmed = true): array
[35mpersonnelManagement/app/Http/Controllers/Auth/RegisterController.php[m[36m:[m        // Custom [1;31mpassword[m validation rule
[35mpersonnelManagement/app/Http/Controllers/Auth/RegisterController.php[m[36m:[m            '[1;31mpassword[m'     => config('validation.[1;31mpassword[m'),
[35mpersonnelManagement/app/Http/Controllers/Auth/RegisterController.php[m[36m:[m        ], config('validation.[1;31mpassword[m_messages'));
[35mpersonnelManagement/app/Http/Controllers/Auth/RegisterController.php[m[36m:[m            '[1;31mpassword[m'   => Hash::make($request->[1;31mpassword[m),
[35mpersonnelManagement/app/Http/Controllers/UserController.php[m[36m:[muse App\Mail\[1;31mPassword[mChangedMail;
[35mpersonnelManagement/app/Http/Controllers/UserController.php[m[36m:[m    public function change[1;31mPassword[m(Request $request) {
[35mpersonnelManagement/app/Http/Controllers/UserController.php[m[36m:[m            'current_[1;31mpassword[m' => 'required',
[35mpersonnelManagement/app/Http/Controllers/UserController.php[m[36m:[m            'new_[1;31mpassword[m' => config('validation.[1;31mpassword[m'),
[35mpersonnelManagement/app/Http/Controllers/UserController.php[m[36m:[m        ], config('validation.[1;31mpassword[m_messages'));
[35mpersonnelManagement/app/Http/Controllers/UserController.php[m[36m:[m        if (!Hash::check($request->current_[1;31mpassword[m, $user->[1;31mpassword[m)) {
[35mpersonnelManagement/app/Http/Controllers/UserController.php[m[36m:[m            return back()->withErrors(['current_[1;31mpassword[m' => 'Incorrect current [1;31mpassword[m.']);
[35mpersonnelManagement/app/Http/Controllers/UserController.php[m[36m:[m        $user->[1;31mpassword[m = Hash::make($request->new_[1;31mpassword[m);
[35mpersonnelManagement/app/Http/Controllers/UserController.php[m[36m:[m            Mail::to($user->email)->send(new [1;31mPassword[mChangedMail($user));
[35mpersonnelManagement/app/Http/Controllers/UserController.php[m[36m:[m            Log::error('Failed to send [1;31mpassword[m change email: ' . $e->getMessage());
[35mpersonnelManagement/app/Http/Controllers/UserController.php[m[36m:[m            // Continue even if email fails - [1;31mpassword[m was already changed
[35mpersonnelManagement/app/Http/Controllers/UserController.php[m[36m:[m        return back()->with('success', '[1;31mPassword[m changed successfully. A confirmation email has been sent.');
[35mpersonnelManagement/app/Http/Controllers/UserController.php[m[36m:[m            'delete_[1;31mpassword[m' => 'required',
[35mpersonnelManagement/app/Http/Controllers/UserController.php[m[36m:[m        if (!Hash::check($request->delete_[1;31mpassword[m, $user->[1;31mpassword[m)) {
[35mpersonnelManagement/app/Http/Controllers/UserController.php[m[36m:[m            return back()->withErrors(['delete_[1;31mpassword[m' => 'Incorrect [1;31mpassword[m.']);
[35mpersonnelManagement/app/Mail/PasswordChangedMail.php[m[36m:[mclass [1;31mPassword[mChangedMail extends Mailable
[35mpersonnelManagement/app/Mail/PasswordChangedMail.php[m[36m:[m        return $this->subject('[1;31mPassword[m Changed - Personnel Management System')
[35mpersonnelManagement/app/Mail/PasswordChangedMail.php[m[36m:[m                    ->view('emails.[1;31mpassword[m-changed')
[35mpersonnelManagement/app/Mail/PasswordResetMail.php[m[36m:[mclass [1;31mPassword[mResetMail extends Mailable
[35mpersonnelManagement/app/Mail/PasswordResetMail.php[m[36m:[m        return $this->subject('Reset Your [1;31mPassword[m - Personnel Management System')
[35mpersonnelManagement/app/Mail/PasswordResetMail.php[m[36m:[m                    ->view('emails.[1;31mpassword[m-reset')
[35mpersonnelManagement/app/Models/User.php[m[36m:[m        '[1;31mpassword[m',
[35mpersonnelManagement/app/Models/User.php[m[36m:[m        '[1;31mpassword[m',
[35mpersonnelManagement/app/Models/User.php[m[36m:[m            '[1;31mpassword[m' => 'hashed',
[35mpersonnelManagement/composer.lock[m[36m:[m            "description": "🛠  Nette Utils: lightweight utilities for string & array manipulation, image handling, safe JSON encoding/decoding, validation, slug or strong [1;31mpassword[m generating etc.",
[35mpersonnelManagement/composer.lock[m[36m:[m                "[1;31mpassword[m",
[35mpersonnelManagement/config/auth.php[m[36m:[m    | This option defines the default authentication "guard" and [1;31mpassword[m
[35mpersonnelManagement/config/auth.php[m[36m:[m        '[1;31mpassword[ms' => env('AUTH_[1;31mPASSWORD[m_BROKER', 'users'),
[35mpersonnelManagement/config/auth.php[m[36m:[m    | Resetting [1;31mPassword[ms
[35mpersonnelManagement/config/auth.php[m[36m:[m    | These configuration options specify the behavior of Laravel's [1;31mpassword[m
[35mpersonnelManagement/config/auth.php[m[36m:[m    | generating more [1;31mpassword[m reset tokens. This prevents the user from
[35mpersonnelManagement/config/auth.php[m[36m:[m    | quickly generating a very large amount of [1;31mpassword[m reset tokens.
[35mpersonnelManagement/config/auth.php[m[36m:[m    '[1;31mpassword[ms' => [
[35mpersonnelManagement/config/auth.php[m[36m:[m            'table' => env('AUTH_[1;31mPASSWORD[m_RESET_TOKEN_TABLE', '[1;31mpassword[m_reset_tokens'),
[35mpersonnelManagement/config/auth.php[m[36m:[m    | [1;31mPassword[m Confirmation Timeout
[35mpersonnelManagement/config/auth.php[m[36m:[m    | Here you may define the amount of seconds before a [1;31mpassword[m confirmation
[35mpersonnelManagement/config/auth.php[m[36m:[m    | window expires and users are asked to re-enter their [1;31mpassword[m via the
[35mpersonnelManagement/config/auth.php[m[36m:[m    '[1;31mpassword[m_timeout' => env('AUTH_[1;31mPASSWORD[m_TIMEOUT', 10800),
[35mpersonnelManagement/config/cache.php[m[36m:[m                env('MEMCACHED_[1;31mPASSWORD[m'),
[35mpersonnelManagement/config/database.php[m[36m:[m            '[1;31mpassword[m' => env('DB_[1;31mPASSWORD[m', ''),
[35mpersonnelManagement/config/database.php[m[36m:[m            '[1;31mpassword[m' => env('DB_[1;31mPASSWORD[m', ''),
[35mpersonnelManagement/config/database.php[m[36m:[m            '[1;31mpassword[m' => env('DB_[1;31mPASSWORD[m', ''),
[35mpersonnelManagement/config/database.php[m[36m:[m            '[1;31mpassword[m' => env('DB_[1;31mPASSWORD[m', ''),
[35mpersonnelManagement/config/database.php[m[36m:[m            '[1;31mpassword[m' => env('REDIS_[1;31mPASSWORD[m'),
[35mpersonnelManagement/config/database.php[m[36m:[m            '[1;31mpassword[m' => env('REDIS_[1;31mPASSWORD[m'),
[35mpersonnelManagement/config/mail.php[m[36m:[m            '[1;31mpassword[m' => env('MAIL_[1;31mPASSWORD[m'),
[35mpersonnelManagement/config/validation.php[m[36m:[m    | [1;31mPassword[m Validation Rules
[35mpersonnelManagement/config/validation.php[m[36m:[m    | Centralized [1;31mpassword[m validation rules for consistency across
[35mpersonnelManagement/config/validation.php[m[36m:[m    | registration, [1;31mpassword[m change, and [1;31mpassword[m reset operations.
[35mpersonnelManagement/config/validation.php[m[36m:[m    '[1;31mpassword[m' => [
[35mpersonnelManagement/config/validation.php[m[36m:[m        'confirmed',          // Must match [1;31mpassword[m_confirmation field
[35mpersonnelManagement/config/validation.php[m[36m:[m    | [1;31mPassword[m Error Messages
[35mpersonnelManagement/config/validation.php[m[36m:[m    | Custom error messages for [1;31mpassword[m validation failures.
[35mpersonnelManagement/config/validation.php[m[36m:[m    '[1;31mpassword[m_messages' => [
[35mpersonnelManagement/config/validation.php[m[36m:[m        'required' => '[1;31mPassword[m is required.',
[35mpersonnelManagement/config/validation.php[m[36m:[m        'min' => '[1;31mPassword[m must be at least 8 characters.',
[35mpersonnelManagement/config/validation.php[m[36m:[m        'confirmed' => '[1;31mPassword[m confirmation does not match.',
[35mpersonnelManagement/config/validation.php[m[36m:[m        'regex' => '[1;31mPassword[m must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*#?&).',
[35mpersonnelManagement/database/factories/UserFactory.php[m[36m:[m     * The current [1;31mpassword[m being used by the factory.
[35mpersonnelManagement/database/factories/UserFactory.php[m[36m:[m    protected static ?string $[1;31mpassword[m;
[35mpersonnelManagement/database/factories/UserFactory.php[m[36m:[m            '[1;31mpassword[m' => static::$[1;31mpassword[m ??= Hash::make('[1;31mpassword[m'),
[35mpersonnelManagement/database/migrations/0001_01_01_000000_create_users_table.php[m[36m:[m            $table->string('[1;31mpassword[m');
[35mpersonnelManagement/database/migrations/0001_01_01_000000_create_users_table.php[m[36m:[m        Schema::create('[1;31mpassword[m_reset_tokens', function (Blueprint $table) {
[35mpersonnelManagement/database/migrations/0001_01_01_000000_create_users_table.php[m[36m:[m        Schema::dropIfExists('[1;31mpassword[m_reset_tokens');
[35mpersonnelManagement/database/migrations/0001_01_01_000011_create_password_resets_table.php[m[36m:[m        Schema::create('[1;31mpassword[m_resets', function (Blueprint $table) {
[35mpersonnelManagement/database/migrations/0001_01_01_000011_create_password_resets_table.php[m[36m:[m        Schema::dropIfExists('[1;31mpassword[m_resets');
[35mpersonnelManagement/database/migrations_backup/0001_01_01_000000_create_users_table.php[m[36m:[m            $table->string('[1;31mpassword[m');
[35mpersonnelManagement/database/migrations_backup/0001_01_01_000000_create_users_table.php[m[36m:[m        Schema::create('[1;31mpassword[m_reset_tokens', function (Blueprint $table) {
[35mpersonnelManagement/database/migrations_backup/0001_01_01_000000_create_users_table.php[m[36m:[m        Schema::dropIfExists('[1;31mpassword[m_reset_tokens');
[35mpersonnelManagement/database/migrations_backup/2025_07_06_020918_create_password_resets_table.php[m[36m:[m        Schema::create('[1;31mpassword[m_resets', function (Blueprint $table) {
[35mpersonnelManagement/database/migrations_backup/2025_07_06_020918_create_password_resets_table.php[m[36m:[m        Schema::dropIfExists('[1;31mpassword[m_resets');
[35mpersonnelManagement/database/seeders/ApplicationSeeder.php[m[36m:[m                '[1;31mpassword[m' => Hash::make('[1;31mPassword[m123!'),
[35mpersonnelManagement/database/seeders/ApplicationSeeder.php[m[36m:[m                '[1;31mpassword[m' => Hash::make('[1;31mPassword[m123!'),
[35mpersonnelManagement/database/seeders/ApplicationSeeder.php[m[36m:[m                '[1;31mpassword[m' => Hash::make('[1;31mPassword[m123!'),
[35mpersonnelManagement/database/seeders/DatabaseSeeder.php[m[36m:[m        $this->command->info('   HR Admin: hradmin@villspms.com / [1;31mPassword[m123!');
[35mpersonnelManagement/database/seeders/DatabaseSeeder.php[m[36m:[m        $this->command->info('   HR Staff: hrstaff@villspms.com / [1;31mPassword[m123!');
[35mpersonnelManagement/database/seeders/DatabaseSeeder.php[m[36m:[m        $this->command->info('   Employees: juan.delacruz@villspms.com / [1;31mPassword[m123! (and 4 more)');
[35mpersonnelManagement/database/seeders/DatabaseSeeder.php[m[36m:[m        $this->command->info('   Applicants: carlo.santos0@applicant.com / [1;31mPassword[m123! (and 14 more)');
[35mpersonnelManagement/database/seeders/UserSeeder.php[m[36m:[m            '[1;31mpassword[m' => Hash::make('[1;31mPassword[m123!'),
[35mpersonnelManagement/database/seeders/UserSeeder.php[m[36m:[m            '[1;31mpassword[m' => Hash::make('[1;31mPassword[m123!'),
[35mpersonnelManagement/documentations/AUTHENTICATION_SECURITY.md[m[36m:[m- **[1;31mPassword[m Reset:** 3 attempts per minute
[35mpersonnelManagement/documentations/AUTHENTICATION_SECURITY.md[m[36m:[m### 4. Secure [1;31mPassword[m Reset
[35mpersonnelManagement/documentations/AUTHENTICATION_SECURITY.md[m[36m:[m- Token-based [1;31mpassword[m reset
[35mpersonnelManagement/documentations/AUTHENTICATION_SECURITY.md[m[36m:[m- **Location:** `app/Http/Controllers/Auth/Forgot[1;31mPassword[mController.php`
[35mpersonnelManagement/documentations/AUTHENTICATION_SECURITY.md[m[36m:[m7. **[1;31mPassword[m Breach Detection**
[35mpersonnelManagement/documentations/AUTHENTICATION_SECURITY.md[m[36m:[m   - Check [1;31mpassword[ms against haveibeenpwned API
[35mpersonnelManagement/documentations/AUTHENTICATION_SECURITY.md[m[36m:[m   - Warn users of compromised [1;31mpassword[ms
[35mpersonnelManagement/documentations/AUTHENTICATION_SECURITY.md[m[36m:[m   - Force [1;31mpassword[m change on breach detection
[35mpersonnelManagement/documentations/AUTHENTICATION_SECURITY.md[m[36m:[m# Try logging in with wrong [1;31mpassword[m 5 times
[35mpersonnelManagement/documentations/AUTHENTICATION_SECURITY.md[m[36m:[m- [ ] Strong database [1;31mpassword[ms set
[35mpersonnelManagement/documentations/CRITICAL_FIXES_SUMMARY.md[m[36m:[m### 5. **Weak [1;31mPassword[m Reset** ✅
[35mpersonnelManagement/documentations/CRITICAL_FIXES_SUMMARY.md[m[36m:[m- **File:** `Forgot[1;31mPassword[mController.php`
[35mpersonnelManagement/documentations/CRITICAL_FIXES_SUMMARY.md[m[36m:[m- **Impact:** Secure [1;31mpassword[m reset with anti-enumeration
[35mpersonnelManagement/documentations/CRITICAL_FIXES_SUMMARY.md[m[36m:[m4. ✅ `app/Http/Controllers/Auth/Forgot[1;31mPassword[mController.php`
[35mpersonnelManagement/documentations/CRITICAL_FIXES_SUMMARY.md[m[36m:[m- [ ] [1;31mPassword[m reset (test rate limiting)
[35mpersonnelManagement/documentations/CRITICAL_FIXES_SUMMARY.md[m[36m:[m| Weak [1;31mPassword[m Reset | MEDIUM-HIGH | ✅ Fixed |
[35mpersonnelManagement/documentations/CRITICAL_FIXES_SUMMARY.md[m[36m:[m- [ ] Implement [1;31mpassword[m history (prevent reuse of last 5 [1;31mpassword[ms)
[35mpersonnelManagement/documentations/CRITICAL_FIXES_SUMMARY.md[m[36m:[m- [ ] Add security questions for [1;31mpassword[m reset
[35mpersonnelManagement/documentations/CRITICAL_FIXES_SUMMARY.md[m[36m:[m| [1;31mPassword[m Reset | 4 requests in 1 hour | 4th rejected | ⏳ Pending |
[35mpersonnelManagement/documentations/CRITICAL_FIXES_SUMMARY.md[m[36m:[m| [1;31mPassword[m Reset | Invalid token | Generic error | ⏳ Pending |
[35mpersonnelManagement/documentations/CRITICAL_FIXES_SUMMARY.md[m[36m:[m| [1;31mPassword[m Reset | Reuse current [1;31mpassword[m | Rejected | ⏳ Pending |
[35mpersonnelManagement/documentations/CRITICAL_FIXES_SUMMARY.md[m[36m:[m- ✅ Enhanced [1;31mpassword[m security
[35mpersonnelManagement/documentations/DEPLOYMENT_CHECKLIST.md[m[36m:[m- [ ] Verify strong database [1;31mpassword[m is set
[35mpersonnelManagement/documentations/DEPLOYMENT_CHECKLIST.md[m[36m:[m- [ ] Test [1;31mpassword[m reset
[35mpersonnelManagement/documentations/MERGE_SUMMARY.md[m[36m:[m  - Change [1;31mPassword[m (both roles)
[35mpersonnelManagement/documentations/SECURITY_FIXES_IMPLEMENTED.md[m[36m:[m## 5. ✅ Improved [1;31mPassword[m Reset Security
[35mpersonnelManagement/documentations/SECURITY_FIXES_IMPLEMENTED.md[m[36m:[m**Fix Location:** `app/Http/Controllers/Auth/Forgot[1;31mPassword[mController.php`
[35mpersonnelManagement/documentations/SECURITY_FIXES_IMPLEMENTED.md[m[36m:[m- **Before**: `hash('sha256', $token)` - Fast, not suitable for [1;31mpassword[ms
[35mpersonnelManagement/documentations/SECURITY_FIXES_IMPLEMENTED.md[m[36m:[m- [1;31mPassword[m must contain:
[35mpersonnelManagement/documentations/SECURITY_FIXES_IMPLEMENTED.md[m[36m:[m#### D. New [1;31mPassword[m Requirements
[35mpersonnelManagement/documentations/SECURITY_FIXES_IMPLEMENTED.md[m[36m:[m- Cannot reuse current [1;31mpassword[m
[35mpersonnelManagement/documentations/SECURITY_FIXES_IMPLEMENTED.md[m[36m:[m- Email sent to user when [1;31mpassword[m is changed
[35mpersonnelManagement/documentations/SECURITY_FIXES_IMPLEMENTED.md[m[36m:[m- Uses existing `[1;31mPassword[mChangedMail` class
[35mpersonnelManagement/documentations/SECURITY_FIXES_IMPLEMENTED.md[m[36m:[m- Prevents account enumeration: "If an account exists with this email, a [1;31mpassword[m reset link has been sent."
[35mpersonnelManagement/documentations/SECURITY_FIXES_IMPLEMENTED.md[m[36m:[m- "Email not found" vs "[1;31mPassword[m incorrect" (reveals if email exists)
[35mpersonnelManagement/documentations/SECURITY_FIXES_IMPLEMENTED.md[m[36m:[m- "If an account exists, an email has been sent" ([1;31mpassword[m reset)
[35mpersonnelManagement/documentations/SECURITY_FIXES_IMPLEMENTED.md[m[36m:[m### [1;31mPassword[m Reset Testing
[35mpersonnelManagement/documentations/SECURITY_FIXES_IMPLEMENTED.md[m[36m:[m- [ ] Verify cannot reuse current [1;31mpassword[m
[35mpersonnelManagement/documentations/SECURITY_FIXES_IMPLEMENTED.md[m[36m:[m- Check failed [1;31mpassword[m reset attempts
[35mpersonnelManagement/documentations/SECURITY_FIXES_IMPLEMENTED.md[m[36m:[m4. `app/Http/Controllers/Auth/Forgot[1;31mPassword[mController.php`
[35mpersonnelManagement/resources/views/admins/README.md[m[36m:[m   - Purpose: [1;31mPassword[m change form for account security
[35mpersonnelManagement/resources/views/admins/README.md[m[36m:[m   - Features: Update current [1;31mpassword[m with validation
[35mpersonnelManagement/resources/views/admins/SUMMARY.txt[m[36m:[m   ✓ [1;31mPassword[m change form
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m<!-- Forgot [1;31mPassword[m Modal -->
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m<div x-show="activeModal === 'forgot[1;31mPassword[m'"
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m     x-data="forgot[1;31mPassword[mModal()"
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m    <div x-show="activeModal === 'forgot[1;31mPassword[m'"
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m            <h2 class="text-2xl font-bold text-[#A06E45] mb-6 text-center uppercase tracking-wide">Forgot [1;31mPassword[m</h2>
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m            <div x-show="forgot[1;31mPassword[mStatus"
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m                 x-text="forgot[1;31mPassword[mStatus"
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m            <div x-show="forgot[1;31mPassword[mErrors.length > 0"
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m                    <template x-for="error in forgot[1;31mPassword[mErrors" :key="error">
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m                Enter your email address and we'll send you a link to reset your [1;31mpassword[m.
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m            <!-- Forgot [1;31mPassword[m Form -->
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m            <form @submit.prevent="submitForgot[1;31mPassword[m">
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m                           x-model="forgot[1;31mPassword[mForm.email"
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m                        :disabled="forgot[1;31mPassword[mLoading"
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m                    <span x-show="!forgot[1;31mPassword[mLoading">Send Reset Link</span>
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m                    <span x-show="forgot[1;31mPassword[mLoading" class="flex items-center justify-center">
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m<!-- Forgot [1;31mPassword[m Modal Script -->
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m    function forgot[1;31mPassword[mModal() {
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m            // Forgot [1;31mpassword[m form
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m            forgot[1;31mPassword[mForm: {
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m            forgot[1;31mPassword[mErrors: [],
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m            forgot[1;31mPassword[mStatus: '',
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m            forgot[1;31mPassword[mLoading: false,
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m            // Submit forgot [1;31mpassword[m form
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m            async submitForgot[1;31mPassword[m() {
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m                this.forgot[1;31mPassword[mLoading = true;
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m                this.forgot[1;31mPassword[mErrors = [];
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m                this.forgot[1;31mPassword[mStatus = '';
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m                    const response = await axios.post('/forgot-[1;31mpassword[m', this.forgot[1;31mPassword[mForm, {
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m                        this.forgot[1;31mPassword[mStatus = response.data.message || response.data.status || '[1;31mPassword[m reset link sent to your email!';
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m                        this.forgot[1;31mPassword[mForm.email = '';
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m                            text: this.forgot[1;31mPassword[mStatus,
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m                        this.forgot[1;31mPassword[mErrors = Object.values(error.response.data.errors).flat();
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m                        this.forgot[1;31mPassword[mErrors = [error.response.data.message];
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m                        this.forgot[1;31mPassword[mErrors = ['An error occurred. Please try again.'];
[35mpersonnelManagement/resources/views/auth/forgot-password.blade.php[m[36m:[m                    this.forgot[1;31mPassword[mLoading = false;
[35mpersonnelManagement/resources/views/auth/login.blade.php[m[36m:[m                        <label for="login_[1;31mpassword[m" class="block text-gray-700 mb-1">[1;31mPassword[m</label>
[35mpersonnelManagement/resources/views/auth/login.blade.php[m[36m:[m                        <input type="[1;31mpassword[m"
[35mpersonnelManagement/resources/views/auth/login.blade.php[m[36m:[m                               x-model="loginForm.[1;31mpassword[m"
[35mpersonnelManagement/resources/views/auth/login.blade.php[m[36m:[m                               id="login_[1;31mpassword[m"
[35mpersonnelManagement/resources/views/auth/login.blade.php[m[36m:[m                                @click="activeModal = 'forgot[1;31mPassword[m'"
[35mpersonnelManagement/resources/views/auth/login.blade.php[m[36m:[m                            Forgot [1;31mPassword[m?
[35mpersonnelManagement/resources/views/auth/login.blade.php[m[36m:[m                [1;31mpassword[m: '',
[35mpersonnelManagement/resources/views/auth/login.blade.php[m[36m:[m            showLogin[1;31mPassword[m: false,
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                    <!-- [1;31mPassword[m Field -->
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                        <label for="reg_[1;31mpassword[m" class="block text-gray-700 mb-1 text-sm">[1;31mPassword[m <span class="text-red-500">*</span></label>
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                            <input :type="showReg[1;31mPassword[m ? 'text' : '[1;31mpassword[m'"
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                                   x-model="registerForm.[1;31mpassword[m"
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                                   @input="validateRegister[1;31mPassword[m"
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                                   @focus="show[1;31mPassword[mRules = true"
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                                   id="reg_[1;31mpassword[m"
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                                    @click="showReg[1;31mPassword[m = !showReg[1;31mPassword[m"
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                                    x-text="showReg[1;31mPassword[m ? 'Hide' : 'Show'"></button>
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                    <!-- Re-type [1;31mPassword[m -->
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                        <label for="reg_[1;31mpassword[m_confirmation" class="block text-gray-700 mb-1 text-sm">Re-type [1;31mPassword[m <span class="text-red-500">*</span></label>
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                            <input :type="showRegConfirm[1;31mPassword[m ? 'text' : '[1;31mpassword[m'"
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                                   x-model="registerForm.[1;31mpassword[m_confirmation"
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                                   @input="validateRegister[1;31mPassword[m"
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                                   id="reg_[1;31mpassword[m_confirmation"
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                                    @click="showRegConfirm[1;31mPassword[m = !showRegConfirm[1;31mPassword[m"
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                                    x-text="showRegConfirm[1;31mPassword[m ? 'Hide' : 'Show'"></button>
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                    <!-- [1;31mPassword[m Rules Indicator -->
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                    <div x-show="show[1;31mPassword[mRules && registerForm.[1;31mpassword[m.length > 0"
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                                <span class="w-2 h-2 rounded-full" :class="[1;31mpassword[mRules.length ? 'bg-green-500' : 'bg-red-500'"></span>
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                                <span class="w-2 h-2 rounded-full" :class="[1;31mpassword[mRules.lowercase ? 'bg-green-500' : 'bg-red-500'"></span>
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                                <span class="w-2 h-2 rounded-full" :class="[1;31mpassword[mRules.uppercase ? 'bg-green-500' : 'bg-red-500'"></span>
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                                <span class="w-2 h-2 rounded-full" :class="[1;31mpassword[mRules.number ? 'bg-green-500' : 'bg-red-500'"></span>
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                                <span class="w-2 h-2 rounded-full" :class="[1;31mpassword[mRules.special ? 'bg-green-500' : 'bg-red-500'"></span>
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                                <span class="w-2 h-2 rounded-full" :class="[1;31mpassword[mRules.match ? 'bg-green-500' : 'bg-red-500'"></span>
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                                [1;31mPassword[ms must match
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                [1;31mpassword[m: '',
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                [1;31mpassword[m_confirmation: '',
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m            showReg[1;31mPassword[m: false,
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m            showRegConfirm[1;31mPassword[m: false,
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m            show[1;31mPassword[mRules: false,
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m            [1;31mpassword[mRules: {
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m            // Validate register [1;31mpassword[m
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m            validateRegister[1;31mPassword[m() {
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                const [1;31mpassword[m = this.registerForm.[1;31mpassword[m;
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                const confirm = this.registerForm.[1;31mpassword[m_confirmation;
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                this.[1;31mpassword[mRules.length = [1;31mpassword[m.length >= 8;
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                this.[1;31mpassword[mRules.lowercase = /[a-z]/.test([1;31mpassword[m);
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                this.[1;31mpassword[mRules.uppercase = /[A-Z]/.test([1;31mpassword[m);
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                this.[1;31mpassword[mRules.number = /[0-9]/.test([1;31mpassword[m);
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                this.[1;31mpassword[mRules.special = /[@$!%*#?&]/.test([1;31mpassword[m);
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                this.[1;31mpassword[mRules.match = [1;31mpassword[m && confirm && [1;31mpassword[m === confirm;
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                return this.[1;31mpassword[mRules.length &&
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                       this.[1;31mpassword[mRules.lowercase &&
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                       this.[1;31mpassword[mRules.uppercase &&
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                       this.[1;31mpassword[mRules.number &&
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                       this.[1;31mpassword[mRules.special &&
[35mpersonnelManagement/resources/views/auth/register.blade.php[m[36m:[m                       this.[1;31mpassword[mRules.match &&
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m<!-- Reset [1;31mPassword[m Modal -->
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m<div x-show="activeModal === 'reset[1;31mPassword[m'"
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m     x-data="reset[1;31mPassword[mModal()"
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m    <div x-show="activeModal === 'reset[1;31mPassword[m'"
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m            <h2 class="text-2xl font-bold text-[#BD6F22] mb-6 text-center tracking-wide">Reset Your [1;31mPassword[m</h2>
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m            <div x-show="reset[1;31mPassword[mStatus"
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                 x-text="reset[1;31mPassword[mStatus"
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m            <div x-show="reset[1;31mPassword[mErrors.length > 0"
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                    <template x-for="error in reset[1;31mPassword[mErrors" :key="error">
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m            <!-- Reset [1;31mPassword[m Form -->
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m            <form @submit.prevent="submitReset[1;31mPassword[m" class="space-y-5">
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                <input type="hidden" x-model="reset[1;31mPassword[mForm.token">
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                <input type="hidden" x-model="reset[1;31mPassword[mForm.email">
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                <!-- New [1;31mPassword[m -->
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                    <label for="reset_[1;31mpassword[m" class="block font-semibold text-[#3A2C1D] mb-1">New [1;31mPassword[m</label>
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                        <input :type="showReset[1;31mPassword[m ? 'text' : '[1;31mpassword[m'"
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                               x-model="reset[1;31mPassword[mForm.[1;31mpassword[m"
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                               @input="validateReset[1;31mPassword[m"
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                               id="reset_[1;31mpassword[m"
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                                @click="showReset[1;31mPassword[m = !showReset[1;31mPassword[m"
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                                x-text="showReset[1;31mPassword[m ? 'Hide' : 'Show'"></button>
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                <!-- Confirm [1;31mPassword[m -->
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                    <label for="reset_[1;31mpassword[m_confirmation" class="block font-semibold text-[#3A2C1D] mb-1">Confirm [1;31mPassword[m</label>
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                        <input :type="showResetConfirm[1;31mPassword[m ? 'text' : '[1;31mpassword[m'"
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                               x-model="reset[1;31mPassword[mForm.[1;31mpassword[m_confirmation"
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                               @input="validateReset[1;31mPassword[m"
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                               id="reset_[1;31mpassword[m_confirmation"
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                                @click="showResetConfirm[1;31mPassword[m = !showResetConfirm[1;31mPassword[m"
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                                x-text="showResetConfirm[1;31mPassword[m ? 'Hide' : 'Show'"></button>
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                <!-- [1;31mPassword[m Rules Indicator -->
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                <div x-show="reset[1;31mPassword[mForm.[1;31mpassword[m.length > 0"
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                            <span class="w-2 h-2 rounded-full" :class="reset[1;31mPassword[mRules.length ? 'bg-green-500' : 'bg-red-500'"></span>
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                            <span class="w-2 h-2 rounded-full" :class="reset[1;31mPassword[mRules.uppercase ? 'bg-green-500' : 'bg-red-500'"></span>
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                            <span class="w-2 h-2 rounded-full" :class="reset[1;31mPassword[mRules.number ? 'bg-green-500' : 'bg-red-500'"></span>
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                            <span class="w-2 h-2 rounded-full" :class="reset[1;31mPassword[mRules.special ? 'bg-green-500' : 'bg-red-500'"></span>
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                            <span class="w-2 h-2 rounded-full" :class="reset[1;31mPassword[mRules.match ? 'bg-green-500' : 'bg-red-500'"></span>
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                            [1;31mPassword[ms must match
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                            :disabled="!isResetFormValid || reset[1;31mPassword[mLoading"
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                            :class="(!isResetFormValid || reset[1;31mPassword[mLoading) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#a85d1f]'">
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                        <span x-show="!reset[1;31mPassword[mLoading">Reset [1;31mPassword[m</span>
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                        <span x-show="reset[1;31mPassword[mLoading" class="flex items-center justify-center">
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m<!-- Reset [1;31mPassword[m Modal Script -->
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m    function reset[1;31mPassword[mModal() {
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m            // Reset [1;31mpassword[m form
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m            reset[1;31mPassword[mForm: {
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                [1;31mpassword[m: '',
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                [1;31mpassword[m_confirmation: ''
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m            reset[1;31mPassword[mErrors: [],
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m            reset[1;31mPassword[mStatus: '',
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m            reset[1;31mPassword[mLoading: false,
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m            showReset[1;31mPassword[m: false,
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m            showResetConfirm[1;31mPassword[m: false,
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m            reset[1;31mPassword[mRules: {
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                    this.reset[1;31mPassword[mForm.token = token;
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                    this.reset[1;31mPassword[mForm.email = email;
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m            // Validate reset [1;31mpassword[m
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m            validateReset[1;31mPassword[m() {
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                const [1;31mpassword[m = this.reset[1;31mPassword[mForm.[1;31mpassword[m;
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                const confirm = this.reset[1;31mPassword[mForm.[1;31mpassword[m_confirmation;
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                this.reset[1;31mPassword[mRules.length = [1;31mpassword[m.length >= 8;
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                this.reset[1;31mPassword[mRules.lowercase = /[a-z]/.test([1;31mpassword[m);
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                this.reset[1;31mPassword[mRules.uppercase = /[A-Z]/.test([1;31mpassword[m);
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                this.reset[1;31mPassword[mRules.number = /[0-9]/.test([1;31mpassword[m);
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                this.reset[1;31mPassword[mRules.special = /[@$!%*#?&]/.test([1;31mpassword[m);
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                this.reset[1;31mPassword[mRules.match = [1;31mpassword[m && confirm && [1;31mpassword[m === confirm;
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                return this.reset[1;31mPassword[mRules.length &&
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                       this.reset[1;31mPassword[mRules.lowercase &&
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                       this.reset[1;31mPassword[mRules.uppercase &&
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                       this.reset[1;31mPassword[mRules.number &&
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                       this.reset[1;31mPassword[mRules.special &&
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                       this.reset[1;31mPassword[mRules.match;
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m            // Submit reset [1;31mpassword[m form
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m            async submitReset[1;31mPassword[m() {
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                this.reset[1;31mPassword[mLoading = true;
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                this.reset[1;31mPassword[mErrors = [];
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                this.reset[1;31mPassword[mStatus = '';
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                    const response = await axios.post('/reset-[1;31mpassword[m', this.reset[1;31mPassword[mForm, {
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                            title: '[1;31mPassword[m Reset!',
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                            text: response.data.message || 'Your [1;31mpassword[m has been reset successfully!',
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                        this.reset[1;31mPassword[mErrors = Object.values(error.response.data.errors).flat();
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                        this.reset[1;31mPassword[mErrors = [error.response.data.message];
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                        this.reset[1;31mPassword[mErrors = ['An error occurred. Please try again.'];
[35mpersonnelManagement/resources/views/auth/reset-password.blade.php[m[36m:[m                    this.reset[1;31mPassword[mLoading = false;
[35mpersonnelManagement/resources/views/components/shared/settings.blade.php[m[36m:[m    <!-- Change [1;31mPassword[m Section -->
[35mpersonnelManagement/resources/views/components/shared/settings.blade.php[m[36m:[m            Change [1;31mPassword[m
[35mpersonnelManagement/resources/views/components/shared/settings.blade.php[m[36m:[m            Update your [1;31mpassword[m regularly to keep your account secure.
[35mpersonnelManagement/resources/views/components/shared/settings.blade.php[m[36m:[m        <form method="POST" action="{{ route('user.change[1;31mPassword[m') }}"
[35mpersonnelManagement/resources/views/components/shared/settings.blade.php[m[36m:[m                    Current [1;31mPassword[m
[35mpersonnelManagement/resources/views/components/shared/settings.blade.php[m[36m:[m                    <i class="fas fa-question-circle ml-1 text-gray-400" title="Enter your existing [1;31mpassword[m first"></i>
[35mpersonnelManagement/resources/views/components/shared/settings.blade.php[m[36m:[m                <input @if(in_array(auth()->user()->role, ['hrAdmin', 'hrStaff'])) id="current_pass" @endif name="current_[1;31mpassword[m" type="[1;31mpassword[m" required
[35mpersonnelManagement/resources/views/components/shared/settings.blade.php[m[36m:[m                    New [1;31mPassword[m
[35mpersonnelManagement/resources/views/components/shared/settings.blade.php[m[36m:[m                    <i class="fas fa-question-circle ml-1 text-gray-400" title="Choose a strong [1;31mpassword[m with letters, numbers, and symbols"></i>
[35mpersonnelManagement/resources/views/components/shared/settings.blade.php[m[36m:[m                <input @if(in_array(auth()->user()->role, ['hrAdmin', 'hrStaff'])) id="new_pass" @endif name="new_[1;31mpassword[m" type="[1;31mpassword[m" required
[35mpersonnelManagement/resources/views/components/shared/settings.blade.php[m[36m:[m                    Re-type [1;31mPassword[m
[35mpersonnelManagement/resources/views/components/shared/settings.blade.php[m[36m:[m                <input @if(in_array(auth()->user()->role, ['hrAdmin', 'hrStaff'])) id="new_pass_confirm" @endif name="new_[1;31mpassword[m_confirmation" type="[1;31mpassword[m" required
[35mpersonnelManagement/resources/views/components/shared/settings.blade.php[m[36m:[m                <label class="block text-sm font-medium text-gray-700 mb-1">Current [1;31mPassword[m</label>
[35mpersonnelManagement/resources/views/components/shared/settings.blade.php[m[36m:[m                <input type="[1;31mpassword[m" name="delete_[1;31mpassword[m" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#DD6161]">
[35mpersonnelManagement/resources/views/emails/password-changed.blade.php[m[36m:[m    <title>[1;31mPassword[m Changed Notification</title>
[35mpersonnelManagement/resources/views/emails/password-changed.blade.php[m[36m:[m        <h2 style="color: #333333; margin-bottom: 20px;">[1;31mPassword[m Changed Successfully</h2>
[35mpersonnelManagement/resources/views/emails/password-changed.blade.php[m[36m:[m            This is a confirmation that your [1;31mpassword[m was successfully changed on {{ $timestamp }}.
[35mpersonnelManagement/resources/views/emails/password-changed.blade.php[m[36m:[m            If you did not make this change, please contact our support team immediately or reset your [1;31mpassword[m to secure your account.
[35mpersonnelManagement/resources/views/emails/password-reset.blade.php[m[36m:[m    <title>[1;31mPassword[m Reset Notification</title>
[35mpersonnelManagement/resources/views/emails/password-reset.blade.php[m[36m:[m        <h2 style="color: #333333; margin-bottom: 20px;">[1;31mPassword[m Reset Request</h2>
[35mpersonnelManagement/resources/views/emails/password-reset.blade.php[m[36m:[m            You are receiving this email because a [1;31mpassword[m reset request was submitted for your account. If you made this request, please click the button below to proceed:
[35mpersonnelManagement/resources/views/emails/password-reset.blade.php[m[36m:[m                Reset [1;31mPassword[m
[35mpersonnelManagement/resources/views/welcome.blade.php[m[36m:[m  @include('auth.forgot-[1;31mpassword[m')
[35mpersonnelManagement/resources/views/welcome.blade.php[m[36m:[m  @include('auth.reset-[1;31mpassword[m')
[35mpersonnelManagement/resources/views/welcome.blade.php[m[36m:[m          // Check for reset [1;31mpassword[m token in URL
[35mpersonnelManagement/resources/views/welcome.blade.php[m[36m:[m            this.activeModal = 'reset[1;31mPassword[m';
[35mpersonnelManagement/routes/web.php[m[36m:[muse App\Http\Controllers\Auth\Forgot[1;31mPassword[mController;
[35mpersonnelManagement/routes/web.php[m[36m:[m    // Change [1;31mpassword[m route
[35mpersonnelManagement/routes/web.php[m[36m:[m// Route parin ng change [1;31mpassword[m at delete account
[35mpersonnelManagement/routes/web.php[m[36m:[m    Route::post('/user/change-[1;31mpassword[m', [UserController::class, 'change[1;31mPassword[m'])->name('user.change[1;31mPassword[m');
[35mpersonnelManagement/routes/web.php[m[36m:[m// [1;31mPassword[m reset (with rate limiting for security)
[35mpersonnelManagement/routes/web.php[m[36m:[mRoute::get('/forgot-[1;31mpassword[m', function () {
[35mpersonnelManagement/routes/web.php[m[36m:[m    return view('auth.forgot-[1;31mpassword[m'); // Change this to load the Blade
[35mpersonnelManagement/routes/web.php[m[36m:[m})->name('[1;31mpassword[m.request');
[35mpersonnelManagement/routes/web.php[m[36m:[mRoute::post('/forgot-[1;31mpassword[m', [Forgot[1;31mPassword[mController::class, 'sendResetLinkEmail'])
[35mpersonnelManagement/routes/web.php[m[36m:[m    ->name('[1;31mpassword[m.email');
[35mpersonnelManagement/routes/web.php[m[36m:[mRoute::get('/reset-[1;31mpassword[m', function (Request $request) {
[35mpersonnelManagement/routes/web.php[m[36m:[m    return view('auth.reset-[1;31mpassword[m', [
[35mpersonnelManagement/routes/web.php[m[36m:[m})->name('[1;31mpassword[m.reset');
[35mpersonnelManagement/routes/web.php[m[36m:[mRoute::post('/reset-[1;31mpassword[m', [\App\Http\Controllers\Auth\Forgot[1;31mPassword[mController::class, 'reset[1;31mPassword[m'])
[35mpersonnelManagement/routes/web.php[m[36m:[m    ->name('[1;31mpassword[m.update');
[35mpersonnelManagement/routes/web.php[m[36m:[m    // Settings / Change [1;31mPassword[m
[35mvendor/illuminate/contracts/Auth/Authenticatable.php[m[36m:[m     * Get the name of the [1;31mpassword[m attribute for the user.
[35mvendor/illuminate/contracts/Auth/Authenticatable.php[m[36m:[m    public function getAuth[1;31mPassword[mName();
[35mvendor/illuminate/contracts/Auth/Authenticatable.php[m[36m:[m     * Get the [1;31mpassword[m for the user.
[35mvendor/illuminate/contracts/Auth/Authenticatable.php[m[36m:[m    public function getAuth[1;31mPassword[m();
[35mvendor/illuminate/contracts/Auth/CanResetPassword.php[m[36m:[minterface CanReset[1;31mPassword[m
[35mvendor/illuminate/contracts/Auth/CanResetPassword.php[m[36m:[m     * Get the e-mail address where [1;31mpassword[m reset links are sent.
[35mvendor/illuminate/contracts/Auth/CanResetPassword.php[m[36m:[m    public function getEmailFor[1;31mPassword[mReset();
[35mvendor/illuminate/contracts/Auth/CanResetPassword.php[m[36m:[m     * Send the [1;31mpassword[m reset notification.
[35mvendor/illuminate/contracts/Auth/CanResetPassword.php[m[36m:[m    public function send[1;31mPassword[mResetNotification($token);
[35mvendor/illuminate/contracts/Auth/PasswordBroker.php[m[36m:[minterface [1;31mPassword[mBroker
[35mvendor/illuminate/contracts/Auth/PasswordBroker.php[m[36m:[m    const RESET_LINK_SENT = '[1;31mpassword[ms.sent';
[35mvendor/illuminate/contracts/Auth/PasswordBroker.php[m[36m:[m     * Constant representing a successfully reset [1;31mpassword[m.
[35mvendor/illuminate/contracts/Auth/PasswordBroker.php[m[36m:[m    const [1;31mPASSWORD[m_RESET = '[1;31mpassword[ms.reset';
[35mvendor/illuminate/contracts/Auth/PasswordBroker.php[m[36m:[m    const INVALID_USER = '[1;31mpassword[ms.user';
[35mvendor/illuminate/contracts/Auth/PasswordBroker.php[m[36m:[m    const INVALID_TOKEN = '[1;31mpassword[ms.token';
[35mvendor/illuminate/contracts/Auth/PasswordBroker.php[m[36m:[m    const RESET_THROTTLED = '[1;31mpassword[ms.throttled';
[35mvendor/illuminate/contracts/Auth/PasswordBroker.php[m[36m:[m     * Send a [1;31mpassword[m reset link to a user.
[35mvendor/illuminate/contracts/Auth/PasswordBroker.php[m[36m:[m     * Reset the [1;31mpassword[m for the given token.
[35mvendor/illuminate/contracts/Auth/PasswordBrokerFactory.php[m[36m:[minterface [1;31mPassword[mBrokerFactory
[35mvendor/illuminate/contracts/Auth/PasswordBrokerFactory.php[m[36m:[m     * Get a [1;31mpassword[m broker instance by name.
[35mvendor/illuminate/contracts/Auth/PasswordBrokerFactory.php[m[36m:[m     * @return \Illuminate\Contracts\Auth\[1;31mPassword[mBroker
[35mvendor/illuminate/contracts/Auth/UserProvider.php[m[36m:[m     * Rehash the user's [1;31mpassword[m if required and supported.
[35mvendor/illuminate/contracts/Auth/UserProvider.php[m[36m:[m    public function rehash[1;31mPassword[mIfRequired(Authenticatable $user, #[\SensitiveParameter] array $credentials, bool $force = false);
[35mvendor/illuminate/support/ConfigurationUrlParser.php[m[36m:[m            '[1;31mpassword[m' => $url['pass'] ?? null,
[35mvendor/illuminate/support/DefaultProviders.php[m[36m:[m            \Illuminate\Auth\[1;31mPassword[ms\[1;31mPassword[mResetServiceProvider::class,
[35mvendor/illuminate/support/Facades/Auth.php[m[36m:[m * @method static \Illuminate\Contracts\Auth\Authenticatable|null logoutOtherDevices(string $[1;31mpassword[m)
[35mvendor/illuminate/support/Facades/Facade.php[m[36m:[m            '[1;31mPassword[m' => [1;31mPassword[m::class,
[35mvendor/illuminate/support/Facades/Http.php[m[36m:[m * @method static \Illuminate\Http\Client\PendingRequest withBasicAuth(string $username, string $[1;31mpassword[m)
[35mvendor/illuminate/support/Facades/Http.php[m[36m:[m * @method static \Illuminate\Http\Client\PendingRequest withDigestAuth(string $username, string $[1;31mpassword[m)
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[muse Illuminate\Contracts\Auth\[1;31mPassword[mBroker;
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[m * @method static \Illuminate\Contracts\Auth\[1;31mPassword[mBroker broker(string|null $name = null)
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[m * @method static \Illuminate\Contracts\Auth\CanReset[1;31mPassword[m|null getUser(array $credentials)
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[m * @method static string createToken(\Illuminate\Contracts\Auth\CanReset[1;31mPassword[m $user)
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[m * @method static void deleteToken(\Illuminate\Contracts\Auth\CanReset[1;31mPassword[m $user)
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[m * @method static bool tokenExists(\Illuminate\Contracts\Auth\CanReset[1;31mPassword[m $user, string $token)
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[m * @method static \Illuminate\Auth\[1;31mPassword[ms\TokenRepositoryInterface getRepository()
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[m * @see \Illuminate\Auth\[1;31mPassword[ms\[1;31mPassword[mBrokerManager
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[m * @see \Illuminate\Auth\[1;31mPassword[ms\[1;31mPassword[mBroker
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[mclass [1;31mPassword[m extends Facade
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[m     * Constant representing a successfully sent [1;31mpassword[m reset email.
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[m    const ResetLinkSent = [1;31mPassword[mBroker::RESET_LINK_SENT;
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[m     * Constant representing a successfully reset [1;31mpassword[m.
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[m    const [1;31mPassword[mReset = [1;31mPassword[mBroker::[1;31mPASSWORD[m_RESET;
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[m     * Constant indicating the user could not be found when attempting a [1;31mpassword[m reset.
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[m    const InvalidUser = [1;31mPassword[mBroker::INVALID_USER;
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[m     * Constant representing an invalid [1;31mpassword[m reset token.
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[m    const InvalidToken = [1;31mPassword[mBroker::INVALID_TOKEN;
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[m     * Constant representing a throttled [1;31mpassword[m reset attempt.
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[m    const ResetThrottled = [1;31mPassword[mBroker::RESET_THROTTLED;
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[m    const RESET_LINK_SENT = [1;31mPassword[mBroker::RESET_LINK_SENT;
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[m    const [1;31mPASSWORD[m_RESET = [1;31mPassword[mBroker::[1;31mPASSWORD[m_RESET;
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[m    const INVALID_USER = [1;31mPassword[mBroker::INVALID_USER;
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[m    const INVALID_TOKEN = [1;31mPassword[mBroker::INVALID_TOKEN;
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[m    const RESET_THROTTLED = [1;31mPassword[mBroker::RESET_THROTTLED;
[35mvendor/illuminate/support/Facades/Password.php[m[36m:[m        return 'auth.[1;31mpassword[m';
[35mvendor/illuminate/support/Facades/Request.php[m[36m:[m * @method static string|null get[1;31mPassword[m()
[35mvendor/illuminate/support/Facades/Session.php[m[36m:[m * @method static void [1;31mpassword[mConfirmed()
[35mvendor/illuminate/support/Str.php[m[36m:[m     * Generate a random, secure [1;31mpassword[m.
[35mvendor/illuminate/support/Str.php[m[36m:[m    public static function [1;31mpassword[m($length = 32, $letters = true, $numbers = true, $symbols = true, $spaces = false)
[35mvendor/illuminate/support/Str.php[m[36m:[m        $[1;31mpassword[m = new Collection();
[35mvendor/illuminate/support/Str.php[m[36m:[m            ->each(fn ($c) => $[1;31mpassword[m->push($c[random_int(0, count($c) - 1)]))
[35mvendor/illuminate/support/Str.php[m[36m:[m        $length = $length - $[1;31mpassword[m->count();
[35mvendor/illuminate/support/Str.php[m[36m:[m        return $[1;31mpassword[m->merge($options->pipe(
[35mvendor/illuminate/support/Uri.php[m[36m:[m    public function user(bool $with[1;31mPassword[m = false): ?string
[35mvendor/illuminate/support/Uri.php[m[36m:[m        return $with[1;31mPassword[m
[35mvendor/illuminate/support/Uri.php[m[36m:[m     * Get the [1;31mpassword[m from the URI.
[35mvendor/illuminate/support/Uri.php[m[36m:[m    public function [1;31mpassword[m(): ?string
[35mvendor/illuminate/support/Uri.php[m[36m:[m        return $this->uri->get[1;31mPassword[m();
[35mvendor/illuminate/support/Uri.php[m[36m:[m     * Specify the user and [1;31mpassword[m for the URI.
[35mvendor/illuminate/support/Uri.php[m[36m:[m    public function withUser(Stringable|string|null $user, #[SensitiveParameter] Stringable|string|null $[1;31mpassword[m = null): static
[35mvendor/illuminate/support/Uri.php[m[36m:[m        return new static($this->uri->withUserInfo($user, $[1;31mpassword[m));
[35mvendor/laravel/installer/src/Concerns/ConfiguresPrompts.php[m[36m:[muse Laravel\Prompts\[1;31mPassword[mPrompt;
[35mvendor/laravel/installer/src/Concerns/ConfiguresPrompts.php[m[36m:[m        [1;31mPassword[mPrompt::fallbackUsing(fn ([1;31mPassword[mPrompt $prompt) => $this->promptUntilValid(
[35mvendor/laravel/installer/src/NewCommand.php[m[36m:[m            'DB_[1;31mPASSWORD[m=',
[35mvendor/laravel/installer/src/NewCommand.php[m[36m:[m            '# DB_[1;31mPASSWORD[m=',
[35mvendor/laravel/prompts/src/Concerns/Themes.php[m[36m:[muse Laravel\Prompts\[1;31mPassword[mPrompt;
[35mvendor/laravel/prompts/src/Concerns/Themes.php[m[36m:[muse Laravel\Prompts\Themes\Default\[1;31mPassword[mPromptRenderer;
[35mvendor/laravel/prompts/src/Concerns/Themes.php[m[36m:[m            [1;31mPassword[mPrompt::class => [1;31mPassword[mPromptRenderer::class,
[35mvendor/laravel/prompts/src/FormBuilder.php[m[36m:[m    public function [1;31mpassword[m(string $label, string $placeholder = '', bool|string $required = false, mixed $validate = null, string $hint = '', ?string $name = null, ?Closure $transform = null): self
[35mvendor/laravel/prompts/src/FormBuilder.php[m[36m:[m        return $this->runPrompt([1;31mpassword[m(...), get_defined_vars());
[35mvendor/laravel/prompts/src/PasswordPrompt.php[m[36m:[mclass [1;31mPassword[mPrompt extends Prompt
[35mvendor/laravel/prompts/src/PasswordPrompt.php[m[36m:[m     * Create a new [1;31mPassword[mPrompt instance.
[35mvendor/laravel/prompts/src/Themes/Default/PasswordPromptRenderer.php[m[36m:[muse Laravel\Prompts\[1;31mPassword[mPrompt;
[35mvendor/laravel/prompts/src/Themes/Default/PasswordPromptRenderer.php[m[36m:[mclass [1;31mPassword[mPromptRenderer extends Renderer
[35mvendor/laravel/prompts/src/Themes/Default/PasswordPromptRenderer.php[m[36m:[m     * Render the [1;31mpassword[m prompt.
[35mvendor/laravel/prompts/src/Themes/Default/PasswordPromptRenderer.php[m[36m:[m    public function __invoke([1;31mPassword[mPrompt $prompt): string
[35mvendor/laravel/prompts/src/helpers.php[m[36m:[mif (! function_exists('\Laravel\Prompts\[1;31mpassword[m')) {
[35mvendor/laravel/prompts/src/helpers.php[m[36m:[m    function [1;31mpassword[m(string $label, string $placeholder = '', bool|string $required = false, mixed $validate = null, string $hint = '', ?Closure $transform = null): string
[35mvendor/laravel/prompts/src/helpers.php[m[36m:[m        return (new [1;31mPassword[mPrompt(...get_defined_vars()))->prompt();
[35mvendor/symfony/polyfill-php83/bootstrap.php[m[36m:[m    function ldap_connect_wallet(?string $uri, string $wallet, string $[1;31mpassword[m, int $auth_mode = \GSLC_SSL_NO_AUTH) { return ldap_connect($uri, $wallet, $[1;31mpassword[m, $auth_mode); }
[35mvendor/symfony/polyfill-php83/bootstrap81.php[m[36m:[m    function ldap_connect_wallet(?string $uri, string $wallet, #[\SensitiveParameter] string $[1;31mpassword[m, int $auth_mode = \GSLC_SSL_NO_AUTH): \LDAP\Connection|false { return ldap_connect($uri, $wallet, $[1;31mpassword[m, $auth_mode); }
[35mvendor/symfony/translation/Provider/AbstractProviderFactory.php[m[36m:[m    protected function get[1;31mPassword[m(Dsn $dsn): string
[35mvendor/symfony/translation/Provider/AbstractProviderFactory.php[m[36m:[m        return $dsn->get[1;31mPassword[m() ?? throw new IncompleteDsnException('[1;31mPassword[m is not set.', $dsn->getOriginalDsn());
[35mvendor/symfony/translation/Provider/Dsn.php[m[36m:[m    private ?string $[1;31mpassword[m;
[35mvendor/symfony/translation/Provider/Dsn.php[m[36m:[m        $this->[1;31mpassword[m = '' !== ($params['pass'] ?? '') ? rawurldecode($params['pass']) : null;
[35mvendor/symfony/translation/Provider/Dsn.php[m[36m:[m    public function get[1;31mPassword[m(): ?string
[35mvendor/symfony/translation/Provider/Dsn.php[m[36m:[m        return $this->[1;31mpassword[m;
