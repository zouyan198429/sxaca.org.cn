<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */
    // 这里是指定默认的看守器
    // web 的意思取下面 guards 数组 key 为 web 的那个
    // passwords 是重置密码相关，暂时不懂什么意思
    'defaults' => [
        'guard' =>  'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Supported: "session", "token"
    |
    */

    // 这里定义可以用的 guard
    // driver 指的就是上面的对 Guard 契约的具体实现那个类了
    // users 是下面 providers 数组 key 为 users 的那个
    'guards' => [
        'web' => [
            'driver' => 'session',// SessionGuard 实现
            'provider' => 'users',
        ],

        'api' => [
            // 'jwt' api 为使用 jwt 机制，provider 对应你要用的用户认证表，一般就是登录注册那张表
            // JWTGuard 实现，源码中为 token，我这改成 jwt 了
            //'driver' => 'jwt',// .原值'token',
            //此调整会让你的应用程序在在验证传入的 API 的请求时使用 Passport 的 TokenGuard 来处理
            'driver' => 'passport',
            'provider' => 'users',
             'hash' => false,
        ],
        // 小程序登录
//        'wechat_api' => [
//            'driver' => 'passport',
//            'provider' => 'wechat',
//        ],
        //  接口使用xcx， 保护项（ driver ）改为 passport 。
        //  此调整会让你的应用程序在接收到 API 的授权请求时使用 Passport 的 TokenGuard 来处理：
//        'xcx' => [
//            'driver' => 'passport',
//            'provider' => 'usercu',
//        ],
        // 比如针对客户表和管理员表分别做 Auth 认证的情况
//        'admin_api' => [
//            'driver' => 'passport',
//            'provider' => 'admin_users',
//        ],
//        'admin' => [
//            // 'jwt' api 为使用 jwt 机制，provider 对应你要用的用户认证表，一般就是登录注册那张表
//            // JWTGuard 实现，源码中为 token，我这改成 jwt 了
//            'driver' => 'jwt',// .原值'token',
//            'provider' => 'admins',
//        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */
    // 这个的作用是指定认证所需的 user 来源的数据表
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\User::class,// 根据你model的位置更改
        ],
//        'wechat' => [
//            'driver' => 'eloquent',
//            'model' => App\Wechat::class
//        ],
//        'usercu' => [
//            'driver' => 'database',
//            'model' => 'App\Models\UserCu::class',
//        ],
//        'admin_users' => [
//            'driver' => 'eloquent',
//            'model' => App\Models\AdminUser::class
//        ],
//        'admins' => [
//            'driver' => 'eloquent',
//            'model' => App\Admin::class,// 根据你model的位置更改
//        ],
        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application and you want to have
    | separate password reset settings based on the specific user types.
    |
    | The expire time is the number of minutes that the reset token should be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
//        'usercu' => [
//            'provider' => 'usercu',
//            'table' => 'password_resets',
//            'expire' => 60,
//        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of seconds before a password confirmation
    | times out and the user is prompted to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => 10800,

];
