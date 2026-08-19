<?php

namespace App\Policies;

use App\Models\Dosen;
use App\Models\User;

class DosenPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAkademik();
    }

    public function view(User $user, Dosen $dosen): bool
    {
        return $user->isAkademik();
    }

    public function create(User $user): bool
    {
        return $user->isAkademik();
    }

    public function update(User $user, Dosen $dosen): bool
    {
        return $user->isAkademik();
    }

    public function delete(User $user, Dosen $dosen): bool
    {
        return $user->isAkademik();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAkademik();
    }
}
