# Plan: Admin-Managed Email Templates (body + subject + preview)

Status: SELESAI (2026-10-31) — 41/41 test hijau, smoke manual lolos (kirim email override nyata via jalur reset password, lalu reset ke default).
Commits: b2ac59c (model) → ed7267d (slot view) → 8e12a35 (send sites) → c08ae69 (admin UI + fix CheckRole) → d1a43b4 (fix closure scope)
Spec: `docs/superpowers/specs/2026-10-31-email-templates-design.md` (approved, amended §2.3)

## Goal
Admin can edit the inner body HTML + subject of all 7 transactional emails from
`/admin/email-templates`, with a live full-email preview. Outer shell (DOCTYPE, CSS,
logo, footer) stays locked in Blade. Row absent / column NULL = exact old behaviour.

## Architecture
- Table `email_templates` (key unique, subject nullable, body longText nullable), upsert per key.
- Model `EmailTemplate` owns: key whitelist, labels, per-key placeholder whitelist + sample
  values, required-placeholder map, substitution, subject/body resolution, reset.
- Blade views `resources/views/emails/*.blade.php` get ONE `@if(!empty($templateBody))` slot
  around their inner content region; `{!! $templateBody !!}` (already-rendered HTML).
- Body substitution is regex `/{(\w+)}/` over a per-key whitelist — NEVER Blade-compiled
  (anti code-exec). Unknown placeholders stay literal.
- Admin controller renders preview by rendering the real Blade shell with a draft
  `$templateBody` + a fake `$message` object (embed() → asset URL), output into an iframe
  via a form with `target="previewFrame"` (no fetch needed).

## Tech Stack
Laravel 12 (existing), Blade, Tailwind-style classes matching `layouts/admin.blade.php`,
PHPUnit + sqlite :memory: + RefreshDatabase (already configured in commit 6bda417).

## Global Constraints
- Fallback rule: no row OR NULL column → old behaviour byte-for-byte. Fresh DB = feature invisible.
- `admin_changed_email`: ONE stored subject overrides BOTH call sites (1467 + 1476); NULL keeps each default.
- `ujian_link` + `app/Mail/UjianLinkMail.php` are currently DEAD CODE (never sent) — wire them anyway per spec; do not delete.
- `app/Mail/VerificationOtp.php` is dead code with no view slot — leave untouched.
- Do not commit `.env`, `..env.swp`. Git identity already set repo-local.
- Placeholder names final (spec §2.3): otp→{nama},{otp}; reset_password→{nama},{linkReset};
  email_change_otp→{nama},{otp}; admin_changed_email→{nama},{emailLama},{emailBaru};
  lolos_seleksi_ujian→{nama},{asalSekolah}; certificate→{nama}; ujian_link→{nama},{linkUjian}.

## Review Focus
- Substitution never evals PHP/Blade from DB content.
- Every send site resolves subject via `subjectOrDefault` and passes `templateBody` (null-safe).
- Preview endpoint: admin-only, invalid key → 404, draft not persisted.
- Views render identically when `$templateBody` is null.

---

## Task 1: Migration + EmailTemplate model (TDD)
Files: `database/migrations/2026_10_31_000002_create_email_templates_table.php`,
`app/Models/EmailTemplate.php`, `tests/Feature/EmailTemplateTest.php`

Migration: `email_templates` — id, `key` string(50) unique, `subject` string nullable,
`body` longText nullable, timestamps.

Model constants + API (all values verified from code):
```php
public const KEYS = ['otp','reset_password','email_change_otp','admin_changed_email',
                     'lolos_seleksi_ujian','certificate','ujian_link'];
public const LABELS = [...];      // Indonesian labels from spec §2.1
public const SAMPLES = [...];     // per key: ['nama'=>'Budi Santoso','otp'=>'123456',
                                  //  'linkReset'=>'https://…/reset-password/abc?email=budi%40…',
                                  //  'emailLama'=>'lama@…','emailBaru'=>'baru@…',
                                  //  'asalSekolah'=>'SMAN 1 Surabaya','linkUjian'=>'https://…/ujian/abc']
public const REQUIRED = [...];    // otp=>{otp}, email_change_otp=>{otp}, reset_password=>{linkReset},
                                  // certificate=>{nama}, lolos_seleksi_ujian=>{nama},
                                  // ujian_link=>{linkUjian}, admin_changed_email=>{emailBaru}
public static function get(string $key): ?self;
public static function subjectOrDefault(string $key, string $default): string;
public static function body(string $key): ?string;                    // override body or null
public static function renderBody(string $key, array $data): ?string; // null if no row/body
public static function render(string $key, string $body, array $data): string;
    // pure: preg_replace_callback('/{(\w+)}/', …) replacing only keys of SAMPLES[$key],
    // values from $data (missing value → leave placeholder literal).
    // renderBody = body($key) ? render(...) : null. Preview uses render() on the DRAFT body.
public static function reset(string $key): void;                      // delete row
public function missingRequired(): array;                             // required placeholders absent from body
```

