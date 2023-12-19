<?php

namespace App\Policies;

use App\Models\User;
use App\Repositories\Interfaces\ActivityRepositoryInterface;
use App\Repositories\Interfaces\DocumentRepositoryInterface;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActivityPolicy extends AbstractPolicy
{
    use HandlesAuthorization, PolicyTrait;

    public function __construct(
        protected GroupRepositoryInterface $groupRepository,
        protected ActivityRepositoryInterface $activityRepository
    ) {
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, string $groupId): bool
    {
        return $this->isAuthorized($user->id, $groupId)|| $this->isAdmin();
    }

    public function update(User $user, string $activityId): bool
    {
        $activity = $this->activityRepository->findById($activityId);
        $group = $this->groupRepository->findById($activity->group_id);
        return $this->isAuthorized($user->id, $group->id)|| $this->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, string $groupId): bool
    {
        return $this->isAuthorized($user->id, $groupId)|| $this->isAdmin();
    }
}
