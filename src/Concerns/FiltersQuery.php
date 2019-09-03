<?php

namespace Spatie\QueryBuilder\Concerns;

use Spatie\QueryBuilder\AdvancedFilters\FilterEq;
use Spatie\QueryBuilder\AdvancedFilters\FilterGt;
use Spatie\QueryBuilder\AdvancedFilters\FilterGte;
use Spatie\QueryBuilder\AdvancedFilters\FilterLt;
use Spatie\QueryBuilder\AdvancedFilters\FilterLte;
use Spatie\QueryBuilder\AdvancedFilters\FilterNeq;
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
        //TODO: Filter only by alowed filters
        if ($allowedFilters == null) {
            $allowedFilters = $this->getModel()->getFilterableFields();
        } else {
            $allowedFilters = is_array($allowedFilters) ? $allowedFilters : func_get_args();
        }

        $filterGroup = $this->createFilterGroup($this->request->filter); //FIXME: TEMP FOR TESTING
        // $filterGroup = $this->createFilterGroup($this->request->filters()->toArray());
        $filterGroup->filter($this);
        return $this;
    }

    public function createFilterGroup($filters)
    {
        $filterGroup = new FilterGroup($filters['type']);

        if (array_key_exists('type', $filters['values'][0])) {
            foreach ($filters['values'] as $filterValue) {
                $childGroup = $this->createFilterGroup($filterValue);
                $filterGroup->addGroup($childGroup);
            }
        } else {
            foreach ($filters['values'] as $filter) {
                switch ($filter['comparison']) {
                    case 'EQ':
                        $filter = new FilterEq($filter['value'], $filter['field']);
                        break;
                    case 'NEQ':
                        $filter = new FilterNeq($filter['value'], $filter['field']);
                        break;
                    case 'GT':
                        $filter = new FilterGt($filter['value'], $filter['field']);
                        break;
                    case 'GTE':
                        $filter = new FilterGte($filter['value'], $filter['field']);
                        break;
                    case 'LT':
                        $filter = new FilterLt($filter['value'], $filter['field']);
                        break;
                    case 'LTE':
                        $filter = new FilterLte($filter['value'], $filter['field']);
                        break;
                    default:
                        continue;
                }
                $filterGroup->addFilter($filter);
            }
        }

        return $filterGroup;
    }
}
