<?php

namespace Spatie\QueryBuilder\AdvancedFilters;

use Illuminate\Database\Eloquent\Builder;

/**
 *  Filter used to compare if column value is equals to given value
 */
class FilterEq extends AdvancedFilter
{
    public function __invoke(Builder $query, $type)
    {
        $query->{$this->getClausuleType($type)}($this->property, $this->value);
        return;
    }
}