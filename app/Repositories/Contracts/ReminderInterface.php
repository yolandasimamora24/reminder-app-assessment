<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface ReminderInterface
{
    public function findAll(int $perPage, array $params, array $columns = ['*']): LengthAwarePaginator;
    public function create(array $data): bool;
    public function update(int $id, array $data): bool;
    public function find(int $id);
    public function delete(int $id);
}