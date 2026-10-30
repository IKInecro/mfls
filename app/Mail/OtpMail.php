<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nama;
    public $otp;

    /**
     * Create a new message instance.
     */
    public function __construct($nama, $otp)
    {
        $this->nama = $nama;
        $this->otp = $otp;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: \App\Models\EmailTemplate::subjectOrDefault('otp', 'Kode OTP Verifikasi Pendaftaran MNCU Future Leader Scholarship', [
                'nama' => $this->nama,
                'otp' => $this->otp,
            ]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
            with: [
                'templateBody' => \App\Models\EmailTemplate::renderBody('otp', [
                    'nama' => $this->nama,
                    'otp' => $this->otp,
                ]),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
