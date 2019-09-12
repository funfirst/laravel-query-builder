<?php

namespace Spatie\QueryBuilder\Concerns;

trait AppliesScopeToQuery
{
    public function applyScopes()
    {
        $scopes = $this->request->scopes; //FIXME: TEMP FOR TESTING
        if ($scopes === null) {
            $scopes = [];
        } 
        foreach ($scopes as $scope) {
            $model = $this->getModel();
            if (method_exists($model, 'scope' . ucfirst($scope))) {
                $this->{$scope}();
            }
        }
        return $this;
    }
}