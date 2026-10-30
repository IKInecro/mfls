<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 20px auto; background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header { margin-bottom: 30px; border-bottom: 2px solid #001f3f; padding-bottom: 15px; text-align: left; }
        .header img { max-width: 120px; }
        .content { padding: 40px; text-align: center; background: #fff; }
        .otp-box { font-size: 36px; font-weight: bold; color: #f97316; letter-spacing: 12px; margin: 30px 0; padding: 25px; border: 2px dashed #f97316; border-radius: 12px; background: #fffaf5; display: inline-block; }
        .footer { background: #f8f9fa; color: #6c757d; padding: 20px; text-align: center; font-size: 12px; }
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
                <h2 style="color: #001f3f; margin-top: 0;">Verifikasi Ganti Email</h2>
                <h3>Halo, {{ $user->nama }}!</h3>
                <p>Kami menerima permintaan untuk mengubah alamat email akun MNCU Future Leader Scholarship Anda. Silakan masukkan kode verifikasi berikut untuk melanjutkan:</p>
                <div class="otp-box">
                    {{ $otp }}
                </div>
                <p style="margin-top: 30px; color: #666; font-size: 14px;">Kode ini hanya berlaku selama 15 menit. <br>Jangan berikan kode ini kepada siapa pun karena bersifat rahasia.</p>
                <hr style="border: 0; border-top: 1px solid #eee; margin: 30px 0;">
                <p style="font-size: 13px; color: #888;">Permintaan ini berasal dari portal pendaftar MNCU Future Leader Scholarship. Jika Anda tidak merasa melakukan permintaan ini, silakan abaikan pesan ini.</p>
            @endif
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} MNC University - Mata Garuda Scholarship. Official Portal.
        </div>
    </div>
</body>
</html>
