<?php

namespace App\Policies;

use App\Models\User;
use Lunar\Admin\Models\Staff;

class UserPolicy
{
    /**
     * Grant all abilities to Admin users.
     * This method is checked before all others.
     */
    public function before($user, string $ability): ?bool
    {
        if ($user instanceof User && $user->hasRole('Admin')) {
            return true;
        }

        // If it's a Lunar Staff member, we usually let Lunar's own permissions handle it,
        // but if we are in the Lunar context, we might want to return true for staff here.
        if ($user instanceof Staff) {
            return true;
        }

        return null; // Let other methods decide
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny($user): bool
    {
        // Only Admins can view a list of all users (handled by before method)
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view($user, User $model): bool
    {
        // A user can view their own profile.
        return $user instanceof User && $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create($user): bool
    {
        // Only Admins can create users directly (handled by before method)
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update($user, User $model): bool
    {
        // A user can update their own profile.
        return $user instanceof User && $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, User $model): bool
    {
        // A user can delete their own account.
        // Admins can delete any account (handled by the 'before' method).
        return $user instanceof User && $user->id === $model->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore($user, User $model): bool
    {
        // Only Admins can restore users (handled by before method)
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete($user, User $model): bool
    {
        // Only Admins can force delete users (handled by before method)
        return false;
    }
}
