<?php

namespace App\Services;

use App\Enums\TypeUserEnum;
use App\Exceptions\OnlyRepresentativesException;
use App\Mail\GroupEntry;
use App\Mail\RegisterEmail;
use App\Repositories\Interfaces\MemberHasGroupRepositoryInterface;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use App\Repositories\Interfaces\RepresentativeRepositoryInterface;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use App\Repositories\Interfaces\TypeGroupRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class GroupService
{
    public function __construct(
        protected GroupRepositoryInterface $groupRepository,
        protected RepresentativeRepositoryInterface $representativeRepository,
        protected UserRepositoryInterface $userRepository,
        protected TypeGroupRepositoryInterface $typeGroupRepository,
        protected MemberHasGroupRepositoryInterface $memberHasGroupRepository,
        protected MemberRepositoryInterface $memberRepository,
    ) {
    }

    public function findMany(array $filters = []): Collection
    {
        if (!empty($filters)) {
            return $this->groupRepository->findByFilters($filters);
        }

        return $this->groupRepository->listAll();
    }

    /**
     * @throws Throwable
     */
    public function create(array $data): Model
    {
        try {
            DB::beginTransaction();

            $payloadTypeGroup = Arr::only($data, ['name', 'type_group']);
            $typeGroup = $this->createTypeGroup($payloadTypeGroup);
            $payloadGroup = Arr::except($data, ['name', 'type_group']);

            $representative = Arr::get($data, 'representative');
            $representative = $this->createGroupHasRepresentative($representative);

            $payloadGroup = array_merge($payloadGroup, [
                'representative_id' => $representative->id,
                'creator_user_id'   => Auth::id(),
                'type_group_id'     => $typeGroup->id,
            ]);
            $group = $this->groupRepository->create($payloadGroup);

            DB::commit();
            return $group;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
    }

    /**
     * @throws Throwable
     */
    public function edit(string $groupId, array $data): Model
    {
        try {
            DB::beginTransaction();
            $typeGroup = Arr::only($data, ['name', 'type_group']);

            $representative = Arr::get($data, 'representative');
            if (!empty($representative)) {
                $representative = $this->updateGroupHasRepresentative($representative, $groupId);
                $data['representative_id'] = $representative->id;
            }

            $group = $this->groupRepository->update($groupId, $data);
            if (!empty($typeGroup)) {
                $this->editTypeGroup($group->typeGroup->id, $typeGroup);
            }

            DB::commit();
            return $group;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
    }

    /**
     * @throws Throwable
     */
    public function delete(string $id): void
    {
        try {
            DB::beginTransaction();
            $group = $this->groupRepository->findById($id);
            $this->deleteMembers($group->members->toArray());
            $group = $this->groupRepository->delete($id);
            $this->typeGroupRepository->delete($group->type_group_id);
            $this->representativeRepository->delete($group->representative_id);
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
    }

    private function deleteMembers(array $members): void
    {
        foreach ($members as $member) {
            $this->memberHasGroupRepository->delete($member['id']);
            $this->memberRepository->delete($member['id']);
        }
    }

    /**
     * @throws OnlyRepresentativesException
     */
    private function createGroupHasRepresentative(string $representative): Model
    {
        $user = $this->userRepository->findByFilters(['email' => $representative]);

        if ($user->isNotEmpty()) {
            $userId = $user->first()->id;

            if (!$this->checkIfIsRepresentative($userId)) {
                throw new OnlyRepresentativesException();
            }

            $data = [
                'email'   => $representative,
                'user_id' => $userId,
            ];

            Mail::to($representative)->send(new GroupEntry(TypeUserEnum::REPRESENTATIVE));
            return $this->representativeRepository->create($data);
        }

        Mail::to($representative)->send(new RegisterEmail(TypeUserEnum::REPRESENTATIVE));
        return $this->representativeRepository->create([
            'email'  => $representative,
        ]);
    }

    /**
     * @throws OnlyRepresentativesException
     */
    private function updateGroupHasRepresentative(string $representative, string $groupId): Model
    {
        $user = $this->userRepository->findByFilters(['email' => $representative]);
        $group = $this->groupRepository->findById($groupId);

        if ($user->isNotEmpty()) {
            $userId = $user->first()->id;

            if (!$this->checkIfIsRepresentative($userId)) {
                throw new OnlyRepresentativesException();
            }

            $data = [
                'email'   => $representative,
                'user_id' => $userId,
            ];

            Mail::to($representative)->send(new GroupEntry(TypeUserEnum::REPRESENTATIVE));
            return $this->representativeRepository->update($group->representative->id, $data);
        }

        Mail::to($representative)->send(new RegisterEmail(TypeUserEnum::REPRESENTATIVE));
        return $this->representativeRepository->update($group->representative->id, [
            'email'   => $representative,
            'user_id' => null,
        ]);
    }

    private function checkIfIsRepresentative(string $userId): bool
    {
        $user = $this->userRepository->findById($userId);
        return $user->role() == TypeUserEnum::REPRESENTATIVE;
    }

    private function createTypeGroup(array $data): Model
    {
        return $this->typeGroupRepository->create($data);
    }

    private function editTypeGroup(string $typeGroupId, array $data): void
    {
        $this->typeGroupRepository->update($typeGroupId, $data);
    }
}
