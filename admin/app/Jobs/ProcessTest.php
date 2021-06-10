<?php

namespace App\Jobs;
// 测试队列
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
// use App\Jobs\Middleware\RateLimited;

class ProcessTest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 应该处理任务的队列连接.
     * 或者，可以将 connection 指定为任务类的属性：
     * @var string
     */
    // public $connection = 'sqs';

    // 你可能想通过任务类自身对最大任务尝试次数进行一个更颗粒化的处理。
    // 如果最大尝试次数是在任务类中定义的，它将优先于命令行中的值提供：
    /**
     * 任务可以尝试的最大次数。
     *
     * @var int
     */
     public $tries = 5;

    // 然而，你可能也想在任务类自身定义一个超时时间。如果在任务类中指定，优先级将会高于命令行：
    /**
     * 任务可以执行的最大秒数 (超时时间)。
     *
     * @var int
     */
    // public $timeout = 120;

//    忽略缺失的模型
//    在向任务中注入 Eloquent 模型时，模型被放入队列前将被自动序列化并在执行任务时还原。
//    但是，如果在任务等待执行时删除了模型，任务可能会失败并抛出 ModelNotFoundException 。
//    为了方便，你可以选择设置任务的 deleteWhenMissingModels 属性为 true 来自动地删除缺失模型的任务。
    /**
     * 如果模型缺失即删除任务。
     *
     * @var bool
     */
    // public $deleteWhenMissingModels = true;

    protected $params = [];
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($params = [])
    {
        $this->params = $params;
        Log::info('队列日志 初始化-->' . date('Y-m-d H:i:s') . __FUNCTION__, $params);

    }

//    基于时间的尝试
//    作为另外一个选择来定义任务在失败前会尝试多少次，你可以定义一个任务超时时间。
//    这样的话，在给定的时间范围内，任务可以无限次尝试。要定义一个任务的超时时间，
//    在你的任务类中新增一个 retryUntil 方法：
//    Tip：你也可以在你的队列事件监听器中使用 retryUntil 方法。
//
//        /**
//         * 定义任务超时时间
//         *
//         * @return \DateTime
//         */
//        public function retryUntil()
//        {
//            return now()->addSeconds(5);
//        }

    /**
     * 得到任务应该经过的中间人。
     *
     * @return array
     */
//    public function middleware()
//    {
//        //
//        Log::info('队列日志 中间人-->'  . date('Y-m-d H:i:s') . __FUNCTION__, $this->params);
//        return [new RateLimited];
//    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        //
        // Log::info('队列日志 执行队列-->'  . date('Y-m-d H:i:s') . __FUNCTION__, $this->params);
        // Redis::throttle('key')->allow(1)->every(5)->then(function () {
            // 任务逻辑...
            Log::info('队列日志 执行队列-->'  . date('Y-m-d H:i:s') . __FUNCTION__, $this->params);
        // }, function () {
            // 无法获得锁...
//            Log::info('队列日志 无法获得锁-->'  . date('Y-m-d H:i:s') . __FUNCTION__, $this->params);
//
//            return $this->release(5);
        // });
    }

    /**
     * 任务失败的处理过程
     * 你可以直接在任务类中定义 failed 方法，允许你在任务失败时执行针对于该任务的清理工作。
     * 这是向用户发送警报或恢复任务执行的任何操作的绝佳位置。
     * 导致任务失败的 Exception 将被传递给 failed 方法：
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        // 给用户发送任务失败的通知，等等……
        Log::info('队列日志 执行队列-失败-->'  . date('Y-m-d H:i:s') . __FUNCTION__, $this->params);
    }
}
