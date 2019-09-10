<?php

namespace Spatie\QueryBuilder\Concerns;

use Illuminate\Support\Arr;
use Spatie\QueryBuilder\AdvancedFilters\FilterContains;
use Spatie\QueryBuilder\AdvancedFilters\FilterDateExactly;
use Spatie\QueryBuilder\AdvancedFilters\FilterDateLessThan;
use Spatie\QueryBuilder\AdvancedFilters\FilterDateMoreThan;
use Spatie\QueryBuilder\AdvancedFilters\FilterDoesNotContain;
use Spatie\QueryBuilder\AdvancedFilters\FilterEndsWith;
use Spatie\QueryBuilder\AdvancedFilters\FilterEqual;
use Spatie\QueryBuilder\AdvancedFilters\FilterGreaterThan;
use Spatie\QueryBuilder\AdvancedFilters\FilterGreaterThanOrEqual;
use Spatie\QueryBuilder\AdvancedFilters\FilterHasAnyValue;
use Spatie\QueryBuilder\AdvancedFilters\FilterIsUnknown;
use Spatie\QueryBuilder\AdvancedFilters\FilterLessThan;
use Spatie\QueryBuilder\AdvancedFilters\FilterLessThanOrEqual;
use Spatie\QueryBuilder\AdvancedFilters\FilterNotEqual;
use Spatie\QueryBuilder\AdvancedFilters\FilterStartsWith;
use Spatie\QueryBuilder\AdvancedFilters\FilterUnknown;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Exceptions\InvalidFilterQuery;
use Spatie\QueryBuilder\FilterGroups\FilterGroup;

trait FiltersQuery
{
    /** @var \Illuminate\Support\Collection */
    protected $allowedFilters;
    protected $advancedFiltersToApply = [];

    public function allowedFilters($filters): self
    {
        $filters = is_array($filters) ? $filters : func_get_args();

        $this->allowedFilters = collect($filters)->map(function ($filter) {
            if ($filter instanceof AllowedFilter) {
                return $filter;
            }

            return AllowedFilter::partial($filter);
        });

        $this->ensureAllFiltersExist();

        $this->addFiltersToQuery();

        return $this;
    }

    protected function addFiltersToQuery()
    {
        $this->allowedFilters->each(function (AllowedFilter $filter) {
            if ($this->isFilterRequested($filter)) {
                $value = $this->request->filters()->get($filter->getName());
                $filter->filter($this, $value);

                return;
            }

            if ($filter->hasDefault()) {
                $filter->filter($this, $filter->getDefault());

                return;
            }
        });
    }

    protected function findFilter(string $property): ?AllowedFilter
    {
        return $this->allowedFilters
            ->first(function (AllowedFilter $filter) use ($property) {
                return $filter->isForFilter($property);
            });
    }

    protected function isFilterRequested(AllowedFilter $allowedFilter): bool
    {
        return $this->request->filters()->has($allowedFilter->getName());
    }

    protected function ensureAllFiltersExist()
    {
        $filterNames = $this->request->filters()->keys();

        $allowedFilterNames = $this->allowedFilters->map(function (AllowedFilter $allowedFilter) {
            return $allowedFilter->getName();
        });

        $diff = $filterNames->diff($allowedFilterNames);

        if ($diff->count()) {
            throw InvalidFilterQuery::filtersNotAllowed($diff, $allowedFilterNames);
        }
    }

    // Automatically check get available Filters -> Model fillables, properties, custom fields
    // Add filters from request to query if filter is from available filter
    public function filter($allowedFilters = [])
    {
        if ($this->usesAdvancedQueryBuilder()) {
            return $this->applyAdvancedFilter($allowedFilters);
        } else {
            return $this->allowedFilters($allowedFilters);
        }
    }

    protected function applyAdvancedFilter($allowedFilters)
    {
        // $filters = $this->request->filters()->toArray();
        $filters = $this->request->filter; //FIXME: TEMP FOR TESTING
        if ($allowedFilters == null) {
            $allowedFilters = $this->getModel()->getFilterableFields();
        } else {
            $allowedFilters = is_array($allowedFilters) ? $allowedFilters : func_get_args();
        }

        $filterGroup = $this->createFilterGroup($filters, $allowedFilters);
        $filterGroup->filter($this);
        return $this;
    }

