<?php

namespace App\Http\Controllers;

use AlibabaCloud\Dysmsapi\V20170525\SendSms;
use App\Business\BaseBusiness;
use App\Business\Controller\API\RunBuy\CTAPIJobsFailedBusiness;
use App\Jobs\ProcessTest;
use App\Mail\OrderShipped;
use \App\Events\OrderShipped as OrderShippedEvent;
use App\ModelsVerify\RunBuy\brands;
use App\ModelsVerify\RunBuy\brands_history;
use App\ModelsVerify\RunBuy\users;
use App\Notifications\InvoicePaid;
use App\Notifications\SMSVerificationCodeNotification;
use App\Services\AlibabaCloud\AlibabaAPI;
use App\Services\File\DownFile;
use App\Services\Limit\LimitHandle;
use App\Services\Request\API\Sites\APIRunBuyRequest;
use App\Services\Common;
use App\Services\Request\API\HttpRequest;
use App\Services\Request\CommonRequest;
use App\Services\Secure\AesDesCrypt;
use App\Services\SessionCustom\SessionCustom;
use App\Services\Tool;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Lvht\GeoHash;

class IndexController extends WorksController
{
    /**
     * 注册
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function test(Request $request)
    {
        phpinfo();
        // 锁测试
        $data ='外部数据<br/>';
        $aaa = '外部aaa<br/>';
        $doFun = function($infoConfig) use(&$data, &$aaa){
//            echo '$data=' .  $data . '<br/>';
//            echo '$aaa=' .  $aaa . '<br/>';
            return false;
        };
//        $doFunTwo = function($infoConfig) use(&$data, &$aaa){
////            echo '$data=' .  $data . '<br/>';
////            echo '$aaa=' .  $aaa . '<br/>';
//            return false;
//        };
        $lockingFun =  function($infoConfig, $lockMsg, $failednNum) use(&$data, &$aaa){
//            echo '$data=' .  $data . '<br/>';
//            echo '$aaa=' .  $aaa . '<br/>';
            return true;
        };
        $lockedFun =  function(&$infoConfig, &$lockedInfo) use(&$data, &$aaa){
//            echo '$data=' .  $data . '<br/>';
//            echo '$aaa=' .  $aaa . '<br/>';
            return true;
        };
        $doKey = 'reg';
        $doName = '登录';
        $limitConfig = [
            [
                'cacheKey' => '1',// 整个二维数组中-唯一，作为缓存键的名称部分的
                'unitTime' => 60 * 2,// 单位时间内 单位:秒，如10分钟，可以为零：代表所有时间内
//                'doFun' =>  $doFun,// 具体执行什么操作，返回值 true:成功；false:失败 ；参数 $infoConfig-单个限定配置， 通过use 引用传参到函数体内
                'errNum' => 4,// 连续出错次数, 最小为1（> 0）
                'lockTime' => 30,// 锁定时长，单位:秒; 如果值<=0 ，则值为 unitTime 的值, 如果真为0(unitTime 也为 0时)，代表永久锁定
                'lockingFun' => $lockingFun, // 每次锁定时执行的具体操作--不再执行后面代码；返回值：无；参数 $infoConfig-单个限定配置, $lockedInfo--锁的Redis数组, $failednNum --失败次数， 通过use 引用传参到函数体内
                'lockedFun' => $lockedFun, // 每次已锁定状态时执行的具体操作--不再执行后面代码；返回值：无；参数 $infoConfig-单个限定配置, $lockedInfo--锁的Redis数组， 通过use 引用传参到函数体内
                'lockNums' => [// 二维数组 为空，则不做累积锁定限定
//                    [
//                        'limitTime' => 60 * 50 ,// 限定时间内；单位:秒，如10分钟；如果值<=0 ，则值为 unitTime 的值
//                        'limitLockNum' => 5,// 连续锁定次数
//                        'limitingFun' => $limitingFun,// 刚达到时，执行的操作，返回值 true:还要继续执行后面代码；false:不执行后面代码 ；无参函数， 通过use 引用传参到函数体内
//                        'limitedFun' => $limitedFun,// 最前面判断，如果已达到时，执行的操作，返回值 true:还要继续执行后面代码；false:不执行后面代码 ；无参函数， 通过use 引用传参到函数体内
//                    ]
                ],
            ],
//            [
//                'cacheKey' => '2',// 整个二维数组中-唯一，作为缓存键的名称部分的
//                'unitTime' => 60 * 5,// 单位时间内 单位:秒，如10分钟，可以为零：代表所有时间内
////                'doFun' =>  $doFun,// 具体执行什么操作，返回值 true:成功；false:失败 ；参数 $infoConfig-单个限定配置， 通过use 引用传参到函数体内
//                'errNum' => 4,// 连续出错次数, 最小为1（> 0）
//                'lockTime' => 60,// 锁定时长，单位:秒; 如果值<=0 ，则值为 unitTime 的值, 如果真为0(unitTime 也为 0时)，代表永久锁定
//                'lockingFun' => $lockingFun, // 每次锁定时执行的具体操作--不再执行后面代码；返回值：无；参数 $infoConfig-单个限定配置, $lockedInfo--锁的Redis数组, $failednNum --失败次数， 通过use 引用传参到函数体内
//                'lockedFun' => $lockedFun, // 每次已锁定状态时执行的具体操作--不再执行后面代码；返回值：无；参数 $infoConfig-单个限定配置, $lockedInfo--锁的Redis数组， 通过use 引用传参到函数体内
//                'lockNums' => [// 二维数组 为空，则不做累积锁定限定
////                    [
////                        'limitTime' => 60 * 50 ,// 限定时间内；单位:秒，如10分钟；如果值<=0 ，则值为 unitTime 的值
////                        'limitLockNum' => 5,// 连续锁定次数
////                        'limitingFun' => $limitingFun,// 刚达到时，执行的操作，返回值 true:还要继续执行后面代码；false:不执行后面代码 ；无参函数， 通过use 引用传参到函数体内
////                        'limitedFun' => $limitedFun,// 最前面判断，如果已达到时，执行的操作，返回值 true:还要继续执行后面代码；false:不执行后面代码 ；无参函数， 通过use 引用传参到函数体内
////                    ]
//                ],
//            ],
        ];

        try {
            LimitHandle::doLimit($doFun, $doKey, $doName, $limitConfig);

        } catch ( \Exception $e) {
            echo 'getMessage=' . $e->getMessage() . '<br/>';
            echo 'getCode=' . $e->getCode() . '<br/>';
            // throws($e->getMessage(), $e->getCode());
        }finally{
            // $lockObj->unlock($lockState);//解锁
            echo 'finally' . '<br/>';
        }
        // LimitHandle::clearLimit($doKey, $limitConfig);
        die('成功');
        // pr(Tool::formatSecondNum(60 * 60 * 24 * 365 + 60 * 60 * 24 + 60 * 60 * 5 + 60 + 56));
//        $currentTime = Carbon::now();// date('Y-m-d H:i:s');//当前时间 2020-06-02 15:48:49
//        $endTime = (Carbon::now())->addSeconds(61);
//        echo($endTime);
//        echo($currentTime);
//        die;
        // pr(date('Y-m-d H:i:s'));

//        $data = '12345678';
//        $aaa = SessionCustom::set('test', $data, 10);
//        echo 'set session_id()= ' . $aaa . '<br/>';
//        echo 'aget=' . SessionCustom::get('test') . '<br/>'; // 未过期，输出
//        sleep(10);
//        echo 'bget=' .  SessionCustom::get('test') . '<br/>'; // 已过期
//        // echo 'aaa';
//        die;

        // 自动续期
        $data = '12345678';
        if(false){
            $aaa = SessionCustom::set('test', $data, 0);
            echo 'set session_id()= ' . $aaa . '<br/>';
        }else{
            $aaa = 'get';
        }
        echo 'aget=' . SessionCustom::get('test', true,  function($sessionData) use(&$data, &$aaa){
               echo '$sessionData=' .  $sessionData . '<br/>';
                echo '$data=' .  $data . '<br/>';
                echo '$aaa=' .  $aaa . '<br/>';
            }) . '<br/>'; // 未过期，输出
        die;
//        echo 'hello word !!1';
//        // $aaa = Tool::getProjectKey(64, ':', ':');
//        $aaa = Tool::getProjectKey(1 | 2 | 4, ':', ':');
//        pr($aaa);
//        phpinfo();
//        die;
//         $ip = $request->ip();
//         dd($ip);

        // 多语言字段值配置

        //  $dbFileTag = 'models';
        // $langConfig = brands::getVerifyRuleArr($dbFileTag);
        // $langFieldsConfig = brands::getVerifyRule('fields', $dbFileTag);
        // $langConfig = brands_history::getVerifyRuleArr($dbFileTag);
        // $langFieldsConfig = brands_history::getVerifyRule('fields', $dbFileTag);
        // $langFieldsConfig = users::getVerifyRule('', $dbFileTag);
        // pr($langConfig);
        // pr($langFieldsConfig);

        //  Dingo API headers设置

//        $url = "http://runbuy.api.cunwo.net/api/test";
//        $params = [
//           'aaa' => 122222222,
//        ];
//        $http = new Client();
//        $result = $http->post($url,[
//            'json' => $params,
//            'headers' => [
//                'Accept' => "application/vnd.myInternalApIaPp.v1+json"
//            ]
//        ]);
//        if (200 != $result->getStatusCode()) {
//            throws('速通：请求失败 StatusCode: ' . $result->getStatusCode());
//        }
//
//        $ret = $result->getBody()->getContents();
//
//        $resultData = json_decode($ret, true);
//        pr($resultData);


//        try {
//            $res = 1/0;
//        } catch (\App\Exceptions\ExportException $e) {
//            throw new \App\Exceptions\ExportException($e->getMessage());
//        }
//        try {
//            $res = 1/0;
//        } catch ( \ErrorException $e) {
//            throw new \App\Exceptions\ExportException($e->getMessage());
//        }
//        try {
//            $res = 1/0;
//        } catch ( \Exception $e) {
//            throw new \App\Exceptions\ExportException($e->getMessage());
//        }
        // throws('aaaaaaaaa');
        // BaseBusiness::test();
        // die;

        // $dbDir = explode('\\', 'RunBuy\JobsFailed')[0] ?? 'aaa';
        // pr($dbDir);


        // 多语言配置自动验证数据
//        $judgeDataItem = [
//            'id' => 1,
//            'connection' => '2',
//            'queue' => '1',
//            'payload' => 'payload',
//            'exception' => 'ddd',
//            'ddd' => 'ddd',
//        ];
//        $judgeData = $judgeDataItem;
//        $mustFields = ['connection', 'queue'];
//        // $judgeData = [$judgeDataItem, $judgeDataItem];
//        // $result = Tool::judgeInDBData(0, $judgeData, $mustFields, 'failed_jobs', 'fields', 'RunBuy', 'models', 1);
//        $result = CTAPIJobsFailedBusiness::judgeDBDataThrowErr(1, $judgeData, $mustFields, 1, "<br/>", 'fields');
//        pr($result);

        // 获得语言文件的数据

        // echo __('passwords.apple');
        // echo __('aaa.user');
        // $dataArr = [];// 需要验证的数据 一维/二维
        // $mustFields = [];// 必填字段
        // $fieldsConfig = __('modelsRunBuyfailed_jobs.fields');
        // pr($fieldsConfig);
        // echo trans_choice('passwords.apples', 20);
        // echo trans_choice('passwords.minutes_ago', 5, ['value' => 5]);
        // die;


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
//        $dataParams = [
//            'shuffle' => true,// 如果配置文件config('easysms.default.gateways')有多个 值时，是否重新排序。 true:重新排序； false:不重新排序[默认]。
//            'smsType' => 'verification_code_params',
//            'countryCode' => 86,// 有的需要 国家码 '86' 阿里的暂时无用
//            'mobile' => ['15591017827']  ,
//            'operateKey' => 'reg',
//            'dataParams' => [
//                'operateType' => '注册',// '注册', //操作类型 注册--- 腾讯验证码的模板参数不能有中文及字母，只能是<=6位的数字
//                'code' => '78658', // 验证码
//                'validMinute' => 4// 有效时间(单位分钟) // 有缓存时间[有此下标]，则缓存
//            ],
//        ];
//        Notification::send((object) $dataParams, new SMSVerificationCodeNotification());

        // User::find(7)->notify(new SMSVerificationCodeNotification($dataParams));


        // 发送通知

        // 使用 Notifiable Trait
        // 通知可以通过两种方法发送: Notifiable trait 的 notify 方法或 Notification facade。

        // 首先，让我们来探讨下使用 trait：
        // 使用 Notifiable Trait
        // User::find(7)->notify(new InvoicePaid());

        // 使用 Notification Facade
        // 主要用在当你给多个可接收通知的实体发送的时候，比如给用户集合发送通知。使用 Facade 发送通知的话，要把可以接收通知和通知的实例传递给 send 方法：
        // Notification::send(User::find([7,10,11]), new InvoicePaid());


        // ~~~~~~~~~~~事件监听~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // $order = ['price' => 12.234,'','name' => '测试订单'];
        // event(new OrderShippedEvent($order));

        // ~~~~~~~~~~~队列~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // 生成任务类 放在 app/Jobs 目录
        // php artisan make:job ProcessPodcast
        // $order = ['price' => 12.234,'name' => '测试订单'];

        // 分发任务
        // 一旦你写完了你的任务类你就可以使用它自带的 dispatch 方法分发它。
        // 传递给 dispatch 方法的参数将会被传递给任务的构造函数：
        // ProcessTest::dispatch($order);

        // 延迟分发
        // 如果你想延迟你的队列任务的执行，你可以在分发任务的时候使用 delay 方法。
        //  例如，让我们详细说明一个十分钟之后才会执行的任务：
        // 注意：Amazon SQS 队列服务最大延迟 15 分钟的时间。
        // ProcessTest::dispatch($order)
        //    ->delay(now()->addMinutes(1));

        // 同步调度
        // 如果您想立即（同步）执行队列任务，可以使用 dispatchNow 方法。
        // 使用此方法时，队列任务将不会排队，并立即在当前进程中运行：
        // ProcessTest::dispatchNow($order);

        // 任务链
        // 任务链允许你具体定义一个按序列执行队列任务的列表。一旦序列中的任务失败了，剩余的工作将不会执行。
        // 要运行一个任务链，你可以对可分发的任务使用 withChain 方法：
        // 注意：使用 $this->delete() 方法删除队列任务不会阻止任务链任务执行。
        // 只有当任务链中的任务执行失败时，任务链才会停止执行。
//        ProcessTest::withChain([
//            // new OptimizePodcast,
//            // new ReleasePodcast
//            ProcessTest::dispatch($order),// ->delay(now()->addMinutes(1)),
//            ProcessTest::dispatch($order),// ->delay(now()->addMinutes(1))
//        ])->dispatch($order);

        // 链连接和队列
        // 如果你想定义用于任务链的默认连接和队列，你可以使用 allOnConnection 和 allOnQueue 方法。
        // 这些方法指定了所需队列的连接和队列 —— 除非队列任务被明确指定给了不同的连接 / 队列：
//        ProcessTest::withChain([
//            // new OptimizePodcast,
//            // new ReleasePodcast
//            ProcessTest::dispatch($order),// ->delay(now()->addMinutes(1)),
//            ProcessTest::dispatch($order),// ->delay(now()->addMinutes(1))
//        ])->dispatch($order)->allOnConnection('redis')->allOnQueue('podcasts');

        // 自定义连接 & 队列
        // 分发任务到指定队列
        // 通过将任务分发到不同队列，你可以将你的队列任务「分类」，甚至指定给不同队列分配的任务数量。
        // 记住，这不是推送任务到你定义的队列配置文件的不同的连接里，而是一个单一的连接。
        //  要指定队列，在分发任务时使用 onQueue 方法：
        // ProcessTest::dispatch($order)->onQueue('podcasts');

        // 分发任务到指定连接
        // 如果你在多队列连接中工作，你可以指定将任务分发到哪个连接。要指定连接，在分发任务时使用 onConnection 方法：
        // ProcessTest::dispatch($order)->onConnection('sqs');

        // 当然，你可以链式调用 onConnection 和 onQueue 方法来指定连接和队列：
        // ProcessTest::dispatch($order)->onConnection('sqs')->onQueue('podcasts');

        // 或者，可以将 connection 指定为任务类的属性：
        /**
         * 应该处理任务的队列连接.
         * 或者，可以将 connection 指定为任务类的属性：
         * @var string
         */
        // public $connection = 'sqs';


//        指定最大任务尝试次数 / 超时值
//        最大尝试次数
//        在一个任务重指定最大尝试次数可以通过 Artisan 命令的 --tries 选项 指定：
//        php artisan queue:work --tries=3

