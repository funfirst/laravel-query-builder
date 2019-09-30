<?php

namespace Spatie\QueryBuilder\Concerns;

trait SearchInQuery
{
    public function search()
    {
        // $search = $this->request->search; //FIXME: TEMP FOR TESTING
        // if ($search !== null) {
        //     $searchParts = explode(' ', $search);
        //     $this->where(function ($q) use ($searchParts) {
        //         foreach($searchParts as $searchPart) {
        //             $model = $this->getModel();
        //             if (!$model->customQuerySearch($q)) {
        //                 $modelSearchables = $model->getSearchableFields();
        //                 $q->where(function ($query) use ($modelSearchables, $searchPart) {
        //                     $query->where(function ($q) use ($modelSearchables, $searchPart) {
        //                         foreach ($modelSearchables as $modelSearchable) {
        //                             $q->orWhere($modelSearchable, 'LIKE', '%' . $searchPart . '%');
        //                         }
        //                     });
        //                 });
        //             }
        //         }
        //     });
        // }
        // return $this;

        //FIXME: BUG when trying to search one field with value = BLACK S, it does same as with Jakub Gause, it divides and search for BLACK and S not for BLACK S

        $search = $this->request->search;
        if ($search !== null) {
            $searchParts = explode(' ', $search);
            $this->where(function ($q) use ($searchParts) {
                foreach ($searchParts as $searchPart) {
                    $model = $this->getModel();
                    $modelSearchables = $model->getSearchableFields();
                    $q->where(function ($query) use ($modelSearchables, $searchPart) {
                        $query->where(function ($q) use ($modelSearchables, $searchPart) {
                            foreach ($modelSearchables as $modelSearchable) {
                                $q->orWhere($modelSearchable, 'LIKE', '%' . $searchPart . '%');
                            }
                        });

                        $usesProperties = array_key_exists(\App\API\Properties\Traits\UsesProperties::class, class_uses($this->getModel()));
                        if ($usesProperties) {
                            $query->orWhereHas('properties', function ($q) use ($searchPart) {
                                $q->where(function ($query) use ($searchPart) {
                                    $query->where('value', 'LIKE', '%' . $searchPart . '%');
                                });
                            });
                        }
                    });
                }
            });
        }
        return $this;
    }
}
