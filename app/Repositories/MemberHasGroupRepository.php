<?php

namespace App\Repositories;

use App\Models\MemberHasGroup;
use App\Repositories\Interfaces\MemberHasGroupRepositoryInterface;
use App\Repositories\Traits\CRUDTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MemberHasGroupRepository implements MemberHasGroupRepositoryInterface
{
    use CRUDTrait;

    protected MemberHasGroup $model;

    public function __construct(MemberHasGroup $model)
    {
        $this->model = $model;
    }

    public function findByGroupAndMember(string $memberId, string $groupId)
    {
        $memberHasGroup = $this->findByFilters([
            'member_id' => $memberId,
            'group_id'  => $groupId,
        ]);

        if($memberHasGroup->isEmpty()){
            throw new NotFoundHttpException($this->model->getNotFoundMessage());
        }

        return $memberHasGroup->first();
    }
}