        // 你可能想通过任务类自身对最大任务尝试次数进行一个更颗粒化的处理。
        // 如果最大尝试次数是在任务类中定义的，它将优先于命令行中的值提供：
        /**
         * 任务可以尝试的最大次数。
         *
         * @var int
         */
        // public $tries = 5;

//    基于时间的尝试
//    作为另外一个选择来定义任务在失败前会尝试多少次，你可以定义一个任务超时时间。
//    这样的话，在给定的时间范围内，任务可以无限次尝试。要定义一个任务的超时时间，
//    在你的任务类中新增一个 retryUntil 方法：
//    Tip：你也可以在你的队列事件监听器中使用 retryUntil 方法。
//        /**
//         * 定义任务超时时间
//         *
//         * @return \DateTime
//         */
//        public function retryUntil()
//        {
//            return now()->addSeconds(5);
//        }

//        超时
//        注意：timeout 特性对于 PHP 7.1+ 和 pcntl PHP 扩展进行了优化.
//        同样的，任务执行最大秒数的数值可以通过 Artisan 命令行的 --timeout 选项指定。
//        php artisan queue:work --timeout=30

        // 然而，你可能也想在任务类自身定义一个超时时间。如果在任务类中指定，优先级将会高于命令行：
        /**
         * 任务可以执行的最大秒数 (超时时间)。
         *
         * @var int
         */
        // public $timeout = 120;

//        频率限制
//        注意：这个特性要求你的应用可以使用 Redis 服务器.
//            如果你的应用使用了 Redis，你可以通过时间或并发限制你的队列任务。
//        当你的队列任务通过同样有速率限制的 API 使用时，这个特性将很有帮助。
//        例如，使用 throttle 方法，你可以限制一个给定类型的任务每 60 秒只执行 10 次。
//        如果没有获得锁，一般情况下你应该将任务放回队列以使其可以被稍后重试。

//        Redis::throttle('key')->allow(10)->every(60)->then(function () {
//            // 任务逻辑...
//        }, function () {
//            // 无法获得锁...
//            return $this->release(10);
//        });

//        Tip：在上述的例子里，key 可以是任何你想要限制频率的任务类型的唯一识别字符串。
//        例如，使用构件基于任务类名的 key，或它操作的 Eloquent 模型的 ID。
//        注意：将受限制的作业释放回队列，仍然会增加工作的总数 attempts。
//
//        或者，你可以指定一个任务可以同时执行的最大数量。
//        在如下情况时这会很有用处：当一个队列中的任务正在修改资源时，一次只能被一个任务修改。
//        例如，使用 funnel 方法，你可以限制一个给定类型的任务一次只能执行一个处理器：

//        Redis::funnel('key')->limit(1)->then(function () {
//            // 任务逻辑...
//        }, function () {
//            // 无法获得锁...
//            return $this->release(10);
//        });

//        Tip：当使用频率限制时，任务执行成功的尝试的次数可能会难以确定。
//        所以，将频率限制与 时间限制 组合是很有作用的。

//        错误处理
//        如果在任务执行的时候出现异常，任务会被自动释放到队列中以再次尝试。
//        任务将会一直被释放直到达到应用允许的最大重试次数。
//        最大重试的数值由 queue:work Artisan 命令的 --tries 选项定义，或者在任务类中定义。
//        更多执行队列处理器的信息可以 在以下找到 。
//
//        排队闭包
//        你也可以直接调用闭包，而不是将任务类调度到队列中。这对于需要执行的快速、简单的任务非常有用：
//        $podcast = App\Podcast::find(1);
//        dispatch(function () use ($podcast) {
//            $podcast->publish();
//        });
//        将闭包分派给队列时，闭包的代码内容将以加密方式签名，因此无法在传输过程中对其进行修改。

//        运行队列处理器
//        Laravel 包含了一个队列处理器以将推送到队列中的任务执行。
//       你可以使用 queue:work Artisan 命令运行处理器。
//       注意一旦 queue:work 命令开始执行，它会一直运行直到它被手动停止或终端被关闭。
//        php artisan queue:work
//        Tip：要使 queue:work 进程一直在后台运行，
//        你应该使用进程管理器比如 Supervisor 来确保队列处理器不会停止运行
//
//        记住，队列处理器是一个常驻的进程并且在内存中保存着已经启动的应用状态。
//        因此，它们并不会在启动后注意到你代码的更改。所以，在你的重新部署过程中，请记得 重启你的队列处理器.
//
//        指定连接 & 队列
//        你也可以具体说明队列处理器应该使用哪个队列连接。
//        传递给 work 的连接名应该与你的 config/queue.php 配置文件中定义的连接之一相符。
//        php artisan queue:work redis

//        你甚至可以自定义你的队列处理器使其只执行连接中指定的队列。
//        例如，如果你的所有邮件都由 redis 连接的 emails 队列处理，
//        你可以使用如下的命令启动一个仅执行此队列的处理器：
//        php artisan queue:work redis --queue=emails

//        执行单一任务
//        --once 选项用于使队列处理器只处理队列中的单一任务。
//        php artisan queue:work --once
//
//        处理所有队列的任务然后退出
//        --stop-when-empty 选项可用于处理队列处理器处理所有作业然后优雅地退出。
//        如果您希望在队列为空后关闭容器，则在 Docker 容器中运行 Laravel 队列时，此选项很有用：
//        php artisan queue:work --stop-when-

//        资源注意事项
//        后台驻留的队列处理器不会在执行完每个任务后「重启」框架。
//        因此，你应该在每个任务完成后释放任何占用过大的资源。
//        例如，如果你正在用 GD 库执行图像处理，你应该在完成后使用 imagedestroy 释放内存。

//        队列优先级
//        有时你可能想确定队列执行的优先顺序。
//        例如在 config/queue.php 中你可以将 redis 连接的 queue 队列的优先级从 default 设置为 low。
//        然而， 偶尔你也想像如下方式将一个任务推送到 high 队列：
//        dispatch((new Job)->onQueue('high'));
//
//        要运行一个处理器来确认 low 队列中的任务在全部的 high 队列任务完成后才继续执行，
//        你可以传递一个逗号分隔的队列名列表作为 work 命令的参数。
//        php artisan queue:work --queue=high,low

//        队列处理器 & 部署
//        因为队列处理器是常驻进程，他们在重启前不会应用你代码的更改。
//        因此，部署使用队列处理器的应用最简单的方法是在部署进程中重启队列处理器。
//        你可以平滑地重启所有队列处理器通过使用 queue:restart 方法：
//        php artisan queue:restart
//
//        这个命令将会引导所有的队列处理器在完成当前任务后平滑「中止」，这样不会有丢失的任务。
//        由于在执行 queue:restart 后队列处理器将会中止，
//        所以你应该运行一个进程管理器例如 Supervisor 来自动重启队列处理器。
//        Tip：队列使用 缓存 存储重启信号，所以你应该确定在使用这个功能之前配置好缓存驱动。

//        任务过期 & 超时
//        任务过期
//        在你的 config/queue.php 配置文件中，每个队列连接都定义了一个 retry_after 选项。
//        这个选项指定了队列连接在重试一个任务前应该等它执行多久。
//        例如，如果 retry_after 的值设置为 90 ，那么任务在执行了 90 秒后将会被放回队列而不是删除它。
//        一般情况下，你应该将 retry_after 的值设置为你认为你的任务可能会执行需要最长时间的值。
//
//        注意：只有在 Amazon SQS 中不存在 retry_after 这个值。
//        SQS 将会以 AWS 控制台配置的 默认可见超时值 作为重试任务的依据。

//        处理器超时
//        queue:work Artisan 命令包含一个 --timeout 选项。
//        --timeout 选项指定了 Laravel 的队列主进程在中止一个执行任务的子进程之前需要等到多久。
//        有时一个子进程可能会因为各种原因「冻结」，比如一个外部的 HTTP 请求失去响应。
//        --timeout 选项会移除那些超过指定时间被冻结的进程。
//        php artisan queue:work --timeout=60
//
//        retry_after 配置项和 --timeout 命令行配置并不同，
//        但将它们同时使用可以确保任务不会丢失并且任务只会成功执行一次。
//        注意：--timeout 的值应该比你在 retry_after 中配置的值至少短几秒。
//        这会确保处理器永远会在一个任务被重试之前中止。
//        如果你的 --timeout 值比 retry_after 的值长的话，你的任务可能会被执行两次。

//        队列进程睡眠时间
//        当任务在队列中可用时，处理器将会一直无间隔地处理任务。 然而， sleep 选项定义了如果没有新任务的时候处理器将会「睡眠」多长时间。在处理器睡眠时，它不会处理任何新任务 —— 任务将会在队列处理器再次启动后执行。
//        php artisan queue:work --sleep=3

