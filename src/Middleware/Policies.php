<?php

namespace Jundayw\Policy\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Jundayw\Policy\Exceptions\PolicyException;
use Jundayw\Policy\Policy;
use Jundayw\Policy\Support\NamespaceControllerActionNameTrait;

class Policies
{
    use NamespaceControllerActionNameTrait;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return Response|PolicyException
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->user()->can($this->getNamespaceControllerActionName('.'), [Policy::class])) {
            return $next($request);
        }

        throw new PolicyException;
    }
}
