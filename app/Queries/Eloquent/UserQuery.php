<?php

namespace App\Queries\Eloquent;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use App\Queries\Contracts\UserQuery as Query;

/**
 * Search User
 */
class UserQuery implements Query
{
    /**
     * Search for User with parameters
     *
     * @param array $params
     * @return Builder $query
     */
    public function search($params): Builder
    {
        $query = User::query();

        // Search query string
        if (isset($params['keyword'])) {
            $query = $query->where(function ($q) use ($params) {
                $keyword = '%' . $params['keyword'] . '%';
                return $q->where('first_name', 'like', $keyword)
                    ->orWhere('last_name', 'like', $keyword)
                    ->orWhere('middle_name', 'like', $keyword)
                    ->orWhere('email', 'like', $keyword);
            });
        }

        $query = $query->orderBy('first_name', 'asc')
                    ->orderBy('last_name', 'asc')
                    ->orderBy('middle_name', 'asc')
                    ->orderBy('email', 'asc');

        return $query;
    }


    /**
     * Search for a single record with children
     *
     * @return Builder $query
     */
    public function childrenQuery(): Builder
    {
        $query = User::query();

        $query =  $query->with(['children' => function ($q) {
            $q->orderBy('position', 'asc');
        }]);

        return $query;
    }
}