        // ~~~~~~~~~~~邮件发送~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // 生成可邮寄类 存放在 app/Mail 目录
        // php artisan make:mail OrderShipped
        // 邮件发送
        // 发送订单...
        // 收件人时将自动使用它们的 email 和 name 属性
//         $order=['price' => 12.234,'name' => '测试订单'];
//         $user = (object)[
//             'name' => '娅妮',
//             'email' => '305463219@qq.com'
//         ];
         // 在浏览器中预览 Mailable
        // return new OrderShipped($order);
        // 有时可能希望捕获 mailable 的 HTML 内容，而不发送它。可以调用 mailable 的 render 方法实现此目的。
        // return (new OrderShipped($order))->render();
        // 直接发送邮件
        // Mail::to($user)->send(new OrderShipped($order));
       //  Mail::to(User::find(7))->send(new OrderShipped($order));
        // 将邮件消息加入队列
        // Mail::to(User::find(7))->queue(new OrderShipped($order));
        // 延迟消息队列
        // $when = now()->addMinutes(10);
        // Mail::to(User::find(7))->later($when,new OrderShipped($order));
        // 推送到指定队列
//        $message = (new OrderShipped($order))
//            ->onConnection('sqs')
//            ->onQueue('emails');
//
//        Mail::to($request->user())
//            ->cc($moreUsers)
//            ->bcc($evenMoreUsers)
//            ->queue($message);
        // 本地化 Mailable
        // Laravel 允许你使用有别于当前语言的区域设置发送 mailable，即使被加入到队列中也保留该区域设置。
        //
        //为达到此目的， Mail facade 提供了 locale 方法设置目标语言。应用在格式化 mailable 是将切换到该区域设置，并在格式化完成后恢复到原来的区域设置：
        //
        //Mail::to($request->user())->locale('es')->send(
        //    new OrderShipped($order)
        //);
        // 用户首选区域设置
//        有时候，应用存储每个用户的首选区域设置。通过在一个或多个模型上实现 HasLocalePreference 契约，可以通知 Laravel 再发送邮件时使用预存的区域设置：
//
//        use Illuminate\Contracts\Translation\HasLocalePreference;
//
//        class User extends Model implements HasLocalePreference
//        {
//            /**
//             * 获取用户首选区域设置。
//             *
//             * @return string
//             */
//            public function preferredLocale()
//            {
//                return $this->locale;
//            }
//        }
//        一旦实现了此接口，Laravel 在向此模型发送 mailable 和通知时，将自动使用首选区域设置。因此在使用此接口时不需要调用 locale 方法：
//
//        Mail::to($request->user())->send(new OrderShipped($order));


//        echo (string) Str::uuid();// 1d0a24d0-5abe-426e-8473-870ed4575684
//        echo '<br/>';
//        echo  Str::orderedUuid();
//        die;
        phpinfo();
        die;
//        dd(Tool::getIp());
        $params = CommonRequest::getParamsByUbound($request, 2, false, [], []);
        /**
         *
         *  服务端接到这个请求：
         *  1 先验证sign签名是否合理，证明请求参数没有被中途篡改
         *  2 再验证timestamp是否过期，证明请求是在最近60s被发出的
         *  3 最后验证nonce是否已经有了，证明这个请求不是60s内的重放请求
         *
         */
        $res = CommonRequest::apiJudgeSign($request, $params, 1,  '111222333');
        if(is_string($res)) ajaxDataArr(0, null, $res);
        return ajaxDataArr(1, $res, '');
//        pr($res);
//        $secureTypeArr = [];//['md5' => []];
//        $nonceStr = HttpRequest::createNonce('',0, 10000, $secureTypeArr);
        return ajaxDataArr(1, $nonceStr, '');
//        $key = "456fggrhgfhhfghf中g";
//        $method = 'des-ede3';
//        $data = '中华人民共和国dfasfsdf1145';
//        $pass = AesDesCrypt::CommonEncrypt($method,$data, $key, 0, false, false);
//        echo $pass;
//        echo "<br>";
//
//        $src = AesDesCrypt::CommonDecrypt($method,$pass,$key, OPENSSL_ZERO_PADDING);
//
//        echo $src;
//        $hash = AesDesCrypt::hashHmac('sha1', $data, $key, false);
//        echo "<br>";
//        echo $hash;
//        $signature = Tool::getSignature($data, $key);
//        echo "<br>";
//        echo $signature;
//
//        die;


