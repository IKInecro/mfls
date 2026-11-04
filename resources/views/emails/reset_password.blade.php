@if(!empty($templateBody) && \App\Models\EmailTemplate::isFullDocument($templateBody))
{!! $templateBody !!}
@else
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 20px auto; background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header { margin-bottom: 30px; border-bottom: 2px solid #001f3f; padding-bottom: 15px; text-align: left; }
        .header img { max-width: 120px; }
        .content { padding: 40px; background: #fff; }
        .btn { display: inline-block; padding: 15px 30px; background-color: #f97316; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: bold; margin-top: 20px; }
        .footer { background: #f8f9fa; color: #6c757d; padding: 20px; text-align: center; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if(file_exists(public_path('icon/logoo.png')))
                <img src="{{ $message->embed(public_path('icon/logoo.png')) }}" alt="MNCU Logo">
            @else
                <!-- Fallback to default asset if not available -->
                <img src="{{ $message->embed(public_path('icon/loog.png')) }}" alt="MNCU Logo">
            @endif
        </div>
        <div class="content">
            @if(!empty($templateBody))
                {!! $templateBody !!}
            @else
                <h1 style="color: #001f3f; font-size: 24px; margin-top: 0;">Atur Ulang Kata Sandi</h1>
                <h3>Halo, {{ $nama ?? $user->nama ?? 'Peserta' }}!</h3>
                <p>Kami menerima permintaan untuk mengatur ulang kata sandi akun MNCU Future Leader Scholarship Anda. Silakan klik tombol di bawah ini untuk melanjutkan:</p>
                <div style="text-align: center;">
                    <a href="{{ $linkReset ?? url('reset-password/'.$token.'?email='.urlencode($user->email)) }}" class="btn">Atur Ulang Password</a>
                </div>
                <p style="margin-top: 30px;">Tautan ini akan kedaluwarsa dalam 60 menit. Jika Anda tidak merasa melakukan permintaan ini, abaikan saja email ini.</p>
                <hr style="border: 0; border-top: 1px solid #eee; margin: 30px 0;">
                <p style="font-size: 13px; color: #888;">Jika mengalami masalah saat menekan tombol, salin dan tempel URL berikut di browser Anda:<br>
                <span style="color: #001f3f;">{{ $linkReset ?? url('reset-password/'.$token.'?email='.urlencode($user->email)) }}</span></p>
            @endif
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} MNC University - Mata Garuda Scholarship. Official Portal.
        </div>
    </div>
</body>
</html>

@endif
