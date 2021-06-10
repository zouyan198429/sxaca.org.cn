<?php
// 事件监听器队列-基类，其它要用队列的监听继承此类
namespace App\Listeners;
use App\Events\OrderShipped;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

// 事件监听器队列
// 如果你的监听器中要执行诸如发送电子邮件或发出 HTTP 请求之类的耗时任务，你可以将任务丢给队列处理。
// 在开始使用队列监听器之前，请确保在你的服务器或者本地开发环境中能够 配置队列并启动一个队列监听器。
//
// 要指定监听器启动队列，你可以在监听器类中实现 ShouldQueue 接口。
// 由 Artisan 命令 event:generate 生成的监听器已经将此接口导入到当前命名空间中，因此你可以直接使用：

// 当这个监听器被事件调用时，事件调度器会自动使用 Laravel 的队列系统自动排队。
// 如果在队列中执行监听器时没有抛出异常，任务会在执行完成后自动从队列中删除。
class SendBaseQueueNotification  implements ShouldQueue
{
    // 手动访问队列
    // 如果你需要手动访问监听器下面队列任务的 delete 和 release 方法，
    // 你可以通过使用 Illuminate\Queue\InteractsWithQueue trait 来实现。
    // 这个 trait 会默认加载到生成的监听器中，并提供对这些方法的访问：
     use InteractsWithQueue;

    // 自定义队列连接 & 队列名称
    // 如果你想要自定义事件监听器所使用的队列的连接和名称，你可以在监听器类中定义 $connection， $queue 或 $delay 属性：

    /**
     * 任务连接名称。
     *
     * @var string|null
     */
    // public $connection = 'sqs';

    /**
     * 任务发送到的队列的名称.
     *
     * @var string|null
     */
     public $queue = 'event';// 'listeners';

    /**
     * 处理任务的延迟时间.
     *
     * @var int
     */
    // public $delay = 60;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        // 修改队列的名称.
        $this->queue = config('public.horizon.event_queue');// 'event'
        //
        Log::info('事件监听日志 监听到事件--初始化-->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . __FUNCTION__, []);
    }

    /**
     * Handle the event.
     *
     * @param  OrderShipped  $event
     * @return void
     */
    public function handle(OrderShipped $event)
    {
        // 使用 $event->order 来访问 order ...
        Log::info('事件监听日志 监听到事件--监听处理-->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . __FUNCTION__, [$event, $event->order]);

        // 停止事件传播
        // 有时，你可以通过在监听器的 handle 方法中返回 false 来阻止事件被其它的监听器获取。
        // return false;

        // if (true) {
        //     $this->release(30);
        // }
    }

    // 条件监听队列
    // 有时，你可能需要根据某些运行时的数据（满足某些条件）对监听器进行排队，
    // 为此，可以在侦听器中添加 shouldQueue 方法，以确定是否应该将监听器排队并同步执行：
    /**
     * 确定监听器是否应加入队列
     *
     * @param  \App\Events\OrderPlaced  $event
     * @return bool
     */
//    public function shouldQueue(OrderPlaced $event)
//    {
//        return $event->order->subtotal >= 5000;
//    }

    // 处理失败任务
    // 有时事件监听器的队列任务可能会失败。如果监听器的队列任务超过了队列中定义的最大尝试次数，则会在监听器上调用 failed 方法。
    // failed 方法接收事件实例和导致失败的异常作为参数：
    /**
     * 处理失败任务
     *
     * @param  \App\Events\OrderShipped  $event
     * @param  \Exception  $exception
     * @return void
     */
    public function failed(OrderShipped $event, $exception)
    {

            Log::info('事件监听日志 监听到事件-处理失败任务->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . __FUNCTION__, [$event, $exception]);
        //
    }
}
