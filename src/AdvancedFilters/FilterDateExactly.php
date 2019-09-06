<?php

namespace Spatie\QueryBuilder\AdvancedFilters;

use Illuminate\Database\Eloquent\Builder;

/**
 *  Filter used to compare if column value is equals to given value
 */
class FilterDateExactly extends AdvancedFilter
{
    public function __invoke(Builder $query, $type)
    {
        $days = (int)$this->getParsedValue($query);
        $startOfDay = \Carbon\Carbon::parse("+{$days} days")->setTime(0, 0, 0);
        $endOfDay = \Carbon\Carbon::parse("+{$days} days")->setTime(23, 59, 59);
        
        $query->{$this->getClausuleType($type) . 'Between'}($this->getColumnName(), [$startOfDay, $endOfDay]);
        return;
    }
}
