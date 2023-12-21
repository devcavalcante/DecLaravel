<?php

namespace App\Policies;

use App\Models\User;
use App\Repositories\Interfaces\TypeUserRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy extends AbstractPolicy
{
    use HandlesAuthorization;

    public function __construct(
        protected TypeUserRepositoryInterface $typeUserRepository,
        protected UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(): bool
    {
        return $this->isAdmin() || $this->isManager() || $this->isRepresentative();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, string $id): bool
    {
        return $user->id == $id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, string $id): bool
    {
        return $user->id == $id || $this->isAdmin();
    }

    public function restore(User $user, string $id): bool
    {
        return $user->id == $id || $this->isAdmin();
    }
}
