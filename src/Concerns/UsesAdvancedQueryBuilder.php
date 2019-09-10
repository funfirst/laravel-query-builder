<?php

namespace Spatie\QueryBuilder\Concerns;

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

    public function applyCustomFilter($query, $filter, $type)
    {
        return false;
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
