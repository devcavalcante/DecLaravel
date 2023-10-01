<?php

namespace App\Repositories;

use App\Models\PasswordResetToken;
use App\Repositories\Interfaces\PasswordResetTokenRepositoryInterface;
use App\Repositories\Traits\CRUDTrait;

class PasswordResetTokenRepository implements PasswordResetTokenRepositoryInterface
{
    use CRUDTrait;

    protected PasswordResetToken $model;

    public function __construct(PasswordResetToken $model)
    {
        $this->model = $model;
    }

    public function deletePasswordResetByToken(array $data): void
    {
        $this->model->where($data)->delete();
    }
}
