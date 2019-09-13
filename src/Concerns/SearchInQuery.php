<?php

namespace Spatie\QueryBuilder\Concerns;

trait SearchInQuery
{
    public function search()
    {
        $search = $this->request->search; //FIXME: TEMP FOR TESTING
        if ($search !== null) {
            $searchParts = explode(' ', $search);
            $this->where(function ($q) use ($searchParts) {
                foreach($searchParts as $searchPart) {
                    $model = $this->getModel();
                    $modelSearchables = [];

                    $q->where(function ($query) use ($modelSearchables, $searchPart) {
                        $query->where(function ($q) use ($modelSearchables, $searchPart) {
                            foreach ($modelSearchables as $modelSearchable) {
                                $q->orWhere($modelSearchable, 'LIKE', '%' . $searchPart . '%');
                            }
                        })->orWhereHas('properties', function($q) use ($searchPart) {
                            $q->where(function($query) use ($searchPart) {
                                $query->where('type', 'SYSTEM')
                                    ->where('value', 'LIKE', '%' . $searchPart . '%');
                            });
                        });
                    });
                   
                }
            });
        }
        return $this;
    }
}