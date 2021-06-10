<?php
namespace App\Services\Tencent\API30SDK ;

use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Sms\V20190711\SmsClient;
use TencentCloud\Sms\V20190711\Models\SendSmsRequest;
class CloudSMS
{

    /**
     * 调用SendSms发送短信。
     * @param string $appid 短信应用 SDK AppID
     * @param string $SecretId 通过接口访问时的 SecretId 密钥 必填： 是.类型： String.描述：短信SdkAppid在[短信控制台](https://console.cloud.tencent.com/sms/smslist) 添加应用后生成的实际SdkAppid,示例如1400006666。
     * @param string $SecretKey 通过接口访问时的 SecretKey 密钥
     * @param string $regionId 空值  地域和可用区 --腾讯不传
     * @param array $params
     *
     *  $dataParams = [
     *      // 必填： 是.类型： Array Of String.描述：下发手机号码，采用 e.164 标准，+[国家或地区码][手机号] ，
     *      // 示例如：+8613711112222， 其中前面有一个+号 ，86为国家码，13711112222为手机号。最多不要超过200个手机号。
     *      'PhoneNumbers' => '15900000000',
     *      // 必填： 否.类型： String.描述：短信签名内容，使用 UTF-8 编码，必须填写已审核通过的签名 签名信息可登录[短信控制台](https://console.cloud.tencent.com/sms/smslist) 查看。
     *      'SignName' => '腾讯云',
     *      // 必填： 是.类型： String.描述：模板 ID，必须填写已审核通过的模板 ID。模板ID可登录[短信控制台](https://console.cloud.tencent.com/sms/smslist)查看。
     *      'TemplateCode' => '468796',
     *      // 必填： 否.类型：  Array Of String..描述：模板参数，若无模板参数，则设置为空。
     *      'TemplateParam' => [
     *          'operateType' => '注册', //操作类型 注册
     *          'code' => '8894', // 验证码
     *          'validMinute' => 3// 有效时间(单位分钟) // 有缓存时间[有此下标]，则缓存
     *      ],
     *      // ExtendCode 必填： 否.类型： Integer.描述： 短信码号扩展号，默认未开通，如需开通请联系 [sms helper](https://cloud.tencent.com/document/product/382/3773)
     *     'ExtendCode' => ''
     *      // SessionContext 必填： 否.类型： String.描述：用户的 session 内容，可以携带用户侧ID等上下文信息,server 会原样返回
     *    'SessionContext' => ''
     *    // SenderId 必填： 否.类型： String.描述：国际/港澳台短信senderid，国内短信填空。 默认未开通，如需开通请联系 [sms helper](https://cloud.tencent.com/document/product/382/3773)
     *    'SenderId' => ''
     *
     *  ];
     *  尊敬的用户：您的{1}验证码{2}，请在{3}分钟内使用，工作人员不会索取，请勿泄漏。
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return mixed sting 具体错误 ； throws 错误 数组(result 为 0 则说明请求成功)
     *
     *  $result = [
     *      "SendStatusSet" => [
     *          [
     *              "SerialNo" => "2056:107880240115734611088741785",// 成功后有编号，失败为空
     *              "PhoneNumber" => "+8615591017857",// 手机号
     *              "Fee" => 1,// 有效数量
     *              "SessionContext" => "",
     *              "Code" => "Ok",// 成功：Ok , 其它失败：如 RequestLimitExceeded.AmountOfPhoneNumberDailyExceedLimit
     *              "Message" => "send success",// 成功  "send success"; 失败信息：如  the number of sms messages sent from a single mobile number within 1 hour exceeds the upper limit
     *          ]
     *      ],
     *      "RequestId" => "65882beb-d028-4650-ad1c-613b652d231e",// 请求编号
     *  ];
     *  也会有这样的错误
     *   [
     *      "Response" => [
     *          "Error" => [
     *               "Code" => "InvalidParameter",
     *              "Message" => "参数 `TemplateParamSet.0` 取值类型错误。"
     *          ],
     *           "RequestId" => "3834bc30-5fcb-4c3d-b3f6-89c1e83f328c"
     *       ]
     *   ]
     *
     *
     */
    public static function SendSms($appid = '',$SecretId = '', $SecretKey = '', $regionId = 'ap-beijing', $dataParams = [], $errDo = 1){
        try {
            $cred = new Credential($SecretId, $SecretKey);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("sms.tencentcloudapi.com");

            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new SmsClient($cred, $regionId, $clientProfile);// "ap-chengdu"


            $phoneNumbers = $dataParams['PhoneNumbers'] ?? [];
            // 字符串转为数组
            if(is_string($phoneNumbers)) $phoneNumbers = explode(',', $phoneNumbers);

            $templateId = $dataParams['TemplateCode'] ?? '';
            $smsSign = $dataParams['SignName'] ?? '';

            $TemplateParamSet = $dataParams['TemplateParam'] ?? [];
            if(is_array($TemplateParamSet)){
                $TemplateParamSet = array_values($TemplateParamSet);
                // 多代多余，但是接口要求必须是字符的，所以这样处理下
                foreach($TemplateParamSet as $k => $v){
                    $TemplateParamSet[$k] = $v . '';
                }
                // 数组转为json
                // $TemplateParamSet = json_encode($TemplateParamSet);
            }

            $req = new SendSmsRequest();
            $params = [
                // 必填： 是.类型： String.描述：短信SdkAppid在[短信控制台](https://console.cloud.tencent.com/sms/smslist) 添加应用后生成的实际SdkAppid,示例如1400006666。
                "SmsSdkAppid"=> $appid,// "1400283298" //
                // 必填： 是.类型： Array Of String.描述：下发手机号码，采用 e.164 标准，+[国家或地区码][手机号] ，
                // 示例如：+8613711112222， 其中前面有一个+号 ，86为国家码，13711112222为手机号。最多不要超过200个手机号。
                "PhoneNumberSet" => $phoneNumbers,// ["15829686962","15591017827"],
                // 必填： 是.类型： String.描述：模板 ID，必须填写已审核通过的模板 ID。模板ID可登录[短信控制台](https://console.cloud.tencent.com/sms/smslist)查看。
                "TemplateID"=> $templateId,// "468796",
                // 必填： 否.类型： String.描述：短信签名内容，使用 UTF-8 编码，必须填写已审核通过的签名 签名信息可登录[短信控制台](https://console.cloud.tencent.com/sms/smslist) 查看。
                "Sign"=> $smsSign,// "村沃沃",
                // 必填： 否.类型：  Array Of String..描述：模板参数，若无模板参数，则设置为空。
                "TemplateParamSet"=> $TemplateParamSet,// ["注册","8894","3"],
            ];
            // ExtendCode 必填： 否.类型： Integer.描述： 短信码号扩展号，默认未开通，如需开通请联系 [sms helper](https://cloud.tencent.com/document/product/382/3773)
            if(isset($dataParams['ExtendCode']) && is_numeric($dataParams['ExtendCode'])) $params['ExtendCode'] = $dataParams['ExtendCode'];
            // SessionContext 必填： 否.类型： String.描述：用户的 session 内容，可以携带用户侧ID等上下文信息,server 会原样返回
            if(isset($dataParams['SessionContext']) && is_string($dataParams['SessionContext']) && strlen(trim($dataParams['SessionContext'])) > 0) $params['SessionContext'] = $dataParams['SessionContext'];
            // SenderId 必填： 否.类型： String.描述：国际/港澳台短信senderid，国内短信填空。 默认未开通，如需开通请联系 [sms helper](https://cloud.tencent.com/document/product/382/3773)
            if(isset($dataParams['SenderId']) && strlen(trim($dataParams['SenderId'])) > 0) $params['SenderId'] = $dataParams['SenderId'];

            // $params = '{"PhoneNumberSet":["15829686962","15591017827"],"TemplateID":"468796","Sign":"村沃沃","TemplateParamSet":["注册","8894","3"],"SmsSdkAppid":"1400283298"}';
            // 数组转为json
            $params = json_encode($params);
            $req->fromJsonString($params);


            $resp = $client->SendSms($req);
            $rsp = json_decode($resp->toJsonString(), true);
            return  $rsp;
            // print_r($resp->toJsonString());
        }
        catch(TencentCloudSDKException $e) {
            $errMsg = $e->getMessage();
            if($errDo == 1) throws($errMsg);
            return $errMsg;
            // echo $e;
        }
    }




}