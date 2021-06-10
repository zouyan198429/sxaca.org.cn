<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPICertificateScheduleBusiness;
use App\Business\Controller\API\QualityControl\CTAPISmsModuleParamsCommonBusiness;
use App\Business\Controller\API\QualityControl\CTAPISmsTemplateBusiness;
use App\Business\Controller\API\QualityControl\CTAPIStaffBusiness;
// use App\Business\Controller\API\RunBuy\CTAPITablesBusiness;
use App\Business\Controller\API\QualityControl\CTAPIVodTypeBusiness;
use App\Business\DB\QualityControl\AbilityCodeDBBusiness;
use App\Business\DB\QualityControl\AbilityJoinItemsDBBusiness;
use App\Business\DB\QualityControl\AbilityJoinItemsResultsDBBusiness;
use App\Business\DB\QualityControl\AbilitysDBBusiness;
use App\Business\DB\QualityControl\CertificateScheduleDBBusiness;
use App\Business\DB\QualityControl\CompanyGradeConfigDBBusiness;
use App\Business\DB\QualityControl\CompanyStatementDBBusiness;
use App\Business\DB\QualityControl\CompanySubjectDBBusiness;
use App\Business\DB\QualityControl\InvoicesDBBusiness;
use App\Business\DB\QualityControl\OrderPayDBBusiness;
use App\Business\DB\QualityControl\StaffDBBusiness;
use App\Http\Controllers\WorksController;
use App\Models\QualityControl\CompanySubject;
use App\Models\QualityControl\CompanySubjectAnswer;
use App\Models\QualityControl\CompanySubjectHistory;
use App\Models\QualityControl\Staff;
use App\Notifications\SMSSendNotification;
use App\Notifications\SMSVerificationCodeNotification;
use App\Services\alipaySdk\AlipayBillAPI;
use App\Services\alipaySdk\AlipayPayAPI;
use App\Services\alipaySdk\alipayTest;
use App\Services\alipaySdk\AlipayToolAPI;
use App\Services\Captcha\CaptchaCode;
use App\Services\Code\QRCode;
use App\Services\DB\CommonDB;
use App\Services\File\DownFile;
use App\Services\Request\API\HttpRequest;
use App\Services\Request\CommonRequest;
use App\Services\SessionCustom\SessionCustom;
use App\Services\Tool;
use App\Services\Wechat\BasicWechat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class IndexController extends BasicController
{
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

    public function getSign(){

    }

    public function testAPI(Request $request){

        /**
         *
         *  服务端接到这个请求：
         *  1 先验证sign签名是否合理，证明请求参数没有被中途篡改
         *  2 再验证timestamp是否过期，证明请求是在最近60s被发出的
         *  3 最后验证nonce是否已经有了，证明这个请求不是60s内的重放请求
         *
         */
        $params = CommonRequest::getParamsByUbound($request, 2, false, [], []);
        $res = CommonRequest::apiJudgeSign($request, $params, 1,  '111222333');
        if(is_string($res)) ajaxDataArr(0, null, $res);
        return ajaxDataArr(1, $res, '');
    }

    public static function aaa($isLoop = true, $aaa = 1){
        $numargs = func_get_args ();
        pr($numargs);
        echo '--aaa---' . __FUNCTION__ . '<br/>';
        if($isLoop) static::{__FUNCTION__}(false);
    }

    public function test(Request $request){
$aaa = <<<str
读书
<br/>写字
<br/>打球
str;
        pr(replace_enter_char($aaa,1));

//        $val = '1+1>=[[{0<|=|>1<|=|>2}]]';
//        $input_val = str_replace([CompanySubject::SUBJECT_TYPE_BIG_SPLIT_MID],['　或　'], $val);
//        die('<span style="text-decoration:underline;">' . $input_val . '</span>');
//        $paramArr = [
//            'name' => '小明',
//            'age' => '25',
//            'sex' => '1',
//        ];
//        $paramArr = array_merge($paramArr, [
//            'appid' => '123456789',
//            'noncestr' => '6efc66f0f1d2645fc9d23be3a647a3d7',
//            'timestamp' => '1605480322',
//
//        ]);
//        ksort($paramArr);
//        $cc = http_build_query($paramArr);
//        pr($cc);
//        $strA = sha1('111222333' . urldecode(http_build_query($paramArr)) . '111222333');
////        $kvArr = [];
////        foreach($paramArr as $k => $v){
////            array_push($kvArr, $k . '=' . $v);
////        }
////        $strA = implode('&', $kvArr);
//        pr($strA);
        $splitBegin = CompanySubject::SUBJECT_TYPE_BIG_SPLIT_BEGIN ;
        $splitEnd = CompanySubject::SUBJECT_TYPE_BIG_SPLIT_END ;
        $bracketsBegin = CompanySubject::SUBJECT_TYPE_BRACKETS_SPLIT_BEGIN ;
        $bracketsEnd = CompanySubject::SUBJECT_TYPE_BRACKETS_SPLIT_END ;
        $bracketsUnderLineBegin = CompanySubject::SUBJECT_TYPE_UNDERLINE_BRACKETS_SPLIT_BEGIN ;
        $bracketsUnderLineEnd = CompanySubject::SUBJECT_TYPE_UNDERLINE_BRACKETS_SPLIT_END ;
        // $title = '墙面装饰分为[[{15}]]、[[(10)]]和[([{8}])]，不同的墙面有着不同的装饰效果和功能。';
        $title = '三原色是[[{红色}]]、[[(绿色)]]、[([{蓝色}])]。';
        $reArr = CompanySubjectDBBusiness::formatQuoteTitle($title, 1 | 8, 32, true);
        pr($reArr);
        $bb = Tool::getLabelArr($title, $bracketsUnderLineBegin, $bracketsUnderLineEnd);
//        $aa = '墙面装饰分为[[{51}]]、[[(52)]]和[([{53}])]，不同的{墙面}有着不同的装饰效果和功能。';
//        $bb = Tool::getLabelArr($aa, '{', '}');
        pr($bb);
        $operate_staff_id = 1;
        $operate_staff_id_history = StaffDBBusiness::getStaffHistoryId($operate_staff_id);
//        $key = "quality_control:dbfields:QualityControl:staff";
//        $keyRedisPre = "quality_control:dbfields:QualityControl:";
//        $table_name = "staff_history";// "staff";
//        $operateRedis = 1;
//        $tableFields = Tool::getRedis($keyRedisPre . $table_name, $operateRedis);
//        vd($tableFields);
        vd($operate_staff_id_history);
//        vd(CompanySubjectHistory::aaa());
        $openStatusArr = [
            '1' => '开通',
            '2' => '关闭',
            '3' => '作废',
        ];
        $aaa = Tool::getBitVals($openStatusArr,  0 | 1, ',');
        vd($aaa);
        $aaa = Tool::createRandomStr(5);
        pr($aaa);
        for($i = 0; $i < 10; $i++){
            $aaa = Tool::getRandNum(5, 200);// 生成5 到 20 的随机数
            echo $aaa . PHP_EOL;
        }
        pr(000);
//        $file_path = '202011091819227042d1f0f7cb0f39.xlsx';
//        $suffix = DownFile::getLocalFileExt($file_path);
//        pr($suffix);
//        $url = '202011091819227042d1f0f7cb0f39.xlsx';// 'http://qualitycontrol.admin.cunwo.net/resource/company/45/excel/2020/11/09/202011091819227042d1f0f7cb0f39.xlsx';
//        $url_file_name = basename($url);// basename() 函数返回路径中的文件名部分。
//        $url_file_extension = pathinfo($url_file_name,PATHINFO_EXTENSION);
//        pr($url_file_extension);

        $filePath = 'http://qualitycontrol.admin.cunwo.net/resource/company/45/excel/2020/11/09/202011091819227042d1f0f7cb0f39.xlsx';
        $fileContentBit = DownFile::getFileContent($filePath, 2, 3);
        $fileContent = base64_encode($fileContentBit);
        // pr($fileContent);

        // $fileArr = DownFile::saveFileContentToLocal($fileContent, '202011091819227042d1f0f7cb0f39.xlsx',  0, 2,1, 'test', '' );
        $fileArr = CertificateScheduleDBBusiness::saveContentFile('202011091819227042d1f0f7cb0f39.xlsx', $fileContent, '202011091819227042d1f0f7cb0f39.xlsx', 0);
        pr($fileArr);
        $aaa = sha1("wbxhkCAZG3I3aZgbV2wu3MOSrkxzi1aLaddr=西安市灞桥区灞桥街道豁口村五组3&appid=1bIgiCS2gHuLfYShfiOKpbkY3sAR87wX&certificate_no=182701065097&company_name=陕西灿宁公路工程试验检测有限公司&contact_mobile=18229002378&contact_name=张曼斐2&hidden_option=0&noncestr=b6d813d3121839ce7f7abe33a6dbd396&ratify_date=2021-04-27&timestamp=1619405497&valid_date=2021-04-30wbxhkCAZG3I3aZgbV2wu3MOSrkxzi1aL");
        vd($aaa);
        // 获得推荐课程
        $staff_list = CTAPIStaffBusiness::getFVFormatList( $request,  $this, 2, 1
            ,  ['admin_type' => 1], false,[]
            , array_merge([
                'sqlParams' => [
                    // 'where' => [['is_perfect', 2], ['open_status', 2], ['account_status', 1]],
                    'orderBy' => CTAPIStaffBusiness::$orderBy
                ]
            ], []));
        pr($staff_list);
        $staffInfo = CTAPIStaffBusiness::getListKV($request, $this, ['key' => 'id', 'val' => 'real_name'], [
            'sqlParams' => ['where' => [['admin_type', 1]]], 'pagesize' => 2, 'page' => 2
        ]);
        pr($staffInfo);
        pr(111);
        // 浏览数自增
        $modelName = CTAPIVodTypeBusiness::modelNameFormat($request, $this, '');
        $saveData = [
           [
               'Model_name' => $modelName,// 'model名称',
               'primaryVal' => '8',// '主键字段值',
               'incDecType' => 'inc',// '增减类型 inc 增 ;dec 减[默认]',
               'incDecField' => 'sort_num',// '增减字段',
               'incDecVal' => 1,//'增减值',
               'modifFields' => [],// '修改的其它字段 -没有，则传空数组',
            ],
        ];
        $result = CTAPIVodTypeBusiness::bathIncDecByArrCTL($request, $this, '', $saveData, 1);
        pr($result);
//        $incQueryParams = Tool::getParamQuery(['id' => 5]);
//        $result = CTAPIVodTypeBusiness::incDecByQueyCTL($request, $this, '', 'inc',  'sort_num', 2, [], $incQueryParams, 1, []);
//        pr($result);
        $saveData = [];
        for($i = 0; $i < 10; $i++){
            array_push($saveData, [
                'id' => 0,
                'name'=> 'name_' . $i,
            ]);
        }
        // 调用新加或修改接口
        $apiParams = [
            'saveData' => $saveData,
//
//            'company_id' => $company_id,
//            'id' => $id,
//            'operate_staff_id' => $user_id,
//            'modifAddOprate' => ($modifAddOprate == true) ? 1 : 0 ,// 0,

//            'fixParams' => '',
//            'primaryKeyValType' => 2,// 256,


//            'valNums' => 1,// 3,
//            'maxValNum' => 1,
        ];
        $methodName = 'setDataPrimaryKeyVal';// 'getDBMultiSignerArr';// 'getNextPrimaryKeyVal';// 'replaceById';
        $company_id = $this->user_id;
        $result = CTAPIStaffBusiness::exeDBBusinessMethodCT($request, $this, '',  $methodName, $apiParams, $company_id, 1);
        pr($result);
        pr(( date('Hi')));
        static::aaa(true);
        die();
        $vodTypeIds = [1,2];
        throws('记录【' . implode(',', $vodTypeIds) . '】已有点播课程，不可删除！<br/>您可以删除课程后再操作！');

        $v['ratify_date'] = "2021/03/05";
        if(isset($v['ratify_date']) && strpos($v['ratify_date'], '/') !== false){
            $v['ratify_date'] = str_replace(['/'], ['-'], $v['ratify_date']);
        }
        pr($v);
        $v['ratify_date'] = "2021年03月05";
        if(isset($v['ratify_date']) && strpos($v['ratify_date'], '年') !== false){
            $v['ratify_date'] = str_replace(['年', '月', '日'], ['-', '-', ''], $v['ratify_date']);
        }
        pr($v);
        pr(1);



        // 平台公钥加密数据
//        $str = '我是邹燕';
//        $WechatpaySerial = '';
//        $encryptStr = BasicWechat::getEncryptPublic(BasicWechat::$apiConfig, $str, $WechatpaySerial, function($certificates){
//            print_r($certificates);
//        } );
//        echo $WechatpaySerial;
//        vd($encryptStr);

//        $effective_time = '2021-02-08 15:41:42';
//        $expire_time = '2021-02-08 15:50:42';
//        $keySerial = [
//            'effective_time' => $effective_time,// 生效时间 2021-01-04 13:19:42
//            'expire_time' => $expire_time,// 过期时间 2026-01-03 13:19:42
//            // 'serial_no' => $serial_no, // 证书序列号 35B4105DBFB51A3845213F8FF5F79413A6E48304
//        ];
//        $aa = BasicWechat::judgeKeySerial($keySerial);
//        vd($aa);

//        $dateTime = '2021-01-04T13:19:42+08:00';
//        $formatDateTime = BasicWechat::getFormatDateTime($dateTime);
//        // die($formatDateTime);
//        $dateFormatTime = judgeDate($formatDateTime,"Y-m-d H:i:s");
//        vd($dateFormatTime);


        // 加密解密测试
//        $str = '我是邹燕。也是邹国松';
//        // 公钥加密
//        $encryptStr = BasicWechat::getEncryptPublicFile($str, BasicWechat::$apiConfig['apiclient_cert_path'], BasicWechat::$apiConfig['updated_at'], BasicWechat::$apiConfig['apiv3_secret']);
//        // 私钥解密
//        $decryptStr = BasicWechat::getDencryptPrivateFile($encryptStr, BasicWechat::$apiConfig['apiclient_key_path'], BasicWechat::$apiConfig['updated_at'], BasicWechat::$apiConfig['apiv3_secret']);
//        pr($decryptStr);

        // 获得私钥内容对象
        // $apiConfig = BasicWechat::$apiConfig;
        // $fileContent = BasicWechat::getPrivateKeyContent($apiConfig['apiclient_key_path'], $apiConfig['updated_at'], $apiConfig['apiv3_secret']);
        // vd($fileContent);



        //生成V3请求 header认证信息
        // $url="https://api.mch.weixin.qq.com/v3/certificates";
        // $header = BasicWechat::createAuthorization(BasicWechat::$apiConfig, $url );
        // vd($header);
        // $requestWechatpaySerial = '';
        // $result = BasicWechat::getCertificates(BasicWechat::$apiConfig, $requestWechatpaySerial, '', []);//
        // print_r($result);
        // vd($requestWechatpaySerial);
        // BasicWechat::aaa(BasicWechat::$apiConfig);
        // pr(1222);

        $alipayConfig = config('public.alipayConfig.APIConfig');
        // alipayTest::test($alipayConfig);
        $app_auth_token = "202101BBa54ffe4585f54301a73361d3c9fb8A42";

        $apiParams = [
            'bill_type' => 'trade',
            'bill_date' => '2021-01-30',
        ];
        // $alipayConfig['appId'] = '2021002125631695';

        $url = AlipayBillAPI::getDownloadUrlByDate($alipayConfig, $apiParams, $app_auth_token);
        $fileConfig = AlipayBillAPI::downBillFile($url);
        pr(json_encode($fileConfig));
        // $result = AlipayToolAPI::getOpenAuthTokenAppQuery($alipayConfig, $app_auth_token);
        // 统一收单线下交易查询
        $apiParams = [
            'out_trade_no' => '20150320010101001'
        ];
        $result = AlipayPayAPI::getTradeQuery($alipayConfig, $apiParams, $app_auth_token);
        pr($result);
        $aaa = '{"fp_url":"http://web.hydzfp.com/ei_access/html/downloadMobilePdf.do?key=pdf&data=eyJuc3JzYmgiOiI5MTMyMDEwNjU5ODAzNTQ2OVciLCJvcmRlcl9udW0iOiI1MTIxMDE3MzEwMDEwMDA0IiwidGltZSI6IjE2MTA1MDQ5OTA3ODgiLCJjayI6ImVhNjRkNzlhYmVhYjBlOTBmNzAxYzFlODc4M2ZiZTc0In0=&pdfkey=pdf_znRtMq91610504989234","qd_url":""}';
        $aaARR = json_decode($aaa, true);
        // pr($aaARR);
        $company_id = 0;
        $organize_id = 48;
        $order_num = "5121017310010004";
        $nsrsbh = "91320106598035469W";
        $invoiceConfigInfo = [
            'open_id' => 'I9lhovOqS1bdamapbn17NYMxgLbwEq8HmLhRdrR4d3VHma6JC9C1493185737711',
            'app_secret' => '6ocogiVdlv7G2jpYcNxmPDaQXDhoZvubxtrayaq3U4WwWOjRwUV1493185737714'
        ];
        InvoicesDBBusiness::getInvoiceFile($company_id, $organize_id, $order_num, $nsrsbh, $invoiceConfigInfo, 0, 0);

        pr(date('YmdHis'));
        $tem_append_name = '邹燕';
        $tem_xmmc = '*非学历教育服务*培训费*';
        $lastStr = mb_substr($tem_xmmc,-1);
        pr($lastStr);
        $tem_good_name = $tem_xmmc;// *非学历教育服务*培训费
        if($tem_append_name == 2){

            // $tem_good_name .= $temInfo['good_name'];
        }

        vd(1);
           $aa = [
             '中化人吴三桂' => 1,
             '陕西省四小要夺末桂林时尚服饰陕西省四小要夺末桂林时尚服饰'  => 2,
           ];
           pr($aa['陕西省四小要夺末桂林时尚服饰陕西省四小要夺末桂林时尚服饰']);
//        $aa = [
//          'aa' => 'aa',
//          'bb' => 'bb'
//        ];
//        pr(json_encode($aa));
//        $actionName = request()->route()->getActionName();
//        pr($actionName);

        // 获得所有的企业信息
//        $market_id= 1332;
//        $url = "http://113.140.67.203:1283/jgjbqk_getJbqkList.action?id=" . $market_id;
//        // $DownFile = DownFile::curlGetFileContents($url);
//        $requestData = [
////            'sortField' => 'id',
////            'sortOrder' => 'desc',
////            'pageIndex' => 0,
////            'pageSize' => 100,
//        ];
//        $result = $this->HttpRequestApi($url, [], $requestData, 'GET');
//        $content = $result[0]['CZR'] ?? '';// 内容
//        vd(empty($content));
//        // if(empty($content)) return false;
//        pr($result);

        // 能力附表
//        $url = "http://113.140.67.203:1284/jgjbqk_getFujian.action";// ?sqid=1298&type=1";
//        // $DownFile = DownFile::curlGetFileContents($url);
//        $requestData = [
//            'sortField' => 'id',
//            'sortOrder' => 'esc',
//            'pageIndex' => 0,
//            'pageSize' => 10,
//            'sqid' => 1298,
//            'type' => 1,
//        ];
//        $result = $this->HttpRequestApi($url, [], $requestData, 'POST');
//        pr($result);

        // 获得所有的企业信息
        // $market_id = '1332';
        // sortField=id&sortOrder=desc&pageIndex=0&pageSize=100
        // $url = "http://113.140.67.203:1283/jgfujian_getJgFuJianMap1.action?sqid=" . $market_id;// . 'sortField=id&sortOrder=desc&pageIndex=0&pageSize=100';
//        $url = 'http://113.140.67.203:1283/jgfujian_getJgFuJianMap1.action';// ?sqid=1330';
//        // $DownFile = DownFile::curlGetFileContents($url);
//        $requestData = [
////            // 'sqid' => $market_id,
//            'sortField' => 'id',
//            'sortOrder' => 'desc',
//            'pageIndex' => 0,
//            'pageSize' => 100,
//            'sqid' => 1330,
//        ];
//        $result = $this->HttpRequestApi($url, [], $requestData, 'POST');
//        $total = $result['total'] ?? 0;// 总数量
//        pr($result);
//        $url = "http://113.140.67.203:1284/jgjbqk_SearchList.action";
//        // $DownFile = DownFile::curlGetFileContents($url);
//        $requestData = [
//            'pageSize' => 80000,
//            'Banb' => 0,
//        ];
//        $result = HttpRequest::sendHttpRequest($url, [], $requestData, 'POST');
//
//        pr($result);
        // 能力附表
//        $url = "http://113.140.67.203:1284/jgjbqk_getFujian.action";// ?sqid=1298&type=1";
//        // $DownFile = DownFile::curlGetFileContents($url);
//        $requestData = [
//            'sortField' => 'id',
//            'sortOrder' => 'esc',
//            'pageIndex' => 0,
//            'pageSize' => 10,
//            'sqid' => 1298,
//            'type' => 1,
//        ];
//        $result = HttpRequest::sendHttpRequest($url, [], $requestData, 'POST');
//        pr($result);
        // 文件保存
        // $fileUrl = 'https://pics1.baidu.com/feed/4ec2d5628535e5dd14c41cc0f11425e8cf1b621b.jpeg?token=d1d67a7e1a7de030a93d8d69ca310e87';
        // $files_names = 'aaa.jpeg';
//        $fileUrl = 'http://113.140.67.203:1284/jsp/LookFj.jsp?id=3788';
//        $files_names = 'e3b8d4d1-c2da-461d-9a9d-474aefc1d2f7.pdf';
//        $returnSave = DownFile::getUrlFileToLocal($fileUrl, 0,2, '', $files_names);
//        pr($returnSave);
//        $fileName = '2020年4月法人变更自我声明';// '能力附表';
//        $filePath = '2020-4-26/8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf';// '2020-4-22/e3b8d4d1-c2da-461d-9a9d-474aefc1d2f7.xls';
//        $fileUrl = 'http://113.140.67.203:1283/jsp/Jyjc/ZzxxDown.jsp?fileName=' . $fileName . '&filePath=' . $filePath;
//
//        $filename = '/usr/local/php/etc/php.ini';
//        $mimetype = DownFile::getLocalFileMIME($filename);
//        $file_size = DownFile::getLocalFileSize($filename);
//
//        $suffix = DownFile::getLocalFileExt($filename);// strtolower(pathinfo($filename,PATHINFO_EXTENSION));
////        $finfo = finfo_open(FILEINFO_MIME);
////        $mimetype = finfo_file($finfo,$filename);// text/plain; charset=utf-8
////        finfo_close($finfo);
//        pr($suffix);


//        $fi = new finfo(FILEINFO_MIME_TYPE);
//        $mime_type = $fi->file($filePath);
//        echo $mime_type; // image/jpeg
//        die;

//        $files_names = '';// '8c980322-5e92-40c4-ae9c-9f756e7fe4cd.pdf';
//        $returnSave = DownFile::getUrlFileToLocal($fileUrl, 0,2, '', $files_names);
//        pr($returnSave);
//        $dateTime =  date('Y-m-d H:i:s');
//        $aaa = Tool::addMinusDate($dateTime, ['+30 day'], 'Y-m-d H:i:s', 1, '时间');
//        pr($aaa);
//
//        $aaa = SessionCustom::set('test', '1112', 0);
//        pr($aaa);
//        $bbb = SessionCustom::get('loginKeyadmin',true);
//
//        pr($bbb);
//        $redisKey = 'PHPREDIS_SESSION:' . session_id();
//        pr($redisKey);
//        $currentTime = date('Y-m-d H:i:s');
//        $endTime = '2020-06-02 15:48:49';
//        $endCarbon = carbon::parse ($endTime); // 格式化一个时间日期字符串为 carbon 对象
//        // 减当前时间 ; > 0 没有过期 = 0 马上过期  < 0 过期
//        $diffSeconds = (new Carbon)->diffInSeconds ($endCarbon, false); // $int 为正负数
//        // $diffSeconds = strtotime($currentTime) - strtotime($endTime);
//        dd($diffSeconds);
//        CTAPICertificateScheduleBusiness::mergeRequest($request, $this, [
//            'field' => 'method_name',
//            'keyword' => '标',
//        ]);
//        $queryParams = [
//            'select' => [
//                'company_id'
//                //,'position_name','sort_num'
//                //,'operate_staff_id','operate_staff_id_history'
//                // ,'created_at'
//            ],
//            'distinct'=> 'company_id',
//        ];
//        $aa = CTAPICertificateScheduleBusiness::getList($request, $this, 2 + 4, $queryParams, [], []);
//        pr($aa);
//        CertificateScheduleDBBusiness::test();
//        pr(123);
        // $ability_code = AbilityCodeDBBusiness::getAbilityCode();// 单号 生成  2020NLYZ0001
        //pr($ability_code);
//        $currentNow = Carbon::now()->toDateTimeString();
//        $aa = date('Y-m-d 23:59:59');
//        $duration_minute = 13;
//        $submit_off_time = Tool::addMinusDate(date('Y-m-d 23:59:59'), ['+' . $duration_minute . ' day'], 'Y-m-d H:i:s', 1, '时间');;
//        echo $submit_off_time;
//        AbilityJoinItemsDBBusiness::initReslut();
//        die();
//        $bbb = '555';
//        $aaa = CommonDB::doTransactionFun(function () use(&$bbb){
//            $bbb .= '666';
//            return 'bcd';
//        });
//        echo $bbb . '<br/>';
//
//        pr($aaa);

//        $operate_type = 8;
//        $page_size = 2;
//        $fieldValParams = ['issuper' => 1];
//        $fieldEmptyQuery = false;
//        $relations = '';
//        $extParams = [];
//        $data = StaffDBBusiness::getDBFVFormatList($operate_type, $page_size, $fieldValParams, $fieldEmptyQuery, $relations, $extParams);
//
//        if(is_object($data)) vd($data);
//        if(is_array($data)) pr($data);
         phpinfo();
        die;
        $extParams['sqlParams']['whereIn']['id'] = 123;
        pr($extParams);
            $this->InitParams($request);
        $reDataArr = $this->reDataArr;
        //pr($this->getUserInfo($request));
        //die;
        pr($this->user_id);
        echo '1111';
    }

    /**
     * 首页
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function index(Request $request)
    {
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//            return view('admin.QualityControl.index', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
        return $this->exeDoPublicFun($request, 1, 1, 'admin.QualityControl.index', true
            , 'doListPage', [], function (&$reDataArr) use ($request){

            });
    }

    /**
     * ajax获得模型表的缓存时间；没有缓存时间-则返回当前时间
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_getTableUpdateTime(Request $request){
        return $this->exeDoPublicFun($request, 0, 4,'', true, '', [], function (&$reDataArr) use ($request){
            $module_name = CommonRequest::get($request, 'module_name');// QualityControl\CTAPIStaff
            if(empty($module_name)) throws('参数【module_name】不能为空！');

            $objClass = 'App\\Business\\Controller\API\\' . $module_name  . 'Business';// 'App\Business\Controller\API\QualityControl\CTAPIStaffBusiness';
            if (! class_exists($objClass )) {
                throws('参数[module_name]类不存在！');
            }
            // 空或 string(29) "2020-09-04 15:00:03!!!9840900"  [true, 4]
            $tableUpdateTime = $objClass::exeMethodCT($request, $this, '', 'getTableUpdateTimeCache', [], 1, 1);
            if(!empty($tableUpdateTime)) list($tableUpdateTime, $cacheMsecint) = array_values(Tool::getTimeMsec($tableUpdateTime));
            if(empty($tableUpdateTime)) $tableUpdateTime = date('Y-m-d H:i:s');
            return ajaxDataArr(1, $tableUpdateTime, '');
        });
    }

    /**
     * api生成验证码图片信息
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_captcha(Request $request)
//    {
//        $captchaParams = CaptchaCode::createCodeAPI(__CLASS__ . $request->ip(),'default');// app('captcha')->create('default', true);
//
//        return ajaxDataArr(1, $captchaParams, '');
//    }

    /**
     * api验证验证码信息是否正确
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_captcha_verify(Request $request)
//    {
//        $captcha_code = CommonRequest::get($request, 'captcha_code');
//        $captcha_key = CommonRequest::get($request, 'captcha_key');
////        if(!captcha_api_check($captcha_code, $captcha_key)) {
////            Cache::forget($captcha_key);
////            return ajaxDataArr(0, null, '验证码错误');
////        }
////        Cache::forget($captcha_key);
//        CaptchaCode::captchaCheckAPI($captcha_code, $captcha_key, false, 1);
//        return ajaxDataArr(1, ['data' => 1], '验证码正确');
//    }

    /**
     * 登陆
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function login(Request $request)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
            // 正常流程的代码

            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            return view('admin.QualityControl.login', $reDataArr);

        }, $this->errMethod, $reDataArr, $this->errorView);
    }

    /**
     * 修改密码
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function password(Request $request)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
            // 正常流程的代码

            $this->InitParams($request);
            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            $user_info = $this->user_info;
            $reDataArr = array_merge($reDataArr, $user_info);
            return view('admin.QualityControl.admin.password', $reDataArr);

        }, $this->errMethod, $reDataArr, $this->errorView);
    }

    /**
     * 显示
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function info(Request $request)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
            // 正常流程的代码

            $this->InitParams($request);
            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            $user_info = $this->user_info;

            $reDataArr['adminType'] =  Staff::$adminTypeArr;
            $reDataArr['defaultAdminType'] = $user_info['admin_type'] ?? 0;// 列表页默认状态
            $reDataArr = array_merge($reDataArr, $user_info);
            return view('admin.QualityControl.admin.info', $reDataArr);

        }, $this->errMethod, $reDataArr, $this->errorView);
//        return $this->exeDoPublicFun($request, 17179869184, 1,'web.QualityControl.admin.info', true
//            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){
//
//            });
    }

    /**
     * err404
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function err404(Request $request)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
            // 正常流程的代码

            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            return view('404', $reDataArr);

        }, $this->errMethod, $reDataArr, $this->errorView);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/ajax_login",
     *     tags={"大后台-帐号注册登录"},
     *     summary="帐号密码登录",
     *     description="通过帐号、密码、图形验证码进行登录",
     *     operationId="adminIndexAjax_login",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/common_Parameter_admin_username"),
     *     @OA\Parameter(ref="#/components/parameters/common_Parameter_admin_password"),
     *     @OA\Parameter(ref="#/components/parameters/Parameter_Object_captcha_captcha_key"),
     *     @OA\Parameter(ref="#/components/parameters/Parameter_Object_captcha_captcha_code"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_info_staff_login"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_multi_brands"}
     */
    /**
     * ajax保存数据
     *
     * @param Request $request
     * @return array
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_login(Request $request)
    {
        // $this->InitParams($request);
        // $company_id = $this->company_id;
        return CTAPIStaffBusiness::loginCaptchaCode($request, $this,1, 1, 1);
    }

    /**
     * ajax保存数据--手机验证码登录
     *
     * @param Request $request
     * @return array
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_login_sms(Request $request)
    {
        // $this->InitParams($request);
        // $company_id = $this->company_id;
        return CTAPIStaffBusiness::loginCaptchaCode($request, $this,1, 2, 2);
    }

    /**
     * 注销
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function logout(Request $request)
    {
        // $this->InitParams($request);
        CTAPIStaffBusiness::loginOut($request, $this);
        $reDataArr = $this->reDataArr;
        return redirect('admin/login');
    }

    /**
     * ajax修改密码
     *
     * @param int $id
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_password_save(Request $request)
    {
        $this->InitParams($request);
        return CTAPIStaffBusiness::modifyPassWord($request, $this);
    }

    /**
     * ajax 修改设置
     *
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_info_save(Request $request)
    {
        $this->InitParams($request);
        $user_info = $this->user_info;

        $id = $this->user_id;
        $company_id = $this->company_id;
        $admin_username = CommonRequest::get($request, 'admin_username');
        $mobile = CommonRequest::get($request, 'mobile');
        $real_name = CommonRequest::get($request, 'real_name');
        $sex = CommonRequest::getInt($request, 'sex');
        $tel = CommonRequest::get($request, 'tel');
        $qq_number = CommonRequest::get($request, 'qq_number');

        $saveData = [
            'admin_type' => $user_info['admin_type'],
            'admin_username' => $admin_username,
            'mobile' => $mobile,
            'real_name' => $real_name,
            'sex' => $sex,
           // 'gender' => $sex,
            'tel' => $tel,
            'qq_number' => $qq_number,
        ];
        $extParams = [
            // 'judgeDataKey' => 'replace',// 数据验证的下标
        ];
        $resultDatas = CTAPIStaffBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
        return ajaxDataArr(1, $resultDatas, '');
    }

    /**
     * 下载二维码
     *
     * @param Request $request
     * @param int $id id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function down(Request $request,$id = 0)
//    {
//        $this->InitParams($request);
//        // $this->source = 2;
//        $reDataArr = $this->reDataArr;
//        $relations = '';//  CTAPITablesBusiness::getExtendParamsConfig($request, $this, 'list_page_admin', 'relationsArr');
//
//        $info = CTAPITablesBusiness::getInfoData($request, $this, $id, ['id','table_name','has_qrcode','qrcode_url'], $relations, []);
//        $has_qrcode = $info['has_qrcode'] ?? 1;
//        $qrcode_url = $info['qrcode_url'] ?? '';//  http://runbuy.admin.cunwo.net/resource/company/1/images/qrcode/tables/1.png
//        $qrcode_url_old = $info['qrcode_url_old'] ?? '';// /resource/company/1/images/qrcode/tables/1.png
//        if($has_qrcode != 2 ) die('记录不存在或未生成二维码');
//        // 下载二维码文件
//        $publicPath = Tool::getPath('public');
//        $save_file_name = CommonRequest::get($request, 'save_file_name');// 下载时保存的文件名 [可以不用加文件扩展名，不加会自动加上]--可以为空：用源文件的名称
//        $res = DownFile::downFilePath(2, $publicPath . $qrcode_url_old, 1024, $save_file_name);
//        if(is_string($res)) echo $res;
//    }

    /**
     * 下载网页打印机驱动
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function down_drive(Request $request)
    {
//        $this->InitParams($request);
        // $this->source = 2;
//        $reDataArr = $this->reDataArr;
        // 下载二维码文件
        $publicPath = Tool::getPath('public');
        $fileName = '/CLodopPrint_Setup_for_Win32NT.exe';
        $save_file_name = CommonRequest::get($request, 'save_file_name');// 下载时保存的文件名 [可以不用加文件扩展名，不加会自动加上]--可以为空：用源文件的名称
        $res = DownFile::downFilePath(2, $publicPath . '/' . $fileName, 1024, $save_file_name);
        if(is_string($res)) echo $res;
    }

    // **************公用方法**********************开始*******************************

    /**
     * 公用列表页 --- 可以重写此方法--需要时重写
     *  主要把要传递到视图或接口的数据 ---放到 $reDataArr 数组中
     * @param Request $request
     * @param array $reDataArr // 需要返回的参数
     * @param array $extendParams // 扩展参数
     *   $extendParams = [
     *      'pageNum' => 1,// 页面序号  同 属性 $fun_id【查看它指定的】 (其它根据具体的业务单独指定)
     *      'returnType' => 1,// 返回类型 1 视图[默认] 2 ajax请求的json数据[同视图数据，只是不显示在视图，是ajax返回]
     *                          4 ajax 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果 8 视图 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果
     *      'view' => 'index', // 显示的视图名 默认index
     *      'hasJudgePower' => true,// 是否需要判断登录权限 true:判断[默认]  false:不判断
     *      'doFun' => 'doListPage',// 具体的业务方法，动态或 静态方法 默认'' 可有返回值 参数  $request,  &$reDataArr, $extendParams ；
     *                               doListPage： 列表页； doInfoPage：详情页
     *      'params' => [],// 需要传入 doFun 的数据 数组[一维或多维]
     *  ];
     * @return mixed 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public function doListPage(Request $request, &$reDataArr, $extendParams = []){
        // 需要隐藏的选项 1、2、4、8....[自己给查询的或添加页的下拉或其它输入框等编号]；靠前面的链接传过来 &hidden_option=0;
        $hiddenOption = CommonRequest::getInt($request, 'hidden_option');
        // $pageNum = $extendParams['pageNum'] ?? 1;// 1->1 首页；2->2 列表页； 12->2048 弹窗选择页面；
        // $user_info = $this->user_info;
        // $id = $extendParams['params']['id'];

//        // 拥有者类型1平台2企业4个人
//        $reDataArr['adminType'] =  AbilityJoin::$adminTypeArr;
//        $reDataArr['defaultAdminType'] = -1;// 列表页默认状态

        $reDataArr['hidden_option'] = $hiddenOption;
    }

    /**
     * 公用详情页 --- 可以重写此方法-需要时重写
     *  主要把要传递到视图或接口的数据 ---放到 $reDataArr 数组中
     * @param Request $request
     * @param array $reDataArr // 需要返回的参数
     * @param array $extendParams // 扩展参数
     *   $extendParams = [
     *      'pageNum' => 1,// 页面序号  同 属性 $fun_id【查看它指定的】 (其它根据具体的业务单独指定)
     *      'returnType' => 1,// 返回类型 1 视图[默认] 2 ajax请求的json数据[同视图数据，只是不显示在视图，是ajax返回]
     *                          4 ajax 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果 8 视图 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果
     *      'view' => 'index', // 显示的视图名 默认index
     *      'hasJudgePower' => true,// 是否需要判断登录权限 true:判断[默认]  false:不判断
     *      'doFun' => 'doListPage',// 具体的业务方法，动态或 静态方法 默认'' 可有返回值 参数  $request,  &$reDataArr, $extendParams ；
     *                               doListPage： 列表页； doInfoPage：详情页
     *      'params' => [],// 需要传入 doFun 的数据 数组[一维或多维]
     *  ];
     * @return mixed 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public function doInfoPage(Request $request, &$reDataArr, $extendParams = []){
        // 需要隐藏的选项 1、2、4、8....[自己给查询的或添加页的下拉或其它输入框等编号]；靠前面的链接传过来 &hidden_option=0;
        $hiddenOption = CommonRequest::getInt($request, 'hidden_option');
        // $pageNum = $extendParams['pageNum'] ?? 1;// 5->16 添加页； 7->64 编辑页；8->128 ajax详情； 35-> 17179869184 详情页
//        // $user_info = $this->user_info;
//        $id = $extendParams['params']['id'] ?? 0;
//
////        // 拥有者类型1平台2企业4个人
////        $reDataArr['adminType'] =  AbilityJoin::$adminTypeArr;
////        $reDataArr['defaultAdminType'] = -1;// 列表页默认状态
//        $info = [
//            'id'=>$id,
//            //   'department_id' => 0,
//        ];
//        $operate = "添加";
//
//        if ($id > 0) { // 获得详情数据
//            $operate = "修改";
//            $info = CTAPIRrrDdddBusiness::getInfoData($request, $this, $id, [], '', []);
//        }
//        // $reDataArr = array_merge($reDataArr, $resultDatas);
//        $reDataArr['info'] = $info;
//        $reDataArr['operate'] = $operate;

        $reDataArr['hidden_option'] = $hiddenOption;
    }
    // **************公用方法********************结束*********************************

    /**
     * 下载文件
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function down_file(Request $request)
    {
//        $this->InitParams($request);
        // $this->source = 2;
//        $reDataArr = $this->reDataArr;
        // 下载二维码文件
        $publicPath = Tool::getPath('public');
        $fileName = CommonRequest::get($request, 'resource_url');// '/CLodopPrint_Setup_for_Win32NT.exe';
        $save_file_name = CommonRequest::get($request, 'save_file_name');// 下载时保存的文件名 [可以不用加文件扩展名，不加会自动加上]--可以为空：用源文件的名称
        $res = DownFile::downFilePath(2, $publicPath . $fileName, 1024, $save_file_name);
        if(is_string($res)) echo $res;
    }
}
