<?php

namespace App\Services\Auth;

use App\Enums\TypeUserEnum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

abstract class AbstractAuthService
{
    protected function updateRepresentativeOrMember(
        Collection $representative,
        Collection $member,
        string $userId
    ): void
    {
        $payload = ['user_id' => $userId];
        if ($representative->isNotEmpty()) {
            $this->representativeRepository->update($representative->first()->id, $payload);
        }
        if ($member->isNotEmpty()) {
            $this->memberRepository->update($member->first()->id, $payload);
        }
    }

    protected function getTypeUserId(Collection $representative): string
    {
        if ($representative->isNotEmpty()) {
            return $this->getTypeUserIdByName(TypeUserEnum::REPRESENTATIVE);
        }
        return $this->getTypeUserIdByName(TypeUserEnum::VIEWER);
    }

    protected function getTypeUserIdByName(string $typeName): string
    {
        return $this->typeUserRepository->findByFilters(['name' => $typeName])->first()->id;
    }

    protected function createUserWithType(array $data): Model
    {
        $email = Arr::get($data, 'email');
        $representative = $this->representativeRepository->findByFilters(['email' => $email]);
        $member = $this->memberRepository->findByFilters(['email' => $email]);

        $typeUserId = $this->getTypeUserId($representative);

        $user = $this->userRepository->create(array_merge($data, ['type_user_id' => $typeUserId]));
        $this->updateRepresentativeOrMember($representative, $member, $user->id);
        return $user;
    }
}
