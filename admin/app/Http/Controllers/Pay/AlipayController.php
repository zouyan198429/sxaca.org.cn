<?php

namespace App\Http\Controllers\Pay;

use App\Business\Controller\API\QualityControl\CTAPIAlipayAuthTokenBusiness;
use App\Business\Controller\API\QualityControl\CTAPIOrderPayBusiness;
use App\Services\alipaySdk\AlipayPayAPI;
use App\Services\pay\weixin\easyWechatPay;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class AlipayController extends BasicController
{
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

    /*

        $payConfig = [// $orderType 订单类型编号 1 订单号 2 退款订单 3 支付跑腿费  4 追加跑腿费 5 冲值  6 提现 7 压金或保证金
            'order_type' => [
                'operate_type' => 0,// 操作类型1充值2提现3交压金/保证金4订单付款5追加付款8退款16冻结32解冻
                'operate_text' => '',// 操作名称
            ]
        ];
     *
     */

    public static $payConfig = [
        '3' => [
            'operate_type' => 4,
            'operate_text' => '订单付款',
        ],
        '4' => [
            'operate_type' => 5,
            'operate_text' => '追加付款',
        ],
        '5' => [
            'operate_type' => 1,
            'operate_text' => '充值',
        ],
        '7' => [
            'operate_type' => 3,
            'operate_text' => '保证金',
        ],
    ];

    //  授权回调地址
    public function authRedirect(Request $request)
    {
        return $this->exeDoPublicFun($request, 0, 8, 'pay.QualityControl.Alipay.authRedirect', false
            , '', [], function (&$reDataArr) use ($request){

                // $this->InitParams($request);
                // 日志
                $requestLog = [
                    'files' => $request->file(),
                    'posts' => $request->post(),
                    'input' => $request->input(),
                    'post_data' => apiGetPost(),
                ];
                Log::info('支付宝支付日志-->授权回调' . __FUNCTION__, $requestLog);
                $app_id = CommonRequest::get($request, 'app_id');// 2021002124696736
                $source = CommonRequest::get($request, 'source');// alipay_app_auth
                $application_type = CommonRequest::get($request, 'application_type');// WEBAPP,MOBILEAPP
                $app_auth_code = CommonRequest::get($request, 'app_auth_code');// P78879f6aa936405c94a4118224d1a20
                $state = CommonRequest::get($request, 'state');// state 对应的值 在授权过程中，建议在拼接授权 URL 的时候，开发者可增加自己的一个自定义信息（即 URL 拼接规则中的 state 参数），便于开发者识别是哪个商户的授权。

                $authData = [
                    'app_id' => $app_id,// 开发者应用的 AppId
                    'source' => $source,// 授权类型；如：alipay_app_auth
                    'application_type' => $application_type,// 应用类型；多个用,号分隔； MOBILEAPP (移动应用)，WEBAPP（网页应用），PUBLICAPP（生活号），TINYAPP（小程序），ARAPP（AR应用）;
                    'app_auth_code' => $app_auth_code,// 应用授权码
                    'state' => base64_decode($state),// 商户自定义参数；便于开发者识别是哪个商户的授权。
                ];

                 $orderPayConfigInfo = CTAPIAlipayAuthTokenBusiness::alipayAuthAjax($request, $this, $authData, 1);
                 $reDataArr['info'] = $orderPayConfigInfo;
            });
    }


    //  支付结果通知--回调
    public function alipayNotify(Request $request)
    {
        // $this->InitParams($request);
        // 日志
        $requestLog = [
            'files'       => $request->file(),
            'posts'  => $request->post(),
            'input'      => $request->input(),
            // 'post_data' => apiGetPost(),
        ];
        Log::info('支付宝支付日志 回调-->' . __FUNCTION__,$requestLog);
        $notifyParams = $request->post();
//        $notifyParams = [
//            "gmt_create" => "2021-01-27 23:03:11",// Date  否 交易创建时间。该笔交易创建的时间。格式为 yyyy-MM-dd HH:mm:ss。 示例值：2015-04-27 15:45:57
//            "charset" => "utf-8",// aaaa
//            "seller_email" => "305463219@qq.com",// String(100) 否 卖家支付宝账号。 示例值：zhuzhanghu@alitest.com
//            "subject" => "面授课--支付宝收款码支付费用",// String(256)  订单标题。商品的标题/交易标题/订单标题/订单关键字等，是请求时对应的参数，原样通知回来。 示例值：当面付交易
//            // String(256) 是 签名。请参考异步返回结果的验签（如果开发者手动验签，不使用 SDK 验签，可以不传此参数）。 示例值：601510b7970e52cc63db0f44997cf70e
//            "sign" => "bH3CEr7g3FqHVeITrQGnpPtcM5rq4onBfA3hO5U7mupIKoF1eUf1JF0YHUb2GSZFwZMqxLCP52E/d1tB2Gwypz1mo//MmjEn+v1qJyg+TP0+zDubsG7I/4KMXH3mKCCqkCX/trXIK6J34JJHngarD1k/kJpRarQq7LGW25cYQR7o+XG/IoRInr3JrFVYNNv+DVftpe8e5C2u85YQTXJ+D1tvOqsz2tSKMN0FByBbsLbG+ILa7Q37Xwb6ty/gEO4DLJKT/sSCrXwkAIvfTA9AkiW0PXqjicDDWWt8lEmPu6RLMUlPnhUb3FXKa+pVJdUUK/pN2cuv3jFBGIA54OgAfA==",
//            "body" => "面授课--支付宝收款码支付费用",// String(400) 否 商品描述。该订单的备注、描述、明细等。对应请求时的 body 参数，原样通知回来。 示例值：当面付交易内容
//            "buyer_id" => "2088002440437990",// String(30) 否 卖家支付宝用户号。 示例值：2088101106499364
//            "invoice_amount" => "1.00",// Number(9,2)  否 开票金额。用户在交易中支付的可开发票的金额。 示例值：10.00
//            "notify_id" => "2021012700222230321037991409643036",// String(128) 是 通知校验 ID。 示例值：ac05099524730693a8b330c5ecf72da9786
//            // String(512) 否 支付金额信息。支付成功的各个渠道金额信息，详情请参见 资金明细信息说明。 示例值：[{"amount":"15.00","fundChannel":"ALIPAYACCOUNT"}]
//            // 支付渠道说明
//            // 支付渠道代码   支付渠道
//            // COUPON        支付宝红包
//            // ALIPAYACCOUNT  支付宝余额
//            // POINT          集分宝
//            // DISCOUNT       折扣券
//            // PCARD          预付卡
//            // FINANCEACCOUNT 余额宝
//            // MCARD          商户储值卡
//            // MDISCOUNT      商户优惠券
//            // MCOUPON        商户红包
//            // PCREDIT        蚂蚁花呗
//            "fund_bill_list" => "[{\"amount\":\"1.00\",\"fundChannel\":\"PCREDIT\"}]",
//            "notify_type" => "trade_status_sync",// String(64) 是 通知类型。示例值：trade_status_sync
//            // String(32) 是 交易状态。交易目前所处的状态。示例值：TRADE_CLOSED
//            // 交易状态说明
//            // WAIT_BUYER_PAY : 交易创建，等待买家付款。
//            //TRADE_CLOSED :未付款交易超时关闭，或支付完成后全额退款。
//            //TRADE_SUCCESS :交易支付成功。
//            //TRADE_FINISHED : 交易结束，不可退款。
//            "trade_status" => "TRADE_SUCCESS",
//            "receipt_amount" => "1.00",// Number(9,2) 否 实收金额。商户在交易中实际收到的款项，单位为人民币（元）。示例值：15
//            "app_id" => "2021002124696736",// String(32) 是 开发者的 app_id。支付宝分配给开发者的应用 APPID。 示例值：2014072300007148
//            "buyer_pay_amount" => "1.00",// Number(9,2) 否 付款金额。用户在交易中支付的金额。 示例值：13.88
//            // String(10) 是 签名类型。商户生成签名字符串所使用的签名算法类型，目前支持 RSA2 和 RSA，推荐使用 RSA2（如果开发者手动验签，不使用 SDK 验签，可以不传此参数）。
//            // 示例值：RSA2
//            "sign_type" => "RSA2",// aaaa
//            "seller_id" => "2088041334900422",// String(30) 否 卖家支付宝用户号。示例值：2088101106499364
//            "gmt_payment" => "2021-01-27 23:03:20",// Date 否 交易付款时间。该笔交易的买家付款时间。格式为 yyyy-MM-dd HH:mm:ss。 示例值：2015-04-27 15:45:57
//            "notify_time" => "2021-01-27 23:17:40",// Date 是 通知时间。通知的发送时间。格式为yyyy-MM-dd HH:mm:ss。示例值：2011-12-27 06:30:30
//            "passback_params" => "3121037443010033",// aaaa
//            "version" => "1.0",// aaaa
//            "out_trade_no" => "3121037443010033",// String(64) 是 商户订单号。原支付请求的商户订单号。 示例值：6823789339978248
//            "total_amount" => "1.00",// Number(9,2)  是 订单金额。本次交易支付的订单金额，单位为人民币（元）。 示例值：20
//            "trade_no" => "2021012722001437991458368986",// String(64) 是 支付宝交易号。支付宝交易凭证号。 示例值：2013112011001004330000121536
//            "auth_app_id" => "2021002125631695",// 第三方应用的 APPID 第三方代商户调用支付接口得到的异步通知有所不同，会带上
//            "buyer_logon_id" => "zou***@163.com",// String(100) 否 买家支付宝账号。 示例值：15901825620
//            "point_amount" => "0.00"// Number(9,2) 否 集分宝金额。使用集分宝支付的金额。  示例值：12.00
//        ];
        $alipayConfig = config('public.alipayConfig.APIConfig');
       if(!AlipayPayAPI::notifyJudgeSign($alipayConfig, $notifyParams)){
           Log::info('支付宝支付日志 回调-->异步返回结果验签--失败' . __FUNCTION__,[]);
           return response('验签失败')->send();
       }
       // 需要严格按照如下描述校验通知数据的正确性：
       // 商户需要验证该通知数据中的 out_trade_no 是否为商户系统中创建的订单号。
       // 判断 total_amount 是否确实为该订单的实际金额（即商户订单创建时的金额）。
        // 校验通知中的 seller_id（或者seller_email) 是否为 out_trade_no 这笔单据的对应的操作方（有的时候，一个商户可能有多个 seller_id/seller_email）。
        // 上述有任何一个验证不通过，则表明本次通知是异常通知，务必忽略。在上述验证通过后商户必须根据支付宝不同类型的业务通知，正确的进行不同的业务处理，并且过滤重复的通知结果数据。在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
        // 注意：
        // 状态 TRADE_SUCCESS 的通知触发条件是商户签约的产品支持退款功能的前提下，买家付款成功。
        // 交易状态 TRADE_FINISHED 的通知触发条件是商户签约的产品不支持退款功能的前提下，买家付款成功；或者，商户签约的产品支持退款功能的前提下，交易已经成功并且已经超过可退款期限。
        $out_trade_no = $notifyParams['out_trade_no'] ?? '';
        $total_amount = $notifyParams['total_amount'] ?? '0';
        $seller_id = $notifyParams['seller_id'] ?? '0';// 可能存在
        $trade_status = $notifyParams['trade_status'] ?? '';
        $notify_id = $notifyParams['notify_id'] ?? '';
        $trade_no = $notifyParams['trade_no'] ?? '';
        // true 成功 或 throws 有误
        try{
            CTAPIOrderPayBusiness::payAlipayNotify($request, $this, $notifyParams);
        } catch ( \Exception $e) {
            Log::info('支付宝支付日志 回调-->error' . __FUNCTION__,[$e->getMessage(), $out_trade_no, $trade_no]);
            return response($e->getMessage())->send();
        }
        Log::info('支付宝支付日志 回调-->success' . __FUNCTION__,[$out_trade_no, $trade_no]);
        return response("success")->send();
    }
    // **************公用重写方法********************开始*********************************
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
    // **************公用重写方法********************结束*********************************

}
