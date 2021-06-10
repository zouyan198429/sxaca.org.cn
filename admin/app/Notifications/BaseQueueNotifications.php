<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

// 通知队列化
// 注：使用通知队列前需要配置队列并 开启一个队列任务。
// 发送通知可能是耗时的，尤其是通道需要调用额外的 API 来传输通知。
// 为了加速应用的响应时间，可以将通知推送到队列中异步发送，
// 而要实现推送通知到队列，可以让对应通知类实现 ShouldQueue 接口并使用 Queueable trait。
// 如果通知类是通过 make:notification 命令生成的，那么该接口和 trait 已经默认导入，你可以快速将它们添加到通知类

class BaseQueueNotifications extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        // 发送到的队列的名称.
        $this->onQueue(config('public.horizon.notification_queue'));// $this->onQueue('notification');
        // 如果用到自定义队列基类，需要调用父类构造函数
        // parent::__construct();
    }

    /**
     * Get the notification's delivery channels.
     *  每个通知类都包含一个 via 方法以及一个或多个消息构建的方法（比如 toMail 或者 toDatabase)，
     *  它们会针对指定的渠道把通知转换为对应的消息。
     *
     * 发送指定频道
     * 每个通知类都会有个 via 方法，它决定了通知会在哪个频道上发送。
     * 开箱即用的频道有 mail，database，broadcast，nexmo，和 slack。
     * 提示：如果你想使用其他的频道，比如 Telegram 或者 Pusher，你可以去看下社区驱动的 Laravel 通知频道网站。
     *  https://laravel-notification-channels.com/about/#contributing
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        Log::info('消息通知日志 --via-->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . __FUNCTION__, [$notifiable]);
        return ['mail'];
        // 你可以用 $notifiable 来决定这个通知用哪些频道来发送
        // return $notifiable->prefers_sms ? ['nexmo'] : ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        Log::info('消息通知日志 --toMail-->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . __FUNCTION__, [$notifiable]);
//        return (new MailMessage)
//                    ->line('The introduction to the notification.')
//                    ->action('Notification Action', url('/'))
//                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        Log::info('消息通知日志 --toArray-->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . __FUNCTION__, [$notifiable]);
        return [
            //
        ];
    }
}
