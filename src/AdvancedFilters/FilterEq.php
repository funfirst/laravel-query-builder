<?php

namespace Spatie\QueryBuilder\AdvancedFilters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\FilterGroups\FilterGroup;

class FilterEq implements AdvancedFilter
{
    public $value;
    public $property;
    public $relationConstraints = [];

    public function __construct($value, string $property)
    {
        $this->value = $value;
        $this->property = $property;
    }

    public function __invoke(Builder $query, $type)
    {
        // if ($this->isRelationProperty($query, $this->property)) {
        //     $relationship = explode('.', $this->property)[0];
        //     $property = explode('.', $this->property)[1];

        //     if ($type === FilterGroup::TYPE_AND) {
        //         $query->whereHas($relationship, function($q) use ($property) {
        //             $q->where($property, $this->value);
        //         });
        //         return;
        //     } else {
        //         $query->orWhereHas($relationship, function($q) use ($property) {
        //             $q->where($property, $this->value);
        //         });
        //         return;
        //     }
        // }

        if ($type === FilterGroup::TYPE_AND) {
            $query->where($this->property, $this->value);
            return;
        } else {
            $query->orWhere($this->property, $this->value);
            return;
        }
    }

    protected function isRelationProperty(Builder $query, string $property) : bool
    {
        if (!Str::contains($property, '.')) {
            return false;
        }

        if (in_array($property, $this->relationConstraints)) {
            return false;
        }

        if (Str::startsWith($property, $query->getModel()->getTable() . '.')) {
            return false;
        }

        return true;
    }
}
