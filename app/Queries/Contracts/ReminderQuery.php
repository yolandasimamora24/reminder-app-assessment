<?php

namespace App\Queries\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * Interface for address queries.
 */
interface ReminderQuery
{
    public function search(array $params): Builder;
    public function childrenQuery(): Builder;
}