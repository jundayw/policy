<a id="readme-top"></a>

# Policy

A flexible policy verification system for Laravel, supporting dynamic return of permission identifiers through model methods and providing easy integration with middleware.

[中文](./README_CN.md) | English

[![GitHub Tag][GitHub Tag]][GitHub Tag URL]
[![Total Downloads][Total Downloads]][Packagist URL]
[![Packagist Version][Packagist Version]][Packagist URL]
[![Packagist PHP Version Support][Packagist PHP Version Support]][Repository URL]
[![Packagist License][Packagist License]][Repository URL]

<!-- TABLE OF CONTENTS -->
<details>
    <summary>Table of Contents</summary>
    <ol>
        <li><a href="#installation">Installation</a></li>
        <li><a href="#usage">Usage</a></li>
        <li><a href="#contributing">Contributing</a></li>
        <li><a href="#contributors">Contributors</a></li>
        <li><a href="#license">License</a></li>
    </ol>
</details>

<!-- INSTALLATION -->

## Installation

You can install the package via [Composer]:

```bash
composer require jundayw/policy
```

<p align="right">[<a href="#readme-top">back to top</a>]</p>

<!-- USAGE EXAMPLES -->

## Usage

### Model Preparation

The model to be verified (e.g., `App\Models\User`) must implement the `Jundayw\Policy\Contracts\Policeable` interface and use the `Jundayw\Policy\Concerns\HasPolicy` trait. Also, implement the `getPolicies(string $ability, array $arguments = []): array` method to return an array of permission identifiers that the current user possesses.

```php
namespace App\Models;

use Jundayw\Policy\Concerns\HasPolicy;
use Jundayw\Policy\Contracts\Policeable;

class User extends Authenticatable implements Policeable
{
    use HasPolicy;

    public function getPolicies(string $ability, array $arguments = []): array
    {
        // Here you can obtain the user's permission list from the database, cache, etc.
        return [
            'backend.module.create',
            'backend.module.edit',
            'backend.policy.list',
            'backend.policy.create',
            'backend.policy.update',
            'backend.policy.destroy',
            'backend.role.*',
            'backend.*.*',
        ];
    }
}
```

### Using Built-in Middleware

The package provides a `policy` middleware that you can use directly in controllers or routes for permission verification.

**In the controller constructor:**

```php
class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('policy');
    }
}
```

**In route definitions:**

```php
Route::middleware('policy')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    // ...
});
```

When a user is unauthorized, the built-in middleware throws a `Jundayw\Policy\Exceptions\PolicyException`. You should handle this exception according to your application's needs (e.g., return a 403 page or JSON response).

### Custom Middleware

If you need more control (e.g., custom rules for generating permission identifiers), you can create your own middleware:

```php
use Jundayw\Policy\Policy;
use Jundayw\Policy\Exceptions\PolicyException;

class CustomPolicyMiddleware
{
    public function handle($request, $next)
    {
        // Generate a permission identifier based on the request, e.g., "admin.user.update"
        $policy = $this->determinePolicy($request);

        if ($request->user()->can($policy, [Policy::class])) {
            return $next($request);
        }

        // Custom unauthorized behavior
        throw new PolicyException('Unauthorized', 403);
    }

    protected function determinePolicy($request)
    {
        // Example: dynamically construct a permission string from the route or request
        return $request->route()->getName();
    }
}
```

### Custom Permission Identifier

Create a custom rule class for generating permission identifiers:

```php
use Illuminate\Http\Request;
use Jundayw\Policy\Contracts\CanPoliceable;

class CustomPoliceable implements CanPoliceable
{
    public function __invoke(Request $request): mixed
    {
        if (app()->runningInConsole()) {
            return null;
        }

        return $request->path();
    }
}
```

Register it in the container:

```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Jundayw\Policy\Contracts\CanPoliceable;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->bind(CanPoliceable::class, function () {
            return new CustomPoliceable;
        });
    }
}
```

### Debug Mode

You can enable or disable permission verification by publishing the configuration file, which is useful for temporarily bypassing permission checks in development or testing environments.

Publish the configuration file:

```bash
php artisan vendor:publish --tag=policy-config
```

The configuration file will be located at `config/policy.php` with the following content:

```php
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
```

You can control the verification status by adding the following to your `.env` file:

```
POLICY_ENABLED=false
```

## Exception Handling

When permission verification fails, the system throws a `Jundayw\Policy\Exceptions\PolicyException`. It is recommended to handle this exception uniformly in `App\Exceptions\Handler`, for example, by returning a friendly error page or JSON response:

```php
public function register()
{
    $this->renderable(function (PolicyException $e, $request) {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return response()->view('errors.403', [], 403);
    });
}
```

<!-- CONTRIBUTING -->

## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

<p align="right">[<a href="#readme-top">back to top</a>]</p>

<!-- CONTRIBUTORS -->

## Contributors

Thanks goes to these wonderful people:

<a href="https://github.com/jundayw/policy/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=jundayw/policy" alt="contrib.rocks image" />
</a>

Contributions of any kind are welcome!

<p align="right">[<a href="#readme-top">back to top</a>]</p>

<!-- LICENSE -->

## License

Distributed under the MIT License (MIT). Please see [License File] for more information.

<p align="right">[<a href="#readme-top">back to top</a>]</p>

[GitHub Tag]: https://img.shields.io/github/v/tag/jundayw/policy

[Total Downloads]: https://img.shields.io/packagist/dt/jundayw/policy?style=flat-square

[Packagist Version]: https://img.shields.io/packagist/v/jundayw/policy

[Packagist PHP Version Support]: https://img.shields.io/packagist/php-v/jundayw/policy

[Packagist License]: https://img.shields.io/github/license/jundayw/policy

[GitHub Tag URL]: https://github.com/jundayw/policy/tags

[Packagist URL]: https://packagist.org/packages/jundayw/policy

[Repository URL]: https://github.com/jundayw/policy

[GitHub Open Issues]: https://github.com/jundayw/policy/issues

[Composer]: https://getcomposer.org

[License File]: https://github.com/jundayw/policy/blob/main/LICENSE
