<?php

namespace Tests\Unit;

use App\Services\SkNomorAllocator;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class SkNomorAllocatorTest extends TestCase
{
    public function test_starts_at_001_when_empty(): void
    {
        $nomor = SkNomorAllocator::next('SK-PEMBIMBING', [], Carbon::parse('2026-08-22'));

        $this->assertSame('001/SK-PEMBIMBING/08/2026', $nomor);
    }

    public function test_uses_highest_sequence_even_if_newer_id_has_lower_number(): void
    {
        // Produksi: SK terbit tidak urut id. Id terbaru bisa 001 padahal 002 sudah dipakai.
        $existingInIdDescOrder = [
            '001/SK-PEMBIMBING/08/2026',
            '002/SK-PEMBIMBING/08/2026',
        ];

        $nomor = SkNomorAllocator::next('SK-PEMBIMBING', $existingInIdDescOrder, Carbon::parse('2026-08-22'));

        $this->assertSame('003/SK-PEMBIMBING/08/2026', $nomor);
    }

    public function test_counts_across_months_in_the_same_year(): void
    {
        $existing = [
            '001/SK-PEMBIMBING/07/2026',
            '002/SK-PEMBIMBING/08/2026',
        ];

        $nomor = SkNomorAllocator::next('SK-PEMBIMBING', $existing, Carbon::parse('2026-08-22'));

        $this->assertSame('003/SK-PEMBIMBING/08/2026', $nomor);
    }
}
