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
                <h1>Selamat! Pendaftaran Terverifikasi</h1>
                <p>Halo <strong>{{ $nama }}</strong>,</p>
                <p>Kami dengan senang hati menginformasikan bahwa pendaftaran Anda di program <strong>MNCU Future Leader Scholarship</strong> telah berhasil diverifikasi.</p>
                <p>Sebagai bentuk apresiasi atas partisipasi awal Anda, kami melampirkan <strong>Sertifikat Partisipasi</strong> dalam format PDF pada email ini. Silakan unduh dan simpan sertifikat tersebut.</p>
                <p>Tahap selanjutnya akan diinformasikan melalui dashboard pendaftar dan email resmi kami. Mohon pantau secara berkala.</p>
                <p>Terima kasih atas semangat dan antusiasme Anda untuk bergabung dengan MNC University.</p>
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
