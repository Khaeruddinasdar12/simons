<?php

namespace App\Services;

use App\Mail\SkPembimbingTerbitMail;
use App\Models\PermohonanPembimbing;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;
use Throwable;

class SkPembimbingMailService
{
    /**
     * Send SK-terbit notification email to the mahasiswa.
     *
     * @throws InvalidArgumentException when email is missing/invalid
     * @throws Throwable when SMTP/mail transport fails
     */
    public function sendTerbitNotification(PermohonanPembimbing $permohonan): string
    {
        $permohonan->loadMissing('mahasiswa');
        $email = trim((string) ($permohonan->mahasiswa?->email ?? ''));

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Log::error('Gagal kirim email SK: email tidak valid', [
                'permohonan_id' => $permohonan->id,
                'email' => $permohonan->mahasiswa?->email,
            ]);

            throw new InvalidArgumentException(
                'Email penerima tidak valid atau kosong (nilai: '.($permohonan->mahasiswa?->email ?: 'kosong').').'
            );
        }

        try {
            Mail::to($email)->send(new SkPembimbingTerbitMail($permohonan));

            Log::info('Email SK berhasil dikirim', [
                'permohonan_id' => $permohonan->id,
                'email' => $email,
                'nomor_sk' => $permohonan->nomor_sk,
            ]);

            return $email;
        } catch (Throwable $e) {
            Log::error('Gagal kirim email SK', [
                'permohonan_id' => $permohonan->id,
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
