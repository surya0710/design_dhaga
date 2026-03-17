<h2>Reset Your Password</h2>
<p>Click the link below to reset your password. This link will expire in 60 minutes.</p>
<a href="{{ url('/reset-password?token=' . $token . '&email=' . urlencode($email)) }}">
    Reset Password
</a>
<p>If you didn't request this, please ignore this email.</p>