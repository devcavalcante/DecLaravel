<?php

namespace App\Repositories;

use App\Models\TypeGroup;
use App\Repositories\Interfaces\TypeGroupRepositoryInterface;
use App\Repositories\Traits\CRUDTrait;

class TypeGroupRepository implements TypeGroupRepositoryInterface
{
    use CRUDTrait;

    protected TypeGroup $model;

    public function __construct(TypeGroup $model)
    {
        $this->model = $model;
    }
}
