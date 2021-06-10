<?php
namespace App\Services\AlibabaCloud;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class AlibabaAPI
{
    /**
   public static function do($accessKeyId = '', $accessKeySecret = '',  $regionId = 'cn-hangzhou'){
       // 设置一个全局客户端
       AlibabaCloud::accessKeyClient('key', 'secret')
           ->regionId('cn-hangzhou')// 请替换为自己的 Region ID
           ->asGlobalClient();

       try {
           // 目前支持部分产品快捷调用，例如：
           $result = AlibabaCloud::ecs() // 指定产品
           ->v20140526() // 指定版本
           ->describeRegions() // 指定接口
           ->withResourceType('type') // API参数以 with 开头
           ->request(); // 执行请求

           // 对于没有支持快捷调用的产品您可以发起自定义请求，以上一个请求为例：
           $result2 = AlibabaCloud::rpcRequest() // 指定接口风格
           ->product('Ecs') // 指定产品
           ->version('2014-05-26') // 指定版本
           ->action('DescribeRegions') // 指定接口
           ->options([
               'query' => [
                   'ResourceType' => 'type', // 参数设定
               ],
           ])->request(); // 执行请求

           // 访问结果里的 Regions 字段
           print_r($result['Regions']);
       } catch (ClientException $exception) {
           echo $exception->getMessage() . PHP_EOL;
       } catch (ServerException $exception) {
           echo $exception->getMessage() . PHP_EOL;
           echo $exception->getErrorCode() . PHP_EOL;
           echo $exception->getRequestId() . PHP_EOL;
           echo $exception->getErrorMessage() . PHP_EOL;
       }
   }
     * */
    /**
     * 调用SendSms发送短信。
     * SendSms接口是短信发送接口，支持在一次请求中向多个不同的手机号码发送同样内容的短信。
     * 如果您需要在一次请求中分别向多个不同的手机号码发送不同签名和模版内容的短信，请使用SendBatchSms接口。
     * 调用该接口发送短信时，请注意：
     * 发送短信会根据发送量计费，价格请参考计费说明。
     * 在一次请求中，最多可以向1000个手机号码发送同样内容的短信。
     * @param string $accessKeyId
     * @param string $accessKeySecret
     * @param string $regionId  地域和可用区 https://help.aliyun.com/document_detail/40654.html?spm=a2c6h.13066369.0.0.54a120f89HVXHt
     * @param array $params
     *
     *  $params = [
     *      // 接收短信的手机号码。
     *      // 格式：
     *      // 国内短信：11位手机号码，例如15951955195。
     *      // 国际/港澳台消息：国际区号+号码，例如85200000000。
     *      // 支持对多个手机号码发送短信，手机号码之间以英文逗号（,）分隔。上限为1000个手机号码。批量调用相对于单条调用及时性稍有延迟。
     *      // 说明 验证码类型短信，建议使用单独发送的方式。
     *      // 必填
     *      'PhoneNumbers' => '15900000000',
     *      // 短信签名名称。请在控制台签名管理页面签名名称一列查看。
     *      // 说明 必须是已添加、并通过审核的短信签名。
     *      // 必填
     *      'SignName' => '阿里云',
     *      // 短信模板ID。请在控制台模板管理页面模板CODE一列查看。
     *      // 说明 必须是已添加、并通过审核的短信签名；且发送国际/港澳台消息时，请使用国际/港澳台短信模版。
     *      // 必填
     *      'TemplateCode' => 'SMS_153055065',
     *      // 主账号AccessKey的ID
     *      // 选填
     *       'AccessKeyId' => 'LTAIP00vvvvvvvvv',
     *      // 系统规定参数。取值：SendSms。
     *      // 选填
     *      'Action' => 'SendSms',
     *      // 外部流水扩展字段。
     *      // 选填
     *      'OutId' => 'abcdefgh',
     *      // 上行短信扩展码，无特殊需要此字段的用户请忽略此字段。
     *      // 选填
     *      'SmsUpExtendCode' => '90999',
     *      // 短信模板变量对应的实际值，JSON格式。说明 如果JSON中需要带换行符，请参照标准的JSON协议处理。
     *      // 选填
     *      'TemplateParam' => '{"code":"1111"}',
     *  ];
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return mixed sting 具体错误 ； throws 错误 数组(Code 为 OK 则说明请求成功)
     *
     *   $return = [
     *      'Message' => 'OK',// 状态码的描述。
     *      'RequestId' => '41751B02-9356-42DA-95C2-B705C0E25762',// 请求ID。
     *      'BizId' => ' 383804473357373193^0',// 发送回执ID，可根据该ID在接口QuerySendDetails中查询具体的发送状态。
     *      // 请求状态码。返回OK代表请求成功。
     *      // 其他错误码详见错误码列表。https://help.aliyun.com/document_detail/101346.html?spm=a2c1g.8271268.10000.125.772fdf25MMZXu0
     *      'Code' => 'OK',
     *      ];
     *
     */

    public static function SendSms($accessKeyId = '', $accessKeySecret = '',  $regionId = 'cn-hangzhou', $params = [], $errDo = 1){

        // 设置一个全局客户端

        try {
            AlibabaCloud::accessKeyClient($accessKeyId, $accessKeySecret)
                ->regionId($regionId)// 请替换为自己的 Region ID
                // ->asGlobalClient();
                ->asDefaultClient();

        } catch ( \Exception $e) {
            $errMsg = $e->getMessage();
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }finally{

        }

        $query = [
            'RegionId' => $regionId,// "cn-hangzhou",
        ];
        $query = array_merge($params, $query);

        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
//                    'query' => [
//                        'RegionId' => $regionId,// "cn-hangzhou",
//                    ],
                    'query' => $query
                ])
                ->request();
            return $result->toArray();
        } catch (ClientException $e) {
            // throws($e->getErrorMessage());
           // echo $e->getErrorMessage() . PHP_EOL;
            $errMsg = $e->getErrorMessage();
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        } catch (ServerException $e) {
          //  throws($e->getErrorMessage());
          //  echo $e->getErrorMessage() . PHP_EOL;
            $errMsg = $e->getErrorMessage();
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }

    }
}