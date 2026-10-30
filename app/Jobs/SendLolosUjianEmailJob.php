<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendLolosUjianEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $pesertaId;
    protected $batchId;

    /**
     * Create a new job instance.
     */
    public function __construct($pesertaId, $batchId = null)
    {
        $this->pesertaId = $pesertaId;
        $this->batchId = $batchId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $peserta = \App\Models\Peserta::with(['akun', 'daftar'])->find($this->pesertaId);

        if (!$peserta) {
            Log::error("SendLolosUjianEmailJob failed: Peserta with ID {$this->pesertaId} not found.");
            $this->incrementBatchProgress();
            return;
        }

        $emailTujuan = $peserta->akun->email ?? null;
        if (!$emailTujuan) {
            Log::warning("SendLolosUjianEmailJob skipped: Peserta ID {$this->pesertaId} does not have an email address.");
            $this->incrementBatchProgress();
            return;
        }

        $nama        = $peserta->akun->nama ?? $peserta->nama;
        $asalSekolah = $peserta->daftar->asal_sekolah ?? ($peserta->nama_sekolah ?? '-');

        try {
            Log::info("Sending Lolos Ujian Email to {$emailTujuan} (Peserta ID: {$this->pesertaId})");
            
            \Illuminate\Support\Facades\Mail::mailer('hostinger')
                ->send('emails.lolos_seleksi_ujian',
                    array_merge(compact('nama', 'asalSekolah'), [
                        'templateBody' => \App\Models\EmailTemplate::renderBody('lolos_seleksi_ujian', compact('nama', 'asalSekolah')),
                    ]),
                    function ($message) use ($emailTujuan, $nama) {
                        $message->to($emailTujuan, $nama)
                                ->subject(\App\Models\EmailTemplate::subjectOrDefault('lolos_seleksi_ujian', '🎉 Selamat! Anda Lolos Seleksi Ujian MNCU Future Leader Scholarship 2026', compact('nama', 'asalSekolah')));
                    }
                );

            // Mark email as sent successfully
            $peserta->update(['is_email_dikirim' => true]);
            
        } catch (\Exception $e) {
            Log::error("SendLolosUjianEmailJob error for Peserta ID {$this->pesertaId}: " . $e->getMessage());
        } finally {
            $this->incrementBatchProgress();
        }
    }

    /**
     * Increment batch progress cache
     */
    protected function incrementBatchProgress()
    {
        if ($this->batchId) {
            $key = "ujian_email_batch_{$this->batchId}_current";
            $current = \Illuminate\Support\Facades\Cache::get($key, 0);
            \Illuminate\Support\Facades\Cache::put($key, (int)$current + 1, 3600);
            Log::info("Incremented Ujian Email Progress for Batch {$this->batchId} to: " . ($current + 1));
        }
    }
}
