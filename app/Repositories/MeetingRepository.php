<?php

namespace App\Repositories;

use App\Models\Meeting;
use App\Repositories\Interfaces\MeetingRepositoryInterface;
use App\Repositories\Traits\CRUDTrait;

class MeetingRepository implements MeetingRepositoryInterface
{
    use CRUDTrait;

    protected Meeting $model;

    public function __construct(Meeting $model)
    {
        $this->model = $model;
    }
}
