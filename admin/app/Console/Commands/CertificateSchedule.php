<?php

namespace App\Console\Commands;

use App\Business\DB\QualityControl\CertificateScheduleDBBusiness;
use App\Services\Tool;
use Illuminate\Console\Command;

class CertificateSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:replaceEnter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '对能力范围已入数据库的数据，进行回车换行替换修复';

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
//        ini_set('memory_limit', '3072M');    // 临时设置最大内存占用为 3072M 3G
//        ini_set("max_execution_time", 0);
//        set_time_limit(0);   // 设置脚本最大执行时间 为0 永不过期
        // 获得所有的记录
        $dataList = CertificateScheduleDBBusiness::getDBFVFormatList(1, 1, [], true);
        $bar = $this->output->createProgressBar(count($dataList));
        $bar->start();
        foreach($dataList as $info){
            $id = $info['id'];
            $saveInfo = [];
            $method_name = $info['method_name'] ?? '';// 标准（方法）名称
            if(!empty($method_name)){
                $new_method_name = replace_enter_char($method_name, 1);
                if($method_name != $new_method_name) $saveInfo['method_name'] = $new_method_name;

            }
            $limit_range = $info['limit_range'] ?? '';// 限制范围
            if(!empty($limit_range)){
                $new_limit_range = replace_enter_char($limit_range, 1);
                if($limit_range != $new_limit_range) $saveInfo['limit_range'] = $new_limit_range;
            }
            $explain_text = $info['explain_text'] ?? '';// 说明
            if(!empty($explain_text)){
                $new_explain_text = replace_enter_char($explain_text, 1);
                if($explain_text != $new_explain_text) $saveInfo['explain_text'] = $new_explain_text;
            }
            // 更新
            if(!empty($saveInfo)) {
                $company_id = $info['company_id'];
                CertificateScheduleDBBusiness::replaceById($saveInfo, $company_id, $id, 0, 0);
            }

            $bar->advance();
        }
        $bar->finish();
        $this->info('数据处理完成！');

    }
}
