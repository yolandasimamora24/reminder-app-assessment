<?php

namespace App\Http\Controllers\Admin\Traits;

use Carbon\Carbon;

trait TimezoneTrait
{
    protected function tzQuery(&$query, $column, $key, $value, $col='*')
    {
        $query->select($col)->where(function ($query) use ($column, $key, $value) {
            collect(config('timezone.' . $column))->each(function ($tz, $tz_key) use ($query, $column, $key, $value) {
                $query->orWhere(function ($q) use ($tz, $tz_key, $column, $key, $value) {
                    $dates = json_decode((string) $value, null);
                    $startDate = Carbon::parse($dates->from, $tz['tz'])->setTimezone('UTC');
                    $endDate = Carbon::parse($dates->to, $tz['tz'])->setTimezone('UTC');
                    $q->where($column, '=', $tz_key)->whereBetween($key, [
                        $startDate->format('Y-m-d H:i:s'),
                        $endDate->format('Y-m-d H:i:s'),
                    ]);
                });
            });
        });
    }

    protected function tzEasternQuery(&$query, $key, $value, $col='*')
    {
        $query->select($col)->where(function ($q) use ($key, $value) {
            $dates = json_decode((string) $value, null);
            $startDate = Carbon::parse($dates->from, 'US/Eastern')->setTimezone('UTC');
            $endDate = Carbon::parse($dates->to, 'US/Eastern')->setTimezone('UTC');
            $q->whereBetween($key, [
                $startDate->format('Y-m-d H:i:s'),
                $endDate->format('Y-m-d H:i:s'),
            ]);
        });
    }

    protected function tzFormat($entry, $column, $key, $type)
    {
        $format = $type === 'datetime' ? 'Y-m-d H:i:s' : 'Y-m-d';
        $suffix = $type === 'datetime' ? config('timezone.' . $column . '.' . $entry->$column . '.tz_short') : '';
        return $entry->$key
            ? collect([
                Carbon::parse(strtotime((string) $entry->$key))
                    ->tz(config('timezone.' . $column . '.' . $entry->$column . '.tz'))
                    ->format($format),
                $suffix,
            ])
                ->filter()
                ->join(' ')
            : '';
    }

    protected function tzEasternFormat($entry, $key, $type)
    {
        $format = $type === 'datetime' ? 'Y-m-d H:i:s' : 'Y-m-d';
        $suffix = $type === 'datetime' ? config('timezone.location.New York.tz_short') : '';
        return $entry->$key
            ? collect([
                Carbon::parse(strtotime((string) $entry->$key))
                    ->tz(config('timezone.location.New York.tz'))
                    ->format($format),
                $suffix,
            ])
                ->filter()
                ->join(' ')
            : '';
    }
}
