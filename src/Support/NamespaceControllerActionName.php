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

        $uri = match ($request->route()->getName()) {
            'gateway' => app('router')->current()?->getName() ?? app('router')->current()?->uri() ?? $request->path(),
            default => $request->route()?->getName() ?? $request->path()
        };

        return str_replace('/', '.', $uri);
    }
}
