<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderShipped extends Mailable
{
    // 默认队列
    //  如果一个 mailable 类终是要队列化，可以在此类上实现 ShouldQueue 契约。
    //  这样一来，即使你在发送时调用了 send 方法， mailable 也将被序列化：
    // use Illuminate\Contracts\Queue\ShouldQueue;
    // class OrderShipped extends Mailable implements ShouldQueue
    // {
    use Queueable, SerializesModels;

    /**
     * 订单实例.
     *
     * @var Order
     */
    public $order;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( $order)
    {
        $this->order = (object)($order);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.orders.shipped')->with([
            'orderName' => $this->order->name,
            'orderPrice' => $this->order->price,
        ]);
    }
}
