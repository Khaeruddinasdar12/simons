<?php

namespace App\Policies;

use App\Enums\StatusPermohonan;
use App\Models\PermohonanPenguji;
use App\Models\User;

class PermohonanPengujiPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role !== null;
    }

    public function view(User $user, PermohonanPenguji $record): bool
    {
        return $user->role !== null;
    }

    public function create(User $user): bool
    {
        return $user->isSuperadmin();
    }

    public function update(User $user, PermohonanPenguji $record): bool
    {
        if ($user->isSuperadmin()) {
            return true;
        }

        return $user->isAkademik() && $record->isEditableByAkademik();
    }

    public function delete(User $user, PermohonanPenguji $record): bool
    {
        return $user->isSuperadmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isSuperadmin();
    }

    public function hapusDataNim(User $user, PermohonanPenguji $record): bool
    {
        return $user->isSuperadmin();
    }

    public function sendToPimpinan(User $user, PermohonanPenguji $record): bool
    {
        return $user->isAkademik() && $record->canBeSentByAkademik();
    }

    public function rejectFinal(User $user, PermohonanPenguji $record): bool
    {
        return $user->isAkademik() && $record->canBeRejectedByAkademik();
    }

    public function verifyKabag(User $user, PermohonanPenguji $record): bool
    {
        return $user->isKabag()
            && $record->isAwaitingPimpinan()
            && $record->kabag_status === 'pending';
    }

    public function verifyWadek1(User $user, PermohonanPenguji $record): bool
    {
        return $user->isWadek1()
            && $record->isAwaitingPimpinan()
            && $record->wadek1_status === 'pending';
    }

    public function issueSk(User $user, PermohonanPenguji $record): bool
    {
        return $user->isDekan() && $record->canDekanVerify();
    }

    public function previewSk(User $user, PermohonanPenguji $record): bool
    {
        return $user->role !== null;
    }
}
