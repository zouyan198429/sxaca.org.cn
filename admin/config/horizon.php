<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Horizon Domain
    |--------------------------------------------------------------------------
    |
    | This is the subdomain where Horizon will be accessible from. If this
    | setting is null, Horizon will reside under the same domain as the
    | application. Otherwise, this value will serve as the subdomain.
    |
    */

    'domain' => null,

    /*
    |--------------------------------------------------------------------------
    | Horizon Path
    |--------------------------------------------------------------------------
    |
    | This is the URI path where Horizon will be accessible from. Feel free
    | to change this path to anything you like. Note that the URI will not
    | affect the paths of its internal API that aren't exposed to users.
    |
    */

    'path' => 'horizon',

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Connection
    |--------------------------------------------------------------------------
    |
    | This is the name of the Redis connection where Horizon will store the
    | meta information required for it to function. It includes the list
    | of supervisors, failed jobs, job metrics, and other information.
    |
    */

    'use' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Prefix
    |--------------------------------------------------------------------------
    |
    | This prefix will be used when storing all Horizon data in Redis. You
    | may modify the prefix when you are running multiple installations
    | of Horizon on the same server so that they don't have problems.
    |
    */

    'prefix' => env('HORIZON_PREFIX', 'horizon:'),

    /*
    |--------------------------------------------------------------------------
    | Horizon Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will get attached onto each Horizon route, giving you
    | the chance to add your own middleware to this list or change any of
    | the existing middleware. Or, you can simply stick with this list.
    |
    */

    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Queue Wait Time Thresholds
    |--------------------------------------------------------------------------
    | 配置等待时间过长的阈值
    | This option allows you to configure when the LongWaitDetected event
    | will be fired. Every connection / queue combination may have its
    | own, unique threshold (in seconds) before this event is fired.
    | 设置等待时间过长的具体秒数。 waits 配置项可以针对每一个 链接 / 队列 配置阈值：
    */

    'waits' => [
        'redis:default' => 60,
        'redis:apiDefault' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Job Trimming Times
    |--------------------------------------------------------------------------
    |
    | Here you can configure for how long (in minutes) you desire Horizon to
    | persist the recent and failed jobs. Typically, recent jobs are kept
    | for one hour while all failed jobs are stored for an entire week.
    | 任务修整
    | horizon 配置文件允许你配置应保留最近和失败任务的时间（以分钟为单位）。
    | 默认情况下，最近的任务保留一小时，而失败的任务保留一周：
    */

    'trim' => [
        'recent' => 60,
        'recent_failed' => 10080,
        'failed' => 10080,
        'monitored' => 10080,
    ],

    /*
    |--------------------------------------------------------------------------
    | Fast Termination
    |--------------------------------------------------------------------------
    |
    | When this option is enabled, Horizon's "terminate" command will not
    | wait on all of the workers to terminate unless the --wait option
    | is provided. Fast termination can shorten deployment delay by
    | allowing a new instance of Horizon to start while the last
    | instance will continue to terminate each of its workers.
    |
    */

    'fast_termination' => false,

    /*
    |--------------------------------------------------------------------------
    | Memory Limit (MB)
    |--------------------------------------------------------------------------
    |
    | This value describes the maximum amount of memory the Horizon worker
    | may consume before it is terminated and restarted. You should set
    | this value according to the resources available to your server.
    |
    */

    'memory_limit' => 64,

    /*
    |--------------------------------------------------------------------------
    | Queue Worker Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may define the queue worker settings used by your application
    | in all environments. These supervisors and settings handle all your
    | queued jobs and will be provisioned by Horizon during deployment.
    |
    */

    'environments' => [
        'production' => [
            'supervisor-1' => [
                'connection' => 'redis',
                // default:admin站点的默认；apiDefault：api站点的默认
                'queue' => array_filter([
                    env('REDIS_QUEUE', ''),// 默认队列
                    env('EVENT_QUEUE', ''),// 事件监听器队列
                    env('EMAIL_QUEUE', ''),// 邮件队列
                    env('NOTIFICATION_QUEUE', ''),// 消息通知队列
                ]),// 自动去空值 , 'apiDefault'
                // 三种均衡策略： simple ， auto ，和 false 。默认的是 simple , 会将收到的任务均分给队列进程：
                // auto 策略会根据当前的工作量调整每个队列的工作进程任务数量。
                //  例如：如果 notifications 队列有 1000 个待执行任务，但是你的 render 队列是空的，Horizon 会分配更多的工作进程给 notifications 队列，
                //  直到 notifications 队列中所有任务执行完成。
                //  当配置项 balance 配置为 false ，Horizon 会使用 Laravel 默认执行行为，它将按照配置中列出的顺序处理队列任务。
                'balance' => 'simple',
                // 当使用 auto 策略时，您可以定义 minProcesses 和 maxProcesses 配置选项，
                //  以控制最小和最大进程数范围应向上和向下扩展到：
                'minProcesses' => 1,
                'maxProcesses' => 10,
                'processes' => 10,
                'tries' => 3,
            ],
        ],

        'local' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue' => array_filter([
                        env('REDIS_QUEUE', ''),// 默认队列
                        env('EVENT_QUEUE', ''),// 事件监听器队列
                        env('EMAIL_QUEUE', ''),// 邮件队列
                        env('NOTIFICATION_QUEUE', ''),// 消息通知队列
                    ]),// 自动去空值 , 'apiDefault'
                'balance' => 'simple',
                'minProcesses' => 1,
                'maxProcesses' => 10,
                'processes' => 3,
                'tries' => 3,
            ],
        ],
    ],
];
