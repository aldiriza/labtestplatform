<?php

namespace App\Policies;

use App\Models\Material;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MaterialPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can inspect list
    }

    public function view(User $user, Material $material): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('purchasing');
    }

    public function update(User $user, Material $material): bool
    {
        return $user->hasRole('admin') || $user->hasRole('purchasing');
    }

    public function delete(User $user, Material $material): bool
    {
        return $user->hasRole('admin') || $user->hasRole('purchasing');
    }

    public function restore(User $user, Material $material): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Material $material): bool
    {
        return $user->hasRole('admin');
    }
}
