<?php

namespace Spatie\QueryBuilder\AdvancedFilters;

abstract class AdvancedFilter implements AdvancedFilterInterface
{
    protected $value;
    protected $property;

    public function __construct($value, string $property)
    {
        $this->value = $value;
        $this->property = $property;
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
                    return \Carbon\Carbon::parse($this->value);
            }
        }
        return $this->value;
    }
}
