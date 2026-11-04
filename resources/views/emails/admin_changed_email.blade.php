@if(!empty($templateBody) && \App\Models\EmailTemplate::isFullDocument($templateBody))
{!! $templateBody !!}
@else
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 20px auto; background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header { margin-bottom: 30px; border-bottom: 2px solid #001f3f; padding-bottom: 15px; text-align: left; }
        .header img { max-width: 120px; }
        .content { padding: 40px; background: #fff; }
        .footer { background: #f8f9fa; color: #6c757d; padding: 20px; text-align: center; font-size: 12px; }
        .info-box { background: #f0f7ff; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0; border-radius: 0 8px 8px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header" style="padding: 20px; background: #fff;">
            @if(file_exists(public_path('icon/logoo.png')))
                <img src="{{ $message->embed(public_path('icon/logoo.png')) }}" alt="MNCU Logo">
            @endif
        </div>
        <div class="content">
            @if(!empty($templateBody))
                {!! $templateBody !!}
            @else
                <h1 style="color: #001f3f; font-size: 24px; margin-top: 0;">Pembaruan Alamat Email</h1>
                <h3>Halo, {{ $nama }}!</h3>
                <p>Kami ingin menginformasikan bahwa administrator **MNCU Future Leader Scholarship** telah memperbarui alamat email akun Anda sesuai permintaan atau keperluan verifikasi.</p>

                <div class="info-box">
                    <p style="margin: 5px 0;"><strong>Email Lama:</strong> {{ $oldEmail ?? $emailLama ?? '-' }}</p>
                    <p style="margin: 5px 0; color: #f97316;"><strong>Email Baru:</strong> {{ $newEmail ?? $emailBaru ?? '-' }}</p>
                </div>

                <p>Mulai saat ini, silakan gunakan <strong>Email Baru</strong> Anda untuk masuk (Login) ke portal pendaftar.</p>

                <p>Jika Anda tidak merasa meminta perubahan ini atau mengalami kendala, silakan segera hubungi pusat bantuan kami.</p>

                <p>Terima kasih atas perhatiannya.</p>
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

@endif
