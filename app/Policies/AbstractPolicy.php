<?php

namespace App\Policies;

use App\Enums\TypeUserEnum;
use Illuminate\Support\Facades\Auth;

abstract class AbstractPolicy
{
    protected function isAdmin(): bool
    {
        $authenticatedUser = Auth::user();
        return $authenticatedUser->role() == TypeUserEnum::ADMIN;
    }

    protected function isManager(): bool
    {
        $authenticatedUser = Auth::user();
        return $authenticatedUser->role() == TypeUserEnum::MANAGER;
    }

    protected function isRepresentative(): bool
    {
        $authenticatedUser = Auth::user();
        return $authenticatedUser->role() == TypeUserEnum::REPRESENTATIVE;
    }

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
