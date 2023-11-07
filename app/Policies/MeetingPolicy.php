<?php
namespace App\Policies;

use App\Models\User;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use Illuminate\Auth\Access\HandlesAuthorization;

class MeetingPolicy extends AbstractPolicy
{
    use HandlesAuthorization;

    public function __construct(
        protected GroupRepositoryInterface $groupRepository,
        protected MemberRepositoryInterface $memberRepository
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
        $member = $this->memberRepository->findById($memberId);
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
