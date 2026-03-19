<?php

namespace Jundayw\Policy;

use BadMethodCallException;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Policy
{
    use HandlesAuthorization;

    public function __call($method, $arguments)
    {
        if (app('config')->get('policy.enabled') === false) {
            return true;
        }

        $method = Str::kebab($method);

        $instance       = Arr::first($arguments);
        $instanceMethod = 'hasPolicies';

        if (method_exists($instance, $instanceMethod) === false) {
            throw new BadMethodCallException(
                sprintf('Call to undefined method %s::%s', get_class($instance), $instanceMethod)
            );
        }

        $args = [$method];

        foreach ($arguments as $argument) {
            $args[] = $argument;
        }

        return call_user_func_array([$instance, $instanceMethod], $args);
    }
}
