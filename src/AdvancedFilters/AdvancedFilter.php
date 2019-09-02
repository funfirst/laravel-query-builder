<?php

namespace Spatie\QueryBuilder\AdvancedFilters;

use Illuminate\Database\Eloquent\Builder;

interface AdvancedFilter
{
    public function __construct($value, string $property);

    public function __invoke(Builder $query, $type);
}
