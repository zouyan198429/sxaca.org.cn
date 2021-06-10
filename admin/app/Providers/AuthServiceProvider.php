<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
//use App\Models\Passport\Client;
//use App\Models\Passport\Token;
//use App\Models\Passport\AuthCode;
//use App\Models\Passport\PersonalAccessClient;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     * 注册任意应用认证／授权服务。
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
         Passport::routes();
         // 可以使用 Passport::loadKeysFrom 方法来自定义 Passport 密钥的加载路径
        //  Passport::loadKeysFrom('/secret-keys/oauth');
        // 默认情况下，Passport 发放的访问令牌是有一年有效期的，不需要刷新。
        //  但是如果你想自定义访问令牌的有效期，可以使用 tokensExpireIn 和 refreshTokensExpireIn 方法。
        //  上述两个方法同样需要在 AuthServiceProvider 的 boot 方法中调用
        // 时间过长或者过短，您还可以使用addMinutes()或者时addMonths()都可以的。
         Passport::tokensExpireIn(now()->addDays(15));// token有效期
         Passport::refreshTokensExpireIn(now()->addDays(30));// 可刷新token时间
         Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        // 覆盖默认模型
        // 可以自由扩展 Passport 使用的模型，通过 Passport 类自定义模型覆盖默认模型
//        Passport::useTokenModel(Token::class);
//        Passport::useClientModel(Client::class);
//        Passport::useAuthCodeModel(AuthCode::class);
//        Passport::usePersonalAccessClientModel(PersonalAccessClient::class);

        // 隐式授权类似于授权码授权，但是它只将令牌返回给客户端而不交换授权码。
        // 这种授权最常用于无法安全存储客户端凭据的 JavaScript 或移动应用程序。
        // 通过调用 AuthServiceProvider 中的 enableImplicitGrant 方法来启用这种授权
        // Passport::enableImplicitGrant();

        // 定义 API 的作用域。tokensCan 方法接受一个作用域名称、描述的数组作为参数。
        // 作用域描述将会在授权确认页中直接展示给用户，你可以将其定义为任何你需要的内容：
//        Passport::tokensCan([
//            'place-orders' => 'Place orders',
//            'check-status' => 'Check order status',
//        ]);

        //默认令牌发放的有效期是永久
        //Passport::tokensExpireIn(Carbon::now()->addDays(2));
        //Passport::refreshTokensExpireIn(Carbon::now()->addDays(4));
//        Passport::routes(function (RouteRegistrar $router) {
//            //对于密码授权的方式只要这几个路由就可以了
//            config(['auth.guards.api.provider' => 'users']);
//            $router->forAccessTokens();
//        });

    }
}
