<?php

namespace App\Notifications;
// 手机短信验证码
use App\Channels\SendSMSChannel;
use App\Services\Redis\RedisString;
use App\Services\SMS\SendSMS;
use App\Services\Tool;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/*
 *     调用方法
        $dataParams = [
            'shuffle' => true,// 如果配置文件config('easysms.default.gateways')有多个 值时，是否重新排序。 true:重新排序； false:不重新排序[默认]。
            'smsType' => 'verification_code_params',
            'countryCode' => 86,// 有的需要 国家码 '86' 阿里的暂时无用
            'mobile' => ['15829686962']  ,
            // 操作类型关键字【发送的是验证码需要此字段，缓存标识作用】
            // reg:注册 ; login:登录 ; modifyPassword: 修改密码; chmodMobile更变手机号认证码
            'operateKey' => 'reg';
            'dataParams' => [
                'operateType' => '',// '注册', //操作类型 注册--- 腾讯验证码的模板参数不能有中文及字母，只能是<=6位的数字
                'code' => '8894', // 验证码
                'validMinute' => 3// 有效时间(单位分钟) // 有缓存时间[有此下标]，则缓存
            ],
        ];
        Notification::send((object) $dataParams, new SMSVerificationCodeNotification());
 *
 */


class SMSVerificationCodeNotification extends BaseQueueNotifications
{
    // use Queueable;

   // public $SMSParams = [];

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($SMSParams = [])
    {
      //  $this->SMSParams = $SMSParams;
        // 如果用到自定义队列基类，需要调用父类构造函数
         parent::__construct();
        // 发送到的队列的名称.
        // $this->onQueue('notification');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
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
        Log::info('消息通知日志 --via-->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . __FUNCTION__, [$notifiable]);
        // return ['mail'];
        return [SendSMSChannel::class];
    }

    /**
     * 获取短信验证码形式的通知.
     *
     * @param  mixed  $notifiable
     * @return mixed
     */
    public function toSendSMS($notifiable)
    {
        // $SMSParams = $this->SMSParams;
        // $notifiable 的内容同上面的 via 方法中的说明
        Log::info('消息通知日志 --toSendSMS-->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . __FUNCTION__, [$notifiable]);// , $SMSParams
        // ...
        $smsConfig = config('easysms');
        // 默认可用的发送网关
        $gateways = $smsConfig['default']['gateways'] ?? [];
        $configs = $smsConfig['gateways'] ?? [];
        if(empty($gateways) || empty($configs)) return true;
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

        try {
            SendSMS::sendVerificationCode($shuffle, $smsType, $gateways, $configs, $countryCode, $mobiles, $dataParams, 1);

            // 缓存起来
            if($needCache){
//            if(count($mobiles) <= 1){
//                $mobiles = implode(',', $mobiles);
//                RedisString::setex($smsType . '_' . $mobiles, $validSecond, $code);
//                return true;
//            }
                $keyPre = Tool::getProjectKey(1, ':', ':') . 'verificationCode:' . $smsType . ':';
                if(is_string($operateKey) && strlen($operateKey)) $keyPre .= ($operateKey .  ':');
                $mobilePre = ( strlen($countryCode) > 0) ? $countryCode : '';
                foreach($mobiles as $mobile){
                    // RedisString::setex( $keyPre . $mobile, $validSecond, $code);
                    Tool::setRedis($keyPre, $mobilePre . $mobile, $code, $validSecond , 3);
                }
            }

            // 操作成功时，处理成功的业务逻辑 --可以不处理
            if(method_exists(new SendSMS,'sendSmsCodeStatusOperate'))  SendSMS::sendSmsCodeStatusOperate($notifiable, 1);
            return true;
        } catch ( \Exception $e) {
            $errMsg = $e->getMessage();
            Log::info('消息通知日志 --发送短信失败-->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . __FUNCTION__, [$mobiles, $countryCode, $code, $errMsg]);
            // 操作失败时，处理失败的业务逻辑 --可以不处理
            if(method_exists(new SendSMS,'sendSmsCodeStatusOperate')) SendSMS::sendSmsCodeStatusOperate($notifiable, 2, $errMsg);

            throws($errMsg);
            return false;
        }finally{

        }
 //       $result = SendSMS::sendVerificationCode(true, $smsType, $gateways, $configs, $mobile, $dataParams, 2);
//        if(is_string($result)) {
//            $errMsg = $result;
//            return false;
//        }
        // return true;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
//        return (new MailMessage)
//                    ->line('The introduction to the notification.')
//                    ->action('Notification Action', url('/'))
//                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
