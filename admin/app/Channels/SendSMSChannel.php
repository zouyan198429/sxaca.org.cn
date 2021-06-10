<?php
namespace App\Channels;

use Illuminate\Notifications\Notification;
class SendSMSChannel
{
    /**
     * 发送指定的通知[手机短信].
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toSendSMS($notifiable);

        // Send notification to the $notifiable instance...
    }
}