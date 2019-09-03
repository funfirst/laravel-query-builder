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
}