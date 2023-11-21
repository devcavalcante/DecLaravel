<?php

namespace App\Policies;

use App\Enums\TypeUserEnum;
use App\Http\Requests\UserRequest;
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
     * Determine whether the user can create models.
     */
    public function create(User $user, UserRequest $userRequest): bool
    {
        return $this->isUserAuthorized($userRequest->type_user_id);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, string $id): bool
    {
        return $this->canModifiedUser($user->id, $id) || $user->id == $id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, string $id): bool
    {
        return $this->canModifiedUser($user->id, $id) || $user->id == $id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, string $id): bool
    {
        return $this->canModifiedUser($user->id, $id) || $user->id == $id;
    }

    private function isUserAuthorized(string $typeUserId): bool
    {
        $isAdmin = $this->isAdmin();
        $isManager = $this->isManager();
        $isRepresentative = $this->isRepresentative();
        $typeUser = $this->typeUserRepository->findById($typeUserId);

        return $isAdmin
            || ($isManager && $typeUser->name == TypeUserEnum::REPRESENTATIVE)
            || ($isRepresentative && $typeUser->name == TypeUserEnum::VIEWER);
    }

    private function canModifiedUser(string $userId, string $id): bool
    {
        $user = $this->userRepository->findById($id);
        return $user->creator_user_id == $userId;
    }
}
