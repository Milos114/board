<?php

namespace App\QueryFilters;

class UserFilter
{
    public function handle($query, \Closure $next)
    {
        if (request()->has('filter.user')) {
            $query->where('user_id', request('filter.user'));
        }

        return $next($query);
    }
}
