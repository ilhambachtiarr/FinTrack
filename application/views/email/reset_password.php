<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Password</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 5px; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #3b82f6; color: #fff !important; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .footer { margin-top: 20px; font-size: 0.8em; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Halo <?= htmlspecialchars($name) ?>,</h2>
        <p>Kami menerima permintaan untuk mereset password akun Anda di FinTrack.</p>
        <p>Jika Anda memang memintanya, silakan klik tombol di bawah ini untuk mengatur password baru:</p>
        <p style="text-align: center; margin: 30px 0;">
            <a href="<?= base_url('auth/reset_password/' . $token) ?>" class="btn">Reset Password</a>
        </p>
        <p>Atau Anda dapat menyalin dan menempel tautan berikut ke browser Anda:</p>
        <p><a href="<?= base_url('auth/reset_password/' . $token) ?>"><?= base_url('auth/reset_password/' . $token) ?></a></p>
        <div class="footer">
            <p><strong>Catatan:</strong> Link reset password ini hanya berlaku selama 60 menit.</p>
            <p>Jika Anda tidak meminta reset password, abaikan saja email ini. Password Anda akan tetap aman.</p>
        </div>
    </div>
</body>
</html>
