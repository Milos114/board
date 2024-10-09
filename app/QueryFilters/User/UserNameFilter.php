<?php

namespace App\QueryFilters\User;

class UserNameFilter
{
    public function handle($query, \Closure $next)
    {
        if (request()->has('filter.name')) {
            $query->where('name', 'like', request('filter.name') . '%');
        }

        return $next($query);
    }
}
