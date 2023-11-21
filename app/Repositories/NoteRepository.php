<?php

namespace App\Repositories;

use App\Models\Note;
use App\Repositories\Interfaces\NoteRepositoryInterface;
use App\Repositories\Traits\CRUDTrait;

class NoteRepository implements NoteRepositoryInterface
{
    use CRUDTrait;

    protected Note $model;

    public function __construct(Note $model)
    {
        $this->model = $model;
    }
}
