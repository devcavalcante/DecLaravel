<?php

namespace App\Repositories;

use App\Models\Document;
use App\Repositories\Interfaces\DocumentRepositoryInterface;
use App\Repositories\Traits\CRUDTrait;

class DocumentRepository implements DocumentRepositoryInterface
{
    use CRUDTrait;

    protected Document $model;

    public function __construct(Document $model)
    {
        $this->model = $model;
    }
}
