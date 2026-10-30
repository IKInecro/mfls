<!DOCTYPE html>
<html>
<head>
    <title>Undangan Ujian Online MNCU Future Leader Scholarship</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="background-color: #ffffff; border-radius: 10px; max-width: 600px; margin: 0 auto; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
        <div style="padding: 20px; border-bottom: 2px solid #001f3f;">
            <img src="{{ $message->embed(public_path('icon/logoo.png')) }}" alt="MNCU Logo" style="max-width: 120px;">
        </div>
        <div style="padding: 30px;">
        @if(!empty($templateBody))
            {!! $templateBody !!}
        @else
        <h2 style="color: #111827;">Halo, {{ $nama }}!</h2>
        <p style="color: #555; line-height: 1.6;">
            Selamat! Berkas pendaftaran Anda telah lolos verifikasi administrasi. Tahap selanjutnya adalah <strong>Ujian Online (Seleksi Potensi Akademik)</strong>.
        </p>
        <div style="margin: 30px 0; text-align: center;">
            <a href="{{ $linkUjian }}" style="background-color: #F2B451; color: #111827; padding: 15px 30px; text-decoration: none; font-weight: bold; border-radius: 50px;">
                Mulai Ujian Sekarang
            </a>
        </div>
        <p style="color: #777; font-size: 12px; text-align: center;">
            Jika tombol tidak berfungsi, silakan copy link berikut: <br> {{ $linkUjian }}
        </p>
        <hr style="border: 0; border-top: 1px solid #eee; margin: 30px 0;">
        <p style="color: #aaa; font-size: 12px; text-align: center;">
            Panitia Seleksi MNCU Future Leader Scholarship 2026
        </p>
        @endif
    </div>
</body>
</html>
