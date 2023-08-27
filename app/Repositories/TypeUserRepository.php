<?php

namespace App\Repositories;

use App\Models\TypeUser;
use App\Repositories\Interfaces\TypeUserRepositoryInterface;
use App\Repositories\Traits\CRUDTrait;

class TypeUserRepository implements TypeUserRepositoryInterface
{
    use CRUDTrait;

    protected TypeUser $model;

    public function __construct(TypeUser $model)
    {
        $this->model = $model;
    }
}
