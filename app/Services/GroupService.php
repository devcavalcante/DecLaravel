<?php

namespace App\Services;

use App\Enums\TypeUserEnum;
use App\Exceptions\OnlyRepresentativesException;
use App\Repositories\Interfaces\GroupHasRepresentativeRepositoryInterface;
use App\Repositories\Interfaces\GroupRepositoryInterface;
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
            $data['creator_user_id'] = Auth::id();
            $representatives = Arr::get($data, 'representatives');
            $group = $this->groupRepository->create($data);
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
            $representatives = Arr::get($data, 'representatives');
            $group = $this->groupRepository->update($groupId, $data);
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
            $groupRepresentatives = $this->groupHasRepresentativeRepository->findByFilters(['group_id' => $group->id]);

            foreach ($groupRepresentatives->toArray() as $groupRepresentative) {
                $this->groupHasRepresentativeRepository->delete($groupRepresentative['id']);
            }

            $this->groupRepository->delete($id);
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
        foreach ($representatives as $representative) {
            if (!$this->checkIfIsRepresentative($representative)) {
                throw new OnlyRepresentativesException();
            }
            $data = [
                'user_id'  => $representative,
                'group_id' => $groupId,
            ];
            $this->groupHasRepresentativeRepository->create($data);
        }
    }

    /**
     * @throws OnlyRepresentativesException
     */
    private function updateGroupHasRepresentatives(array $representatives, string $groupId): void
    {
        $isNotRepresentative = array_filter($representatives, function ($representative) {
            return !$this->checkIfIsRepresentative($representative);
        });

        if (!empty($isNotRepresentative)) {
            throw new OnlyRepresentativesException();
        }

        $group = $this->groupRepository->findById($groupId);
        $group->representatives()->sync($representatives);
        $group->refresh();
    }

    private function checkIfIsRepresentative(string $userId): bool
    {
        $user = $this->userRepository->findById($userId);
        return $user->role() == TypeUserEnum::REPRESENTATIVE;
    }
}
