<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    public function getHeading(): string|Htmlable
    {
        return (string) config('app.name');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return config('app.full_name');
    }
}
