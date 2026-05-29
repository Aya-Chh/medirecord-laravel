<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        // Only admin can view the list of users (according to spec: GET/api/v1/users -> admin)
        return $user?->hasRole('admin') ?? false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $profileUser): bool
    {
        // Admin can view any user, or the user himself can view his own record (according to spec: GET/api/v1/users/{id} -> admin, ou soi-même)
        return $user->hasRole('admin') || $user->id === $profileUser->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admin can create users (according to spec: POST/api/v1/users -> admin? Not explicitly stated, but we assume admin only for consistency)
        // However, note that the registration is handled by AuthController and is public for patients.
        // This policy is for the UserController which is for admin management.
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $profileUser): bool
    {
        // Admin can update any user, or the user himself can update his own record (according to spec: PUT/api/v1/users/{id} -> admin, ou soi-même)
        return $user->hasRole('admin') || $user->id === $profileUser->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $profileUser): bool
    {
        // Only admin can delete users (according to spec: DELETE/api/v1/users/{id} -> admin)
        return $user->hasRole('admin');
    }
}