<?php

namespace Spatie\QueryBuilder\AdvancedFilters;

use Illuminate\Database\Eloquent\Builder;

/**
 *  Filter used to compare if column value is equals to given value
 */
class FilterContains extends AdvancedFilter
{
    public function __invoke(Builder $query, $type): Builder
    {
        $query->{$this->getClausuleType($type)}($this->getColumnName(), 'LIKE', '%' . $this->getParsedValue($query) . '%');
        return $query;
    }
}