Tests (write first, RED):
- `get('otp')` null on empty table
- after create: `subjectOrDefault('otp','X')` returns stored subject; NULL subject → 'X'
- `renderBody('otp', ['nama'=>'Budi','otp'=>'9999'])` on body `Halo {nama}, kode {otp}` → substituted;
  unknown `{wah}` stays literal; no row → null
- `reset('otp')` removes row

Commit: "feat: email_templates table + EmailTemplate model with safe placeholder substitution"

## Task 2: Blade slots in 7 email views (TDD)
File: `tests/Feature/EmailTemplateViewTest.php`, edit 7 views in `resources/views/emails/`.

Test helper: render view directly with fake message:
```php
$message = new class { public function embed($path) { return 'cid:logo'; } };
$html = view('emails.otp', ['nama'=>'B','otp'=>'1','message'=>$message,
          'templateBody'=>'<p>ISI BARU</p>'])->render();
```
Per view: (a) `templateBody` null → contains default inner text, does NOT contain 'ISI BARU';
(b) `templateBody='<p>ISI BARU</p>'` → contains 'ISI BARU', does NOT contain default inner text.
(reset_password/email_change_otp need extra vars `user` (stdClass nama+email) / `otp`;
certificate/lolos/ujian_link/others per their variable list.)

Slot regions (wrap with `@if(!empty($templateBody)) {!! $templateBody !!} @else … @endif`):
| view | region |
|---|---|
| otp | inner `<div class="content">` (lines 24–33: h1 … closing `</div>` before `.footer`) |
| reset_password | inner `<div class="content">` (lines 25–34) |
| email_change_otp | inner `<div class="content">` (lines 22–35) |
| admin_changed_email | inner `<div class="content">` (lines 23–42) |
| certificate | inner `<div class="content">` (lines 22–34) |
| lolos_seleksi_ujian | from `<h2` "Selamat! Anda Lolos…" (line ~39) through `<p>Panitia MNCU Future Leader Scholarship</p>` (line ~125); Divider + Footer table stay OUTSIDE the slot |
| ujian_link | inner `<div style="padding: 30px;">` (lines 12–26) |

Commit with Task 1 or separately: "feat: $templateBody slot in 7 email views with Blade fallback"

## Task 3: Wire send sites (TDD where cheap)
Pattern at every site: `subject` → `EmailTemplate::subjectOrDefault($key, '<old literal>')`;
add `'templateBody' => EmailTemplate::renderBody($key, [...placeholder data...])` to view data.

| site (anchor) | key | placeholder data |
|---|---|---|
| `app/Mail/OtpMail.php` Envelope:33 + Content():44 | otp | `['nama'=>$this->nama,'otp'=>$this->otp]` |
| `AuthController.php:338-340` Mail::send reset_password | reset_password | `['nama'=>$user->nama,'linkReset'=>url('reset-password/'.$token.'?email='.urlencode($user->email))]` |
| `PendaftarController.php:662-664` email_change_otp | email_change_otp | `['nama'=>$user->nama,'otp'=>$otpCode]` |
| `AdminController.php:827-831` lolos ujian | lolos_seleksi_ujian | `['nama'=>$nama,'asalSekolah'=>$asalSekolah]` |
| `app/Jobs/SendLolosUjianEmailJob.php:54-59` | lolos_seleksi_ujian | same |
| `AdminController.php:1463-1476` (BOTH subjects) | admin_changed_email | `['nama'=>$nama,'emailLama'=>$oldEmail,'emailBaru'=>$newEmail]` |
| `app/Mail/CertificateMail.php` Envelope:34 + Content():44 | certificate | `['nama'=>$this->nama]` |
| `app/Mail/UjianLinkMail.php` Envelope:28 + Content():35 | ujian_link | `['nama'=>$this->nama,'linkUjian'=>$this->linkUjian]` |

