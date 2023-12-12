<?php

namespace App\Policies;

trait PolicyTrait
{
    protected function isAuthorized(string $userId, string $groupId): bool
    {
        $group = $this->groupRepository->findById($groupId);
        $representatives = $group->representatives()->get()->toArray();
        $representativeIds = array_column($representatives, 'id');
        $isRepresentativeOfTheGroup = array_filter($representativeIds, function ($representativeId) use ($userId) {
            return $representativeId == $userId;
        });
        return !empty($isRepresentativeOfTheGroup);
    }
}
