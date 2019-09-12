<?php

namespace Spatie\QueryBuilder\AdvancedFilters;

use Illuminate\Database\Eloquent\Builder;

/**
 *  Filter used to compare if column value is equals to given value
 */
class FilterDateMoreThan extends AdvancedFilter
{
    public function __invoke(Builder $query, $type): Builder
    {
        $days = (int)$this->getParsedValue($query);
        $date = \Carbon\Carbon::parse("+{$days} days");

        $query->{$this->getClausuleType($type)}($this->getColumnName(), '>', $date);
        return $query;
    }
}
