<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class CertificateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nama;
    public $pdfContent;

    /**
     * Create a new message instance.
     */
    public function __construct($nama, $pdfContent)
    {
        $this->nama = $nama;
        $this->pdfContent = $pdfContent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: \App\Models\EmailTemplate::subjectOrDefault('certificate', "Sertifikat Partisipasi MNCU Future Leader Scholarship - {$this->nama}", [
                'nama' => $this->nama,
            ]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.certificate',
            with: [
                'templateBody' => \App\Models\EmailTemplate::renderBody('certificate', [
                    'nama' => $this->nama,
                ]),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfContent, "Sertifikat_{$this->nama}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
