<?php

namespace App\Services;

use App\Exceptions\MembersExists;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class MemberService
{
    public function __construct(
        private GroupRepositoryInterface $groupRepository,
        private MemberRepositoryInterface $memberRepository,
    ) {
    }

    public function list(string $groupId)
    {
        $group = $this->groupRepository->findById($groupId);
        return $group->userMember;
    }

    /**
     * @throws MembersExists
     * @throws Throwable
     */
    public function createMany(string $groupId, array $data): Collection
    {
        try {
            DB::beginTransaction();
            $group = $this->groupRepository->findById($groupId);

            foreach ($data as $payload) {
                $this->checkIfGroupExistsUser($payload['user_id'], $groupId);
                $this->memberRepository->create(array_merge($payload, ['group_id' => $groupId]));
            }

            DB::commit();
            return $group->userMember;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
    }

    public function edit(string $id, array $data): Model
    {
        $data = Arr::only($data, ['role', 'phone', 'entry_date', 'departure_date']);
        return $this->memberRepository->update($id, $data);
    }

    /**
     * @throws Throwable
     */
    public function delete(string $groupId, string $memberId): void
    {
        $this->groupRepository->findById($groupId);
        $this->memberRepository->delete($memberId);
    }

    /**
     * @throws MembersExists
     */
    private function checkIfGroupExistsUser(string $userId, string $groupId): void
    {
        $member = $this->memberRepository->findByFilters(['user_id' => $userId, 'group_id' => $groupId]);

        if (!$member->isEmpty()) {
            throw new MembersExists();
        }
    }
}
