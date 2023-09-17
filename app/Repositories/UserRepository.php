<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Traits\CRUDTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserRepository implements UserRepositoryInterface
{
    use CRUDTrait;

    protected User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function listWithTypeUsers(): Collection
    {
        return $this->model->with('typeUser')->get();
    }

    public function findWithTypeUser(string $id): Model
    {
        $model = $this->model->with('typeUser')->find($id);

        if (!$model) {
            throw new NotFoundHttpException($this->model->getNotFoundMessage());
        }

        return $model;
    }
}
