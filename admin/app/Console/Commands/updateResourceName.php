<?php

namespace App\Console\Commands;

use App\Business\DB\QualityControl\CertificateDBBusiness;
use App\Business\DB\QualityControl\CitysDBBusiness;
use App\Business\DB\QualityControl\CompanyPunishDBBusiness;
use App\Business\DB\QualityControl\CompanyScheduleDBBusiness;
use App\Business\DB\QualityControl\CompanyStatementDBBusiness;
use App\Business\DB\QualityControl\CompanySuperviseDBBusiness;
use App\Business\DB\QualityControl\ResourceDBBusiness;
use App\Business\DB\QualityControl\StaffDBBusiness;
use App\Services\DB\CommonDB;
use App\Services\File\DownFile;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use App\Services\Upload\UploadFile;
use Illuminate\Console\Command;

class updateResourceName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'market:updateResourceName';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '修改抓取陕西省市场监督管理局的企业信息的资源文件的名称';

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
        try{
            // ini_set('memory_limit','3072M');    // 临时设置最大内存占用为 3072M 3G
            Tool::phpInitSet();
            // 获得所有的企业信息
            $url = "http://113.140.67.203:1284/jgjbqk_SearchList.action";
            // $DownFile = DownFile::curlGetFileContents($url);
            $requestData = [
                'pageSize' => 80000,
                'Banb' => 0,
            ];
            $result = $this->HttpRequestApi($url, [], $requestData, 'POST');
            $total = $result['total'] ?? 0;// 总数量
            $this->line('total=' . $total);
            $totalCount = $result['totalCount'] ?? 0;// 总数量
            $this->line('totalCount=' . $totalCount);
            $data = $result['data'] ?? [];// 数据
            // $this->line('data=' . $data);
            $currentPage = $result['currentPage'] ?? 0;// 当前页
            $this->line('currentPage=' . $currentPage);
            $totalPage = $result['totalPage'] ?? 0;// 总页数
            $this->line('totalPage=' . $totalPage);

            // 开始处理数据
             $bar = $this->output->createProgressBar(count($data));
             $bar->start();
             $k = 0;
             $testOpen = false;// 是事测试， true:测试 false 非测试
            foreach ($data as $info) {
                /**
                 * {
                "id": 1331,  --id
                "LXR": "邢荣",  --联系人
                "MEMO": null,   -- 备忘录--不要
                "CZDATE": null,  -- 注册时间--不要
                "YXRQ": "2021-10-08", -- 有效日期
                "LXDH": "13720531352",  --联系电话
                "JGDZ": "陕西省西安市咸宁东路351号",  --机构地址
                "JGMC": "陕西铁诚工程试验检测技术有限公司", --机构名称
                "FZRQ": "2015-10-08",  --- 发证日期
                "ZZRDZSBH": "152701060345" --资质认定证书编号
                }
                 */
                $market_id = $info['id'];
                $this->line('market_id=' . $market_id);
                 $k++;
                 // 获取文件并保存
                 $this->saveFiles($market_id);
                $bar->advance();
                  if($testOpen && $k >= 3) break;
            }
             $bar->finish();
            $this->info('获取数据完成！');
        } catch ( \Exception $e) {
            // throws($e->getMessage());
            $this->error($e->getMessage());

        }finally {
        }


    }


    // 获取文件并保存 -- 返回已经上传成功的文件：失败了好删除
    public function saveFiles($market_id){
        // 获得所有的企业信息
        $url = "http://113.140.67.203:1283/jgfujian_getJgFuJianMap1.action";// ?sqid=" . $market_id;
        // $DownFile = DownFile::curlGetFileContents($url);
        $requestData = [
            'sortField' => 'id',
            'sortOrder' => 'desc',
            'pageIndex' => 0,
            'pageSize' => 100,
            'sqid' => $market_id,
        ];
        $result = $this->HttpRequestApi($url, [], $requestData, 'POST');
        $total = $result['total'] ?? 0;// 总数量
        $this->line('total=' . $total);
        $data = $result['data'] ?? [];
        $bar = $this->output->createProgressBar(count($data));
        $bar->start();
        foreach ($data as $info) {
            /**
             * {
            id: 3788,
            fileTitle: "能力附表",
            filePath: "2020-4-22/e3b8d4d1-c2da-461d-9a9d-474aefc1d2f7.xls",
            czr: "admin",
            type: "1",
            czDate: "2020-04-22T15:01:29",
            sqid: 1298
            }
             */

            // $isDoed = false;
            CommonDB::doTransactionFun(function() use( &$info, &$market_id, &$isDoed){

                $file_id = $info['id'];
                $file_title = $info['fileTitle'];
                $file_path = $info['filePath'];
                $file_czr = $info['czr'];
                $file_type = $info['type'];
                $file_czdate = $info['czDate'];// "2020-11-04T16:53:27"
                $file_czdate = str_replace('T', ' ', $file_czdate);
                $this->line('file_czdate=' . $file_czdate);
                $file_czdate = judgeDate($file_czdate,"Y-m-d H:i:s");
                $old_file_name = basename($file_path);// 8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf

                $suffix = DownFile::getLocalFileExt($file_path);// strtolower(pathinfo($file_path,PATHINFO_EXTENSION));
                $files_name_txt = $file_title . '.' . $suffix;// basename($file_path);// 8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf

                // 根据id，获得企业信息
//                $resourceInfo = ResourceDBBusiness::getDBFVFormatList(4, 1, ['url_frm' => $old_file_name], false, [], []);
//                if(!empty($resourceInfo)){
//                    ResourceDBBusiness::saveById(['resource_name' => $files_name_txt ], $resourceInfo['id']);
//                }
                $extParams = [
                    'sqlParams' => [
                        'orderBy' => ['id' => 'desc']
                    ]
                ];
                $this->line('文件名称=>' . $old_file_name);
                if(!empty($old_file_name)){
                    $needAddFile = false;
                    $resourceList = ResourceDBBusiness::getDBFVFormatList(1, 1, ['url_frm' => $old_file_name], false, [], $extParams);
                    if(!empty($resourceList) && count($resourceList) > 1){// 数量大于1
                        // $isDoed = true;
                        $this->error('资源内容=>' . json_encode($resourceList));
                        // ResourceDBBusiness::saveById(['resource_name' => $files_name_txt ], $resourceInfo['id']);
                        // 保存一个，其它的删除
                        $needArr = [];
                        foreach($resourceList as $temInfo){
                            $company_id = $temInfo['ower_id'] ?? 0;
                            $resource_id = $temInfo['id'];
                            $resource_ids = ',' . $resource_id . ',';
                            $this->error('resource_ids=>' . $resource_ids);
                            $resourceIdArr = [$resource_id];
                            if($company_id <= 0 ) continue;
                            // 是多余的，要进行删除
                            // 获得
                            $isUpdateName = true;
                            $priKey = $file_type . '_' . $company_id;
                            switch ($file_type)
                            {
                                case 1:// 能力附表
                                    $t_KV = ['company_id' => $company_id];
                                    if($suffix == 'pdf'){
                                        $t_KV['resource_id_pdf'] = $resource_id;

                                    }else{
                                        $t_KV['resource_id'] = $resource_id;
                                    }
                                    $t_Info = CompanyScheduleDBBusiness::getDBFVFormatList(4, 1, $t_KV, false, [], []);
                                    if(!empty($t_Info) && isset($needArr[$priKey])){
                                        // CompanyScheduleDBBusiness::saveById(['resource_name' => $files_name_txt ], $resourceInfo['id']);
                                        // 删除主表记录
                                        CompanyScheduleDBBusiness::delById($company_id, $t_Info['id'], 0,  0, ['doOperate' => 0]);
                                        $isUpdateName = false;
                                        $this->error('删除能力附表=' . $t_Info['id']);
                                    }else if(!empty($t_Info) ){
                                        $needArr[$priKey] = $company_id;
                                        $this->error('能力附表=第一条有效数据，不删除');
                                    }else if(empty($t_Info)){
                                        $needAddFile = true;
                                        $isUpdateName = false;
                                        $this->error('能力附表=数据为空，不进行操作');
                                    }
                                    break;
                                case 2:// 机构自我声明管理
                                    $t_KV = [
                                        'company_id' => $company_id,
                                        'resource_ids' => ['vals' => $resource_ids, 'valsSeparator' => '!!!']
                                    ];
                                    $t_Info = CompanyStatementDBBusiness::getDBFVFormatList(4, 1, $t_KV, false, [], []);
                                    if(!empty($t_Info)  && isset($needArr[$priKey])){
                                        // CompanyScheduleDBBusiness::saveById(['resource_name' => $files_name_txt ], $resourceInfo['id']);
                                        // 删除主表记录
                                        CompanyStatementDBBusiness::delById($company_id, $t_Info['id'], 0,  0, ['doOperate' => 0]);
                                        $isUpdateName = false;
                                        $this->error('删除机构自我声明管理=' . $t_Info['id']);
                                    }else if(!empty($t_Info) ){
                                        $needArr[$priKey] = $company_id;
                                        $this->error('自我声明管理=第一条有效数据，不删除');
                                    }else if(empty($t_Info)){
                                        $needAddFile = true;
                                        $isUpdateName = false;
                                        $this->error('自我声明管理=数据为空，不进行操作');
                                    }

                                    break;
                                case 5:// 机构处罚管理
                                    $t_KV = [
                                        'company_id' => $company_id,
                                        'resource_ids' => ['vals' => $resource_ids, 'valsSeparator' => '!!!']
                                    ];
                                    $t_Info = CompanyPunishDBBusiness::getDBFVFormatList(4, 1, $t_KV, false, [], []);
                                    if(!empty($t_Info) && isset($needArr[$priKey])){
                                        // CompanyScheduleDBBusiness::saveById(['resource_name' => $files_name_txt ], $resourceInfo['id']);
                                        // 删除主表记录
                                        CompanyPunishDBBusiness::delById($company_id, $t_Info['id'], 0,  0, ['doOperate' => 0]);
                                        $isUpdateName = false;
                                        $this->error('删除机构处罚管理=' . $t_Info['id']);
                                    }else if(!empty($t_Info) ){
                                        $needArr[$priKey] = $company_id;
                                        $this->error('机构处罚管理=第一条有效数据，不删除');
                                    }else if(empty($t_Info)){
                                        $needAddFile = true;
                                        $isUpdateName = false;
                                        $this->error('机构处罚管理=数据为空，不进行操作');
                                    }
                                    break;
                                default:
                                    break;
                            }
                            if($isUpdateName){// 是要保留的最新的一个文件，修改文件名称
                                if($temInfo['resource_name'] == $temInfo['url_frm']){
                                    ResourceDBBusiness::saveById(['resource_name' => $files_name_txt ], $temInfo['id']);
                                    $this->line('修改文件名称' . $temInfo['resource_name'] . '=>' . $files_name_txt);
                                }

                            }
                        }
                    }
                }else if(empty($resourceList)){
                    $needAddFile = true;
                }
            });
            $bar->advance();
            // if($isDoed) die('成功执行了一条数据');
        }
        $bar->finish();
        $this->info('获取并保存文件完成！');


    }


    public function HttpRequestApi($url, $params = [], $urlParams = [], $type = 'POST', $options = [])
    {
        $this->line('url=' . $url);
        $result = HttpRequest::sendHttpRequest($url, $params, $urlParams, $type, $options);

        $resultData = json_decode($result, true);
//        $code = $resultData['code'] ?? 0;
//        $msg = $resultData['msg'] ?? '返回数据错误!';
//        $data = $resultData['data'] ?? [];
//        if ($code == 0){
//            throws($msg);
//        }
//
//        return $data;
        return $resultData;
    }
}
