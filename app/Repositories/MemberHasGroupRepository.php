<?php

namespace App\Repositories;

use App\Models\MemberHasGroup;
use App\Repositories\Interfaces\MemberHasGroupRepositoryInterface;
use App\Repositories\Traits\CRUDTrait;

class MemberHasGroupRepository implements MemberHasGroupRepositoryInterface
{
    use CRUDTrait;

    protected MemberHasGroup $model;

    public function __construct(MemberHasGroup $model)
    {
        $this->model = $model;
    }
}
