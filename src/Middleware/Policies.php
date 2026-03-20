<?php

namespace Jundayw\Policy\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Jundayw\Policy\Contracts\CanPoliceable;
use Jundayw\Policy\Exceptions\PolicyException;
use Jundayw\Policy\Policy;

class Policies
{
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
        if ($request->user()->can(app(CanPoliceable::class), [Policy::class])) {
            return $next($request);
        }

        throw new PolicyException;
    }
}
