<?php

namespace Jundayw\Policy\Contracts;

use Illuminate\Http\Request;

interface CanPoliceable
{
    public function __invoke(Request $request): mixed;
}
