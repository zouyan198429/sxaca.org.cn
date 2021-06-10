<?php
namespace App\Services\SMS;
// 发送短信

use App\Notifications\SMSSendNotification;
use App\Services\AlibabaCloud\AlibabaAPI;
use App\Services\Response\Data\CommonAPIFromDBBusiness;
use App\Services\Tencent\API30SDK\CloudSMS;
use App\Services\Tencent\QcloudSMS;
use App\Services\Tool;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class SendSMS
{

    // *****************下面是发送短信通用的********************************************************************************


    /**
     * 模板发送短信-- 一次默认发100条---按手机号批量或单个发送
     *
     * @param int $send_type 发送类型【1系统发送、2手动发送】
     * @param array $templateInfo 模板记录信息 --- 一维数组
     * @param array $smsConfigList 短信配置信息
     * @param array $gateways 默认可用的发送网关 ['yunpian:云片', 'aliyun:阿里云短信', 'qcloud:腾讯云']
     * @param string $templateContent 内容--内容中的参数都已经替换过的了
     * @param array $templateParams 参数值 ['参数下标' => '参数值', ...]
     * @param array $mobileArr 需要发送短信的手机数组；// [15829686962] 或  字符，多个用逗号分隔 15829686962,15829686962
     * @param string $countryCode 国家码 86
     * @param string $smsType 类型类型 配置文件 config('easysms.gateways') 里看具体的 verification_code_params  验证码
     * @param boolean $shuffle 如果配置文件config('easysms.default.gateways')有多个 值时，是否重新排序。 true:重新排序； false:不重新排序[默认]。
     * @return  null
     * @author zouyan(305463219@qq.com)
     */
    public static function sendSmsCommonBath($send_type = 1, $templateInfo = [], $smsConfigList = [], $gateways = ['aliyun', 'qcloud'], $templateContent = '',
                                             $templateParams = [], $mobileArr = [], $countryCode = 86, $smsType = 'verification_code_params',
                                             $shuffle = true){
        Tool::valToArrVal($mobileArr, ',');// 不是数组，则转为数组
        $chunkMobiles = array_chunk($mobileArr, 100);
        $currentNow = Carbon::now();
        foreach($chunkMobiles as $mobile){
            // 创建日志记录
            $smsData = [
                'country_code' => $countryCode,
                'mobile' => is_array($mobile) ? implode(',', $mobile) : $mobile,
                'sms_status' => 1,
                'count_date' => $currentNow->toDateString(),
                'count_year' => $currentNow->year,
                'count_month' => $currentNow->month,
                'count_day' => $currentNow->day,
                'template_id' => $templateInfo['id'] ?? 0,// 短信模板id
                'template_type' => $templateInfo['template_type'] ?? 0,// 模板类型【1腾讯云SMS、2阿里云短信】
                'send_type' => $send_type,// 发送类型【1系统发送、2手动发送】
                'sms_content' => $templateContent,
            ];
            $smsLogId = static::sendCommonSmsLogCreateOperate($smsData);

            // 手机验证码
            // $mobile = [15829686962];// 发送的手机号 , 15686165567
            // $shuffle = true;// 如果配置文件config('easysms.default.gateways')有多个 值时，是否重新排序。 true:重新排序； false:不重新排序[默认]。
            // $smsType = 'verification_code_params';// // 类型类型 配置文件 config('easysms.gateways') 里看具体的 verification_code_params  验证码
            // $countryCode = 86;
            // $sms_key = 'reg';  // --去掉不要了
            // $operate_type = '注册';  // --去掉不要了
            // 发送验证码
            // $mobile_vercode = Tool::generatePassword(4, 1);
            // $templateParams = [];
            // $templateParams = array_merge($templateParams, [
            // 'code' => $mobile_vercode,// '78658', // 验证码
            //'validMinute' => 4// 有效时间(单位分钟) // 有缓存时间[有此下标]，则缓存
            // ]);

            if( !is_array($mobile) ) $mobile = explode(',', $mobile);

            $dataParams = [
                'shuffle' => $shuffle,// true,// 如果配置文件config('easysms.default.gateways')有多个 值时，是否重新排序。 true:重新排序； false:不重新排序[默认]。
                'smsType' => $smsType,// 'verification_code_params',// 类型类型 配置文件 config('easysms.gateways') 里看具体的 verification_code_params  验证码
                'countryCode' => $countryCode,// 86,// 有的需要 国家码 '86' 阿里的暂时无用
                'mobile' => $mobile, // ['15591017827']  ,
                // 'operateKey' => $sms_key,// 'reg',// 验证证码缓存的键关键字 --去掉不要了
                'dataParams' => [
                    // 'operateType' => $operate_type,// '注册',// '注册', //操作类型 注册--- 腾讯验证码的模板参数不能有中文及字母，只能是<=6位的数字
                    // 'code' => $mobile_vercode,// '78658', // 验证码
                    // 'validMinute' => $limit_minute,// 4// 有效时间(单位分钟) // 有缓存时间[有此下标]，则缓存
                ],
                'gateways' => $gateways,// 默认可用的发送网关 ['yunpian:云片', 'aliyun:阿里云短信', 'qcloud:腾讯云']
                'smsConfig' => $smsConfigList,
                'smsLogId' => $smsLogId,
            ];

            if(is_array($templateParams) && (!empty($templateParams))) $dataParams['dataParams'] = array_merge($dataParams['dataParams'], $templateParams);
            Notification::send((object) $dataParams, new SMSSendNotification());
        }
    }

    //~~~~~~~~~~~~~~~~ 如果需要对发送成功或失败，进行业务操作，可以再这里进行，--不需要则不用写代码~~~~~~~~~~~~~~~~~~~~~~~~

    // 发送成功时对发送记录的操作--

    // $notifiable
    /**
     * [
     *   {
     *      "stdClass":{
     *          "shuffle":true,
     *          "smsType":"verification_code_params",
     *           "countryCode":"86",
     *           "mobile":["15829686962"],
     *           "operateKey":"reg",
     *            "dataParams":{
     *                  "operateType":"注册",
     *                  "code":"1849",
     *                  "validMinute":3
     *             }
     *         }
     *     }
     * ]
     *
     */

    /**
     * 发送验证码成功/失败时的业务逻辑操作--通用短信
     *
     * @param object $notifiable
     * @param int $smsLogId 日志记录id
     * @param int $operate_type 操作类型 1 成功 2 失败
     * @param string $failReason 失败的原因
     * @return null
     * @author zouyan(305463219@qq.com)
     */
    public static function sendCommonSmsLogStatusOperate(&$notifiable = null, $smsLogId = 0, $operate_type = 1, $failReason = ''){

        // $smsConfig = config('easysms');
        // 默认可用的发送网关
        // $gateways = $smsConfig['default']['gateways'] ?? [];
        // $configs = $smsConfig['gateways'] ?? [];
        // if(empty($gateways) || empty($configs)) return true;
        // $mobiles = $notifiable->mobile ?? [];// $SMSParams['mobile'] ?? [];// [15829686962];
        // $dataParams = $notifiable->dataParams?? [];// $SMSParams['dataParams'] ?? [];// ['code' => '87654'];
        // $smsType = $notifiable->smsType ?? '';;// $SMSParams['smsType'] ?? '';// 'verification_code_params';
        // $operateKey = $notifiable->operateKey ?? '';
        // $countryCode = $notifiable->countryCode ?? '86'; // 有的需要 国家码 '86' 阿里的暂时无用
        // $code = $dataParams['code'] ?? '';//
        // $validMinute = $dataParams['validMinute'] ?? 3;// 有缓存时间[有此下标]，则缓存 单位分钟
        // $needCache = false;
        // if(isset($dataParams['validMinute'])) $needCache = true;
        // $shuffle = $notifiable->shuffle ?? false;// 如果配置文件config('easysms.default.gateways')有多个 值时，是否重新排序。 true:重新排序； false:不重新排序[默认]。
        // if(!is_bool($shuffle)) $shuffle = false;

        // 分钟转为秒数
        // $validSecond = 0 ;// 缓存 单位秒
        // if(!is_numeric($validMinute) || $validMinute <= 0){
        //    $validMinute = 3;
        // }
        // $validSecond = $validMinute * 60;

        // ~~~~~~~~~~~处理短信记录表中的状态~~~TODO~~在这里写业务处理逻辑~~~~~~~~~~~~~~~~~~~~~~~
        // 短信配置信息
        // $smsTypeConfig = config('public.sms', []);
        // $smsConfigInfo = $smsTypeConfig[$operateKey] ?? '';
        // 配置为空，则直接返回
        // if(empty($smsConfigInfo)) return true;

        // $sms_type = $smsConfigInfo['sms_type'] ?? 0;

        // 修改验证码为已使用
        $sms_status = 2;

        // 失败
        if($operate_type == 2){
            $sms_status = 8;
        }

        $saveData = [
            'sms_status' => $sms_status,
        ];
        // 失败原因
        if($operate_type == 2) $saveData['fail_reason'] = $failReason;

        $smsQueryParams = [
            'where' => [
                 ['id', $smsLogId],
                // ['id', '&' , '16=16'],
                // ['country_code', $countryCode],
                // ['mobile', '=', $mobile],
                // ['sms_code', '=', $code],
                // ['sms_type', $sms_type],
                //['admin_type',self::$admin_type],
                // ['sms_status', 1],
            ],
            'whereIn' => [ 'sms_status' => [1,2,8]]
        ];
//        if(is_array($mobiles)){
//            $smsQueryParams['whereIn']['mobile'] = $mobiles;
//        }else{
//            if(!isset($smsQueryParams['where'])) $smsQueryParams['where'] = [];
//            array_push($smsQueryParams['where'], ['mobile', $mobiles]);
//        }

        $modelObj = null;
        $model_name = config('public.dbModelsDir') . '\SmsLog';// 'DogTools\SmsCode';

        CommonAPIFromDBBusiness::exeUpdate($saveData, $smsQueryParams, $model_name, $modelObj);
        return true;

    }

    /**
     * 创建短信日志
     *
     * @param object $notifiable
     * @param array $smsData 操作类型 1 成功 2 失败
     * @param string $failReason 失败的原因
     * @return int 日志记录id
     * @author zouyan(305463219@qq.com)
     */
    public static function sendCommonSmsLogCreateOperate($smsData = [])
    {
        // 记录发送短信记录
//        $currentNow = Carbon::now();
//        $smsData = [
//            'country_code' => $countryCode,
//            'mobile' => $mobile,
//            'sms_status' => 1,
//            'count_date' => $currentNow->toDateString(),
//            'count_year' => $currentNow->year,
//            'count_month' => $currentNow->month,
//            'count_day' => $currentNow->day,
//            'template_id' => 1,
//            'template_type' => 1,
//            'send_type' => 1,
//            'sms_content' => 1,
//        ];
        // $sms_id = 0;
        $modelObj = null;
        $model_name = config('public.dbModelsDir') . '\SmsLog';// 'DogTools\SmsCode';
        $infoObj = CommonAPIFromDBBusiness::exeCreate($smsData, $model_name, $modelObj);
        Log::info('消息通知日志 --发送短信记录日志--新加->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . __FUNCTION__, [$smsData, $infoObj]);
        return $infoObj->id ?? 0;

    }

    //~~~~~~~~~~~~~~~~~业务~~~结束~~~~~~~~~~~~~~~~~~~~~~~~~
    // *****************下面是发送短信验证码的********************************************************************************

    //~~~~~~~~~~~~~~~~ 如果需要对发送成功或失败，进行业务操作，可以再这里进行，--不需要则不用写代码~~~~~~~~~~~~~~~~~~~~~~~~

    // 发送成功时对发送记录的操作--

    // $notifiable
    /**
     * [
     *   {
     *      "stdClass":{
     *          "shuffle":true,
     *          "smsType":"verification_code_params",
     *           "countryCode":"86",
     *           "mobile":["15829686962"],
     *           "operateKey":"reg",
     *            "dataParams":{
     *                  "operateType":"注册",
     *                  "code":"1849",
     *                  "validMinute":3
     *             }
     *         }
     *     }
     * ]
     *
     */

    /**
     * 发送验证码成功/失败时的业务逻辑操作
     *
     * @param object $notifiable
     * @param int $operate_type 操作类型 1 成功 2 失败
     * @param string $failReason 失败的原因
     * @return null
     * @author zouyan(305463219@qq.com)
     */
    public static function sendSmsCodeStatusOperate(&$notifiable = null, $operate_type = 1, $failReason = ''){

        // $smsConfig = config('easysms');
        // 默认可用的发送网关
        // $gateways = $smsConfig['default']['gateways'] ?? [];
        // $configs = $smsConfig['gateways'] ?? [];
        // if(empty($gateways) || empty($configs)) return true;
        $mobiles = $notifiable->mobile ?? [];// $SMSParams['mobile'] ?? [];// [15829686962];
        $dataParams = $notifiable->dataParams?? [];// $SMSParams['dataParams'] ?? [];// ['code' => '87654'];
        $smsType = $notifiable->smsType ?? '';;// $SMSParams['smsType'] ?? '';// 'verification_code_params';
        $operateKey = $notifiable->operateKey ?? '';
        $countryCode = $notifiable->countryCode ?? '86'; // 有的需要 国家码 '86' 阿里的暂时无用
        $code = $dataParams['code'] ?? '';//
        $validMinute = $dataParams['validMinute'] ?? 3;// 有缓存时间[有此下标]，则缓存 单位分钟
        $needCache = false;
        if(isset($dataParams['validMinute'])) $needCache = true;
        $shuffle = $notifiable->shuffle ?? false;// 如果配置文件config('easysms.default.gateways')有多个 值时，是否重新排序。 true:重新排序； false:不重新排序[默认]。
        if(!is_bool($shuffle)) $shuffle = false;

        // 分钟转为秒数
        $validSecond = 0 ;// 缓存 单位秒
        if(!is_numeric($validMinute) || $validMinute <= 0){
            $validMinute = 3;
        }
        $validSecond = $validMinute * 60;

        // ~~~~~~~~~~~处理短信记录表中的状态~~~TODO~~在这里写业务处理逻辑~~~~~~~~~~~~~~~~~~~~~~~
        // 短信配置信息
        $smsTypeConfig = config('public.sms', []);
        $smsConfigInfo = $smsTypeConfig[$operateKey] ?? '';
        // 配置为空，则直接返回
        if(empty($smsConfigInfo)) return true;

        $sms_type = $smsConfigInfo['sms_type'] ?? 0;

        // 修改验证码为已使用
        $sms_status = 2;

        // 失败
        if($operate_type == 2){
            $sms_status = 8;
        }

        $saveData = [
            'sms_status' => $sms_status,
        ];
        // 失败原因
        if($operate_type == 2) $saveData['fail_reason'] = $failReason;

        $smsQueryParams = [
            'where' => [
                // ['id', '&' , '16=16'],
                ['country_code', $countryCode],
                // ['mobile', '=', $mobile],
                 ['sms_code', '=', $code],
                 ['sms_type', $sms_type],
                //['admin_type',self::$admin_type],
                // ['sms_status', 1],
            ],
            'whereIn' => [ 'sms_status' => [1,2,8]]
        ];
        if(is_array($mobiles)){
            $smsQueryParams['whereIn']['mobile'] = $mobiles;
        }else{
            if(!isset($smsQueryParams['where'])) $smsQueryParams['where'] = [];
            array_push($smsQueryParams['where'], ['mobile', $mobiles]);
        }

        $modelObj = null;
        $model_name = config('public.dbModelsDir') . '\SmsCode';// 'DogTools\SmsCode';

        CommonAPIFromDBBusiness::exeUpdate($saveData, $smsQueryParams, $model_name, $modelObj);
        return true;

    }


    //~~~~~~~~~~~~~~~~~业务~~~结束~~~~~~~~~~~~~~~~~~~~~~~~~
    //~~~~~~~~~~~~~~~~发送验证码~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    /**
     * 发送验证码[按优先顺序，轮留发送，至到一个成功]
     *
     * @param boolean $shuffle 参数$gateways有多个 值时，是否重新排序。 true:重新排序； false:不重新排序[默认]。
     * @param string $smsType 类型类型 配置文件 config('easysms.gateways') 里看具体的
     *                        verification_code_params  验证码
     * @param array $gateways  与配置文件 config('easysms.default.gateways')相同
     * @param array $configs  与配置文件 config('easysms.gateways')相同
     * @param string $countryCode 国家码 '86' 阿里的暂时无用
     * @param string/array  $mobiles 必填 字串/一维数组 接收短信的手机号码。国内短信：11位手机号码，例如15951955195。 国际/港澳台消息：国际区号+号码，例如85200000000。
     *                        支持对多个手机号码发送短信，手机号码之间以英文逗号（,）分隔。上限为1000个手机号码。批量调用相对于单条调用及时性稍有延迟。
     *                          说明 验证码类型短信，建议使用单独发送的方式。
     * @param array $dataParams 短信内的参数 ['code' => '验证码']
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return boolean 结果 true成功; sting 具体错误 ； throws 错误
     * @author zouyan(305463219@qq.com)
     */
    public static function sendVerificationCode($shuffle = false, $smsType = '', $gateways = [], $configs = [], $countryCode = '86', $mobiles = [], $dataParams = [], $errDo = 1){
        if(empty($mobiles)){
            $errMsg = '手机号码不能为空!';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }

        // 把数组中的元素按随机顺序重新排序
        // 该函数为数组中的元素分配新的键名。已有键名将被删除
        if($shuffle && count($gateways) > 0) shuffle($gateways);

        $errMsg = '';// 最后的错误文字
        foreach($gateways as $smsBusiness){
            $businessConfig = $configs[$smsBusiness] ?? [];
            if(empty($businessConfig)) continue;

            switch (strtolower($smsBusiness)) {
                // 阿里云短信
                case 'aliyun':
                    $accessKeyId = $businessConfig['access_key_id'] ?? '';
                    $accessKeySecret = $businessConfig['access_key_secret'] ?? '';
                    $sign_name = $businessConfig['sign_name'] ?? '';
                    $temMobile = $mobiles;
                    if(is_array($temMobile)) implode(',', $temMobile);
                    $regionId = $businessConfig['regionId'] ?? '';
                    // $verificationConfig =  $businessConfig['verification_code_params'] ?? [];
                    $verificationConfig =  $businessConfig[$smsType] ?? [];// 具体模板[如：验证码]相关参数
                    if(!isset($verificationConfig['SignName']) || strlen(trim($verificationConfig['SignName'])) <= 0) $verificationConfig['SignName'] = $sign_name;
                    $temDataParams = $dataParams;
                    $temParams = $businessConfig['template_params'][$smsType] ?? [];// ['code']//  短信模板替换参数
                    Tool::formatArrKeys($temDataParams, Tool::arrEqualKeyVal($temParams), false);

                    try {
                        $result = static::sendVerificationCodeAliYun($accessKeyId, $accessKeySecret, $countryCode, $temMobile, $temDataParams, $verificationConfig,  $regionId, 2);
                        if(is_bool($result) && $result) return true;// 成功
                        if(is_string($result)) {
                            $errMsg = $result;// 最后的错误文字
                            continue 2;
//                            $errMsg = $result;
//                            if($errDo == 1) throws($errMsg);
//                            return $errMsg;
                        }

                    } catch ( \Exception $e) {
                        $errMsg = $e->getMessage();
                    }finally{

                    }
                    break;
                // 腾讯云
                case 'qcloud':
                    $accessKeyId = $businessConfig['sdk_app_id'] ?? '';
                    $accessKeySecret = $businessConfig['app_key'] ?? '';
                    $sign_name = $businessConfig['sign_name'] ?? '';
                    $temMobile = $mobiles;
                    // if(is_array($temMobile)) implode(',', $temMobile);
                    $regionId = $businessConfig['regionId'] ?? '';
                    // $verificationConfig =  $businessConfig['verification_code_params'] ?? [];
                    $verificationConfig =  $businessConfig[$smsType] ?? [];// 具体模板[如：验证码]相关参数
                    if(!isset($verificationConfig['SignName']) || strlen(trim($verificationConfig['SignName'])) <= 0) $verificationConfig['SignName'] = $sign_name;
                    $temDataParams = $dataParams;
                    $temParams = $businessConfig['template_params'][$smsType] ?? [];// ['code']//  短信模板替换参数
                    Tool::formatArrKeys($temDataParams, Tool::arrEqualKeyVal($temParams), false);

                    try {
                        // 开发2.0-老版本，不建议使用了。
                        // $result = static::sendVerificationCodeQcloud($accessKeyId, $accessKeySecret, $countryCode, $temMobile, $temDataParams, $verificationConfig,  $regionId, 2);
                        // 3.0 新接口
                        // 通过接口访问时的 SecretId 密钥 必填： 是.类型： String.描述：短信SdkAppid在[短信控制台](https://console.cloud.tencent.com/sms/smslist) 添加应用后生成的实际SdkAppid,示例如1400006666。
                        $SecretId = $businessConfig['secret_id'] ?? '';
                        //  通过接口访问时的 SecretKey 密钥
                        $SecretKey = $businessConfig['secret_key'] ?? '';
                        $result = static::sendSMSQcloud($accessKeyId, $SecretId, $SecretKey, $countryCode, $temMobile, $temDataParams, $verificationConfig,  $regionId, 2);
                        if(is_bool($result) && $result) return true;// 成功
                        if(is_string($result)) {
                            $errMsg = $result;// 最后的错误文字
                            continue 2;
//                            $errMsg = $result;
//                            if($errDo == 1) throws($errMsg);
//                            return $errMsg;
                        }

                    } catch ( \Exception $e) {
                        $errMsg = $e->getMessage();
                    }finally{

                    }
                    break;
                default:
            }
        }
        if(is_string($errMsg) && strlen($errMsg) > 0) {
            if ($errDo == 1) throws($errMsg);
            return $errMsg;
        }
        return true;
    }


    /**
     * 阿里云发送验证码
     *
     * @param string $accessKeyId  config('easysms.gateways.aliyun.access_key_id'),config('easysms.gateways.aliyun.access_key_secret')
     * @param string $accessKeySecret
     * @param string $countryCode 国家码 '86' 阿里的暂时无用
     * @param string/array  $mobiles 必填 字串/一维数组 接收短信的手机号码。国内短信：11位手机号码，例如15951955195。 国际/港澳台消息：国际区号+号码，例如85200000000。
     *                        支持对多个手机号码发送短信，手机号码之间以英文逗号（,）分隔。上限为1000个手机号码。批量调用相对于单条调用及时性稍有延迟。
     *                          说明 验证码类型短信，建议使用单独发送的方式。
     * @param array $dataParams 短信内的参数 ['code' => '验证码']
     * @param array $config 最多可参看下面 $params的下标
     *   $config = [
     *       // 短信签名名称。请在控制台签名管理页面签名名称一列查看。
     *      // 说明 必须是已添加、并通过审核的短信签名。
     *      // 必填
     *      'SignName' => '',
     *      // 短信模板ID。请在控制台模板管理页面模板CODE一列查看。
     *      // 说明 必须是已添加、并通过审核的短信签名；且发送国际/港澳台消息时，请使用国际/港澳台短信模版。
     *      // 必填
     *      'TemplateCode' => '',
     *      // 这是数组格式； 短信模板变量对应的实际值，JSON格式。说明 如果JSON中需要带换行符，请参照标准的JSON协议处理。
     *      // 选填
     *      // 'TemplateParam' => [],
     *  ];
     * @param string $regionId  地域和可用区 https://help.aliyun.com/document_detail/40654.html?spm=a2c6h.13066369.0.0.54a120f89HVXHt
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return boolean 结果 true成功; sting 具体错误 ； throws 错误
     * @author zouyan(305463219@qq.com)
     */
    public static function sendVerificationCodeAliYun($accessKeyId = '', $accessKeySecret = '', $countryCode = '86', $mobiles = '', $dataParams = [], $config = [],  $regionId = 'cn-hangzhou', $errDo = 1){
        // $config = config('easysms.gateways.aliyun');

        if(empty($mobiles)){
            $errMsg = '手机号码不能为空!';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }
        if(is_array($mobiles)) $mobiles = implode(',', $mobiles);

        $TemplateParam = $config['TemplateParam'] ?? [];

        $TemplateParam = array_merge($TemplateParam, $dataParams);
        // $TemplateParam = $dataParams;
        // 数组转为json
        $TemplateParam = json_encode($TemplateParam);

        // 发送短信
        $params = [
            // 接收短信的手机号码。
            // 格式：
            // 国内短信：11位手机号码，例如15951955195。
            // 国际/港澳台消息：国际区号+号码，例如85200000000。
            // 支持对多个手机号码发送短信，手机号码之间以英文逗号（,）分隔。上限为1000个手机号码。批量调用相对于单条调用及时性稍有延迟。
            // 说明 验证码类型短信，建议使用单独发送的方式。
            // 必填
            'PhoneNumbers' => $mobiles,// '15829686962',
            // 短信签名名称。请在控制台签名管理页面签名名称一列查看。
            // 说明 必须是已添加、并通过审核的短信签名。
            // 必填
       //     'SignName' => '村窝网',
            // 短信模板ID。请在控制台模板管理页面模板CODE一列查看。
            // 说明 必须是已添加、并通过审核的短信签名；且发送国际/港澳台消息时，请使用国际/港澳台短信模版。
            // 必填
       //     'TemplateCode' => 'SMS_177250925',
            // 主账号AccessKey的ID
            // 选填
            // 'AccessKeyId' => 'LTAIP00vvvvvvvvv',
            // 系统规定参数。取值：SendSms。
            // 选填
            // 'Action' => 'SendSms',
            // 外部流水扩展字段。
            // 选填
            // 'OutId' => 'abcdefgh',
            // 上行短信扩展码，无特殊需要此字段的用户请忽略此字段。
            // 选填
            // 'SmsUpExtendCode' => '90999',
            // 短信模板变量对应的实际值，JSON格式。说明 如果JSON中需要带换行符，请参照标准的JSON协议处理。
            // 选填
            'TemplateParam' => $TemplateParam,// '{"code":"2344"}',
        ];
        $params =  array_merge($config, $params);

        try {
            $result = AlibabaAPI::SendSms($accessKeyId, $accessKeySecret, $regionId, $params, $errDo);
            if(is_string($result)) {
                $errMsg = $result;
                if($errDo == 1) throws($errMsg);
                return $errMsg;
            }
            $Code = $result['Code'] ?? '';
            if($Code == 'OK') return true;
            $Message = $result['Message'] ?? '';
            $errMsg = '未知错误' . $Message;
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        } catch ( \Exception $e) {
            $errMsg = $e->getMessage();
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }finally{

        }

    }

    /**
     * 腾讯云发送验证码----开发2.0-老版本，不建议使用了。
     *
     * @param string $appid 短信应用 SDK AppID
     * @param string $appkey 短信应用 SDK AppKey
     * @param string $countryCode 国家码 '86'
     * @param string/array  $mobiles 必填 字串/一维数组 接收短信的手机号码。国内短信：11位手机号码，例如15951955195。或 最好传数组] [15951955195]
     *                        支持对多个手机号码发送短信，手机号码之间以英文逗号（,）分隔。上
     * @param array $dataParams 短信内的参数
     *   [
     *      'operateType' => '注册', //操作类型 注册
     *      'code' => '8894', // 验证码
     *     'validMinute' => 3// 有效时间(单位分钟) // 有缓存时间[有此下标]，则缓存
     *    ],
     * @param array $config 最多可参看下面 $params的下标
     *   $config = [
     *       // 短信签名名称。请在控制台签名管理页面签名名称一列查看。
     *      // 说明 必须是已添加、并通过审核的短信签名。
     *      // 必填
     *      'SignName' => '',
     *      // 短信模板ID。请在控制台模板管理页面模板CODE一列查看。
     *      // 说明 必须是已添加、并通过审核的短信签名；且发送国际/港澳台消息时，请使用国际/港澳台短信模版。
     *      // 必填
     *      'TemplateCode' => '',
     *      // 这是数组格式； 短信模板变量对应的实际值，JSON格式。说明 如果JSON中需要带换行符，请参照标准的JSON协议处理。
     *      // 选填
     *      // 'TemplateParam' => [],
     *  ];
     * @param string $regionId  地域和可用区 https://console.cloud.tencent.com/api/explorer?Product=sms&Version=2019-07-11&Action=SendSms&SignVersion=
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return boolean 结果 true成功; sting 具体错误 ； throws 错误
     * @author zouyan(305463219@qq.com)
     */
    public static function sendVerificationCodeQcloud($appid = '', $appkey = '', $countryCode = '86', $mobiles = [], $dataParams = [], $config = [],  $regionId = 'ap-beijing', $errDo = 1){
        // $config = config('easysms.gateways.aliyun');

        if(empty($mobiles)){
            $errMsg = '手机号码不能为空!';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }
        // if(is_array($mobiles)) $mobiles = implode(',', $mobiles);

        $TemplateParam = $config['TemplateParam'] ?? [];

        $TemplateParam = array_merge($TemplateParam, $dataParams);
        // $TemplateParam = $dataParams;
        // 数组转为json
        // $TemplateParam = json_encode($TemplateParam);

        // 发送短信
        $params = [
            'CountryCode' => $countryCode,// '86',// 国家码
            // 接收短信的手机号码。
            // 格式：
            // 国内短信：11位手机号码，例如15951955195。或 最好传数组] [15951955195]
            // 支持对多个手机号码发送短信，手机号码之间以英文逗号（,）分隔。
            // 必填
            'PhoneNumbers' => $mobiles,// '15829686962',
            // 签名参数使用的是`签名内容`，而不是`签名ID`。这里的签名"腾讯云"只是示例，真实的签名需要在短信控制台申请
            // 必填
            //  'SignName' => '腾讯云',
            // 这里的模板 ID`7839`只是示例，真实的模板 ID 需要在短信控制台中申请
            // 必填
            // 'TemplateCode' => '468796',
            // 短信模板变量对应的实际值，数组格式
            //    [
            //      'operateType' => '注册', //操作类型 注册
            //      'code' => '8894', // 验证码
            //      'validMinute' => 3// 有效时间(单位分钟) // 有缓存时间[有此下标]，则缓存
            //    ],
            // 选填
            'TemplateParam' => $TemplateParam,// '{"code":"2344"}',
        ];
        $params =  array_merge($config, $params);

        try {
            $result = QcloudSMS::SendSms($appid, $appkey, $regionId, $params, $errDo);
            if(is_string($result)) {
                $errMsg = $result;
                if($errDo == 1) throws($errMsg);
                return $errMsg;
            }
            $resultCode = $result['result'] ?? '';
            if($resultCode == '0' || $resultCode == 0) return true;
            $Message = $result['errmsg'] ?? '';
            $errMsg = '未知错误' . $Message;
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        } catch ( \Exception $e) {
            $errMsg = $e->getMessage();
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }finally{

        }

    }


    /**
     * 腾讯云发送验证码----开发3.0
     * @param string $appid 短信应用 SDK AppID
     * @param string $SecretId 通过接口访问时的 SecretId 密钥 必填： 是.类型： String.描述：短信SdkAppid在[短信控制台](https://console.cloud.tencent.com/sms/smslist) 添加应用后生成的实际SdkAppid,示例如1400006666。
     * @param string $SecretKey 通过接口访问时的 SecretKey 密钥
     * @param string $countryCode 国家码 '86'
     * // +[国家或地区码] 在此函数内处理，可不用加入到 $mobiles,当然也可以加入
     * //  必填： 是.类型： Array Of String.描述：下发手机号码，采用 e.164 标准，+[国家或地区码][手机号] ，
     * // 示例如：+8613711112222， 其中前面有一个+号 ，86为国家码，13711112222为手机号。最多不要超过200个手机号。
     * @param string/array  $mobiles
     * @param array $dataParams 短信内的参数 必填： 否.类型：  Array Of String..描述：模板参数，若无模板参数，则设置为空。
     *   [
     *      'operateType' => '注册', //操作类型 注册
     *      'code' => '8894', // 验证码
     *     'validMinute' => 3// 有效时间(单位分钟) // 有缓存时间[有此下标]，则缓存
     *    ],
     * @param array $config 最多可参看下面 $params的下标
     *   $config = [
     *       // 短信签名名称。请在控制台签名管理页面签名名称一列查看。
     *      // 说明 必须是已添加、并通过审核的短信签名。
     *      // 必填
     *      'SignName' => '',
     *      // 短信模板ID。请在控制台模板管理页面模板CODE一列查看。
     *      // 说明 必须是已添加、并通过审核的短信签名；且发送国际/港澳台消息时，请使用国际/港澳台短信模版。
     *      // 必填
     *      'TemplateCode' => '',
     *      // 这是数组格式； 短信模板变量对应的实际值，JSON格式。说明 如果JSON中需要带换行符，请参照标准的JSON协议处理。
     *      // 选填
     *      // 'TemplateParam' => [],
     *      // ExtendCode 必填： 否.类型： Integer.描述： 短信码号扩展号，默认未开通，如需开通请联系 [sms helper](https://cloud.tencent.com/document/product/382/3773)
     *     'ExtendCode' => ''
     *      // SessionContext 必填： 否.类型： String.描述：用户的 session 内容，可以携带用户侧ID等上下文信息,server 会原样返回
     *    'SessionContext' => ''
     *    // SenderId 必填： 否.类型： String.描述：国际/港澳台短信senderid，国内短信填空。 默认未开通，如需开通请联系 [sms helper](https://cloud.tencent.com/document/product/382/3773)
     *    'SenderId' => ''
     *  ];
     * @param string $regionId  地域和可用区 https://console.cloud.tencent.com/api/explorer?Product=sms&Version=2019-07-11&Action=SendSms&SignVersion=
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return boolean 结果 true成功; sting 具体错误 ； throws 错误
     * @author zouyan(305463219@qq.com)
     */
    public static function sendSMSQcloud($appid = '',$SecretId = '', $SecretKey = '', $countryCode = '86', $mobiles = [], $dataParams = [], $config = [],  $regionId = 'ap-beijing', $errDo = 1){
        // $config = config('easysms.gateways.aliyun');

        if(empty($mobiles)){
            $errMsg = '手机号码不能为空!';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }
        // if(is_array($mobiles)) $mobiles = implode(',', $mobiles);
        if(is_string($mobiles)) $mobiles = explode(',', $mobiles);
        if(!is_array($mobiles)) {
            $errMsg = '手机号码不能为空!';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }

        // 处理 +[国家或地区码]
        $countPre = '+';
        foreach($mobiles as $k => $v){
            // 有+号
            if (strpos($v, $countPre) !== false) continue;

            // 没有+号

            if(strlen($countryCode) <= 0){// 国家码为空,则直接加上 + 号
                $mobiles[$k] = $countPre . $v;
                continue;
            }

            // 判断是否加上国家码 如86
            if (strpos($v, $countryCode) !== 0){
                $v = $countryCode . $v;
            }

            // 加上 + 号
            $mobiles[$k] = $countPre . $v;
        }

        $TemplateParam = $config['TemplateParam'] ?? [];

        $TemplateParam = array_merge($TemplateParam, $dataParams);
        // $TemplateParam = $dataParams;
        // 数组转为json
        // $TemplateParam = json_encode($TemplateParam);

        // 发送短信
        $params = [
           // 必填： 是.类型： Array Of String.描述：下发手机号码，采用 e.164 标准，+[国家或地区码][手机号] ，
           // 示例如：+8613711112222， 其中前面有一个+号 ，86为国家码，13711112222为手机号。最多不要超过200个手机号。
            'PhoneNumbers' => $mobiles,// '15829686962',
           // 必填： 否.类型： String.描述：短信签名内容，使用 UTF-8 编码，必须填写已审核通过的签名 签名信息可登录[短信控制台](https://console.cloud.tencent.com/sms/smslist) 查看。
            //  'SignName' => '腾讯云',
           // 必填： 是.类型： String.描述：模板 ID，必须填写已审核通过的模板 ID。模板ID可登录[短信控制台](https://console.cloud.tencent.com/sms/smslist)查看。
            // 'TemplateCode' => '468796',
            // 必填： 否.类型：  Array Of String..描述：模板参数，若无模板参数，则设置为空。
            //    [
            //      'operateType' => '注册', //操作类型 注册
            //      'code' => '8894', // 验证码
            //      'validMinute' => 3// 有效时间(单位分钟) // 有缓存时间[有此下标]，则缓存
            //    ],
            // 选填
            'TemplateParam' => $TemplateParam,// '{"code":"2344"}',
        ];
        $params =  array_merge($config, $params);

        try {
            $result = CloudSMS::SendSms($appid, $SecretId, $SecretKey, $regionId, $params, $errDo);
            // Log::info('消息通知日志 --腾讯发送短信-->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . __FUNCTION__, [$appid, $SecretId, $SecretKey, $regionId, $params, $errDo, $result]);
            if(is_string($result)) {
                $errMsg = $result;
                if($errDo == 1) throws($errMsg);
                return $errMsg;
            }
            if(!isset($result['SendStatusSet'])){
                $errMsg = '未知错误' . ($result['Response']['Error']['Message'] ?? '');
                if($errDo == 1) throws($errMsg);
                return $errMsg;
            }

            $isSuccess = true;
            $errMsg = '';
            $SendStatusSet = $result['SendStatusSet'] ?? [];
            foreach($SendStatusSet as  $tem){
                $Code = $tem['Code'] ?? '';
                $Message = $tem['Message'] ?? '';
                $Fee = $tem['Fee'] ?? 0;
                if(!is_numeric($Fee) || $Fee <= 0 || strtolower(trim($Code)) != 'ok' ){
                    $isSuccess = false;
                    $errMsg = $Message;
                    break;
                }
            }
            if($isSuccess) return true;
            $errMsg = '错误' . $errMsg;
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        } catch ( \Exception $e) {
            $errMsg = $e->getMessage();
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }finally{

        }

    }
}
