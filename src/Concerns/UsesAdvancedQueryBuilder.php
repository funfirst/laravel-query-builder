<?php

namespace Spatie\QueryBuilder\Concerns;

use Spatie\QueryBuilder\AdvancedFilters\AdvancedFilterInterface;
use Illuminate\Database\Eloquent\Builder;

trait UsesAdvancedQueryBuilder
{
    /**
     *  Returns all fields that can be used to filter
     *
     *  @return array
     */
    public function getFilterableFields(): array
    {
        if (method_exists($this, 'getCustomFilterableFields')) {
            return $this->getCustomFilterableFields();
        }
        return array_merge($this->getFillable(), ['properties.value']);
    }

    /**
     *  Returns
     */
    public function getFilterableFieldTypes()
    {
        if (property_exists($this, 'filterableFieldTypes') && is_array($this->filterableFieldTypes)) {
            return $this->filterableFieldTypes;
        }
        return [];
    }

    public function getPredefinedScopes()
    {
        if (method_exists($this, 'getCustomPredefinedScopes')) {
            return $this->getCustomPredefinedScopes();
        }

        if (property_exists($this, 'predifinedScopes') && is_array($this->predifinedScopes)) {
            return $this->predifinedScopes;
        }
        return [];
    }

    /**
     *  Can be overrided to apply custom filter logic
     * 
     *  @param \Illuminate\Database\Eloquent\Builder $query
     *  @param \Spatie\QueryBuilder\AdvancedFilters\AdvancedFilterInterface $filter
     *  @param string $type
     *  @return mixed -> Can return boolean or return query
     */
    public function applyCustomFilter(Builder $query, AdvancedFilterInterface $filter, $type)
    {
        return false;
    }

    /**
     *  Applies Filter on given query with possibility to override property and value properties on Filter instance
     * 
     *  @param \Illuminate\Database\Eloquent\Builder $query
     *  @param \Spatie\QueryBuilder\AdvancedFilters\AdvancedFilterInterface $filter
     *  @param string $type
     *  @param mixed $property
     *  @param mixed $value
     *  @return \Illuminate\Database\Eloquent\Builder
     */
    public function applyFilter(Builder $query, AdvancedFilterInterface $filter, $type = null, $property = null, $value = null)
    {
        if ($property) {
            $filter->setProperty($property);
        }
        
        if ($value) {
            $filter->setValue($value);
        }

        if ($type === null) {
            $type = 'AND';
        }

        $filter($query, $type);
    }

    // (AGE == 100 || AGE < 10) && (GENDER == MALE && NAME contains John) && (TYPE == PERSON)

    // $filter = [
    //     'values' => [
    //         0 => [
    //             'type' => 'OR',
    //             'values' => [
    //                 0 => [
    //                     'field' => 'custom_fields.age',
    //                     'comparison' => 'eq',
    //                     'value' => '10',
    //                 ],
    //                 1 => [
    //                     'field' => 'custom_fields.age',
    //                     'comparison' => 'lt',
    //                     'value' => '10',
    //                 ],
    //             ]
    //         ],
    //         1 => [
    //             'type' => 'AND',
    //             'values' => [
    //                 0 => [
    //                     'field' => 'custom_fields.gender',
    //                     'comparison' => 'eq',
    //                     'value' => 'MALE',
    //                 ],
    //                 1 => [
    //                     'field' => 'custom_fields.name',
    //                     'comparison' => 'contains',
    //                     'value' => 'John',
    //                 ],
    //             ]
    //         ],
    //         2 => [
    //             'type' => 'AND',
    //             'values' => [
    //                 0 => [
    //                     'field' => 'type',
    //                     'comparison' => 'eq',
    //                     'value' => 'PERSON',
    //                 ],
    //             ]
    //         ]
    //     ],
    //     'type' => 'AND',
    // ];
}
