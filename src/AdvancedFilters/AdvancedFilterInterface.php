<?php

namespace Spatie\QueryBuilder\AdvancedFilters;

use Illuminate\Database\Eloquent\Builder;

interface AdvancedFilterInterface
{
    public function __construct($value, string $property, $baseModel, $comparisonType);

    public function __invoke(Builder $query, $type);

    public function getValue();

    public function getProperty();
}
