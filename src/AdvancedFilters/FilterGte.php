<?php

namespace Spatie\QueryBuilder\AdvancedFilters;

use Illuminate\Database\Eloquent\Builder;

/**
 *  Filter used to compare if column value is greater than or equals to given value
 */
class FilterGte extends AdvancedFilter
{
    public function __invoke(Builder $query, $type)
    {
        $query->{$this->getClausuleType($type)}($this->getColumnName(), '>=', $this->getParsedValue($query));
        return;
    }
}
