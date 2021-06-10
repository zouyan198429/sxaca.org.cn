<?php
namespace App\Services\SMS;


use App\Notifications\SMSVerificationCodeNotification;
use App\Services\Redis\RedisString;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Support\Facades\Notification;

class LimitSMS
{

    /**
     * 验证手机验证码是否正确
     *
     * @param string $countryCode 国家码 '86' 阿里的暂时无用
     * @param string/array $mobile 需要发送的手机号 或一维数组
     * @param string $sms_key 验证证码关键字 config目录下 public.sms 下的下标
     * @param string $vercode 验证码
     * @param boolean $del_cache 通过验证是否删除缓存 true:删除：false:不删除
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return  mixed sting 具体错误(验证不通过) ； throws 错误 true:验证通过 ;
     * @author zouyan(305463219@qq.com)
     */
    public static function codeVerify($countryCode = '86', $mobile = '', $sms_key = '', $vercode = '', $del_cache = false, $errDo = 1){
        if(is_string($mobile) && strlen($mobile) <= 0){
            $errMsg = '手机号不能为空！';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }

        if(is_string($vercode) && strlen($vercode) <= 0){
            $errMsg = '验证码不能为空！';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }

        // 获得配置信息
        $smsConfig = static::getLimitConfig($sms_key, $errDo);
        if(is_string($smsConfig)){
            $errMsg = $smsConfig;
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }

        $operate_type = $smsConfig['operate_type'] ?? '';//  操作名称  注册
        $shuffle  = $smsConfig['shuffle'] ?? false;//如果配置文件config('easysms.default.gateways')有多个 值时，是否重新排序。 true:重新排序； false:不重新排序[默认]。
        $smsType =  $smsConfig['smsType'] ?? '';// 确定使用那个短信模板 类型类型 配置文件 config('easysms.gateways') 里看具体的 verification_code_params  验证码
        $limit_minute = $smsConfig['limit_minute'] ?? 3;// 验证码有效期，单位分钟
        $limits = $smsConfig['limits'] ?? [];// 可以有多个时间段及次数限止

        $keyPre = Tool::getProjectKey(1, ':', ':') . 'verificationCode:' . $smsType . ':';
        if(is_string($sms_key) && strlen($sms_key) > 0) $keyPre .= ($sms_key .  ':');
        $mobilePre = ( strlen($countryCode) > 0) ? $countryCode : '';
        if(!RedisString::exists($keyPre . $mobilePre . $mobile)){
            $errMsg = '验证码不存在或已过期，请重新获取验证码!！';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }

        if(Tool::getRedis($keyPre . $mobilePre . $mobile, 3) != $vercode){
            $errMsg = '验证码错误！';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }

        // 验证通过删除缓存
        if($del_cache) Tool::delRedis($keyPre . $mobilePre . $mobile);
        return true;

    }

    /**
     * 发送SMS--有在指定时间段内的次数限止
     *
     * @param string $countryCode 国家码 '86' 阿里的暂时无用
     * @param string/array $mobile 需要发送的手机号 或一维数组
     * @param string $sms_key 验证证码关键字 config目录下 public.sms 下的下标
     * @param array $templateParams 其它数据参数 --一维数组，短信模板替换用
     * @param string $mobile_vercode 需要发送的手机验证码,为空：则自动生成4位数字
     * @param array $smsConfig 短信配置信息 --一维数组；可参考配置文件 public文件下的sms下面
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return  mixed sting 具体错误(验证不通过) ； throws 错误 true:发送成功 ;
     * @author zouyan(305463219@qq.com)
     */
    public static function sendSMSLimit($countryCode = '86', $mobile = '', $sms_key = '', &$templateParams = [], &$mobile_vercode = '', &$smsConfig = [], $errDo = 1){
        // 获得配置信息
        if(empty($smsConfig)) $smsConfig = static::getLimitConfig($sms_key, $errDo);
        if(is_string($smsConfig)){
            $errMsg = $smsConfig;
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }

        // 自动生成验证码
        if(strlen($mobile_vercode) <= 0) $mobile_vercode = Tool::generatePassword(4, 1);

        $operate_type = $smsConfig['operate_type'] ?? '';//  操作名称  注册
        $shuffle  = $smsConfig['shuffle'] ?? false;//如果配置文件config('easysms.default.gateways')有多个 值时，是否重新排序。 true:重新排序； false:不重新排序[默认]。
        $smsType =  $smsConfig['smsType'] ?? '';// 确定使用那个短信模板 类型类型 配置文件 config('easysms.gateways') 里看具体的 verification_code_params  验证码
        $limit_minute = $smsConfig['limit_minute'] ?? 3;// 验证码有效期，单位分钟
        $limits = $smsConfig['limits'] ?? [];// 可以有多个时间段及次数限止

        $keyPre = Tool::getProjectKey(1, ':', ':') . 'verificationCode:' . 'limit:' . $sms_key  . $countryCode;
        foreach($limits as $k => $v){
            $limit_key = $v['limit_key'] ?? $k;// 限止键
            $valid_second = $v['valid_second'] ?? 60;// 单位时间（有效期），单位秒
            $limit_count = $v['limit_count'] ?? 2;// 单个手机号，限止发送的次数（指定时间内）

            // 判断单位时间内是否已经达到上限
            $chcheLimitKey = $keyPre . ':' . $limit_key . ':' . $mobile;
            $requestLimit = Tool::limitIncr($chcheLimitKey, $valid_second, $limit_count, 1, '短信次数超限!', $errDo);
            if(!is_numeric($requestLimit) && is_string($requestLimit)){
                $errMsg = $requestLimit;
                if($errDo == 1) throws($errMsg);
                return $errMsg;
            }

        }

        // 发送验证码
        $templateParams = array_merge($templateParams, [
            'code' => $mobile_vercode,// '78658', // 验证码
            'validMinute' => $limit_minute,// 4// 有效时间(单位分钟) // 有缓存时间[有此下标]，则缓存
        ]);
        $reslutSMS = static::sendSMS($countryCode, $mobile, $sms_key, $templateParams, $smsConfig, $errDo);
        if(is_string($reslutSMS)){
            $errMsg = $reslutSMS;
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }
        return true;
    }

