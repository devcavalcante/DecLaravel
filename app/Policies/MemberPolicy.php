<?php
namespace App\Policies;

use App\Models\User;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use Illuminate\Auth\Access\HandlesAuthorization;

class MemberPolicy extends AbstractPolicy
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
        return $this->isRepresentativeOfGroup($user->id, $groupId);
    }

    public function update(User $user, string $groupId): bool
    {
        return $this->isRepresentativeOfGroup($user->id, $groupId);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, string $groupId): bool
    {
        return $this->isRepresentativeOfGroup($user->id, $groupId);
    }
}
