<?php
namespace App\Policies;

use App\Enums\TypeUserEnum;
use App\Http\Requests\MemberRequest;
use App\Models\Member;
use App\Models\User;
use App\Repositories\Interfaces\TypeUserRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Auth\Access\HandlesAuthorization;

class MemberPolicy extends AbstractPolicy
{
    use HandlesAuthorization;

    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected TypeUserRepositoryInterface $typeUserRepository
    ){
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
    public function create(User $user, MemberRequest $memberRequest): bool
    {
        return $this->isAuthorized($user->typeUser->id);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, string $id): bool
    {
        $member = Member::find($id);

        if (!$member) {
            return false;
        }

        return $this->isAuthorized($user->typeUser->id, TypeUserEnum::REPRESENTATIVE) && $this->isMemberAssociatedToRepresentative($member);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, string $id): bool
    {
        $member = Member::find($id);

        if (!$member) {
            return false;
        }


        return $this->isAuthorized($user->typeUser->id, TypeUserEnum::REPRESENTATIVE) && $this->isMemberAssociatedToRepresentative($member);
    }

    private function isAuthorized(string $typeUserId): bool
    {
        $isAdmin = $this->isAdmin();
        $isManager = $this->isManager();
        $isRepresentative = $this->isRepresentative();

        return $isAdmin ||
            ($isManager && $typeUserId === TypeUserEnum::MANAGER)
            || ($isRepresentative && $typeUserId === TypeUserEnum::REPRESENTATIVE);
    }
}
