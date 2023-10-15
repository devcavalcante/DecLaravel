<?php

namespace App\Repositories;

use App\Models\Member;
use App\Models\User;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Traits\CRUDTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MemberRepository implements MemberRepositoryInterface
{
    use CRUDTrait;

    protected Member $model;

    public function __construct(Member $model)
    {
        $this->model = $model;
    }
}
