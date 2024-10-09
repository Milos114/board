<?php

namespace App\QueryFilters;

class StatusFilter
{
    public function handle($query, \Closure $next)
    {
        if (request()->has('filter.state')) {
            $query->where('lane_id', request('filter.state'));
        }

        return $next($query);
    }
}
