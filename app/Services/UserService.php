<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
    ) {
    }

    public function findMany(array $filters = []): Collection
    {
        if (!empty($filters)) {
            return $this->userRepository->findByFilters($filters);
        }

        return $this->userRepository->listAll();
    }
}
