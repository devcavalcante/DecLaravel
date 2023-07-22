<?php

namespace App\Repositories;

use App\Models\TypeUser;

class TypeUsersRepository extends AbstractRepository implements Interfaces\TypeUsersInterface
{
    protected TypeUser $model;

    public function __construct(TypeUser $model)
    {
        $this->model = $model;
    }
}
