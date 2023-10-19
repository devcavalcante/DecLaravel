<?php

namespace App\Repositories;

use App\Models\GroupHasRepresentative;
use App\Repositories\Interfaces\GroupHasRepresentativeRepositoryInterface;
use App\Repositories\Traits\CRUDTrait;

class GroupHasRepresentativeRepository implements GroupHasRepresentativeRepositoryInterface
{
    use CRUDTrait;

    protected GroupHasRepresentative $model;

    public function __construct(GroupHasRepresentative $model)
    {
        $this->model = $model;
    }
}
