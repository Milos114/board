<?php

namespace App\QueryFilters;

class PriorityFilter
{
    public function handle($query, \Closure $next)
    {
        if (request()->has('filter.priority')) {
            $query->where('priority_id', request('filter.priority'));
        }

        return $next($query);
    }
}