        $lng = 117.031689;
        $lat = 36.65396;
        // $hash = GeoHash::encode($lng,$lat);// wwe0x0euu12
        // vd($hash);
        // $nearHash  = GeoHash::expand('wwe0x0');// 附近8个
        // pr($nearHash);
        // $point = GeoHash::decode('wwe0x0');
        // pr($point);
        // $hash = geohash_encode($lat, $lng, 12);// wwe0x0euu12h
        // vd($hash);
        // $nearHash  = geohash_neighbors('wwe0x0');
        // pr($nearHash);
        // $point = geohash_decode('wwe0x0');
        // pr($point);
        return view('test');
    }

    public function test2(Request $request)
    {
        return view('test2');
    }

    /**
     * 首页
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function index(Request $request)
//    {
//        $this->InitParams($request);
//        $url = config('public.apiUrl') . config('apiUrl.common.index');
//        // 生成带参数的测试get请求
//        // $requestTesUrl = splicQuestAPI($url , $requestData);
//        $requestData['company_id'] = $this->company_id ;
//        $resData = HttpRequest::HttpRequestApi($url, $requestData, [], 'POST');
//        $resData['userInfo'] = $this->user_info;
//        return view('index',$resData);
//        return $this->exeDoPublicFun($request, 1, 1, 'admin.QualityControl.RrrDddd.index', true
//            , 'doListPage', [], function (&$reDataArr) use ($request){
//
//            });
//    }

    /**
     * welcome
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function welcome(Request $request)
    {
        return view('welcome');
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
        $urlArr = $request->server();
        $httpHost = $urlArr['HTTP_HOST'] ?? '';
        return redirect(Common::urlRedirect($httpHost, 1));
    }

    /**
     * 文件上传
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function upload(Request $request)
//    {
//        $errArr = [
//            'result' => 'failed',// 文件上传失败
//            'message' => '文件内容包含违规内容',//用于在界面上提示用户的消息
//        ];
//        // return $errArr;
//
//        $requestLog = [
//            'file'       =>$request->file('file'),
//            'files'       => $request->file(),
//            'posts'  => $request->post(),
//            'input'      => $request->input(),
//        ];
//        Log::info('上传文件日志',$requestLog);
//
//
//        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
//            $photo = $request->file('photo');
//            $extension = $photo->extension();
//            //$store_result = $photo->store('photo');
//            $save_path = 'resource/photo/'. date('Y/m/d/',time());
//            $save_name = Tool::createUniqueNumber(20) .'.' . $extension;
//            $store_result = $photo->storeAs($save_path, $save_name);
//            $output = [
//                'extension' => $extension,
//                'store_result' => $store_result
//            ];
//            $sucArr = [
//                'result' => 'ok',// 文件上传成功
//                'id' => 10001, // 文件在服务器上的唯一标识
//                'url'=> url($save_path . $save_name),//'http://example.com/file-10001.jpg',// 文件的下载地址
//                'output'  => $output,
//            ];
//            return $sucArr;
//            Log::info('上传文件日志',$output);
//            print_r($output);exit();
//        }
//        $errArr = [
//            'result' => 'failed',// 文件上传失败
//            'message' => '文件内容包含违规内容',//用于在界面上提示用户的消息
//        ];
//        return $errArr;
//        $sucArr = [
//            'result' => 'ok',// 文件上传成功
//            'id' => 10001, // 文件在服务器上的唯一标识
//            'url'=> 'http://example.com/file-10001.jpg',// 文件的下载地址
//        ];
//        return $sucArr;
//    }


    /**
     * 注册
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function reg(Request $request)
//    {
//        return view('reg');
//    }

    /**
     * 登陆
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function login(Request $request)
//    {
//        return view('login');
//    }

    /**
     * 注销
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function logout(Request $request)
//    {
//        $this->InitParams($request);
//        // session_start(); // 初始化session
//        //$userInfo = $_SESSION['userInfo'] ?? [];
//        /*
//        if(isset($_SESSION['userInfo'])){
//            unset($_SESSION['userInfo']); //保存某个session信息
//        }
//        */
//        $this->delUserInfo();
//        return redirect('/login');
//    }

    /**
     * err404
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function err404(Request $request)
    {
        return view('404');
    }


}
