@if(!empty($templateBody) && \App\Models\EmailTemplate::isFullDocument($templateBody))
{!! $templateBody !!}
@else
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengumuman Lolos Seleksi MNCU Future Leader Scholarship 2026</title>
</head>

<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6;padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0"
                    style="background:#ffffff; max-width:600px; width:100%; border-radius:8px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.05); border-top:4px solid #1a365d;">

                    {{-- Logo & Top Header --}}
                    <tr>
                        <td style="padding:30px 40px 20px 40px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <img src="{{ $message->embed(public_path('icon/logoo.png')) }}"
                                            alt="MNC University" style="max-height:45px; display:block;">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-top:15px;">
                                        <div style="border-bottom:2px solid #1a365d; width:100%;"></div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Main Content --}}
                    <tr>
                        <td style="padding:20px 40px 40px 40px;">
                            @if(!empty($templateBody))
                                {!! $templateBody !!}
                            @else
                            <h2
                                style="color:#1a365d; font-size:24px; font-weight:800; margin:0 0 6px 0; font-family:'Segoe UI', Arial, sans-serif;">
                                Selamat! Anda Lolos Seleksi Ujian!
                            </h2>
                            <p
                                style="color:#64748b; font-size:13px; font-weight:700; margin:0 0 24px 0; font-family:'Segoe UI', Arial, sans-serif; text-transform:uppercase; letter-spacing:0.05em;">
                                Pengumuman Tahap Seleksi Wawancara MNCU Future Leader Scholarship 2026
                            </p>

                            <p style="color:#334155; font-size:15px; line-height:1.6; margin:0 0 16px 0;">
                                Halo <strong>{{ $nama }}</strong>,
                            </p>

                            <p style="color:#334155; font-size:15px; line-height:1.6; margin:0 0 16px 0;">
                                Berdasarkan hasil proses seleksi administrasi, Tes TPA, dan Tes TBI yang telah
                                dilaksanakan, dengan ini kami menginformasikan bahwa peserta yang menerima pesan ini
                                dinyatakan <strong style="color:#1a365d;">LOLOS ke Tahap Seleksi Wawancara</strong>
                                Program MNCU Future
                                Leader Scholarship.
                            </p>

                            <p style="color:#334155; font-size:15px; line-height:1.6; margin:0 0 20px 0;">
                                Tahap wawancara merupakan bagian lanjutan dari proses seleksi untuk melihat potensi,
                                kesiapan, kemampuan komunikasi, serta kesesuaian peserta dengan nilai dan tujuan program
                                scholarship.
                            </p>

                            {{-- Info Peserta --}}
                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="background:#f8fafc; border-radius:12px; border:1px solid #e2e8f0; margin-bottom:20px;">
                                <tr>
                                    <td style="padding:18px 20px;">
                                        <p
                                            style="margin:0 0 4px 0; font-size:10px; font-weight:900; color:#94a3b8; text-transform:uppercase; letter-spacing:0.1em;">
                                            Nama Peserta</p>
                                        <p style="margin:0 0 16px 0; font-size:15px; font-weight:700; color:#1a365d;">
                                            {{ $nama }}
                                        </p>

                                        <p
                                            style="margin:0 0 4px 0; font-size:10px; font-weight:900; color:#94a3b8; text-transform:uppercase; letter-spacing:0.1em;">
                                            Asal Sekolah</p>
                                        <p style="margin:0; font-size:14px; font-weight:700; color:#334155;">
                                            {{ $asalSekolah }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            {{-- Info Pelaksanaan --}}
                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="background:#f0f9ff; border-radius:12px; border:1px solid #bae6fd; margin-bottom:20px;">
                                <tr>
                                    <td style="padding:18px 20px;">
                                        <p
                                            style="margin:0 0 8px 0; font-size:11px; font-weight:900; color:#0369a1; text-transform:uppercase; letter-spacing:0.1em;">
                                            📅 Jadwal Pelaksanaan Wawancara</p>
                                        <p style="margin:0 0 12px 0; font-size:14px; font-weight:700; color:#0369a1;">
                                            Selasa & Rabu, 19 & 20 Mei 2026
                                        </p>

                                        <p
                                            style="margin:0 0 4px 0; font-size:11px; font-weight:900; color:#0369a1; text-transform:uppercase; letter-spacing:0.1em;">
                                            📍 Media & Teknis</p>
                                        <p style="margin:0; font-size:13px; font-weight:600; color:#0e7490;">
                                            Media dan teknis pelaksanaan akan diinformasikan lebih lanjut
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="color:#334155; font-size:15px; line-height:1.6; margin:0 0 16px 0;">
                                Kami mengucapkan selamat kepada seluruh peserta yang berhasil melanjutkan ke tahap ini.
                                Tetap persiapkan diri dengan baik, jaga komunikasi, dan pastikan untuk terus memantau
                                informasi resmi yang akan disampaikan oleh panitia.
                            </p>

                            <p style="color:#334155; font-size:15px; line-height:1.6; margin:0 0 24px 0;">
                                Demikian pengumuman ini disampaikan. Atas perhatian dan partisipasinya kami ucapkan
                                terima kasih.
                            </p>

                            <p style="color:#334155; font-size:15px; line-height:1.6; margin:0 0 4px 0;">
                                Hormat Kami,
                            </p>
                            <p style="color:#1a365d; font-size:15px; font-weight:700; margin:0 0 30px 0;">
                                Panitia MNCU Future Leader Scholarship
                            </p>
                            @endif

                            {{-- Divider --}}
                            <div style="border-top:1px solid #e2e8f0; margin-bottom:20px;"></div>

                            {{-- Footer Content --}}
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="color:#94a3b8; font-size:12px; line-height:1.5;">
                                        &copy; 2026 MNC University. All rights reserved.<br>
                                        MNC Tower, Jakarta, Indonesia
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
@endif
