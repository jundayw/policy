<?php

namespace Jundayw\Policy\Contracts;

use Illuminate\Database\Eloquent\Model;

interface Policeable
{
    /**
     * @param string $ability
     * @param Model  $authenticate
     * @param mixed  ...$arguments
     *
     * @return bool
     */
    public function hasPolicies(string $ability, Model $authenticate, mixed ...$arguments): bool;

    /**
     * @param string $ability
     * @param array  $arguments
     *
     * @return string[]
     */
    public function getPolicies(string $ability, array $arguments = []): array;
}
