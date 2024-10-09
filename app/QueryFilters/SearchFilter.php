<?php

namespace App\QueryFilters;

class SearchFilter
{
    public function handle($query, \Closure $next)
    {
        if (request()->has('filter.search')) {
            $query->whereAny([
                'title',
                'description'
            ], 'LIKE', request('filter.search') . '%');
        }

        return $next($query);
    }
}
