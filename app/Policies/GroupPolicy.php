<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;
use App\Repositories\Interfaces\GroupRepositoryInterface;

class GroupPolicy extends AbstractPolicy
{

    public function __construct(private readonly GroupRepositoryInterface $groupRepository)
    {
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
    public function update(User $user, string $groupId): bool
    {
        return $this->groupRepository->findById($groupId)->creator_user_id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, string $groupId): bool
    {
        return $this->groupRepository->findById($groupId)->creator_user_id == $user->id;
    }
}
