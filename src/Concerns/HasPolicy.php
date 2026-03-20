<?php

namespace Jundayw\Policy\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasPolicy
{
    private static array $instancesPolicy   = [];
    private static array $instancesPolicies = [];

    public function hasPolicies(string $ability, Model $authenticate, mixed ...$arguments): bool
    {
        // 已有结果中获取
        if (array_key_exists($ability, self::$instancesPolicy)) {
            return self::$instancesPolicy[$ability];
        }
        // 获取策略数组
        if (array_key_exists($ability, self::$instancesPolicies) === false) {
            self::$instancesPolicies[$ability] = $authenticate->getPolicies($ability, $arguments);
        }
        // 当前策略在策略数组中是否满足匹配规则
        foreach (self::$instancesPolicies[$ability] as $policy) {
            // 当前策略是否含有通配符
            if (Str::contains($policy, '*', false)) {
                // Pa.*.*/Aa.b.*
                if (Str::is($policy, $ability, true)) {
                    self::$instancesPolicy[$ability] = true;
                    return true;
                }
            } else {
                // Aa.b.*/Pa.b.c
                if (Str::is($ability, $policy, true)) {
                    self::$instancesPolicy[$ability] = true;
                    return true;
                }
            }
        }
        self::$instancesPolicy[$ability] = false;
        // 默认验证不通过
        return false;
    }
}
