<?php

namespace Spatie\QueryBuilder\AdvancedFilters;

use Illuminate\Database\Eloquent\Builder;

/**
 *  This filter is used when unknown comaprision type is used
 */
class FilterUnknown extends AdvancedFilter
{
    public function __invoke(Builder $query, $type)
    {
        return;
    }
}