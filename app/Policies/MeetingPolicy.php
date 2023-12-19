<?php
namespace App\Policies;

use App\Models\User;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use App\Repositories\Interfaces\MeetingRepositoryInterface;
use Illuminate\Auth\Access\HandlesAuthorization;

class MeetingPolicy extends AbstractPolicy
{
    use HandlesAuthorization, PolicyTrait;

    public function __construct(
        protected GroupRepositoryInterface $groupRepository,
        protected MeetingRepositoryInterface $meetingRepository
    ) {
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, string $groupId): bool
    {
        return $this->isAuthorized($user->id, $groupId)|| $this->isAdmin();
    }

    public function update(User $user, string $meetingId): bool
    {
        $meeting = $this->meetingRepository->findById($meetingId);
        $group = $this->groupRepository->findById($meeting->group_id);
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
