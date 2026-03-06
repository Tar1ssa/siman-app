<?php

namespace App\Policies;

use App\Models\DataInternal;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DataInternalPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DataInternal $dataInternal): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DataInternal $dataInternal): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DataInternal $dataInternal): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DataInternal $dataInternal): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DataInternal $dataInternal): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view locked data.
     */
    public function viewLockedData(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can lock data.
     */
    public function lockData(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can unlock data.
     */
    public function unlockData(User $user): bool
    {
        return $user->isAdmin();
    }
}