    /**
     * 发送SMS--没有次数限止
     *
     * @param string $countryCode 国家码 '86' 阿里的暂时无用
     * @param string/array $mobile 需要发送的手机号 或一维数组
     * @param string $sms_key 验证证码关键字 config目录下 public.sms 下的下标
     * @param array $templateParams 其它数据参数 --一维数组，短信模板替换用
     * @param array $smsConfig 配置参数 --一维数组，为空，则自动重新获取
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return  mixed sting 具体错误(验证不通过) ； throws 错误 true:发送成功 ;
     * @author zouyan(305463219@qq.com)
     */
    public static function sendSMS($countryCode = '86', $mobile = '', $sms_key = '', $templateParams = [], $smsConfig = [], $errDo = 1){
        // 获得配置信息
        if(empty($smsConfig) || (!is_array($smsConfig))){
            $smsConfig = static::getLimitConfig($sms_key, $errDo);
            if(is_string($smsConfig)){
                $errMsg = $smsConfig;
                if($errDo == 1) throws($errMsg);
                return $errMsg;
            }
        }

        $operate_type = $smsConfig['operate_type'] ?? '';//  操作名称
        $shuffle  = $smsConfig['shuffle'] ?? false;//如果配置文件config('easysms.default.gateways')有多个 值时，是否重新排序。 true:重新排序； false:不重新排序[默认]。
        $smsType =  $smsConfig['smsType'] ?? '';// 确定使用那个短信模板 类型类型 配置文件 config('easysms.gateways') 里看具体的 verification_code_params  验证码
        $limit_minute = $smsConfig['limit_minute'] ?? 3;// 验证码有效期，单位分钟
        $limits = $smsConfig['limits'] ?? [];// 可以有多个时间段及次数限止

        // 发送验证码

        // twilio 发送手机短信

//        $sid = "AC0b60519b6f96dd9b07fce36c27854369"; // Your Account SID from www.twilio.com/console
//        $token = "454101e1fee0c1865e9067070785e6cb"; // Your Auth Token from www.twilio.com/console
//
//        $client = new \Twilio\Rest\Client($sid, $token);
//        $message = $client->messages->create(
//            '8615829686962',// '8881231234', // Text this number
//            array(
//                'from' => '12562697793',// '9991231234', // From a valid Twilio number
//                'body' => '尊敬的用户：您的注册验证码2569，请在3分钟内使用，工作人员不会索取，请勿泄漏。!'
//            )
//        );
//
//        pr($message->sid);

        // die;// print $message->sid;

        // 手机短信 动调 腾讯云 SMS

//        $qcloud = config("easysms.gateways.qcloud");
//        $accessKeyId = $qcloud['sdk_app_id'] ?? '';
//        $accessKeySecret = $qcloud['app_key'] ?? '';
//        $verificationConfig = $qcloud['verification_code_params'] ?? [];
//        $countryCode = '86';
//        $temMobile = [15591017827];
//        $temDataParams =  [
//               // 'operateType' => '注册',// '注册', //操作类型 注册--- 腾讯验证码的模板参数不能有中文及字母，只能是<=6位的数字
//                'code' => 4028, // 验证码
//                'validMinute' => 3// 有效时间(单位分钟) // 有缓存时间[有此下标]，则缓存
//            ];
//        $regionId = $qcloud['regionId'] ?? '';


//        // 开发2.0-老版本，不建议使用了。
//        // $result = \App\Services\SMS\SendSMS::sendVerificationCodeQcloud($accessKeyId, $accessKeySecret, $countryCode, $temMobile, $temDataParams, $verificationConfig,  $regionId, 2);
//
//        // 通过接口访问时的 SecretId 密钥 必填： 是.类型： String.描述：短信SdkAppid在[短信控制台](https://console.cloud.tencent.com/sms/smslist) 添加应用后生成的实际SdkAppid,示例如1400006666。
//        $SecretId = $qcloud['secret_id'] ?? '';
//        //  通过接口访问时的 SecretKey 密钥
//        $SecretKey = $qcloud['secret_key'] ?? '';
//        $result = \App\Services\SMS\SendSMS::sendSMSQcloud($accessKeyId, $SecretId, $SecretKey, $countryCode, $temMobile, $temDataParams, $verificationConfig,  $regionId, 2);
//        dd($result);

        // 手机验证码
        if( !is_array($mobile) ) $mobile = explode(',', $mobile);
        $dataParams = [
            'shuffle' => $shuffle,// true,// 如果配置文件config('easysms.default.gateways')有多个 值时，是否重新排序。 true:重新排序； false:不重新排序[默认]。
            'smsType' => $smsType,// 'verification_code_params',// 类型类型 配置文件 config('easysms.gateways') 里看具体的 verification_code_params  验证码
            'countryCode' => $countryCode,// 86,// 有的需要 国家码 '86' 阿里的暂时无用
            'mobile' => $mobile, // ['15591017827']  ,
            'operateKey' => $sms_key,// 'reg',// 验证证码缓存的键关键字
            'dataParams' => [
                'operateType' => $operate_type,// '注册',// '注册', //操作类型 注册--- 腾讯验证码的模板参数不能有中文及字母，只能是<=6位的数字
                // 'code' => $mobile_vercode,// '78658', // 验证码
                // 'validMinute' => $limit_minute,// 4// 有效时间(单位分钟) // 有缓存时间[有此下标]，则缓存
            ],
        ];
        if(is_array($templateParams) && (!empty($templateParams))) $dataParams['dataParams'] = array_merge($dataParams['dataParams'], $templateParams);
        Notification::send((object) $dataParams, new SMSVerificationCodeNotification());

        // User::find(7)->notify(new SMSVerificationCodeNotification($dataParams));
        return true;
    }

