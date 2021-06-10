<?php

return [
    // 自定义各队列--这里只是方便查看用有哪些队列
    'redis_queue' => env('REDIS_QUEUE', 'default'),// 默认队列
    'event_queue' => env('EVENT_QUEUE', ''),// 事件监听器队列
    'email_queue' => env('EMAIL_QUEUE', ''),// 邮件队列
    'notification_queue' => env('NOTIFICATION_QUEUE', ''),// 消息通知队列

    /*
    |--------------------------------------------------------------------------
    | Default Queue Connection Name
    |--------------------------------------------------------------------------
    |
    | Laravel's queue API supports an assortment of back-ends via a single
    | API, giving you convenient access to each back-end using the same
    | syntax for every one. Here you may define a default connection.
    |
    */

    // 'default' => env('QUEUE_DRIVER', 'sync'),
    'default' => env('QUEUE_CONNECTION', 'sync'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with Laravel. You are free to add more.
    |
    | Drivers: "sync", "database", "beanstalkd", "sqs", "redis", "null"
    |
    */

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 90,
        ],

        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host' => 'localhost',
            'queue' => 'default',
            'retry_after' => 90,
            'block_for' => 0,
        ],

        'sqs' => [
            'driver' => 'sqs',
            // 'key' => env('SQS_KEY', 'your-public-key'),
            'key' => env('AWS_ACCESS_KEY_ID'),
            // 'secret' => env('SQS_SECRET', 'your-secret-key'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
            'queue' => env('SQS_QUEUE', 'your-queue-name'),
            // 'region' => env('SQS_REGION', 'us-east-1'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'queue',// 'default',
            // 'queue' => 'default',
            'queue' => env('REDIS_QUEUE', 'default'),
            // 任务在执行了 90 秒后将会被放回队列而不是删除它
            // 设置为你认为你的任务可能会执行需要最长时间的值。
            'retry_after' => 90,
            // 'block_for' => null,
            // 表明这个驱动应该在等待任务可用时阻塞 5 秒
            // 驱动应该在将任务重新放入 Redis 数据库以及处理器轮询之前阻塞多久。
            'block_for' => 5,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control which database and table are used to store the jobs that
    | have failed. You may change them to any database / table you wish.
    |
    */

    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database'),
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'failed_jobs',
    ],

];
