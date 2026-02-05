<?php
// app/Policies/QRSessionPolicy.php

namespace App\Policies;

use App\Models\QRSession;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QRSessionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'lecturer']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, QRSession $qrSession): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        
        if ($user->hasRole('lecturer')) {
            return $user->lecturer->subjects->contains('id', $qrSession->subject_id);
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'lecturer']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, QRSession $qrSession): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        
        if ($user->hasRole('lecturer')) {
            return $user->id === $qrSession->created_by;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, QRSession $qrSession): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        
        if ($user->hasRole('lecturer')) {
            return $user->id === $qrSession->created_by;
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, QRSession $qrSession): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, QRSession $qrSession): bool
    {
        return $user->hasRole('admin');
    }
}