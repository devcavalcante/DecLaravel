<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface MeetingRepositoryInterface
{
    public function create(array $data): Model;
    public function listAll(): Collection;
    public function findById(string $id): Model;
    public function update(string $id, array $data): Model;
    public function delete(string $id): Model;
}