Mailable tests (no HTTP needed): create row, `(new OtpMail(...))->render()` → assert override
HTML + subject via `(new OtpMail(...))->envelope->subject`; same for CertificateMail, UjianLinkMail.
Plus one Mail::fake integration: POST forgot-password route for an existing akun
(find route via `php artisan route:list | grep -i -E 'reset|lupa|forget'`) with stored subject →
`Mail::assertSent(OtpMail… no — closure mail)`; for closure mails assert via
`Mail::fake()->assertSent(...)` is not possible for Mail::send closures — instead assert
`EmailTemplate::subjectOrDefault` behaviour is covered by Task 1 and keep integration to
rendering the Mailables. (Closure sites verified by full-suite + manual smoke in Task 5.)

Commit: "feat: email send sites honour admin template overrides"

## Task 4: Admin CRUD (routes, controller, list + edit views, sidebar)
Files: `app/Http/Controllers/EmailTemplateController.php`,
`resources/views/admin/email-templates/index.blade.php`, `edit.blade.php`,
`routes/web.php`, `resources/views/layouts/admin.blade.php`,
`tests/Feature/EmailTemplateAdminTest.php`

Routes (inside existing auth+role group, NEW nested `role:admin` group):
```
GET    /admin/email-templates                index    name admin.email-templates.index
GET    /admin/email-templates/{key}/edit     edit     name admin.email-templates.edit
POST   /admin/email-templates/{key}/preview  preview  name admin.email-templates.preview
POST   /admin/email-templates/{key}          update   name admin.email-templates.update
DELETE /admin/email-templates/{key}          destroy  name admin.email-templates.destroy
```
Controller: invalid key → 404 (`abort_unless(in_array($key, EmailTemplate::KEYS), 404)`).
`update` validates `subject => ['nullable','string','max:200']`, `body => ['nullable','string','max:65000']`;
upsert via `EmailTemplate::updateOrCreate(['key'=>$key], $validated)`; back with success flash.
`preview`: builds `$data = EmailTemplate::SAMPLES[$key]`, renders
`view('emails.'.$key, $data + ['templateBody'=> $draftBody ? EmailTemplate::render($key, $draftBody, $data) : null,
'message' => new class { public function embed($p){ return asset('icon/logoo.png'); } }])`
→ returns HTML as response (view or raw). Draft subject not rendered (shown as text above iframe).
`destroy`: `EmailTemplate::reset($key)`; back success.

index view: table of 7 keys — label, status badge (Default / Diubah + updated_at), Edit link.
edit view: subject input (hint "kosongkan = pakai judul asli"), monospace textarea body,
placeholder chips (JS insert at cursor), warning box if `missingRequired()` non-empty on saved body,
Preview iframe: `<form method="POST" action="preview" target="previewFrame">` with hidden inputs
synced from textarea/subject via small JS on Preview click; Simpan + Reset ke Default (DELETE form).
Sidebar: "Kelola Email" item after Pengaturan Pendaftaran, wrapped `@if(auth()->user()->role === 'admin')`.

Tests:
- tamu → redirect login (all GET routes)
- role `akademik` → 403 (role:admin middleware)
- admin index 200, edit valid key 200, edit invalid key 404
- update persists subject+body (assertDatabaseHas); reset deletes row
- preview returns 200 containing draft marker text

Commit: "feat: admin UI to edit/preview/reset email templates (admin-only)"

## Task 5: Full suite + manual smoke + report + commit
1. `php artisan test` — all green (existing 14 + new).
2. Migrate dev DB (`php artisan migrate`), restart server if needed
   (`cd /home/jawaa/mfls && php artisan serve --port=8001`).
3. Manual smoke as admin (`/internal/login`, info@beasiswamncu.com/password):
   - `/admin/email-templates` → 7 rows "Default"
   - edit otp: change body (keep {nama}/{otp}), preview shows Budi Santoso + 123456 inside REAL shell
     (logo + footer visible), save, reset back to Default
   - trigger real mail: mode buka + POST /register? OTP kirim butuh Turnstile valid — alternatif:
     tes jalur reset password (POST lupa-password) dengan MAIL_MAILER=log →
     `tail -f storage/logs/laravel.log` shows rendered subject/body override, then reset template.
4. Update `/home/jawaa/laporan-mfls/laporan.md` with feature 2 section.
5. Commit: "feat+docs: …" or separate docs commit.
