<?php

// 账务API

namespace App\Services\alipaySdk;


use App\Services\File\DownFile;
use App\Services\Request\API\HttpRequest;
use Illuminate\Support\Facades\Log;

require_once 'aop/request/AlipayDataDataserviceBillDownloadurlQueryRequest.php';


class AlipayBillAPI extends BasicAlipay
{


    /**
     * --- 接口  alipay.data.dataservice.bill.downloadurl.query(查询对账单下载地址)
     *   https://opendocs.alipay.com/apis/api_15/alipay.data.dataservice.bill.downloadurl.query
     *  为方便商户快速查账，支持商户通过本接口获取商户离线账单下载地址
     *
     *  商户系统调用 alipay.data.dataservice.bill.downloadurl.query(查询对账单下载地址)，传入指定日期，获得该日期账单文件的下载地址；
     *  商户系统通过 HTTP 方式后台访问账单下载链接，将账单 csv 文件下载到本地后自行处理。注意该下载链接仅 30 秒，在得到链接后系统需要立刻下载账单文件。
     * @param array $config  接口相关的配置信息
     * @param array $apiParams  请求参数数组
     *   $apiParams = [
     *      // String	必选	10
     *      // 账单类型，商户通过接口或商户经开放平台授权后其所属服务商通过接口可以获取以下账单类型，支持：
     *       // trade：商户基于支付宝交易收单的业务账单；
     *      // signcustomer：基于商户支付宝余额收入及支出等资金变动的帐务账单。
     *      'bill_type' => 'trade', // * bill_type：固定传入 trade。
     *      // String	必选	15	账单时间：日账单格式为yyyy-MM-dd，最早可下载2016年1月1日开始的日账单；月账单格式为yyyy-MM，最早可下载2016年1月开始的月账单。
     *      'bill_date' => '2016-04-05',// * bill_date：需要下载的账单日期，最晚是当期日期的前一天。
     *  ];
     * @param string $app_auth_token 可选 服务商刷新令牌时必填  默认null 开发者代替商户发起请求时请务必带上 app_auth_token，否则支付宝将认为是本应用替自己发起的请求。请注意 app_auth_token 是 POST 请求参数，不是 biz_content 的子参数；
     * @return string 返回文件地址 // http://dwbillcenter.alipay.com/downloadBillFile.resource?bizType=X&userId=X&fileType=X&bizDates=X&downloadFileName=X&fileId=X
     *        http://dwbillcenter.alipay.com/downloadBillFile.resource?bizType=trade&userId=20880413349004220156&fileType=csv.zip&bizDates=20210128&downloadFileName=20880413349004220156_20210128.csv.zip&fileId=%2Ftrade%2F20880413349004220156%2F20210128.csv.zip×tamp=1611917504&token=91b8b0ac83c01d420cfbedbe2839bef7
     *       * bill_download_url：账单文件下载地址，有效时长：30 秒。
     * @author zouyan(305463219@qq.com)
     */
    public static function getDownloadUrlByDate($config = [], $apiParams = [], $app_auth_token = null){
        //1、execute 使用
        $aop = static::getAop($config);// new \AopCertClient ();
        // 2、对于每一个接口对应的请求类，调用它的 setNeedEncrypt 方法，设置为 true 就可以了。
        static::setNeedEncrypt($request, $config, true);
        // if(static::isOpenEncrypt($config)) $request->setNeedEncrypt(true);

//        "{" .
//        "\"bill_type\":\"trade\"," .
//        "\"bill_date\":\"2016-04-05\"" .
//        "  }"

        $authToken = null;// auth_token
        $appInfoAuthtoken = $app_auth_token;// null;// app_auth_token

        $request = new \AlipayDataDataserviceBillDownloadurlQueryRequest ();

        static::paramsArrToJson($apiParams, []);// 参数值需要转为json格式的参数--自动完成转换
        $request->setBizContent(json_encode($apiParams));
        $result = $aop->execute ( $request, $authToken, $appInfoAuthtoken);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";

        info('支付宝支付日志-alipay.data.dataservice.bill.downloadurl.query(查询对账单下载地址):',[$apiParams,  $request, $result, $responseNode]);
        $resultCode = $result->$responseNode->code;

        static::judgeThrowsErr($result, $responseNode);// 判断接口返回，如果有错误，则抛出误错
//        if(!empty($resultCode)&&$resultCode == 10000){
//            echo "成功";
//        } else {
//            echo "失败";
//        }
        /***
         *
         *
         *$resultObj =[
         *  "alipay_data_dataservice_bill_downloadurl_query_response" => [
         *      "code" => "10000",
         *      "msg" => "Success",
         *      // bill_download_url String	必填	2048	账单下载地址链接，获取连接后30秒后未下载，链接地址失效。
         *      // http://dwbillcenter.alipay.com/downloadBillFile.resource?bizType=X&userId=X&fileType=X&bizDates=X&downloadFileName=X&fileId=X
         *      "bill_download_url" => "http://dwbillcenter.alipay.com/downloadBillFile.resource?bizType=X&userId=X&fileType=X&bizDates=X&downloadFileName=X&fileId=X"
         *  ],
         *  "sign" => "ERITJKEIJKJHKKKKKKKHJEREEEEEEEEEEE"
         *];
         *
         */
        return $result->$responseNode->bill_download_url;
    }

 // $url = "http://dwbillcenter.alipay.com/downloadBillFile.resource?bizType=trade&userId=20880413349004220156&fileType=csv.zip&bizDates=20210128&downloadFileName=20880413349004220156_20210128.csv.zip&fileId=%2Ftrade%2F20880413349004220156%2F20210128.csv.zip×tamp=1611917504&token=91b8b0ac83c01d420cfbedbe2839bef7";

    /**
     * --- 对接口 getDownloadUrlByDate 的文件进行下载工作
     * @param string $url  文件地址
     * @return string
     * @author zouyan(305463219@qq.com)
     */
    public static function downBillFile($url){
        $url_param_arr = parse_url($url);
        $queryParams = $url_param_arr['query'] ?? [];
        parse_str($queryParams, $parameter);
//        $parameter = [
//            "bizType" => "trade",
//            "userId" => "20880413349004220156",
//            "fileType" => "csv.zip",
//            "bizDates" => "20210128",
//            "downloadFileName" => "20880413349004220156_20210128.csv.zip",
//            "fileId" => "/trade/20880413349004220156/20210128.csv.zip×tamp=1611917504",
//            "token" => "91b8b0ac83c01d420cfbedbe2839bef7"
//        ];
        $downloadFileName = $parameter['downloadFileName'] ?? '';
        $fileArr = [];
        if(!empty($downloadFileName)){
            // 保存csv.zip文件
            $fileArr = DownFile::getUrlFileToLocal($url, 0,2, 'bills', $downloadFileName );
//            [
//                "publicPath" => "/srv/www/quality_control/quality_control/admin/public",
//                "savePath" => "/resource/company/0/bills/2021/01/30/",
//                "saveName" => "20880413349004220156_20210129.csv.zip",
//                "files_names" => "/resource/company/0/bills/2021/01/30/20880413349004220156_20210129.csv.zip",
//                "web_url" => "http =>//qualitycontrol.admin.cunwo.net/resource/company/0/bills/2021/01/30/20880413349004220156_20210129.csv.zip",
//                "full_names" => "/srv/www/quality_control/quality_control/admin/public/resource/company/0/bills/2021/01/30/20880413349004220156_20210129.csv.zip"
//            ]
        }

        return $fileArr;
    }
}
