<?php

namespace App\Repositories;

use App\Models\TypeGroup;
use App\Repositories\Interfaces\TypeGroupsInterface;

class TypeGroupsRepository extends AbstractRepository implements TypeGroupsInterface
{
    protected TypeGroup $model;

    public function __construct(TypeGroup $model)
    {
        $this->model = $model;
    }
}
