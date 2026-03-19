<a id="readme-top"></a>

# Laravel Policy

一个灵活的 Laravel 权限校验系统，支持通过模型方法动态返回权限标识，并提供便捷的中间件集成能力。

中文 | [English](./README_EN.md)

[![GitHub Tag][GitHub Tag]][GitHub Tag URL]
[![Total Downloads][Total Downloads]][Packagist URL]
[![Packagist Version][Packagist Version]][Packagist URL]
[![Packagist PHP Version Support][Packagist PHP Version Support]][Repository URL]
[![Packagist License][Packagist License]][Repository URL]

<!-- 目录 -->

<details>
    <summary>目录</summary>
    <ol>
        <li><a href="#installation">安装</a></li>
        <li><a href="#usage">使用方法</a></li>
        <li><a href="#contributing">参与贡献</a></li>
        <li><a href="#contributors">贡献者</a></li>
        <li><a href="#license">许可证</a></li>
    </ol>
</details>

<!-- 安装 -->

## Installation（安装）

你可以通过 [Composer] 安装此扩展包：

```bash
composer require jundayw/policy
```

<p align="right">[<a href="#readme-top">返回顶部</a>]</p>

<!-- 使用 -->

## Usage（使用方法）

### 模型准备

需要被验证权限的模型（例如 `App\Models\User`）必须实现 `Jundayw\Policy\Contracts\Policeable` 接口，并引入 `Jundayw\Policy\Concerns\HasPolicy` trait。同时需要实现 `getPolicies(string $ability, array $arguments = []): array` 方法，返回当前用户所拥有的权限标识符数组。

```php
namespace App\Models;

use Jundayw\Policy\Concerns\HasPolicy;
use Jundayw\Policy\Contracts\Policeable;

class User extends Authenticatable implements Policeable
{
    use HasPolicy;

    public function getPolicies(string $ability, array $arguments = []): array
    {
        // 这里可以从数据库、缓存等地方获取用户的权限列表
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

### 使用内置中间件

包提供了 `policy` 中间件，你可以直接在控制器或路由中使用它进行权限验证。

**在控制器构造函数中：**

```php
class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('policy');
    }
}
```

**在路由定义中：**

```php
Route::middleware('policy')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    // ...
});
```

当用户无权访问时，内置中间件会抛出 `Jundayw\Policy\Exceptions\PolicyException` 异常，请根据应用需求自行捕获处理（例如返回 403 页面或 JSON 响应）。

### 自定义中间件

如果你需要更灵活的控制（例如自定义权限标识符的生成规则），可以创建自己的中间件：

```php
use Jundayw\Policy\Policy;
use Jundayw\Policy\Exceptions\PolicyException;

class CustomPolicyMiddleware
{
    public function handle($request, $next)
    {
        // 根据请求生成权限标识符，例如 "admin.user.update"
        $policy = $this->determinePolicy($request);

        if ($request->user()->can($policy, [Policy::class])) {
            return $next($request);
        }

        // 自定义无权访问行为
        throw new PolicyException('Unauthorized', 403);
    }

    protected function determinePolicy($request)
    {
        // 示例：从路由或请求中动态构造权限字符串
        return $request->route()->getName();
    }
}
```

### 调试模式

你可以通过发布配置文件来开启或关闭权限验证，方便在开发或测试环境中暂时绕过权限检查。

发布配置文件：

```bash
php artisan vendor:publish --tag=policy-config
```

配置文件路径为 `config/policy.php`，内容如下：

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | 权限验证开关
    |--------------------------------------------------------------------------
    |
    | 当设置为 false 时，所有权限检查都会返回 true，即不进行验证。
    | 这可以在本地开发或测试环境中临时关闭权限系统。
    |
    */
    'enabled' => env('POLICY_ENABLED', true),
];
```

你可以在 `.env` 文件中添加以下配置来控制验证状态：

```
POLICY_ENABLED=false
```

## 异常处理

当权限验证失败时，系统会抛出 `Jundayw\Policy\Exceptions\PolicyException`。建议在 `App\Exceptions\Handler` 中统一处理该异常，例如返回友好的错误页面或 JSON 响应：

```php
public function register()
{
    $this->renderable(function (PolicyException $e, $request) {
        if ($request->expectsJson()) {
            return response()->json(['message' => '无权访问'], 403);
        }
        return response()->view('errors.403', [], 403);
    });
}
```

<p align="right">[<a href="#readme-top">返回顶部</a>]</p>

<!-- 贡献 -->

## Contributing（参与贡献）

开源社区之所以如此优秀，正是因为大家的贡献让其不断成长、激发灵感并创造价值。非常欢迎你的参与！

如果你有改进建议：

* Fork 本仓库
* 创建你的功能分支（`git checkout -b feature/AmazingFeature`）
* 提交你的更改（`git commit -m 'Add some AmazingFeature'`）
* 推送到分支（`git push origin feature/AmazingFeature`）
* 提交 Pull Request

你也可以直接提交 Issue，并标记为 `enhancement`。

如果你觉得这个项目对你有帮助，欢迎点个 ⭐ 支持一下！

<p align="right">[<a href="#readme-top">返回顶部</a>]</p>

<!-- 贡献者 -->

## Contributors（贡献者）

感谢以下优秀的贡献者：

<a href="https://github.com/jundayw/policy/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=jundayw/policy" alt="贡献者列表" />
</a>

欢迎任何形式的贡献！

<p align="right">[<a href="#readme-top">返回顶部</a>]</p>

<!-- 许可证 -->

## License（许可证）

本项目基于 MIT License（MIT）开源协议发布。详情请查看 [License File]。

<p align="right">[<a href="#readme-top">返回顶部</a>]</p>

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
