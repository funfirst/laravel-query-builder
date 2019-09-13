<?php

namespace Spatie\QueryBuilder\Concerns;

trait AppliesPagination
{
    public function applyPagination()
    {
        if ($this->request->page) {
            if (array_key_exists('number', $this->request->page)) {
                $currentPage = $this->request->page['number'];
            } else {
                $currentPage = 1;
            }

            if (array_key_exists('size', $this->request->page)) {
                $perPage = $this->request->page['size'];
            } else {
                $perPage = 1;
            }
        } else {
            $perPage = 25;
            $currentPage = 1;
        }

        $this->offset($perPage * $currentPage - $perPage)->take($perPage);

        return $this;
    }
}
