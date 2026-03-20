<?php

namespace Jundayw\Policy;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Jundayw\Policy\Contracts\CanPoliceable;
use Jundayw\Policy\Middleware\Policies;
use Jundayw\Policy\Support\NamespaceControllerActionName;

class PolicyServiceProvider extends AuthServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Policy::class => Policy::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        if (!app()->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/policy.php', 'policy');
        }

        $this->registerBladeExtensions();
        $this->registerCanPoliceable();
        $this->addMiddlewareAlias('policy', Policies::class);
    }

    public function registerBladeExtensions(): void
    {
        $this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {
            $bladeCompiler->if('policy', function (string $ability, $arguments = [Policy::class]) {
                return app(Gate::class)->check($ability, $arguments);
            });
            $bladeCompiler->if('policies', function (array $abilities, $arguments = [Policy::class]) {
                return app(Gate::class)->any($abilities, $arguments);
            });
        });
    }

    public function registerCanPoliceable(): void
    {
        $this->app->bind(CanPoliceable::class, static fn($app) => call_user_func(
            $app->make(NamespaceControllerActionName::class),
            app('request')
        ));
    }

    /**
     * Register the middleware.
     *
     * @param $name
     * @param $class
     *
     * @return mixed
     */
    protected function addMiddlewareAlias($name, $class): mixed
    {
        $router = $this->app['router'];

        if (method_exists($router, 'aliasMiddleware')) {
            return $router->aliasMiddleware($name, $class);
        }

        return $router->middleware($name, $class);
    }

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        if (app()->runningInConsole()) {
            $this->registerPublishing();
        }

        $this->registerPolicies();
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing(): void
    {
        $this->publishes([
            __DIR__.'/../config/policy.php' => config_path('policy.php'),
        ], 'policy-config');
    }
}
