<?php

namespace App\Mail;

use App\Models\PermohonanPembimbing;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SkPembimbingTerbitMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PermohonanPembimbing $permohonan
    ) {
        $this->permohonan->loadMissing('mahasiswa');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Penerbitan SK Pembimbing Telah Selesai — '.$this->permohonan->mahasiswa?->nama_lengkap,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.sk-terbit',
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
