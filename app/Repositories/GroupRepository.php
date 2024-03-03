<?php

namespace App\Repositories;

use App\Models\Group;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use App\Repositories\Traits\CRUDTrait;
use Carbon\Carbon;

class GroupRepository implements GroupRepositoryInterface
{
    use CRUDTrait;

    protected Group $model;

    public function __construct(Group $model)
    {
        $this->model = $model;
    }

    public function findWhereBetween(string $startDate, string $endDate)
    {
        list($startDate, $endDate) = $this->formatDates($startDate, $endDate);
        return $this->model->whereBetween('created_at', [$startDate, $endDate])->get();
    }

    public function findWhereBetweenWithFilters(string $startDate, string $endDate, string $status)
    {
        list($startDate, $endDate) = $this->formatDates($startDate, $endDate);
        return $this->model->where(['status' => $status])->whereBetween('created_at', [$startDate, $endDate])->get();
    }

    private function formatDates(string $startDate, string $endDate): array
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();
        return [$startDate, $endDate];
    }
}
