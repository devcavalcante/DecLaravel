<?php

namespace App\Repositories;

use App\Models\Representative;
use App\Repositories\Interfaces\RepresentativeRepositoryInterface;
use App\Repositories\Traits\CRUDTrait;

class RepresentativeRepository implements RepresentativeRepositoryInterface
{
    use CRUDTrait;

    protected Representative $model;

    public function __construct(Representative $model)
    {
        $this->model = $model;
    }
}
