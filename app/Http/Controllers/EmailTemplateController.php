<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = collect(EmailTemplate::KEYS)->map(fn ($key) => [
            'key' => $key,
            'label' => EmailTemplate::LABELS[$key],
            'row' => EmailTemplate::get($key),
        ]);

        return view('admin.email-templates.index', compact('templates'));
    }

    public function edit(string $key)
    {
        abort_unless(in_array($key, EmailTemplate::KEYS, true), 404);

        return view('admin.email-templates.edit', [
            'key' => $key,
            'label' => EmailTemplate::LABELS[$key],
            'template' => EmailTemplate::get($key),
            'prefill' => EmailTemplate::body($key) ?? $this->defaultFullHtml($key),
            'placeholders' => array_keys(EmailTemplate::SAMPLES[$key]),
            'missing' => EmailTemplate::get($key)?->missingRequired() ?? [],
        ]);
    }

    /**
     * HTML lengkap tampilan default (dgn placeholder literal {nama} dsb) sebagai
     * titik awal edit — admin mulai dari kode email yang berlaku, bukan dari kosong.
     */
    private function defaultFullHtml(string $key): string
    {
        $message = new class
        {
            public function embed($path)
            {
                return asset('icon/logoo.png');
            }
        };

        // Setiap nilai = placeholder literal-nya sendiri, jadi hasil render memuat
        // {nama}/{otp}/dst yang nanti disubstitusi saat email benar-benar dikirim.
        $data = EmailTemplate::viewData(
            $key,
            array_map(fn ($ph) => '{'.$ph.'}', EmailTemplate::SAMPLES[$key]),
            ['token' => '{linkReset}', 'email' => '{email}']
        );

        $html = view('emails.'.$key, array_merge($data, [
            'message' => $message,
            'templateBody' => null,
        ]))->render();

        if ($key === 'reset_password') {
            // url() menghasilkan URL konkret dari token contoh → kembalikan ke {linkReset}
            $link = url('reset-password/{linkReset}?email='.urlencode('{email}'));
            $html = str_replace($link, '{linkReset}', $html);
        }

        return $html;
    }

    /**
     * Render draft (belum disimpan) di dalam cangkang email asli, untuk iframe preview.
     */
    public function preview(Request $request, string $key)
    {
        abort_unless(in_array($key, EmailTemplate::KEYS, true), 404);

        $data = EmailTemplate::viewData($key, EmailTemplate::SAMPLES[$key]);
        $draft = $request->input('body');
        $draftSubject = $request->input('subject');

        $message = new class
        {
            public function embed($path)
            {
                return asset('icon/logoo.png');
            }
        };

        $html = view('emails.'.$key, array_merge($data, [
            'message' => $message,
            'templateBody' => $draft ? EmailTemplate::render($key, $draft, $data) : null,
        ]))->render();

        if ($request->wantsJson()) {
            $defaultSubject = match ($key) {
                'otp' => 'Kode OTP Verifikasi Pendaftaran MNCU Future Leader Scholarship',
                'reset_password' => 'Atur Ulang Kata Sandi - MNCU Future Leader Scholarship',
                'email_change_otp' => 'Kode OTP Verifikasi Perubahan Email',
                'admin_changed_email' => 'Pemberitahuan Perubahan Email Akun MFLS',
                'lolos_seleksi_ujian' => 'Pengumuman Lolos Seleksi Ujian MFLS',
                'certificate' => 'Sertifikat Partisipasi MNCU Future Leader Scholarship',
                'ujian_link' => 'Undangan Ujian Online MNCU Future Leader Scholarship',
                default => 'Notifikasi MFLS',
            };
            $subject = $draftSubject ?: (EmailTemplate::get($key)?->subject ?: $defaultSubject);
            $renderedSubject = EmailTemplate::render($key, $subject, $data);

            return response()->json([
                'html' => $html,
                'subject' => $renderedSubject,
            ]);
        }

        return response($html);
    }

    public function update(Request $request, string $key)
    {
        abort_unless(in_array($key, EmailTemplate::KEYS, true), 404);

        $validated = $request->validate([
            'subject' => ['nullable', 'string', 'max:200'],
            'body' => ['nullable', 'string', 'max:65000'],
        ]);

        $validated['subject'] = $validated['subject'] ?: null;
        $validated['body'] = $validated['body'] ?: null;

        EmailTemplate::updateOrCreate(['key' => $key], $validated);

        return back()->with('success', 'Template email berhasil disimpan.');
    }

    public function destroy(string $key)
    {
        abort_unless(in_array($key, EmailTemplate::KEYS, true), 404);

        EmailTemplate::reset($key);

        return back()->with('success', 'Template email dikembalikan ke default.');
    }
}
