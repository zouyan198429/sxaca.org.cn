<?php

namespace App\Jobs\Middleware;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
// 特别注意：这个中间件,一定要指定尝试的最大次数$tries ，不然就没有重试的机会了，会到执行失败表中
class RateLimited
{
    /**
     * 处理队列中的任务.
     * 用了 Laravel 的 Redis 速率限制功能，使得每 5 秒只允许处理一个任务：
     * @param  mixed  $job
     * @param  callable  $next
     * @return mixed
     */
    public function handle($job, $next)
    {
        Redis::throttle('key')
            ->block(0)->allow(1)->every(5)
            ->then(function () use ($job, $next) {
                // 锁定…
                Log::info('队列日志 锁定并执行-->'  . date('Y-m-d H:i:s') . __CLASS__ . "->" . __FUNCTION__, [$job]);

                $next($job);
            }, function () use ($job) {
                // 无法获取锁…
                Log::info('队列日志 无法获得锁-->'  . date('Y-m-d H:i:s') . __CLASS__ . "->" . __FUNCTION__, [$job]);
                $job->release(5);
            });
    }
}