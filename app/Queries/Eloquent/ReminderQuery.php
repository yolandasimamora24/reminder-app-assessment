<?php

namespace App\Queries\Eloquent;

use App\Models\Reminder;
use Illuminate\Database\Eloquent\Builder;
use App\Queries\Contracts\UserQuery as Query;

/**
 * Search Reminder
 */
class ReminderQuery implements Query
{
    /**
     * Search for Reminder with parameters
     *
     * @param array $params
     * @return Builder $query
     */
    public function search($params): Builder
    {
        $query = Reminder::query();

        // Search query string
        if (isset($params['keyword'])) {
            $query = $query->where(function ($q) use ($params) {
                $keyword = '%' . $params['keyword'] . '%';
                return $q->where('user_id', 'like', $keyword)
                    ->orWhere('provider_id', 'like', $keyword)
                    ->orWhere('status', 'like', $keyword)
                    ->orWhere('reminder_date', 'like', $keyword)
                    ->orWhere('type', 'like', $keyword);
            });
        }

        $query = $query->orderBy('user_id', 'asc')
                    ->orderBy('provider_id', 'asc')
                    ->orderBy('status', 'asc')
                    ->orderBy('reminder_date', 'asc')
                    ->orderBy('type', 'asc');

        return $query;
    }


    /**
     * Search for a single record with children
     *
     * @return Builder $query
     */
    public function childrenQuery(): Builder
    {
        $query = Reminder::query();

        $query =  $query->with(['children' => function ($q) {
            $q->orderBy('position', 'asc');
        }]);

        return $query;
    }
}