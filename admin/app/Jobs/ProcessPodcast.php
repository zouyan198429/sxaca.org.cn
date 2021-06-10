<?php

namespace App\Jobs;

use App\Podcast;
use App\AudioProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Jobs\Middleware\RateLimited;

// 实现了 Illuminate\Contracts\Queue\ShouldQueue 接口，这意味着这个任务将会被推送到队列中，而不是同步执行。
class ProcessPodcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $podcast;

    /**
     * 得到任务应该经过的中间人。
     *
     * @return array
     */
//    public function middleware()
//    {
//        return [new RateLimited];
//    }

    /**
     * Create a new job instance.
     *
     * @param  Podcast  $podcast
     * @return void
     */
    public function __construct(Podcast $podcast)
    {
        $this->podcast = $podcast;
    }

    /**
     * Execute the job.
     * 任务类的结构很简单，一般来说只会包含一个让队列用来调用此任务的 handle 方法。我们来看一个示例的任务类。
     * 这个示例里，假设我们管理着一个播客发布服务，在发布之前需要处理上传播客文件：
     * @param  AudioProcessor  $processor
     * @return void
     */
    public function handle()
    {
        // 处理上传播客...
    }
}
