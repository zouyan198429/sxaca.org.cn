<?php

namespace App\Console\Commands;

use App\Services\Multiprocess\Pcntl;
use App\Services\Signer\Signer;
use App\Services\Tool;
use Illuminate\Console\Command;

class TestPcntl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pcntl:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Tool::phpInitSet();
        $testArr  = [];
        for($i = 0; $i < 100; $i++){
            array_push($testArr, '第' . ($i + 1 ). '条任务');
           // $aaa = Tool::getRandNum(5, 200);// 生成5 到 20 的随机数
            // echo $aaa . PHP_EOL;
           //  $this->info(date('H:i:s') . ' '  . $aaa . '');
        }
//        $bar = $this->output->createProgressBar(count($testArr));
//        $bar->start();
//        $bar->advance();
//        $bar->finish();
        // 多进程执行
        Pcntl::asyTask(function (&$taskInfo){
            $randNum = Tool::getRandNum(5, 20);// 生成5 到 20 的随机数
            // $orderNum = Signer::getNewVal(4, 'testorder');
            $this->info(date('H:i:s') . ' '  .$taskInfo. ' 具体内容！--开始执行,准备模拟执行' . $randNum . '秒' );// . $orderNum
            sleep($randNum);// 模拟执行10秒
            $this->info(date('H:i:s') . ' '  .$taskInfo. ' 具体内容！--结束执行');

//            api调用生成日志号测试
//            $user_id = '';
//            $prefix = 9;
//            $backfix = '';
//            $expireNums = [];
//            $namespace = '';
//
//            // $prefix = '';
//            $userIdBack = str_pad(substr($user_id, -2), 2, '0', STR_PAD_LEFT);
//            $midFix = $userIdBack;
//            $namespace = 'API' . $userIdBack;
//            $length = 6;
//            $needNum = 1 + 2 + 8;
//            $dataFormat = 's';
//            $fixParams = [
//                'prefix' => $prefix,// 前缀[1-2位] 可填;可写业务编号等
//                'midFix' => $midFix,// 日期后中间缀[1-2位] 可填;适合用户编号里的后2位或其它等
//                'backfix' => $backfix,// 后缀[1-2位] 可填;备用
//                'expireNums' => $expireNums,// redis设置缓存 ，在两个值之间时 - 二维 [[1,20,'缓存的秒数365 * 24 * 60 * 60'], [40,50,'缓存的秒数']]
//                'needNum' => $needNum,// 需要拼接的内容 1 年 2日期 4 自定义日期格式 8 自增的序号
//                'dataFormat' => $dataFormat, // needNum 值为 4时的日期格式  'YmdHis'
//            ];
//            $log_no =  Tool::makeOrder($namespace , $fixParams, $length);
//            $this->info(date('H:i:s') . ' '  .$taskInfo. ' $log_no的值= ' . $log_no . '！--结束执行');

        }, $testArr, 5, ['operateNo' => 4]);
        $this->info('数据处理完成！');

    }
}
