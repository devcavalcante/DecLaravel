<?php

namespace App\Services;

use App\Enums\TypeUserEnum;
use App\Exceptions\OnlyRepresentativesException;
use App\Repositories\Interfaces\GroupHasRepresentativeRepositoryInterface;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use App\Repositories\Interfaces\TypeGroupRepositoryInterface;
use App\Repositories\Interfaces\TypeUserRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class GroupService
{
    public function __construct(
        protected GroupRepositoryInterface $groupRepository,
        protected GroupHasRepresentativeRepositoryInterface $groupHasRepresentativeRepository,
        protected UserRepositoryInterface $userRepository,
        protected TypeGroupRepositoryInterface $typeGroupRepository,
        protected TypeUserRepositoryInterface $typeUserRepository,
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

            $data['creator_user_id'] = Auth::id();
            $data['type_group_id'] = $typeGroup->id;
            $group = $this->groupRepository->create(Arr::except($data, ['name', 'type_group']));

            $representatives = Arr::get($data, 'representatives');
            $this->createGroupHasRepresentatives($representatives, $group->id);

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

            $group = $this->groupRepository->update($groupId, $data);

            if (!empty($typeGroup)) {
                $this->editTypeGroup($group->typeGroup->id, $typeGroup);
            }

            $representatives = Arr::get($data, 'representatives');
            if (!empty($representatives)) {
                $this->updateGroupHasRepresentatives($representatives, $groupId);
            }
            DB::commit();
            return $group->load('representatives');
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
            $groupRepresentatives = $this->groupHasRepresentativeRepository->findByFilters([
                'group_id' => $group->id
            ])->toArray();
            $typeUser = $this->typeUserRepository->findByFilters(['name' => TypeUserEnum::VIEWER])->first();
            $typeGroupId = $group->typeGroup->id;

            foreach ($groupRepresentatives as $groupRepresentative) {
                $this->groupHasRepresentativeRepository->delete($groupRepresentative['id']);
            }

            $this->setTypeUser($typeUser->id, $groupRepresentatives, null);
            $this->groupRepository->delete($id);
            $this->typeGroupRepository->delete($typeGroupId);
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
    }

    /**
     * @throws OnlyRepresentativesException
     */
    private function createGroupHasRepresentatives(array $representatives, string $groupId): void
    {
        $typeUser = $this->typeUserRepository->findByFilters(['name' => TypeUserEnum::REPRESENTATIVE])->first();

        foreach ($representatives as $representative) {
            if ($this->checkIfUserIsManager($representative)) {
                throw new OnlyRepresentativesException();
            }
            $data = [
                'user_id'  => $representative,
                'group_id' => $groupId,
            ];
            $this->userRepository->update($representative, ['type_user_id' => $typeUser->id]);
            $this->groupHasRepresentativeRepository->create($data);
        }
    }

    /**
     * @throws OnlyRepresentativesException
     */
    private function updateGroupHasRepresentatives(array $representatives, string $groupId): void
    {
        $isManager = array_filter($representatives, function ($representative) {
            return !$this->checkIfUserIsManager($representative);
        });

        if (!empty($isManager)) {
            throw new OnlyRepresentativesException();
        }

        $typeUserRepresentative = $this->typeUserRepository->findByFilters([
            'name' => TypeUserEnum::REPRESENTATIVE
        ])->first();
        $typeUserViewer = $this->typeUserRepository->findByFilters(['name' => TypeUserEnum::VIEWER])->first();

        $this->setTypeUser($typeUserRepresentative->id, null, $representatives);
        $group = $this->groupRepository->findById($groupId);
        $groupRepresentatives = $group->representatives();

        $this->setTypeUser($typeUserViewer->id, $groupRepresentatives->toArray(), null);
        $groupRepresentatives->sync($representatives);
        $group->refresh();
    }

    private function createTypeGroup(array $data): Model
    {
        return $this->typeGroupRepository->create($data);
    }

    private function editTypeGroup(string $typeGrupId, array $data): void
    {
        $this->typeGroupRepository->update($typeGrupId, $data);
    }

    private function setTypeUser(string $typeUserId, ?array $data, ?array $values): void
    {
        if(!is_null($data)) {
            $ids = array_column($data, 'id');
            $this->userRepository->updateWhereIn($ids, ['type_user_id' => $typeUserId]);
            return;
        }
        $this->userRepository->updateWhereIn($values, ['type_user_id' => $typeUserId]);
    }

    private function checkIfUserIsManager(string $id): bool
    {
        $user = $this->userRepository->findById($id);
        return $user->role() == TypeUserEnum::MANAGER;
    }
}
