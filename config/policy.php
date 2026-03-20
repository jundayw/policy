<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Policy Verification Switch
    |--------------------------------------------------------------------------
    |
    | When set to false, all permission checks will return true, i.e., no verification.
    | This can be used to temporarily disable the policy system in local development or testing.
    |
    */

    'enabled' => env('POLICY_ENABLED', true),
];
