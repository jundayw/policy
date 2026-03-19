<?php

namespace Jundayw\Policy\Support;

use Illuminate\Support\Str;

trait NamespaceControllerActionNameTrait
{
    /**
     * @param string $delimiter
     *
     * @return string|null
     */
    public function getNamespaceControllerActionName(string $delimiter = '.'): ?string
    {
        if (app()->runningInConsole()) {
            return null;
        }

        return app('request')->route()->getName();
    }
}
