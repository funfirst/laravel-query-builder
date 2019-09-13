<?php

namespace Spatie\QueryBuilder\Concerns;

trait AppliesPagination
{
    public function applyPagination()
    {
        $perPage = $this->request->perPage ?: 25; //FIXME: TEMP FOR TESTING
        $currentPage = $this->request->currentPage ?: 1; //FIXME: TEMP FOR TESTING

        $this->offset($perPage * $currentPage - $perPage)->take($perPage);

        return $this;
    }
}