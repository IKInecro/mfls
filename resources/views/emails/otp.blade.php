<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background-color: #ffffff; }
        .container { max-width: 600px; margin: 0 auto; border: 1px solid #eee; padding: 30px; border-radius: 8px; }
        .header { margin-bottom: 30px; border-bottom: 2px solid #1a237e; padding-bottom: 10px; }
        .header img { max-width: 120px; }
        .content h1 { font-size: 22px; color: #1a237e; margin-bottom: 20px; }
        .otp-box { background: #f8f9fa; padding: 20px; text-align: center; border-radius: 8px; margin: 20px 0; }
        .otp-code { font-size: 32px; font-weight: 800; color: #1a237e; letter-spacing: 5px; }
        .footer { margin-top: 30px; font-size: 12px; color: #888; border-top: 1px solid #eee; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if(file_exists(public_path('icon/logoo.png')))
                <img src="{{ $message->embed(public_path('icon/logoo.png')) }}" alt="MNCU Logo">
            @endif
        </div>
        <div class="content">
            @if(!empty($templateBody))
                {!! $templateBody !!}
            @else
                <h1>Verifikasi Akun MNCU Future Leader Scholarship</h1>
                <p>Halo <strong>{{ $nama }}</strong>,</p>
                <p>Terima kasih telah mendaftar di program <strong>MNCU Future Leader Scholarship</strong>. Silakan gunakan kode OTP berikut untuk memverifikasi akun Anda:</p>
                <div class="otp-box">
                    <div class="otp-code">{{ $otp }}</div>
                </div>
                <p>Kode ini berlaku selama 10 menit. Mohon untuk tidak memberitahukan kode ini kepada siapapun demi keamanan akun Anda.</p>
                <p>Terima kasih atas semangat dan antusiasme Anda!</p>
                <p>Salam hangat,<br><strong>Panitia MNCU Future Leader Scholarship</strong></p>
            @endif
        </div>
        <div class="footer">
            &reg; 2026 MNC University. All rights reserved.<br>
            MNC Tower, Jakarta, Indonesia
        </div>
    </div>
</body>
</html>
