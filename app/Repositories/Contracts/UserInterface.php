<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface UserInterface
{
    public function findAll(int $perPage, array $params, array $columns = ['*']): LengthAwarePaginator;
    public function create(array $data): bool;
    public function update(string $email, array $data): bool;
    public function find(string $email);
    public function findById(string $id);
    public function delete(string $email);
}