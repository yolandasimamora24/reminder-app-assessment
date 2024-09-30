<?php

namespace App\Queries\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * Interface for user queries.
 */
interface UserQuery
{
    public function search(array $params): Builder;
    public function childrenQuery(): Builder;
}