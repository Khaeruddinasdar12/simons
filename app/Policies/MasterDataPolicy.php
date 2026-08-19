<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class MasterDataPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAkademik();
    }

    public function view(User $user, Model $record): bool
    {
        return $user->isAkademik();
    }

    public function create(User $user): bool
    {
        return $user->isAkademik();
    }

    public function update(User $user, Model $record): bool
    {
        return $user->isAkademik();
    }

    public function delete(User $user, Model $record): bool
    {
        return $user->isAkademik();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAkademik();
    }
}
