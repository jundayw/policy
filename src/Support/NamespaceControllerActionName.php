<?php

namespace Jundayw\Policy\Support;

use Illuminate\Http\Request;
use Jundayw\Policy\Contracts\CanPoliceable;

class NamespaceControllerActionName implements CanPoliceable
{
    public function __invoke(Request $request): mixed
    {
        if (app()->runningInConsole()) {
            return null;
        }

        return $request->route()->getName();
    }
}
