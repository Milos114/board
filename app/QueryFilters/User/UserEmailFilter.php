<?php

namespace App\QueryFilters\User;

class UserEmailFilter
{
    public function handle($query, \Closure $next)
    {
        if (request()->has('filter.email')) {
            $query->where('email', 'like', request('filter.email') . '%');
        }

        return $next($query);
    }
}
