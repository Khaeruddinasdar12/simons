<?php

namespace App\Mail;

use App\Models\PermohonanPenguji;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SkPengujiTerbitMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PermohonanPenguji $permohonan
    ) {
        $this->permohonan->loadMissing('mahasiswa');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Penerbitan SK Penguji Telah Selesai — '.$this->permohonan->mahasiswa?->nama_lengkap,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.sk-penguji-terbit',
            with: [
                'permohonan' => $this->permohonan,
                'mahasiswa' => $this->permohonan->mahasiswa,
                'trackingUrl' => route('permohonan.tracking', [
                    'nim' => $this->permohonan->mahasiswa_nim,
                ]),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
