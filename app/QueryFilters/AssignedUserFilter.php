<?php

namespace App\QueryFilters;

class AssignedUserFilter
{
    public function handle($query, \Closure $next)
    {
        if (request()->has('filter.assigned_user')) {
            $query->where('assigned_user_id', request('filter.assigned_user'));
        }

        return $next($query);
    }
}