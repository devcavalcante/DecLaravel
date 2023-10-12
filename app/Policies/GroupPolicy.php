<?php

namespace App\Policies;

class GroupPolicy extends AbstractPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(): bool
    {
        return $this->isManager();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(): bool
    {
        return $this->isManager();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(): bool
    {
        return $this->isManager();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(): bool
    {
        return $this->isManager();
    }
}
