<?php

namespace App\Policies;

use App\Models\User;

class ProductPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    function list(): bool
    {

        // All authenticated user can list

        return true;
    }

    function view(): bool
    {

        // Same as list action

        return $this->list();
    }

    function create(User $user): bool
    {

        return $user->isAdministrator();
    }

    function update(User $user): bool
    {

        // Same as create action.

        return $this->create($user);
    }

    function delete(User $user): bool
    {

        // Same as update action,

        // we consider delete is a special case of update.

        return $this->update($user);
    }
}
