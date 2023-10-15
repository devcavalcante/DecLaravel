<?php
namespace App\Policies;

use App\Repositories\Interfaces\TypeUserRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Auth\Access\HandlesAuthorization;

class MemberPolicy extends AbstractPolicy
{
    use HandlesAuthorization;

    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected TypeUserRepositoryInterface $typeUserRepository
    ) {
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
