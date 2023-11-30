<?php

namespace App\Policies;

use App\Models\User;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use App\Repositories\Interfaces\NoteRepositoryInterface;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotePolicy extends AbstractPolicy
{
    use HandlesAuthorization, PolicyTrait;

    public function __construct(
        protected GroupRepositoryInterface $groupRepository,
        protected NoteRepositoryInterface $noteRepository
    ) {
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, string $groupId): bool
    {
        return $this->isAuthorized($user->id, $groupId);
    }

    public function update(User $user, string $activityId): bool
    {
        $activity = $this->noteRepository->findById($activityId);
        $group = $this->groupRepository->findById($activity->group_id);
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
