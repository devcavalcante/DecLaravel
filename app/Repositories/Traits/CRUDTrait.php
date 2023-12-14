<?php

namespace App\Repositories\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait CRUDTrait
{
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function listAll(): Collection
    {
        return $this->model->get();
    }

    public function findById(string $id): Model
    {
        $model = $this->model->find($id);

        if (!$model) {
            throw new NotFoundHttpException($this->model->getNotFoundMessage());
        }

        return $model;
    }

    public function update(string $id, array $data): Model
    {
        $register = $this->findById($id);
        $register->fill($data);
        $register->update();

        return $register;
    }

    public function delete(string $id): Model
    {
        $model = $this->findById($id);
        $model->delete();
        return $model;
    }

    public function findByFilters(array $data): Collection
    {
        return $this->model->where($data)->get();
    }

    public function restore(string $id): Model
    {
        $model = $this->model->withTrashed()->find($id);

        if (!$model) {
            throw new NotFoundHttpException($this->model->getNotFoundMessage());
        }

        $model->restore();
        return $model;
    }

    public function updateWhereIn(array $ids, array $data)
    {
        return $this->model->whereIn('id', $ids)->update($data);
    }
}
