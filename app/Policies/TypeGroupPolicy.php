<?php

namespace App\Policies;

use App\Models\TypeGroup;
use App\Models\User;

class TypeGroupPolicy extends AbstractPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(): bool
    {
        return $this->isRepresentative();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(): bool
    {
        return $this->isRepresentative();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(): bool
    {
        return $this->isRepresentative();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(): bool
    {
        return $this->isRepresentative();
    }
}
