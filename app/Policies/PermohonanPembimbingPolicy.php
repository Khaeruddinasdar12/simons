<?php

namespace App\Policies;

use App\Enums\StatusPermohonan;
use App\Models\PermohonanPembimbing;
use App\Models\User;

class PermohonanPembimbingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role !== null;
    }

    public function view(User $user, PermohonanPembimbing $record): bool
    {
        return $user->role !== null;
    }

    public function create(User $user): bool
    {
        return $user->isSuperadmin();
    }

    public function update(User $user, PermohonanPembimbing $record): bool
    {
        if ($user->isSuperadmin()) {
            return true;
        }

        return $user->isAkademik() && $record->isEditableByAkademik();
    }

    public function delete(User $user, PermohonanPembimbing $record): bool
    {
        return $user->isSuperadmin()
            && $record->status === StatusPermohonan::Diajukan;
    }

    public function sendToPimpinan(User $user, PermohonanPembimbing $record): bool
    {
        return $user->isAkademik() && $record->canBeSentByAkademik();
    }

    public function rejectFinal(User $user, PermohonanPembimbing $record): bool
    {
        return $user->isAkademik() && $record->canBeRejectedByAkademik();
    }

    public function verifyKabag(User $user, PermohonanPembimbing $record): bool
    {
        return $user->isKabag()
            && $record->isAwaitingPimpinan()
            && $record->kabag_status === 'pending';
    }

    public function verifyWadek1(User $user, PermohonanPembimbing $record): bool
    {
        return $user->isWadek1()
            && $record->isAwaitingPimpinan()
            && $record->wadek1_status === 'pending';
    }

    public function issueSk(User $user, PermohonanPembimbing $record): bool
    {
        return $user->isDekan() && $record->canDekanVerify();
    }

    public function previewSk(User $user, PermohonanPembimbing $record): bool
    {
        return $user->role !== null;
    }
}
