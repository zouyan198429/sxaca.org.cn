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

class GetMarketCompanys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'market:getData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '抓取陕西省市场监督管理局的企业信息及资源文件';

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
            $addFiels = [];
            $errCompanyArr = [];// 企业名称已存在的错误，直接先记录，最后显示一下，人工来排查
            $errUserExistArr = [];// 用户名已存在的错误，直接先记录，最后显示一下，人工来排查
             $bar = $this->output->createProgressBar(count($data));
             $bar->start();
             $k = 0;
             $testOpen = false;// 是事测试， true:测试 false 非测试
            foreach ($data as $info) {
                $addFiels = [];// 每一次清空文件
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
                $company_name = $info['JGMC'];
                $this->line('market_id=' . $market_id);
                try{

                    // 根据id，获得企业信息
                    $companyInfo = StaffDBBusiness::getDBFVFormatList(4, 1, ['market_id' => $market_id, 'admin_type' => 2], false, [], []);
                     if(empty($companyInfo)){// 企业信息不存在，才处理
                         $k++;


                         CommonDB::doTransactionFun(function() use( &$info, &$market_id, &$addFiels){

                             $company_id = 0;// 企业 id
                             $isAddNew = false;// 企业是否是新加 true:新加 ； false:已存在
                             // 新加或修改企业信息
                             $this->saveCompany($info, $company_id, $isAddNew);
                             if($company_id > 0){
                                 $this->line('company_id=' . $company_id);
                                 // 获取文件并保存
                                 $this->saveFiles($market_id, $company_id, $addFiels, $isAddNew);
                                 // 监督检查信息管理
                                 $this->saveSupervise($market_id, $company_id);
                             }else{

                                 $this->error('company_id=' . $company_id);
                             }

                         });

                    }
                } catch ( \Exception $e) {
                    // throws($e->getMessage());
                    $errStr = $e->getMessage();
                    $this->error($errStr);
                    if($errStr != '单位名称已存在！' && $errStr != '用户名已存在！'){
                        $this->error('有错误，停止运行！');
                        throws($errStr);
                        break;
                    }
                    if($errStr == '单位名称已存在！') array_push($errCompanyArr, $company_name);
                    if($errStr == '用户名已存在！') array_push($errUserExistArr, $company_name);
                    // 删除发生错误时，上传的文件 TODO
                    if(!empty($addFiels)){
                        Tool::resourceDelFile($addFiels);
                        $this->error('保存出错，删除文件' . json_encode($addFiels));
                        $addFiels = [];
                    }

                }finally {
                    $bar->advance();
                }
                  if($testOpen && $k >= 3) break;
            }
             $bar->finish();
            $this->info('获取数据完成！');
        } catch ( \Exception $e) {
            // throws($e->getMessage());
            $this->error($e->getMessage());
            // 删除发生错误时，上传的文件 TODO
            if(!empty($addFiels)){
                Tool::resourceDelFile($addFiels);
                $this->error('保存出错，删除文件' . json_encode($addFiels));
                $addFiels = [];
            }
        }finally {
            if(!empty($errCompanyArr)) $this->error('企业名称已存在的错误：' . json_encode($errCompanyArr));
            if(!empty($errCompanyArr)) $this->error('用户名已存在的错误：' . json_encode($errUserExistArr));
        }


    }

    // 保存企业信息及证书表
    public function saveCompany($info, &$company_id, &$isAddNew){
        $market_id = $info['id'];
        $company_name = $info['JGMC'];
        $company_certificate_no = $info['ZZRDZSBH'];
        if(strpos($company_certificate_no, '、') !== false){// 存在--- 162721110151、172721110020、（2017）（陕）质监认字002号, 陕西省特种设备检验检测研究院
            $noTemArr = explode($company_certificate_no, '、');
            $company_certificate_no = $noTemArr[0] ?? '';
            $info['ZZRDZSBH'] = $company_certificate_no;
        }

        // 通过企业名称及证书号，获得企业信息
        // 'company_name' => $company_name,
        if(!empty($company_certificate_no)){

            $extParams = [
                'sqlParams' => [
                    'orderBy' => ['open_status' => 'desc', 'id' => 'desc'],// 审核通过的优先拿到
                ]
            ];
            $companyInfo = StaffDBBusiness::getDBFVFormatList(4, 1, [
                'company_certificate_no' => $company_certificate_no,
                'admin_type' => 2,
                'open_status' => [1,2]
            ], false, [], $extParams);
        }
        // 获得城市KV值
        $cityKV = CitysDBBusiness::getKeyVals(['key' => 'id', 'val' => 'city_name']);
        CommonDB::doTransactionFun(function() use( &$info, &$market_id, &$companyInfo, &$company_name, &$company_certificate_no, &$cityKV, &$company_id, &$isAddNew){

            $company_id = $companyInfo['id'] ?? 0;// 企业 id

            $company_contact_name = $info['LXR'];
            $company_contact_mobile = $info['LXDH'];
            $ratify_date = $info['FZRQ'];
            $valid_date = $info['YXRQ'];
            $laboratory_addr = $info['JGDZ'];

            $certificate_info = [
                // 'company_id' => $saveData['company_id'],
                'certificate_no' => $company_certificate_no,
                'ratify_date' => $ratify_date,
                'valid_date' => $valid_date,
                'addr' => $laboratory_addr,
            ];
            // 保存企业信息
            $companyInfoData = [
                'market_id' => $market_id,
                // 'company_contact_name' => $company_contact_name,
                // 'company_contact_mobile' => $company_contact_mobile,
                // 'company_certificate_no' => $company_certificate_no,
                'ratify_date' => $ratify_date,
                'valid_date' => $valid_date,
    //                             'company_name' => $company_name,
    //                             'laboratory_addr' => $laboratory_addr,
            ];
            // 企业不存在时，要加入信息
            $companyInfoExtend = [
                'company_contact_name' => $company_contact_name,
                'company_contact_mobile' => $company_contact_mobile,
                'company_certificate_no' => $company_certificate_no,
                'company_name' => $company_name,
                'laboratory_addr' => $laboratory_addr,

                'admin_type' => 2,
                'admin_username' => $company_contact_mobile,
                'admin_password' => substr($company_contact_mobile, -6, 6),
                'is_perfect' => 2,// 是否完善资料1待完善2已完善
                'issuper' => 2,// 是否超级帐户2否1是
                'open_status' => 2,// 审核状态1待审核2审核通过4审核不通过
                'account_status' => 1,// 状态 1正常 2冻结
                'mobile' => $company_contact_mobile,
                'addr' => $laboratory_addr,// 通讯地址
                // 'city_id' => $aaaa,// 所在城市
            ];
            if($company_id > 0) {// 我们有企业信息
                $isAddNew = false;// 已存在
                $companyInfoExtend['is_import'] = 4;
                // 如果我们是空数据，则以他们的为主
                $tem_company_contact_name = $companyInfo['company_contact_name'] ?? '';
                $tem_company_contact_mobile = $companyInfo['company_contact_mobile'] ?? '';
                $tem_company_certificate_no = $companyInfo['company_certificate_no'] ?? '';
                $tem_company_name = $companyInfo['company_name'] ?? '';
                $tem_laboratory_addr = $companyInfo['laboratory_addr'] ?? '';
                if (empty($tem_company_contact_name)) {//  || $tem_company_contact_name != $company_contact_name
                    $companyInfoData['company_contact_name'] = $company_contact_name;
                }
                if (empty($tem_company_contact_mobile)) {//  || $tem_company_contact_mobile != $company_contact_mobile
                    $companyInfoData['company_contact_mobile'] = $company_contact_mobile;
                }
                if (empty($tem_company_certificate_no)) {//  || $tem_company_certificate_no != $company_certificate_no
                    $companyInfoData['company_certificate_no'] = $company_certificate_no;
                }
                if (empty($tem_company_name)) {//  || $tem_company_name != $company_name
                    $companyInfoData['company_name'] = $company_name;
                }
                if (empty($tem_laboratory_addr)) {//  || $tem_laboratory_addr != $laboratory_addr
                    $companyInfoData['laboratory_addr'] = $laboratory_addr;
                }
            }else{// 我们没有企业信息
                $isAddNew = true;// 新加
                $city_id = 0;
                foreach($cityKV as $t_city_id => $t_city_name){
                    // 公司名称或地址中包含到城市的
                    if (strpos($company_name, $t_city_name) !== false || strpos($laboratory_addr, $t_city_name) !== false) {
                        $city_id = $t_city_id;
                        break;
                    }
                }

                $companyInfoExtend['is_import'] = 1;
                $companyInfoExtend['city_id'] = $city_id;
                $this->line('laboratory_addr=' . $laboratory_addr);
                $this->line('company_name=' . $company_name);
                $this->line('city_id=' . $city_id);

                $companyInfoData = array_merge($companyInfoData, $companyInfoExtend);
            }

            StaffDBBusiness::replaceById($companyInfoData, 0, $company_id, 0, 0);

            // 保存 证书表  certificate
            $certificateObj = null ;
            $searchConditon = [
                'company_id' => $company_id,
                // 'certificate_no' => $certificate_info['certificate_no'],// 一个企业只能有一个证书，所以去掉这个字段
            ];
            CertificateDBBusiness::updateOrCreate($certificateObj, $searchConditon, $certificate_info);

        });
    }


    // 获取文件并保存 -- 返回已经上传成功的文件：失败了好删除
    public function saveFiles($market_id, $company_id, &$addFiels, $isAddNew){
        // $addFiels = [];
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

            CommonDB::doTransactionFun(function() use( &$info, &$market_id, &$company_id, &$addFiels, &$isAddNew){

                $file_id = $info['id'];
                $file_title = $info['fileTitle'];
                $file_path = $info['filePath'];
                $file_czr = $info['czr'];
                $file_type = $info['type'];
                $file_czdate = $info['czDate'];// "2020-11-04T16:53:27"
                $file_czdate = str_replace('T', ' ', $file_czdate);
                $this->line('file_czdate=' . $file_czdate);
                $file_czdate = judgeDate($file_czdate,"Y-m-d H:i:s");

                $suffix = DownFile::getLocalFileExt($file_path);// strtolower(pathinfo($file_path,PATHINFO_EXTENSION));
                $files_name_txt = $file_title . '.' . $suffix;// basename($file_path);// 8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf
                // 文件保存
                $fileArr = $this->saveFile($file_title, $file_path, $company_id);
                $files_names = $fileArr['files_names'];// /resource/company/0/down/2020/11/04/8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf
                $full_names = $fileArr['full_names'];// /srv/www/quality_control/quality_control/admin/public/resource/company/0/down/2020/11/04/8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf

                $this->line('files_names=' . $files_names);
                $this->line('full_names=' . $full_names);
                array_push($addFiels, ['resource_url' => $files_names]);
                $file_size = filesize($full_names);
                $mime_type = DownFile::getLocalFileMIME($full_names);
                // 保存图片资源--到数据库
                // 根据扩展名，重新获得文件的操作类型
                $resourceConfig = UploadFile::getResourceConfig($suffix);
                // if(empty($resourceConfig)) throws('请选择正确的文件！');
                $resourceType = $resourceConfig['resource_type'] ?? 0;
                $this->line('resource_type=' . $resourceType);

                $saveData =[
                    'resource_name' => $files_name_txt,
                    'resource_type' => $resourceType,
                    'resource_url' => $files_names,
                    'resource_size' => $file_size,
                    'resource_size_format' => Tool::formatBytesSize($file_size, 2),
                    'resource_ext' => $suffix,
                    'resource_mime_type' => $mime_type,
                    'ower_type' => 2,// $admin_type
                    'ower_id' => $company_id,
                    'resource_note' => '',
                ];

                if($file_czdate !== false){
                    $saveData['created_at'] = $file_czdate;
                    $saveData['updated_at'] = $file_czdate;
                }

                $resource_id = 0;
                ResourceDBBusiness::replaceById($saveData, $company_id, $resource_id, 0, 0);
                $this->line('resource_id=' . $resource_id);
                if($resource_id > 0){
                    $resource_ids = ',' . $resource_id . ',';
                    $resourceIdArr = [$resource_id];
                    $this->line('file_type=' . $file_type);
                    switch ($file_type)
                    {
                        case 1:// 能力附表

                            $saveData = [
                                'company_id' => $company_id,
                                // 'type_id' => 0,
                                //             'resource_id' => $resource_id[0] ?? 0,// word资源的id
                                //            'resource_ids' => $resource_ids,// word资源id串(逗号分隔-未尾逗号结束)
//                                'resource_id_pdf' => $resource_id_pdf[0] ?? 0,// pdf资源的id
//                                'resource_ids_pdf' => $resource_ids_pdf,// pdf资源id串(逗号分隔-未尾逗号结束)
                                'resourceIds' => $resourceIdArr,// 此下标为图片资源关系
                            ];
                            if($suffix == 'pdf'){
                                $saveData = array_merge($saveData, [
                                    'type_id' => 0,
                                    'resource_id_pdf' => $resourceIdArr[0] ?? 0,// pdf资源的id
                                    'resource_ids_pdf' => $resource_ids,// pdf资源id串(逗号分隔-未尾逗号结束)
                                ]);
                            }else{
                                $saveData = array_merge($saveData, [
                                    'type_id' => 0,
                                    'resource_id' => $resourceIdArr[0] ?? 0,// pdf资源的id
                                    'resource_ids' => $resource_ids,// pdf资源id串(逗号分隔-未尾逗号结束)
                                ]);
                            }
                            if(!$isAddNew) $saveData['is_import'] = 1;

                            if($file_czdate !== false){
                                $saveData['created_at'] = $file_czdate;
                                $saveData['updated_at'] = $file_czdate;
                            }

                            $record_id = 0;
                            $this->line('saveData record_id=' . json_encode($saveData));
                            CompanyScheduleDBBusiness::replaceByIdNew($saveData, $company_id, $record_id, 0, 0);

                            $this->line('CompanySchedule record_id=' . $record_id);
                            break;
                        case 2:// 机构自我声明管理

                            $saveData = [
                                'company_id' => $company_id,
                                'resource_name' => $file_title,
                                // 'resource_id' => $resourceIdArr[0] ?? 0,// 文件资源的id
                                'resource_ids' => $resource_ids,// 资源id串(逗号分隔-未尾逗号结束)
                                'resourceIds' => $resourceIdArr,// 此下标为资源关系
                            ];
                            if($file_czdate !== false){
                                $saveData['created_at'] = $file_czdate;
                                $saveData['updated_at'] = $file_czdate;
                            }

                            $record_id = 0;
                            CompanyStatementDBBusiness::replaceById($saveData, $company_id, $record_id, 0, 0);

                            $this->line('CompanyStatement record_id=' . $record_id);
                            break;
                        case 5:// 机构处罚管理
                            $saveData = [
                                'company_id' => $company_id,
                                'resource_name' => $file_title,
                                // 'resource_id' => $resourceIdArr[0] ?? 0,// 文件资源的id
                                'resource_ids' => $resource_ids,// 资源id串(逗号分隔-未尾逗号结束)
                                'resourceIds' => $resourceIdArr,// 此下标为资源关系
                            ];

                            if($file_czdate !== false){
                                $saveData['created_at'] = $file_czdate;
                                $saveData['updated_at'] = $file_czdate;
                            }

                            $record_id = 0;
                            CompanyPunishDBBusiness::replaceById($saveData, $company_id, $record_id, 0, 0);

                            $this->line('CompanyPunish record_id=' . $record_id);
                            break;
                        default:
                            break;
                    }
                }

            });
            $bar->advance();
        }
        $bar->finish();
        $this->info('获取并保存文件完成！');

        // return $addFiels;

    }

    // 保存单个远程文件到本机

    /**
     * [publicPath] => /srv/www/quality_control/quality_control/admin/public
    [savePath] => /resource/company/0/down/2020/11/04/
    [saveName] => 8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf
    [files_names] => /resource/company/0/down/2020/11/04/8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf
    [web_url] => http://qualitycontrol.admin.cunwo.net/resource/company/0/down/2020/11/04/8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf
    [full_names] => /srv/www/quality_control/quality_control/admin/public/resource/company/0/down/2020/11/04/8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf
     */
    public function saveFile($fileName, $filePath, $company_id){
        // $fileName = '2020年4月法人变更自我声明';// '能力附表';
        // $filePath = '2020-4-26/8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf';// '2020-4-22/e3b8d4d1-c2da-461d-9a9d-474aefc1d2f7.xls';
        $fileUrl = 'http://113.140.67.203:1283/jsp/Jyjc/ZzxxDown.jsp?fileName=' . $fileName . '&filePath=' . $filePath;
        // $files_names = '8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf';
        $files_names = basename($filePath);// 8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf
        return DownFile::getUrlFileToLocal($fileUrl, $company_id,2, '', $files_names);
    }
    // 监督检查信息管理
    public function saveSupervise($market_id, $company_id){

        // 获得所有的企业信息
        $url = "http://113.140.67.203:1283/jgjbqk_getJbqkList.action?id=" . $market_id;
        // $DownFile = DownFile::curlGetFileContents($url);
        $requestData = [
//            'sortField' => 'id',
//            'sortOrder' => 'desc',
//            'pageIndex' => 0,
//            'pageSize' => 100,
        ];
        $result = $this->HttpRequestApi($url, [], $requestData, 'GET');
        $content = $result[0]['MEMO'] ?? '';// 内容
        if(empty($content)) return false;

        $superviseInfo = CompanySuperviseDBBusiness::getDBFVFormatList(4, 1, [
            'company_id' => $company_id,
        ], false, []);

        $record_id = $superviseInfo['id'] ?? 0;
        if(!empty($superviseInfo)){
            $content = $superviseInfo['company_content'] . '<hr/>' . $content;
        }
        CommonDB::doTransactionFun(function() use(&$company_id, &$content, &$record_id){

            $saveData = [
                'company_id' => $company_id,
                'company_content' => $content,
            ];
            CompanySuperviseDBBusiness::replaceById($saveData, $company_id, $record_id, 0, 0);

        });
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
