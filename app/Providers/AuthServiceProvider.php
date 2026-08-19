<?php

namespace App\Providers;

use App\Models\Dosen;
use App\Models\IstilahProdi;
use App\Models\JudulKorpus;
use App\Models\Keahlian;
use App\Models\PermohonanPembimbing;
use App\Models\PermohonanPenguji;
use App\Policies\DosenPolicy;
use App\Policies\MasterDataPolicy;
use App\Policies\PermohonanPembimbingPolicy;
use App\Policies\PermohonanPengujiPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        PermohonanPembimbing::class => PermohonanPembimbingPolicy::class,
        PermohonanPenguji::class => PermohonanPengujiPolicy::class,
        Dosen::class => DosenPolicy::class,
        Keahlian::class => MasterDataPolicy::class,
        IstilahProdi::class => MasterDataPolicy::class,
        JudulKorpus::class => MasterDataPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
