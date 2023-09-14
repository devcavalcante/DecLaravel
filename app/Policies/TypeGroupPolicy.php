<?php

namespace App\Policies;

use App\Models\TypeGroup;
use App\Models\User;

class TypeGroupPolicy extends AbstractPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return $this->isRepresentative();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->isRepresentative();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return $this->isRepresentative();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return $this->isRepresentative();
    }
}
