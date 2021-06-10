<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    // 事件发现
    // 注意：事件发现可用于 Laravel 5.8.9 或更高版本。
    // Laravel 将通过扫描应用程序的 Listeners 目录自动查找并注册您的事件和侦听器。
    // 此外，EventServiceProvider 中列出的任何明确定义的事件仍将被注册。
    //
    // Laravel 通过使用反射扫描监听器类来查找事件监听器。
    // 当 Laravel 找到以 handle 开头的监听器类方法时，
    //  Laravel 会将这些方法注册为方法签名中类型提示的事件的事件监听器：

    // 提示：php artisan event:list 命令可用于显示应用程序注册的所有事件和监听器的列表。
    // 在生产中，您可能不希望框架在每个请求上扫描所有监听器。
    // 因此，在部署过程中，您应该运行 php artisan event:cache 命令来缓存应用程序的所有事件和监听器的列表。
    // 框架将使用此列表来加速事件注册过程。 php artisan event:clear 命令可用于销毁缓存。

    // 默认情况下禁用事件发现，但您可以通过覆盖应用程序的
    // EventServiceProvider 的 shouldDiscoverEvents 方法来启用它：
    /**
     * 确定是否应自动发现事件和侦听器。
     *
     * @return bool
     */
//    public function shouldDiscoverEvents()
//    {
//        return true;
//    }

    // 默认情况下，将扫描应用程序的 Listeners 目录中的所有监听器。
    // 如果要定义扫描的其他目录，可以覆盖 EventServiceProvider 中的 discoverEventsWithin 方法：
    /**
     * 获取应该用于发现事件的监听器目录
     *
     * @return array
     */
//    protected function discoverEventsWithin()
//    {
//        return [
//            $this->app->path('Listeners'),
//        ];
//    }

    /**
     * The event listener mappings for the application.
     * 应用程序的事件监听器映射。
     * @var array
     */
    protected $listen = [
        // 注册发送邮件事件
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        // Laravel 在处理邮件消息发送时触发两个事件。
        //  MessageSending 事件在消息发送前触发，
        //  MessageSent 事件则在消息发送后触发。
        //  切记，这些事件是在邮件被 发送 时触发，而不是在队列化的时候。
        //  可以在 EventServiceProvider 中注册此事件的侦听器：
//        'Illuminate\Mail\Events\MessageSending' => [
//            'App\Listeners\LogSendingMessage',
//        ],
//        'Illuminate\Mail\Events\MessageSent' => [
//            'App\Listeners\LogSentMessage',
//        ],

//        'App\Events\Event' => [
//            'App\Listeners\EventListener',
//        ],
        // 监听事件
//        'Dingo\Api\Event\ResponseWasMorphed' => [
//            'App\Listeners\AddPaginationLinksToResponse'
//        ]
        // Passport 在发出访问令牌和刷新令牌时触发事件。
        //你可以在应用程序 的 EventServiceProvider 中为这些事件追加监听器，并在监听器中撤销或修改其他令牌
//        在App\Listeners\RevokeOldTokens的handle中删除失效token：
//        DB::table('oauth_access_tokens')
//            ->where('id', '<>', $event->tokenId)
//            ->where('user_id', $event->userId)
//            ->where('client_id', $event->clientId)
//            ->delete();

//        'Laravel\Passport\Events\AccessTokenCreated' => [
//            'App\Listeners\RevokeOldTokens',
//        ],
//
//        'Laravel\Passport\Events\RefreshTokenCreated' => [
//            'App\Listeners\PruneOldTokens',
//        ],
        'App\Events\OrderShipped' => [
            'App\Listeners\SendShipmentNotification',
        ],
    ];

    /**
     * Register any events for your application.
     * 注册应用程序中的任何其它事件
     *  @return void
     */
    public function boot()
    {
        parent::boot();

        // 手动注册事件
        // 事件通常是在 EventServiceProvider 类的 $listen 数组中注册，
        // 但是，你也可以在 EventServiceProvider 类的 boot 方法中注册基于事件的闭包
        //Event::listen('event.name', function ($foo, $bar) {
        //        //
        //});

        //  通配符事件监听器
        //  你可以在注册监听器时使用 * 通配符参数，这样能够在同一个监听器上捕获多个事件。
        //  通配符监听器接受事件名称作为其第一个参数，并将整个事件数据数组作为其第二个参数：
        // Event::listen('event.*', function ($eventName, array $data) {
            //
        // });
    }

    /**
     * 需要注册的订阅者类。
     *
     * @var array
     */
//    protected $subscribe = [
//        'App\Listeners\UserEventSubscriber',
//    ];

}
