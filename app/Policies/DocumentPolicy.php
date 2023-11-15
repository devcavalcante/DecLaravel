<?php

namespace App\Policies;

use App\Models\User;
use App\Repositories\Interfaces\DocumentRepositoryInterface;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentPolicy extends AbstractPolicy
{
    use HandlesAuthorization, PolicyTrait;

    public function __construct(
        protected GroupRepositoryInterface $groupRepository,
        protected DocumentRepositoryInterface $documentRepository
    ) {
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, string $groupId): bool
    {
        return $this->isAuthorized($user->id, $groupId);
    }

    public function update(User $user, string $memberId): bool
    {
        $member = $this->documentRepository->findById($memberId);
        $group = $this->groupRepository->findById($member->group_id);
        return $this->isAuthorized($user->id, $group->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, string $groupId): bool
    {
        return $this->isAuthorized($user->id, $groupId);
    }
}
