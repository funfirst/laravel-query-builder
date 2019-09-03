<?php

namespace Spatie\QueryBuilder\AdvancedFilters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\FilterGroups\FilterGroup;

class FilterEq implements AdvancedFilter
{
    public $value;
    public $property;

    public function __construct($value, string $property)
    {
        $this->value = $value;
        $this->property = $property;
    }

    public function __invoke(Builder $query, $type)
    {
        if ($type === FilterGroup::TYPE_AND) {
            $query->where($this->property, $this->value);
            return;
        } else {
            $query->orWhere($this->property, $this->value);
            return;
        }
    }
}
