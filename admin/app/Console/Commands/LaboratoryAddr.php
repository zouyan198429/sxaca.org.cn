<?php

namespace App\Console\Commands;

use App\Business\DB\QualityControl\LaboratoryAddrDBBusiness;
use App\Business\DB\QualityControl\StaffDBBusiness;
use App\Services\Tool;
use Illuminate\Console\Command;

class LaboratoryAddr extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'staff:laboratoryAddr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '对已有的企业表的实验室地址，加入到企业实验室地址';

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
        //
        Tool::phpInitSet();
        // 获得所有的记录
        $dataList = StaffDBBusiness::getDBFVFormatList(1, 1, ['admin_type' => 2], true);
        $bar = $this->output->createProgressBar(count($dataList));
        $bar->start();
        foreach($dataList as $info) {
            $company_id = $info['id'];
            $laboratory_addr = $info['laboratory_addr'] ?? '';
            if(!empty($laboratory_addr)){
                // 保存企业实验室地址
                $addr_id = 0;
                LaboratoryAddrDBBusiness::createOrOpenAddr($company_id, $laboratory_addr, $addr_id, 0, 0);

            }
            $bar->advance();
        }
        $bar->finish();
        $this->info('数据处理完成！');
    }
}
