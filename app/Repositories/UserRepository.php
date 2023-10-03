<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Traits\CRUDTrait;

class UserRepository implements UserRepositoryInterface
{
    use CRUDTrait;

    protected User $model;
}
