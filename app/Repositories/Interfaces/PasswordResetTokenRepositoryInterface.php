<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface PasswordResetTokenRepositoryInterface
{
    public function create(array $data): Model;
    public function deletePasswordResetByToken(array $data): void;
}
