<?php

namespace App\Repositories;

use App\Models\Group;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use App\Repositories\Traits\CRUDTrait;

class GroupRepository implements GroupRepositoryInterface
{
    use CRUDTrait;

    protected Group $model;

    public function __construct(Group $model)
    {
        $this->model = $model;
    }
}
