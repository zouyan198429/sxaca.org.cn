<?php

namespace App\Console\Commands;

use App\Business\DB\QualityControl\CertificateDBBusiness;
use App\Business\DB\QualityControl\CertificateScheduleDBBusiness;
use App\Services\Tool;
use Illuminate\Console\Command;

class CertificateScheduleId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:updateCertificateId';

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
        $dataList = CertificateScheduleDBBusiness::getDBFVFormatList(1, 1, ['certificate_id' => ['vals' => 0, 'excludeVals' =>['']]], true);
        $bar = $this->output->createProgressBar(count($dataList));
        $bar->start();

        $certificateNOId = [];
        foreach($dataList as $info){
            $id = $info['id'];
            $tem_certificate_id = $info['certificate_id'];
            $company_id = $info['company_id'];
            $certificate_no = $info['certificate_no'];
            if(empty($certificate_no) || $tem_certificate_id > 0){
                $bar->advance();
                continue;
            }
            $certificateId = $certificateNOId[$certificate_no] ?? 0;
            if(!isset($certificateNOId[$certificate_no]) || $certificateId <= 0){
                $certificateInfo = CertificateDBBusiness::getDBFVFormatList(4, 1, ['company_id' => $company_id, 'certificate_no' => $certificate_no]);
                if(empty($certificateInfo)){
                    $bar->advance();
                    continue;
                }
                $certificateId = $certificateInfo['id'];
                $certificateNOId[$certificate_no] = $certificateId;
            }

            // 更新
            if($certificateId > 0 ) {
                $saveInfo = [
                    'certificate_id' => $certificateId,
                ];
                CertificateScheduleDBBusiness::replaceById($saveInfo, $company_id, $id, 0, 0);
            }

            $bar->advance();
        }
        $bar->finish();
        $this->info('数据处理完成！');

    }
}
