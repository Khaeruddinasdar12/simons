<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<int, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => UserRole::class,
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role !== null;
    }

    public function isSuperadmin(): bool
    {
        return $this->role === UserRole::Superadmin;
    }

    public function isAkademik(): bool
    {
        return $this->role === UserRole::Akademik || $this->isSuperadmin();
    }

    public function isKabag(): bool
    {
        return $this->role === UserRole::Kabag || $this->isSuperadmin();
    }

    public function isWadek1(): bool
    {
        return $this->role === UserRole::Wadek1 || $this->isSuperadmin();
    }

    public function isDekan(): bool
    {
        return $this->role === UserRole::Dekan || $this->isSuperadmin();
    }

    public function hasRole(UserRole|string $role): bool
    {
        if ($this->isSuperadmin()) {
            return true;
        }

        $value = $role instanceof UserRole ? $role : UserRole::tryFrom($role);

        return $this->role === $value;
    }
}