    public function createFilterGroup($filters, $allowedFilters = [])
    {
        $filterGroup = new FilterGroup($filters['type']);

        if (array_key_exists('type', $filters['values'][0])) {
            foreach ($filters['values'] as $filterValue) {
                $childGroup = $this->createFilterGroup($filterValue, $allowedFilters);
                $filterGroup->addGroup($childGroup);
            }
        } else {
            foreach ($filters['values'] as $filter) {
                if (!in_array($filter['field'], $allowedFilters)) {
                    continue;
                }
                switch ($filter['comparison']) {
                    case 'EQUAL':
                        $filter = new FilterEqual($filter['value'], $filter['field'], $this->model, $filter['comparison']);
                        break;
                    case 'IS':
                        $filter = new FilterEqual($filter['value'], $filter['field'], $this->model, $filter['comparison']);
                        break;
                    case 'ON':
                        $filter = new FilterEqual($filter['value'], $filter['field'], $this->model, $filter['comparison']);
                        break;
                        
                    case 'NOT_EQUAL':
                        $filter = new FilterNotEqual($filter['value'], $filter['field'], $this->model, $filter['comparison']);
                        break;
                    case 'IS_NOT':
                        $filter = new FilterNotEqual($filter['value'], $filter['field'], $this->model, $filter['comparison']);
                        break;
                    case 'NOT_ON':
                        $filter = new FilterNotEqual($filter['value'], $filter['field'], $this->model, $filter['comparison']);
                        break;

                    case 'GREATER_THAN':
                        $filter = new FilterGreaterThan($filter['value'], $filter['field'], $this->model, $filter['comparison']);
                        break;
                    case 'AFTER':
                        $filter = new FilterGreaterThan($filter['value'], $filter['field'], $this->model, $filter['comparison']);
                        break;
                    
                    case 'GREATER_THAN_OR_EQUAL':
                        $filter = new FilterGreaterThanOrEqual($filter['value'], $filter['field'], $this->model, $filter['comparison']);
                        break;
                    case 'AFTER_INCLUDED':
                        $filter = new FilterGreaterThanOrEqual($filter['value'], $filter['field'], $this->model, $filter['comparison']);
                        break;

                    case 'LESS_THAN':
                        $filter = new FilterLessThan($filter['value'], $filter['field'], $this->model, $filter['comparison']);
                        break;
                    case 'BEFORE':
                        $filter = new FilterLessThan($filter['value'], $filter['field'], $this->model, $filter['comparison']);
                        break;

                    
                    case 'LESS_THAN_OR_EQUAL':
                        $filter = new FilterLessThanOrEqual($filter['value'], $filter['field'], $this->model, $filter['comparison']);
                        break;
                    case 'BEFORE_INCLUDED':
                        $filter = new FilterLessThanOrEqual($filter['value'], $filter['field'], $this->model, $filter['comparison']);
                        break;

                    case 'STARTS_WITH':
                        $filter = new FilterStartsWith($filter['value'], $filter['field'], $this->model, $filter['comparison']);
                        break;

                    case 'ENDS_WITH':
                        $filter = new FilterEndsWith($filter['value'], $filter['field'], $this->model, $filter['comparison']);
                        break;

                    case 'CONTAINS':
                        $filter = new FilterContains($filter['value'], $filter['field'], $this->model, $filter['comparison']);
                        break;

                    case 'DOES_NOT_CONTAIN':
                        $filter = new FilterDoesNotContain($filter['value'], $filter['field'], $this->model, $filter['comparison']);
                        break;

                    case 'HAS_ANY_VALUE':
                        $filter = new FilterHasAnyValue($filter['value'], $filter['field'], $this->model, $filter['comparison']);
                        break;

                    case 'IS_UNKNOWN':
                        $filter = new FilterIsUnknown($filter['value'], $filter['field'], $this->model, $filter['comparison']);
                        break;

                    case 'DATE_MORE_THAN':
                        $filter = new FilterDateMoreThan($filter['value'], $filter['field'], $this->model, $filter['comparison']);
                        break;
                    
                    case 'DATE_EXACTLY':
                        $filter = new FilterDateExactly($filter['value'], $filter['field'], $this->model, $filter['comparison']);
                        break;
                    
                    case 'DATE_LESS_THAN':
                        $filter = new FilterDateLessThan($filter['value'], $filter['field'], $this->model, $filter['comparison']);
                        break;

                    default:
                        $filter = new FilterUnknown($filter['value'], $filter['field'], $this->model, $filter['comparison']);
                        continue;
                }
                $filterGroup->addFilter($filter);
            }
        }
        return $filterGroup;
    }
}
