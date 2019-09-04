<?php

namespace Spatie\QueryBuilder\Concerns;

trait UsesAdvancedQueryBuilder
{
    public function getFilterables()
    {
        
    }
    /**
     *  Returns all fields that can be used to filter
     *
     *  @return array
     */
    public function getFilterableFields(): array
    {
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
