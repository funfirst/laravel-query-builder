<?php

namespace App\Utils\Filters;

trait UsesAdvancedQueryBuilder
{
    /**
     *  Returns all fields that can be used to filter
     *
     *  @return array
     */
    public static function getFilterableAttributes(): array
    {
        return (new self())->getFillable();
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
