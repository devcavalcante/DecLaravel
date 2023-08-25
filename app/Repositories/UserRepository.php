<?php

namespace App\Repositories;

use App\Repositories\Interfaces\UsersInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends AbstractRepository implements UsersInterface
{
    protected User $model;

    public function __construct(User $Model)
    {
        $this->model = $Model;
    }
}
