<?php

namespace App\Repositories;

use App\Models\TypeUser;
use App\Repositories\Interfaces\TypeUsersInterface;

class TypeUsersRepository extends AbstractRepository implements TypeUsersInterface
{
    protected TypeUser $model;

    public function __construct(TypeUser $model)
    {
        $this->model = $model;
    }
}
