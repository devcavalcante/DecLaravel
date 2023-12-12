<?php

namespace App\Repositories;

use App\Models\Activity;
use App\Repositories\Interfaces\ActivityRepositoryInterface;
use App\Repositories\Traits\CRUDTrait;

class ActivityRepository implements ActivityRepositoryInterface
{
    use CRUDTrait;

    protected Activity $model;

    public function __construct(Activity $model)
    {
        $this->model = $model;
    }
}