    /**
     * 获得发送限止配置
     *
     * @param string $sms_key 验证证码关键字 config目录下 public.sms 下的下标
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return  mixed sting 具体错误(验证不通过) ； throws 错误 array:限止配置 ;
     *
     * [// 注册
     *      'operate_type' => '注册',// 操作名称
     *      'shuffle' => true,// 如果配置文件config('easysms.default.gateways')有多个 值时，是否重新排序。 true:重新排序； false:不重新排序[默认]。
     *      'smsType' => 'verification_code_params', // 确定使用那个短信模板 类型类型 配置文件 config('easysms.gateways') 里看具体的 verification_code_params  验证码
     *      'limit_minute' => 3,// 验证码有效期，单位分钟 ---如果没有缓存，可以没有此下标
     *      'limits' => [// 可以有多个时间段及次数限止
     *          [// 一天 10次
     *              'limit_key' => 'oneDay',// 限止键
     *              'valid_second' => 60 * 60 * 24 ,// 单位时间（有效期），单位秒 ---如果没有缓存，可以没有此下标
     *              'limit_count' => 10,// 单个手机号，限止发送的次数（指定时间内） ---如果没有缓存，可以没有此下标
     *          ],
     *          [// 一月 30次
     *              'limit_key' => 'oneMonth',// 限止键
     *              'valid_second' => 60 * 60 * 24 * 30 ,// 单位时间（有效期），单位秒 ---如果没有缓存，可以没有此下标
     *              'limit_count' => 30,// 单个手机号，限止发送的次数（指定时间内） ---如果没有缓存，可以没有此下标
     *          ],
     *          [// 半年 50次
     *              'limit_key' => 'halfYear',// 限止键
     *              'valid_second' => 60 * 60 * 24 * 30 * 6 ,// 单位时间（有效期），单位秒 ---如果没有缓存，可以没有此下标
     *              'limit_count' => 50,// 单个手机号，限止发送的次数（指定时间内） ---如果没有缓存，可以没有此下标
     *           ],
     *       ],
     *  ]
     * @author zouyan(305463219@qq.com)
     */
    public static function getLimitConfig($sms_key = '', $errDo = 1){
        $configKey = 'public.sms.' . $sms_key;
        $smsConfig = config($configKey);
        if(!is_array($smsConfig) || empty($smsConfig)){
            $errMsg = 'SMS配置信息有误!';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }
        return $smsConfig;

    }


}
