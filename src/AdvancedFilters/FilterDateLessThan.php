<?php

namespace Spatie\QueryBuilder\AdvancedFilters;

use Illuminate\Database\Eloquent\Builder;

/**
 *  Filter used to compare if column value is equals to given value
 */
class FilterDateLessThan extends AdvancedFilter
{
    public function __invoke(Builder $query, $type)
    {
        $days = (int)$this->getParsedValue($query);
        $date = \Carbon\Carbon::parse("+{$days} days")->setTime(23, 59, 59);

        $query->{$this->getClausuleType($type)}($this->getColumnName(), '<', $date);
        return;
    }
}
