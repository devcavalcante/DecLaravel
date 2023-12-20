<?php
namespace App\Policies;

use App\Models\User;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use App\Repositories\Interfaces\MeetingRepositoryInterface;
use Illuminate\Auth\Access\HandlesAuthorization;

class MeetingPolicy extends AbstractPolicy
{
    use HandlesAuthorization;

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
        return $this->isRepresentativeOfGroup($user->id, $groupId);
    }

    public function update(User $user, string $meetingId): bool
    {
        $meeting = $this->meetingRepository->findById($meetingId);
        return $this->isRepresentativeOfGroup($user->id, $meeting->group_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, string $groupId): bool
    {
        return $this->isRepresentativeOfGroup($user->id, $groupId);
    }
}
