<?php
namespace App\Services\Tencent;
//  开发2.0-老版本，不建议使用了。
use Qcloud\Sms\SmsSingleSender;
use Qcloud\Sms\SmsMultiSender;
use Qcloud\Sms\SmsStatusPuller;
use Qcloud\Sms\SmsMobileStatusPuller;
class QcloudSMS
{

    /**
     * 调用SendSms发送短信。
     * @param string $appid 短信应用 SDK AppID
     * @param string $appkey 短信应用 SDK AppKey
     * @param string $regionId 空值  地域和可用区 --腾讯不传
     * @param array $params
     *
     *  $dataParams = [
     *      'CountryCode' => '86',// 国家码
     *      // 接收短信的手机号码。
     *      // 格式：[
     *      // 国内短信：11位手机号码，例如15951955195。或 最好传数组] [15951955195]
     *      // 支持对多个手机号码发送短信，手机号码之间以英文逗号（,）分隔。或 数组
     *      // 必填
     *      'PhoneNumbers' => '15900000000',
     *      // 签名参数使用的是`签名内容`，而不是`签名ID`。这里的签名"腾讯云"只是示例，真实的签名需要在短信控制台申请
     *      // 必填
     *      'SignName' => '腾讯云',
     *      // 这里的模板 ID`7839`只是示例，真实的模板 ID 需要在短信控制台中申请
     *      // 必填
     *      'TemplateCode' => '468796',
     *      // 短信模板变量对应的实际值，数组格式
     *      // 选填
     *      'TemplateParam' => [
     *          'operateType' => '注册', //操作类型 注册
     *          'code' => '8894', // 验证码
     *          'validMinute' => 3// 有效时间(单位分钟) // 有缓存时间[有此下标]，则缓存
     *      ],
     *  ];
     *  尊敬的用户：您的{1}验证码{2}，请在{3}分钟内使用，工作人员不会索取，请勿泄漏。
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return mixed sting 具体错误 ； throws 错误 数组(result 为 0 则说明请求成功)
     *
     *$result = [
     *   'result' => 0,// 是	number	错误码，0表示成功（计费依据），非0表示失败，更多详情请参见 错误码
     *  'errmsg' => 'OK',// string	错误消息，result 非0时的具体错误信息
     *  'ext' =>'',// 否	string	用户的 session 内容，腾讯 server 回包中会原样返回
     *  'detail' =>[
     *      [
     *      'result' => 0,
     *      'errmsg' => 'OK',
     *      'mobile' => '15829686962',
     *      'nationcode' => '86',
     *      'sid' => '26:19111109540300202010000006590472',// 否	string	本次发送标识 ID，标识一次短信下发记录
     *      'fee' => 1,// 否	number	短信计费的条数
     *      ]
     *  ]
     *];
     *
     */
    public static function SendSms($appid = '', $appkey = '', $regionId = '', $dataParams = [], $errDo = 1){
        // 准备必要参数
        // 短信应用 SDK AppID
        // $appid = 1400009099; // SDK AppID 以1400开头
        // 短信应用 SDK AppKey
        // $appkey = "9ff91d87c2cd7cd0ea762f141975d1df37481d48700d70ac37470aefc60f9bad";
        // 需要发送短信的手机号码

        // $phoneNumbers = ["21212313123", "12345678902", "12345678903"];
        // 短信模板 ID，需要在短信控制台中申请
        // $templateId = 7839;  // NOTE: 这里的模板 ID`7839`只是示例，真实的模板 ID 需要在短信控制台中申请
        // $smsSign = "腾讯云"; // NOTE: 签名参数使用的是`签名内容`，而不是`签名ID`。这里的签名"腾讯云"只是示例，真实的签名需要在短信控制台申请

        $templateId = $dataParams['TemplateCode'] ?? '';
        $smsSign = $dataParams['SignName'] ?? '';
        $params = $dataParams['TemplateParam'] ?? [];
        if(is_array($params)){
            $params = array_values($params);
            // 数组转为json
            // $params = json_encode($params);
        }
        $countryCode = $dataParams['CountryCode'] ?? '86';
        $phoneNumbers = $dataParams['PhoneNumbers'] ?? [];
        // 字符串转为数组
        if(is_string($phoneNumbers)) $phoneNumbers = explode(',', $phoneNumbers);

        // 指定模板 ID 单发短信
        try {
             // $params = json_encode(['注册',"5678", '3']);
            // $params = ['1' => '注册','2' => "5678", '3' =>'3'];
           //  $params = [ 'b',"5678", '3'];
            if(count($phoneNumbers) <= 1 && false){// 单条
                $senderObj = new SmsSingleSender($appid, $appkey);
                // 转为单 条字符
                $phoneNumbers = implode(',', $phoneNumbers);
            }else{// 多条
                $senderObj = new SmsMultiSender($appid, $appkey);
            }
            $result = $senderObj->sendWithParam($countryCode, $phoneNumbers, $templateId,
                $params, $smsSign, "", "");
            $rsp = json_decode($result, true);
            return  $rsp;
        } catch(\Exception $e) {
            $errMsg = $e->getMessage();
            if($errDo == 1) throws($errMsg);
            return $errMsg;
            // echo var_dump($e);
        }

        // 指定模板 ID 群发短信
//        try {
//            $msender = new SmsMultiSender($appid, $appkey);
//            $params = ["5678"];
//            $result = $msender->sendWithParam("86", $phoneNumbers,
//                $templateId, $params, $smsSign, "", "");
//            $rsp = json_decode($result);
//            echo $result;
//        } catch(\Exception $e) {
//            echo var_dump($e);
//        }


    }

//    public static function aaaa(){
//        // 拉取短信回执以及回复
//        try {
//            $spuller = new SmsStatusPuller($appid, $appkey);
//
//            // 拉取短信回执
//            $callbackResult = $spuller->pullCallback(10);
//            $callbackRsp = json_decode($callbackResult);
//            echo $callbackResult;
//
//            // 拉取回复，国际/港澳台短信不支持回复功能
//            $replyResult = $spuller->pullReply(10);
//            $replyRsp = json_decode($replyResult);
//            echo $replyResult;
//        } catch (\Exception $e) {
//            echo var_dump($e);
//        }
//    }

//    public static function bbb(){
//        // 拉取单个手机短信状态
//        try {
//            $beginTime = 1511125600;  // 开始时间（UNIX timestamp）
//            $endTime = 1511841600;    // 结束时间（UNIX timestamp）
//            $maxNum = 10;             // 单次拉取最大量
//            $mspuller = new SmsMobileStatusPuller($appid, $appkey);
//
//            // 拉取短信回执
//            $callbackResult = $mspuller->pullCallback("86", $phoneNumbers[0],
//                $beginTime, $endTime, $maxNum);
//            $callbackRsp = json_decode($callbackResult);
//            echo $callbackResult;
//
//            // 拉取回复，国际/港澳台短信不支持回复功能
//            $replyResult = $mspuller->pullReply("86", $phoneNumbers[0],
//                $beginTime, $endTime, $maxNum);
//            $replyRsp = json_decode($replyResult);
//            echo $replyResult;
//        } catch (\Exception $e) {
//            echo var_dump($e);
//        }
//
//    }

}