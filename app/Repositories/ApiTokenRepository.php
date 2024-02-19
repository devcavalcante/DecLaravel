<?php

namespace App\Repositories;

use App\Models\ApiToken;
use App\Repositories\Interfaces\ApiTokenRepositoryInterface;
use App\Repositories\Traits\CRUDTrait;

class ApiTokenRepository implements ApiTokenRepositoryInterface
{
    use CRUDTrait;

    protected ApiToken $model;

    public function __construct(ApiToken $model)
    {
        $this->model = $model;
    }
}
