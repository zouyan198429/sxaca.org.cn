<?php

namespace App\Console;

use App\Business\API\RunBuy\CityAPIBusiness;
use App\Business\DB\QualityControl\AbilityJoinItemsResultsDBBusiness;
use App\Business\DB\QualityControl\AbilitysDBBusiness;
use App\Business\DB\QualityControl\AlipayAuthTokenDBBusiness;
use App\Business\DB\QualityControl\CompanyGradeConfigDBBusiness;
use App\Business\DB\QualityControl\OrderPayDBBusiness;
use App\Business\DB\QualityControl\PaymentProjectDBBusiness;
use App\Business\DB\QualityControl\PaymentRecordDBBusiness;
use App\Business\DB\QualityControl\VodOrdersDBBusiness;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->call(function () {
            // CityAPIBusiness::autoCancelOrdes();// 跑城市订单过期未接单自动关闭脚本--每一分钟跑一次
            AbilitysDBBusiness::autoBeginJoin();// 未开始的，时间一到进入到开始报名
            AbilitysDBBusiness::autoBeginDoing();// 开始报名的，时间一到结束，进入到进行中
            AbilitysDBBusiness::autoPublishDoing();// 指定时间公布的，时间一到结束，进行公布--每一分钟跑一次
            // AbilityJoinItemsResultsDBBusiness::autosSubmitOverTime();// 如果企业没有按时提交数据，则自动判定结果为不满意--上传数据超时
            CompanyGradeConfigDBBusiness::autoGradeConfig();// 对到时间的会员等级进行处理
            OrderPayDBBusiness::autoRunWXPayResult();//微信-- 脚本去跑页面生成收款码，用户扫码付款的脚本--查询并修改订单状态
            VodOrdersDBBusiness::autoFinish();// 对到期的进行完成处理--每一分钟跑一次
            PaymentProjectDBBusiness::autoPayStart();// 对待收费的进行开始收费处理--每一分钟跑一次
            PaymentProjectDBBusiness::autoPayEnd();// 对开始收费的进行结束收费处理--每一分钟跑一次
            PaymentRecordDBBusiness::autoCancel();// 对 超时的 未付款的进行作废处理--每-分钟跑一次
            PaymentRecordDBBusiness::autoFinish();// 对到期的进行完成处理--每一分钟跑一次
        })->everyMinute();// 每分钟执行一次 锁会在 5 分钟后失效->withoutOverlapping(5)[会失败] ;  ->appendOutputTo($filePath)

        $schedule->call(function () {
            VodOrdersDBBusiness::autoCancel();// 对7天未付款的进行作废处理--每天跑一次
            AlipayAuthTokenDBBusiness::autoRefeshAuth();// 对360天未刷新的进行刷新处理--每天跑一次
        })->dailyAt('2:30');// 每天指定时间执行一次

        // Horizon 包含一个 Metrics 仪表盘，它可以提供任务和队列等待时间和吞吐量信息，
        // 为了填充此仪表盘，你需要配置应用的 snapshot 每五分钟运行一次 Horizon 的 scheduler 每五分钟运行一次 Horizon 的
        $schedule->command('horizon:snapshot')->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
