<?php

namespace Spatie\QueryBuilder\AdvancedFilters;

abstract class AdvancedFilter implements AdvancedFilterInterface
{
    protected $value;
    protected $property;
    protected $baseModel;
    protected $comparisonType;

    protected $filterTypes = [
        'STRING' => [
            'IS', // Uses EQ
            'IS_NOT', // USES NEQ
            'STARTS_WITH', 
            'ENDS_WITH',
            'CONTAINS', 
            'DOES_NOT_CONTAIN',
            'HAS_ANY_VALUE',
            'IS_UNKNOWN',
        ],
        'NUMERIC' => [
            'EQUAL', // EQUAL
            'NOT_EQUAL', // NOT EQUAL
            'GREATER_THAN', // Greater than
            'GREATER_THAN_OR_EQUAL', // Greater than or equals
            'LESS_THAN', // Less than
            'LESS_THAN_OR_EQUAL', // Less than or EQUALS
        ],
        'DATE' => [
            'AFTER', // Greater than
            'AFTER_INCLUDED', // Greater than or EQUALS
            'ON', // Equal
            'NOT_ON', // NOT EQUAL
            'BEFORE', // Less than
            'BEFORE_INCLUDED', // Less than or EQUALS
            'IS_UNKNOWN', 
            'HAS_ANY_VALUE',
            'MORE_THAN', 
            'EXACTLY',
            'LESS_THAN',
        ]
    ];

    public function __construct($value, string $property, $baseModel, $comparisonType)
    {
        $this->value = $value;
        $this->property = $property;
        $this->baseModel = $baseModel;
        $this->comparisonType = $comparisonType;
    }

    /**
     *  Returns Value string that is set inside Filter
     *
     *  @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     *  Returns Property string that is set inside Filter
     *
     *  @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     *  Get Comparison Type
     * 
     *  @return string
     */
    public function getComparisonType()
    {
        return $this->comparisonType;
    }

    /**
     *  Returns clausule as string based on Filter type -> where/orWhere
     *
     *  @return string
     */
    public function getClausuleType($type)
    {
        return $type === 'AND' ? 'where' : 'orWhere';
    }

    /**
     *  Returns column name that should be filtered
     *
     *  @return string
     */
    public function getColumnName()
    {
        $exploded = explode('.', $this->property);
        return end($exploded);
    }

    /**
     *  Returns parsed value based on property type
     * 
     *  @return mixed
     */
    public function getParsedValue($query)
    {
        $propertyDataTypes = $query->getModel()->getFilterableFieldTypes();

        if (array_key_exists($this->getColumnName(), $propertyDataTypes)) {
            switch ($propertyDataTypes[$this->getColumnName()]) {
                case 'DATE':
                    try {
                        $date = \Carbon\Carbon::parse($this->value);
                        return $date;
                    } catch(\Exception $e) {
                        return $this->value;
                    }
                case 'INT':
                    return (int)$this->value;
                case 'STRING':
                    return "{$this->value}";
            }
        }
        return $this->value;
    }

    /**
     *  Set Value
     * 
     *  @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     *  Set Property
     * 
     *  @return void
     */
    public function setProperty($property)
    {
        $this->property = $property;
    }
}
