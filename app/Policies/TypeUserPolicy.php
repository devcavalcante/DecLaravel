<?php

namespace App\Policies;

use App\Models\TypeUser;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TypeUserPolicy extends AbstractPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(): bool
    {
        return $this->isAdmin();
    }
}
