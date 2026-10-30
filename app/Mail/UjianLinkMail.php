<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UjianLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nama;
    public $linkUjian;

    public function __construct($nama, $linkUjian)
    {
        $this->nama = $nama;
        $this->linkUjian = $linkUjian;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: \App\Models\EmailTemplate::subjectOrDefault('ujian_link', 'Undangan Ujian Online MNCU Future Leader Scholarship 2026', [
                'nama' => $this->nama,
                'linkUjian' => $this->linkUjian,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ujian_link',
            with: [
                'templateBody' => \App\Models\EmailTemplate::renderBody('ujian_link', [
                    'nama' => $this->nama,
                    'linkUjian' => $this->linkUjian,
                ]),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
