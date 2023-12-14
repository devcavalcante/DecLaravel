<?php

namespace App\Services;

use App\Enums\TypeUserEnum;
use App\Exceptions\EmailExists;
use App\Repositories\Interfaces\TypeUserRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Throwable;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected TypeUserRepositoryInterface $typeUserRepository
    ) {
    }

    public function findMany(array $filters = []): Collection
    {
        if (!empty($filters)) {
            return $this->userRepository->findByFilters($filters);
        }

        return $this->userRepository->listAll();
    }

    /**
     * @throws Throwable
     */
    public function update(array $data, string $id): Model
    {
        $email = Arr::get($data, 'email');
        $user = $this->userRepository->findByFilters(['email' => $email])->first();

        if ($user && $user->email != $email) {
            throw new EmailExists();
        }

        return $this->userRepository->update($id, $data);
    }

    public function updateTypeUser(string $id): Model
    {
        $typeUser = $this->typeUserRepository->findByFilters(['name' => TypeUserEnum::MANAGER])->first();

        return $this->userRepository->update($id, ['type_user_id' => $typeUser->id]);
    }
}
