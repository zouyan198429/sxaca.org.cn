<?php

// 支付API

namespace App\Services\alipaySdk;


use App\Services\File\DownFile;
use App\Services\Request\API\HttpRequest;
use Illuminate\Support\Facades\Log;

require_once 'aop/request/AlipayTradeQueryRequest.php';
require_once 'aop/request/AlipayTradePrecreateRequest.php';
require_once 'aop/request/AlipayTradePayRequest.php';
require_once 'aop/request/AlipayTradeCancelRequest.php';
require_once 'aop/request/AlipayTradeRefundRequest.php';


class AlipayPayAPI extends BasicAlipay
{


    /**
     * --- 回调 异步返回结果验签
     *
     * @param array $config  接口相关的配置信息
     * @param array $params 回调的数据 一维数组
     * @return boolean 验签的结果 true:通过 ；false:不通过
     * @author zouyan(305463219@qq.com)
     */
    public static function notifyJudgeSign($config, $params){
        if(empty($params)) return false;
        if(!isset($params['sign'])) $params['sign'] = '';
        if(!isset($params['sign_type'])) $params['sign_type'] = 'RSA2';
        //1、execute 使用
        $aop = static::getAop($config);// new \AopCertClient ();
        $rsaPublicKeyFilePath = $aop->alipayrsaPublicKey;
        $result = $aop->rsaCheckV1($params, $rsaPublicKeyFilePath, $params['sign_type']);
        return $result;
    }

    /**
     * --- 回调 根据通知校验 ID ，判断是否是支付发送的回调
     *   https://opendocs.alipay.com/open/58/103597
     *  https://global.alipay.com/docs/ac/global/notify_verify_cn
     * @param string $url  请求接口地址 'https://mapi.alipay.com/gateway.do' 或  https://intlmapi.alipay.com/gateway.do
     * @param string $partner 卖家支付宝用户号。 示例值：2088101106499364， 回调返回的参数 buyer_id 值
     * @param string $notify_id String(128) 是 通知校验 ID。 示例值：ac05099524730693a8b330c5ecf72da9786 回调返回的参数 notify_id 值
     * @return mixed 返回获得的内容， "true":有效，其它： 不成功时：报对应错误
     * @author zouyan(305463219@qq.com)
     */
    public static function judgeAlipayNotify($url = 'https://mapi.alipay.com/gateway.do', $partner = '', $notify_id = ''){

        // $url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&partner=2088041334900422&notify_id=2021012800222070818037991409688167';
        // $result = DownFile::curlGetFileContents($url);
        $urlParams = [
            'service' => 'notify_verify',
            'partner' => $partner,// '2088041334900422',
            'notify_id' => $notify_id,// '2021012800222070818037991409688167'
        ];
        $result = HttpRequest::sendHttpRequest($url, [], $urlParams, 'GET',[]);
        //  boolean 是否支付宝发送的有效的 通知校验 ID  true:是 ；false:不是
        // if(strtolower($result) == 'true') return true;
        // return false;
        return $result;
    }

    /**
     * --- 接口 alipay.trade.precreate(统一收单线下交易预创建)
     *  https://opendocs.alipay.com/apis/api_1/alipay.trade.precreate
     *  通用场景、当面付
     *  收银员通过收银台或商户后台调用支付宝接口，生成二维码后，展示给用户，由用户扫描二维码完成订单支付。
     *
     * @param array $config  接口相关的配置信息
     * @param array $apiParams  请求参数数组
     *   $apiParams = [
     *      'out_trade_no' => '20150320010101001',//String	必选	64 商户订单号,64个字符以内、只能包含字母、数字、下划线；需保证在商户端不重复
     *      'seller_id' => '2088102146225135',// String	可选	28 卖家支付宝用户ID。 如果该值为空，则默认为商户签约账号对应的支付宝用户ID
     *      // Price	必选	11 订单总金额，单位为人民币（元），取值范围为 0.01~100000000.00，精确到小数点后两位。
     *      // 注意：如果同时传入了【打折金额】，【不可打折金额】，【订单总金额】三者，则必须满足如下条件：【订单总金额】=【打折金额】+【不可打折金额】
     *      'total_amount' => '88.88',
     *      // Price	可选	11
     *      // 可打折金额. 参与优惠计算的金额，单位为人民币（元），取值范围为 0.01~100000000.00，精确到小数点后两位。
     *      // 注意：如果该值未传入，但传入了【订单总金额】和【不可打折金额】，则该值默认为【订单总金额】-【不可打折金额】
     *      'discountable_amount' => '8.88',
     *      'subject' => 'Iphone6 16G',// String	必选	256	商品的标题/交易标题/订单标题/订单关键字等。 注意：不可使用特殊字符，如 /，=，& 等。
     *      // GoodsDetail[]	可选		订单包含的商品列表信息.json格式. 其它说明详见：“商品明细说明”
     *      'goods_detail' => [
     *          [
     *              'goods_id' => 'apple-01',// String	必填	32	商品的编号
     *              'goods_name' => 'ipad',// String	必填	256	商品名称
     *              'quantity' => '1',// Number	必填	10	商品数量
     *              'price' => '2000',// Price	必填	9	商品单价，单位为元
     *              'goods_category' => '34543238',// String	可选	24	商品类目
     *              'categories_tree' => '124868003|126232002|126252004',// String	可选	128	商品类目树，从商品类目根节点到叶子节点的类目id组成，类目id值使用|分割
     *              'body' => '特价手机',// String	可选	1000	商品描述信息
     *              'show_url' => 'http://www.alipay.com/xxx.jpg',// String	可选	400	商品的展示地址
     *          ]
     *      ],
     *      // String	可选	64
     *      // 销售产品码。
     *      // 如果签约的是当面付快捷版，则传 OFFLINE_PAYMENT；
     *      // 其它支付宝当面付产品传 FACE_TO_FACE_PAYMENT；
     *      // 不传默认使用 FACE_TO_FACE_PAYMENT。
     *      'product_code' => 'FACE_TO_FACE_PAYMENT',
     *      'operator_id' => 'yx_001',// String	可选	28	商户操作员编号
     *      'store_id' => 'NJ_001',// String	可选	32	商户门店编号
     *      // String	可选	128
     *      // 禁用渠道，用户不可用指定渠道支付
     *      // 当有多个渠道时用“,”分隔
     *      // 注，与enable_pay_channels互斥
     *      // 渠道列表：https://docs.open.alipay.com/common/wifww7
     *      'disable_pay_channels' => 'pcredit,moneyFund,debitCardExpress',
     *      'terminal_id' => 'NJ_T_001',// String	可选	32	商户机具终端编号
     *      'extend_params' => [// ExtendParams	可选		业务扩展参数
     *          'sys_service_provider_id' => '2088511833207846',// String	可选	64	系统商编号 该参数作为系统商返佣数据提取的依据，请填写系统商签约协议的PID
     *          'card_type' => 'S0JP0000',// String	可选	32	卡类型
     *      ],
     *      // String	可选	6
     *      // 该笔订单允许的最晚付款时间，逾期将关闭交易。
     *      // 取值范围：1m～15d。m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）。
     *      // 该参数数值不接受小数点， 如 1.5h，可转换为 90m。
     *      'timeout_express' => '90m',
     *      'settle_info' => [// SettleInfo	可选		描述结算信息，json格式，详见结算参数说明
     *          'settle_detail_infos' => [// SettleDetailInfo[]	必填	10	结算详细信息，json数组，目前只支持一条。
     *              // String	必填	64
     *              // 结算收款方的账户类型。
     *              // cardAliasNo：结算收款方的银行卡编号;
     *              // userId：表示是支付宝账号对应的支付宝唯一用户号;
     *              // loginName：表示是支付宝登录号；
     *              // defaultSettle：表示结算到商户进件时设置的默认结算账号，结算主体为门店时不支持传defaultSettle；
     *              'trans_in_type' => 'cardAliasNo',
     *              // String	必填	64
     *              // 结算收款方。当结算收款方类型是cardAliasNo时，本参数为用户在支付宝绑定的卡编号；结算收款方类型是userId时，
     *              // 本参数为用户的支付宝账号对应的支付宝唯一用户号，以2088开头的纯16位数字；当结算收款方类型是loginName时，
     *              // 本参数为用户的支付宝登录号；当结算收款方类型是defaultSettle时，本参数不能传值，保持为空。
     *              'trans_in' => 'A0001',
     *              // String	可选	64
     *              // 结算汇总维度，按照这个维度汇总成批次结算，由商户指定。
     *              // 目前需要和结算收款方账户类型为cardAliasNo配合使用
     *              'summary_dimension' => 'A0001',
     *              // String	可选	64
     *              // 结算主体标识。当结算主体类型为SecondMerchant时，为二级商户的SecondMerchantID；当结算主体类型为Store时，为门店的外标。
     *              'settle_entity_id' => '2088xxxxx;ST_0001',
     *              'settle_entity_type' => 'SecondMerchant、Store',// String	可选	32 结算主体类型。二级商户:SecondMerchant;商户或者直连商户门店:Store
     *              // Price	必填	9
     *              // 结算的金额，单位为元。在创建订单和支付接口时必须和交易金额相同。在结算确认接口时必须等于交易金额减去已退款金额。
     *              'amount' => '0.1',
     *          ],
     *          // String	可选	10
     *          // 该笔订单的超期自动确认结算时间，到达期限后，将自动确认结算。此字段只在签约账期结算模式时有效。取值范围：1d～365d。d-天。
     *          // 该参数数值不接受小数点。
     *          'settle_period_time' => '7d',
     *      ],
     *      'merchant_order_no' => '20161008001',// String	可选	32	商户原始订单号，最大长度限制32位
     *      // String	可选	512
     *      // 公用回传参数，如果请求时传递了该参数，则返回给商户时会回传该参数。
     *      // 支付宝只会在同步返回（包括跳转回商户网站）和异步通知时将该参数原样返回。
     *      // 本参数必须进行UrlEncode之后才可以发送给支付宝。
     *      'passback_params' => 'merchantBizType%3d3C%26merchantBizNo%3d2016010101111',
     *      // BusinessParams	可选
     *      // 商户传入业务信息，具体值要和支付宝约定，应用于安全，营销等参数直传场景，格式为json格式 {"data":"123"}
     *      'business_params' => [
     *          'campus_card' => '0000306634',// String	可选	64	校园卡编号
     *          'card_type' => 'T0HK0000',// String	可选	128	虚拟卡卡类型
     *          'actual_order_time' => '2019-05-14 09:18:55',// String	可选	256 实际订单时间，在乘车码场景，传入的是用户刷码乘车时间
     *      ],
     *      // String	可选	6
     *      // 该笔订单允许的最晚付款时间，逾期将关闭交易，从生成二维码开始计时，默认有效期2h。
     *      // 取值范围：1m～15d。m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）。
     *      // 该参数数值不接受小数点， 如 1.5h，可转换为 90m。
     *      // 当面付场景最大有效期为2h，该场景下本参数设置超过2h，订单将在2h时关闭。
     *      'qr_code_timeout_express' => '90m',
     *  ];
     *
     * @param string $notifyUrl  String	否	256	支付宝服务器主动通知商户服务器里指定的页面http/https路径。	http://api.test.alipay.net/atinterface/receive_notify.htm
     * @param string $app_auth_token 可选 服务商刷新令牌时必填  默认null 开发者代替商户发起请求时请务必带上 app_auth_token，否则支付宝将认为是本应用替自己发起的请求。请注意 app_auth_token 是 POST 请求参数，不是 biz_content 的子参数；
     * @param string $PID '2088511833207846',// String	可选	64	系统商编号 该参数作为系统商返佣数据提取的依据，请填写系统商签约协议的PID
     * @return array  一维数组
     * [
     *      'out_trade_no' => '6823789339978248',// String	必填	64	商户的订单号 6823789339978248
     *      'qr_code' => 'https://qr.alipay.com/bavh4wjlxf12tper3a',// String	必填	1024	当前预下单请求生成的二维码码串，可以用二维码生成工具根据该码串值生成对应的二维码 https://qr.alipay.com/bavh4wjlxf12tper3a
     *  ]
     * @author zouyan(305463219@qq.com)
     */
    public static function tradePrecreate($config = [], $apiParams = [], $notifyUrl = '', $app_auth_token = null, $PID = '')
    {
        //1、execute 使用
        $aop = static::getAop($config);// new \AopCertClient ();
        // 2、对于每一个接口对应的请求类，调用它的 setNeedEncrypt 方法，设置为 true 就可以了。
        static::setNeedEncrypt($request, $config, true);
        // if(static::isOpenEncrypt($config)) $request->setNeedEncrypt(true);

        /**
         */

//        "{" .
//        "\"out_trade_no\":\"20150320010101001\"," .
//        "\"seller_id\":\"2088102146225135\"," .
//        "\"total_amount\":88.88," .
//        "\"discountable_amount\":8.88," .
//        "\"undiscountable_amount\":80," .
//        "\"buyer_logon_id\":\"15901825620\"," .
//        "\"subject\":\"Iphone6 16G\"," .
//        "      \"goods_detail\":[{" .
//        "        \"goods_id\":\"apple-01\"," .
//        "\"alipay_goods_id\":\"20010001\"," .
//        "\"goods_name\":\"ipad\"," .
//        "\"quantity\":1," .
//        "\"price\":2000," .
//        "\"goods_category\":\"34543238\"," .
//        "\"categories_tree\":\"124868003|126232002|126252004\"," .
//        "\"body\":\"特价手机\"," .
//        "\"show_url\":\"http://www.alipay.com/xxx.jpg\"" .
//        "        }]," .
//        "\"body\":\"Iphone6 16G\"," .
//        "\"product_code\":\"FACE_TO_FACE_PAYMENT\"," .
//        "\"operator_id\":\"yx_001\"," .
//        "\"store_id\":\"NJ_001\"," .
//        "\"disable_pay_channels\":\"pcredit,moneyFund,debitCardExpress\"," .
//        "\"enable_pay_channels\":\"pcredit,moneyFund,debitCardExpress\"," .
//        "\"terminal_id\":\"NJ_T_001\"," .
//        "\"extend_params\":{" .
//        "\"sys_service_provider_id\":\"2088511833207846\"," .
//        "\"hb_fq_num\":\"3\"," .
//        "\"hb_fq_seller_percent\":\"100\"," .
//        "\"industry_reflux_info\":\"{\\\\\\\"scene_code\\\\\\\":\\\\\\\"metro_tradeorder\\\\\\\",\\\\\\\"channel\\\\\\\":\\\\\\\"xxxx\\\\\\\",\\\\\\\"scene_data\\\\\\\":{\\\\\\\"asset_name\\\\\\\":\\\\\\\"ALIPAY\\\\\\\"}}\"," .
//        "\"card_type\":\"S0JP0000\"" .
//        "    }," .
//        "\"timeout_express\":\"90m\"," .
//        "\"royalty_info\":{" .
//        "\"royalty_type\":\"ROYALTY\"," .
//        "        \"royalty_detail_infos\":[{" .
//        "          \"serial_no\":1," .
//        "\"trans_in_type\":\"userId\"," .
//        "\"batch_no\":\"123\"," .
//        "\"out_relation_id\":\"20131124001\"," .
//        "\"trans_out_type\":\"userId\"," .
//        "\"trans_out\":\"2088101126765726\"," .
//        "\"trans_in\":\"2088101126708402\"," .
//        "\"amount\":0.1," .
//        "\"desc\":\"分账测试1\"," .
//        "\"amount_percentage\":\"100\"" .
//        "          }]" .
//        "    }," .
//        "\"settle_info\":{" .
//        "        \"settle_detail_infos\":[{" .
//        "          \"trans_in_type\":\"cardAliasNo\"," .
//        "\"trans_in\":\"A0001\"," .
//        "\"summary_dimension\":\"A0001\"," .
//        "\"settle_entity_id\":\"2088xxxxx;ST_0001\"," .
//        "\"settle_entity_type\":\"SecondMerchant、Store\"," .
//        "\"amount\":0.1" .
//        "          }]," .
//        "\"settle_period_time\":\"7d\"" .
//        "    }," .
//        "\"sub_merchant\":{" .
//        "\"merchant_id\":\"2088000603999128\"," .
//        "\"merchant_type\":\"alipay: 支付宝分配的间连商户编号, merchant: 商户端的间连商户编号\"" .
//        "    }," .
//        "\"alipay_store_id\":\"2016052600077000000015640104\"," .
//        "\"merchant_order_no\":\"20161008001\"," .
//        "\"ext_user_info\":{" .
//        "\"name\":\"李明\"," .
//        "\"mobile\":\"16587658765\"," .
//        "\"cert_type\":\"IDENTITY_CARD\"," .
//        "\"cert_no\":\"362334768769238881\"," .
//        "\"min_age\":\"18\"," .
//        "\"fix_buyer\":\"F\"," .
//        "\"need_check_info\":\"F\"" .
//        "    }," .
//        "\"passback_params\":\"merchantBizType%3d3C%26merchantBizNo%3d2016010101111\"," .
//        "\"business_params\":{" .
//        "\"campus_card\":\"0000306634\"," .
//        "\"card_type\":\"T0HK0000\"," .
//        "\"actual_order_time\":\"2019-05-14 09:18:55\"" .
//        "    }," .
//        "\"qr_code_timeout_express\":\"90m\"" .
//        "  }"


        $authToken = null;// auth_token
        $appInfoAuthtoken = $app_auth_token;// null;// app_auth_token
        if(!empty($PID)){
            if(!isset($apiParams['extend_params'])) $apiParams['extend_params'] = [];
            $apiParams['extend_params']['sys_service_provider_id'] = $PID;
        }

        $request = new \AlipayTradePrecreateRequest  ();
        if(!empty($notifyUrl)) $request->setNotifyUrl($notifyUrl);// String	否	256	支付宝服务器主动通知商户服务器里指定的页面http/https路径。	http://api.test.alipay.net/atinterface/receive_notify.htm

        static::paramsArrToJson($apiParams, ['goods_detail']);// 参数值需要转为json格式的参数--自动完成转换
        $request->setBizContent(json_encode($apiParams));
        $result = $aop->execute ( $request, $authToken, $appInfoAuthtoken);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";

        info('支付宝支付日志-alipay.trade.precreate(统一收单线下交易预创建):',[$apiParams, $notifyUrl, $request, $result, $responseNode]);
        $resultCode = $result->$responseNode->code;

        static::judgeThrowsErr($result, $responseNode);// 判断接口返回，如果有错误，则抛出误错
//        if(!empty($resultCode)&&$resultCode == 10000){
//            echo "成功";
//        } else {
//            echo "失败";
//        }
        /**
         *
         *   $resultObj = [
         *      "alipay_trade_precreate_response" => [
         *          "code" => "10000",
         *          "msg" => "Success",
         *          "out_trade_no" => "6823789339978248",// String	必填	64	商户的订单号	6823789339978248
         *          //  String	必填	1024	当前预下单请求生成的二维码码串，可以用二维码生成工具根据该码串值生成对应的二维码	https://qr.alipay.com/bavh4wjlxf12tper3a
         *          "qr_code" => "https://qr.alipay.com/bavh4wjlxf12tper3a"
         *      ],
         *      "sign" => "ERITJKEIJKJHKKKKKKKHJEREEEEEEEEEEE"
         *  ];
         *
         */
        return [
            'out_trade_no' => $result->$responseNode->out_trade_no,// String	必填	64	商户的订单号 6823789339978248
            'qr_code' => $result->$responseNode->qr_code,// String	必填	1024	当前预下单请求生成的二维码码串，可以用二维码生成工具根据该码串值生成对应的二维码 https://qr.alipay.com/bavh4wjlxf12tper3a
        ];

    }


    /**
     * --- 接口 alipay.trade.pay(统一收单交易支付接口)
     *   https://opendocs.alipay.com/apis/api_1/alipay.trade.pay
     *  通用场景、当面付
     *  收银员通过收银台或商户后台调用支付宝接口，生成二维码后，展示给用户，由用户扫描二维码完成订单支付。
     *  商家系统将用户付款码与订单信息一起通过 alipay.trade.pay(统一收单交易支付接口) 请求到支付宝，并从接口同步返回中获取支付结果。
     *  根据公共返回参数中的 code，这笔交易可能有四种状态：
     *   支付成功（10000），
     *   支付失败（40004），
     *   等待用户付款（10003）和 未知异常（20000）。
     * @param array $config  接口相关的配置信息
     * @param array $apiParams  请求参数数组
     *   $apiParams = [
     *      'out_trade_no' => '20150320010101001',// * String	必选 	64 商户订单号,64个字符以内、只能包含字母、数字、下划线；需保证在商户端不重复
     *      'scene' => 'bar_code',// * String	必选	32	支付场景。 条码支付，取值：bar_code； 声波支付，取值：wave_code
     *      'auth_code' => '28763443825664394',// *  String	必选	64	支付授权码。25~30开头的长度为16~24位的数字，实际字符串长度以开发者获取的付款码长度为准
     *                                         // 用户付款码，25-30 开头的长度为 16-24 位的数字，实际字符串长度以开发者获取的付款码长度为准；付款码使用一次即失效，即使支付失败也需刷新。
     *      // String	可选	64
     *      // 销售产品码，商家和支付宝签约的产品码。
     *       当面付场景下，
     *             如果签约的是当面付快捷版，则传 OFFLINE_PAYMENT;
     *            其它支付宝当面付产品传 FACE_TO_FACE_PAYMENT；
     *            不传则默认使用FACE_TO_FACE_PAYMENT。
     *      'product_code' => 'FACE_TO_FACE_PAYMENT',
     *      'subject' => 'Iphone6 16G',// * String	必选	256	商品的标题/交易标题/订单标题/订单关键字等。 注意：不可使用特殊字符，如 /，=，& 等。
     *      'seller_id' => '2088102146225135',// String	可选	28 卖家支付宝用户 ID。 如果该值为空，则默认为商户签约账号对应的支付宝用户 ID。不允许收款账号与付款方账号相同
     *      // * Price	必选	11 订单总金额，单位为元，精确到小数点后两位，取值范围为 [0.01,100000000]，金额不能为 0。
     *       如果同时传入【可打折金额】和【不可打折金额】，则该参数可以不用传入；
     *       如果同时传入了【可打折金额】，【不可打折金额】，【订单总金额】三者，则必须满足如下条件：【订单总金额】=【可打折金额】+【不可打折金额】
     *      'total_amount' => '88.88',
     *      // Price	可选	11
     *      // 参与优惠计算的金额，单位为元，精确到小数点后两位，取值范围为 [0.01,100000000]。
     *      如果该值未传入，但传入了【订单总金额】和【不可打折金额】，则该值默认为【订单总金额】-【不可打折金额】
     *      'discountable_amount' => '8.88',
     *      'operator_id' => 'yx_001',// String	可选	28	商户操作员编号
     *      'store_id' => 'NJ_001',// * String	可选	32	商户门店编号
     *      'terminal_id' => 'NJ_T_001',// String	可选	32	商户机具终端编号
     *      'extend_params' => [// ExtendParams	可选		业务扩展参数
     *          'sys_service_provider_id' => '2088511833207846',// String	可选	64	系统商编号 该参数作为系统商返佣数据提取的依据，请填写系统商签约协议的PID
     *          'industry_reflux_info' => '{\"scene_code\":\"metro_tradeorder\",\"channel\":\"xxxx\",\"scene_data\":{\"asset_name\":\"ALIPAY\"}}',// String	可选	512	行业数据回流信息, 详见：地铁支付接口参数补充说明
     *          'card_type' => 'S0JP0000',// String	可选	32	卡类型
     *      ],
     *      // String[]	可选	1024	查询选项，商户通过上送该参数来定制同步需要额外返回的信息字段，数组格式。
     *     // ["fund_bill_list","voucher_detail_list","discount_goods_detail"]
     *      'query_options' => [
     *      ],
     *      // GoodsDetail[]	可选		订单包含的商品列表信息.json格式. 其它说明详见：“商品明细说明”
     *      'goods_detail' => [
     *          [
     *              'goods_id' => 'apple-01',// String	必填	32	商品的编号
     *              'goods_name' => 'ipad',// String	必填	256	商品名称
     *              'quantity' => '1',// Number	必填	10	商品数量
     *              'price' => '2000',// Price	必填	9	商品单价，单位为元
     *              'goods_category' => '34543238',// String	可选	24	商品类目
     *              'categories_tree' => '124868003|126232002|126252004',// String	可选	128	商品类目树，从商品类目根节点到叶子节点的类目id组成，类目id值使用|分割
     *              'body' => '特价手机',// String	可选	1000	商品描述信息
     *              'show_url' => 'http://www.alipay.com/xxx.jpg',// String	可选	400	商品的展示地址
     *          ]
     *      ],
     *      // PromoParam	可选	0	优惠明细参数，通过此属性补充营销参数。 注：仅与支付宝协商后可用。
     *       'promo_params' => [
     *              // String	可选	32	存在延迟扣款这一类的场景，用这个时间表明用户发生交易的时间，
     *              // 比如说，在公交地铁场景，用户刷码出站的时间，和商户上送交易的时间是不一样的。
     *              'actual_order_time' => '2018-09-25 22:47:33',
     *       ],
     *      'buyer_id' => '2088202954065786',// String	可选	28	买家的支付宝用户 ID，如果为空，会从传入的码值信息中获取买家 ID
     *      // * String	可选	6
     *      // 该笔订单允许的最晚付款时间，逾期将关闭交易。
     *      // 取值范围：1m～15d。m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）。
     *      // 该参数数值不接受小数点， 如 1.5h，可转换为 90m。
     *      'timeout_express' => '90m',
     *  ];
     *
     * @param string $notifyUrl  String	否	256	支付宝服务器主动通知商户服务器里指定的页面http/https路径。	http://api.test.alipay.net/atinterface/receive_notify.htm
     * @param string $app_auth_token 可选 服务商刷新令牌时必填  默认null 开发者代替商户发起请求时请务必带上 app_auth_token，否则支付宝将认为是本应用替自己发起的请求。请注意 app_auth_token 是 POST 请求参数，不是 biz_content 的子参数；
     * @param string $PID '2088511833207846',// String	可选	64	系统商编号 该参数作为系统商返佣数据提取的依据，请填写系统商签约协议的PID
     * @return object  对象
     * @author zouyan(305463219@qq.com)
     */
    public static function tradePay($config = [], $apiParams = [], $notifyUrl = '', $app_auth_token = null, $PID = '')
    {
        //1、execute 使用
        $aop = static::getAop($config);// new \AopCertClient ();
        // 2、对于每一个接口对应的请求类，调用它的 setNeedEncrypt 方法，设置为 true 就可以了。
        static::setNeedEncrypt($request, $config, true);
        // if(static::isOpenEncrypt($config)) $request->setNeedEncrypt(true);

        /**
         */

//        "{" .
//        "\"out_trade_no\":\"20150320010101001\"," .
//        "\"scene\":\"bar_code\"," .
//        "\"auth_code\":\"28763443825664394\"," .
//        "\"product_code\":\"FACE_TO_FACE_PAYMENT\"," .
//        "\"subject\":\"Iphone6 16G\"," .
//        "\"buyer_id\":\"2088202954065786\"," .
//        "\"seller_id\":\"2088102146225135\"," .
//        "\"total_amount\":88.88," .
//        "\"trans_currency\":\"USD\"," .
//        "\"settle_currency\":\"USD\"," .
//        "\"discountable_amount\":8.88," .
//        "\"undiscountable_amount\":80.00," .
//        "\"body\":\"Iphone6 16G\"," .
//        "      \"goods_detail\":[{" .
//        "        \"goods_id\":\"apple-01\"," .
//        "\"alipay_goods_id\":\"20010001\"," .
//        "\"goods_name\":\"ipad\"," .
//        "\"quantity\":1," .
//        "\"price\":2000," .
//        "\"goods_category\":\"34543238\"," .
//        "\"categories_tree\":\"124868003|126232002|126252004\"," .
//        "\"body\":\"特价手机\"," .
//        "\"show_url\":\"http://www.alipay.com/xxx.jpg\"" .
//        "        }]," .
//        "\"operator_id\":\"yx_001\"," .
//        "\"store_id\":\"NJ_001\"," .
//        "\"terminal_id\":\"NJ_T_001\"," .
//        "\"alipay_store_id\":\"2016041400077000000003314986\"," .
//        "\"extend_params\":{" .
//        "\"sys_service_provider_id\":\"2088511833207846\"," .
//        "\"hb_fq_num\":\"3\"," .
//        "\"hb_fq_seller_percent\":\"100\"," .
//        "\"industry_reflux_info\":\"{\\\\\\\"scene_code\\\\\\\":\\\\\\\"metro_tradeorder\\\\\\\",\\\\\\\"channel\\\\\\\":\\\\\\\"xxxx\\\\\\\",\\\\\\\"scene_data\\\\\\\":{\\\\\\\"asset_name\\\\\\\":\\\\\\\"ALIPAY\\\\\\\"}}\"," .
//        "\"card_type\":\"S0JP0000\"" .
//        "    }," .
//        "\"timeout_express\":\"90m\"," .
//        "\"agreement_params\":{" .
//        "\"agreement_no\":\"20170322450983769228\"," .
//        "\"auth_confirm_no\":\"423979\"," .
//        "\"apply_token\":\"MDEDUCT0068292ca377d1d44b65fa24ec9cd89132f\"" .
//        "    }," .
//        "\"royalty_info\":{" .
//        "\"royalty_type\":\"ROYALTY\"," .
//        "        \"royalty_detail_infos\":[{" .
//        "          \"serial_no\":1," .
//        "\"trans_in_type\":\"userId\"," .
//        "\"batch_no\":\"123\"," .
//        "\"out_relation_id\":\"20131124001\"," .
//        "\"trans_out_type\":\"userId\"," .
//        "\"trans_out\":\"2088101126765726\"," .
//        "\"trans_in\":\"2088101126708402\"," .
//        "\"amount\":0.1," .
//        "\"desc\":\"分账测试1\"," .
//        "\"amount_percentage\":\"100\"" .
//        "          }]" .
//        "    }," .
//        "\"settle_info\":{" .
//        "        \"settle_detail_infos\":[{" .
//        "          \"trans_in_type\":\"cardAliasNo\"," .
//        "\"trans_in\":\"A0001\"," .
//        "\"summary_dimension\":\"A0001\"," .
//        "\"settle_entity_id\":\"2088xxxxx;ST_0001\"," .
//        "\"settle_entity_type\":\"SecondMerchant、Store\"," .
//        "\"amount\":0.1" .
//        "          }]," .
//        "\"settle_period_time\":\"7d\"" .
//        "    }," .
//        "\"sub_merchant\":{" .
//        "\"merchant_id\":\"2088000603999128\"," .
//        "\"merchant_type\":\"alipay: 支付宝分配的间连商户编号, merchant: 商户端的间连商户编号\"" .
//        "    }," .
//        "\"disable_pay_channels\":\"credit_group\"," .
//        "\"merchant_order_no\":\"201008123456789\"," .
//        "\"auth_no\":\"2016110310002001760201905725\"," .
//        "\"ext_user_info\":{" .
//        "\"name\":\"李明\"," .
//        "\"mobile\":\"16587658765\"," .
//        "\"cert_type\":\"IDENTITY_CARD\"," .
//        "\"cert_no\":\"362334768769238881\"," .
//        "\"min_age\":\"18\"," .
//        "\"fix_buyer\":\"F\"," .
//        "\"need_check_info\":\"F\"" .
//        "    }," .
//        "\"auth_confirm_mode\":\"COMPLETE：转交易支付完成结束预授权;NOT_COMPLETE：转交易支付完成不结束预授权\"," .
//        "\"terminal_params\":\"{\\\"key\\\":\\\"value\\\"}\"," .
//        "\"passback_params\":\"merchantBizType%3d3C%26merchantBizNo%3d2016010101111\"," .
//        "\"promo_params\":{" .
//        "\"actual_order_time\":\"2018-09-25 22:47:33\"" .
//        "    }," .
//        "\"advance_payment_type\":\"ENJOY_PAY_V2\"," .
//        "      \"query_options\":[" .
//        "        \"voucher_detail_list\"" .
//        "      ]," .
//        "\"business_params\":{" .
//        "\"campus_card\":\"0000306634\"," .
//        "\"card_type\":\"T0HK0000\"," .
//        "\"actual_order_time\":\"2019-05-14 09:18:55\"" .
//        "    }," .
//        "\"request_org_pid\":\"2088201916734621\"," .
//        "\"is_async_pay\":false" .
//        "  }"

        $authToken = null;// auth_token
        $appInfoAuthtoken = $app_auth_token;// null;// app_auth_token
        if(!empty($PID)){
            if(!isset($apiParams['extend_params'])) $apiParams['extend_params'] = [];
            $apiParams['extend_params']['sys_service_provider_id'] = $PID;
        }

        $request = new \AlipayTradePayRequest ();
        if(!empty($notifyUrl)) $request->setNotifyUrl($notifyUrl);// String	否	256	支付宝服务器主动通知商户服务器里指定的页面http/https路径。	http://api.test.alipay.net/atinterface/receive_notify.htm

        static::paramsArrToJson($apiParams, ['goods_detail']);// 参数值需要转为json格式的参数--自动完成转换
        $request->setBizContent(json_encode($apiParams));
        $result = $aop->execute ( $request, $authToken, $appInfoAuthtoken);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";

        info('支付宝支付日志-alipay.trade.pay(统一收单交易支付接口):',[$apiParams, $notifyUrl, $request, $result, $responseNode]);
        $resultCode = $result->$responseNode->code;

        static::judgeThrowsErr($result, $responseNode);// 判断接口返回，如果有错误，则抛出误错
//        if(!empty($resultCode)&&$resultCode == 10000){
//            echo "成功";
//        } else {
//            echo "失败";
//        }
        /**
         *
         *  $resultObj =[
         *      "alipay_trade_pay_response" => [
         *           10000：支付成功。建议记录交易结果并在客户端显示支付成功，进入后续的业务处理。
         *           40004：支付失败。建议记录交易结果并在客户端显示错误信息（display_message）。
         *           10003：等待用户付款。建议发起轮询流程：等待 5 秒后调用 交易查询接口 alipay.trade.query。
         *                   通过支付时传入的商户订单号（out_trade_no）查询支付结果（返回参数 TRADE_STATUS ），
         *                          如果仍然返回等待用户付款（WAIT_BUYER_PAY），则再次等待5秒后继续查询，
         *                          直到返回确切的支付结果（成功 TRADE_SUCCESS 或 已撤销关闭TRADE_CLOSED），或是超出轮询时间。
         *                  在最后一次查询仍然返回等待用户付款的情况下，必须立即调用 交易撤销接口 alipay.trade.cancel 将这笔交易撤销，避免用户继续支付。
         *           20000：未知异常。建议调用查询接口确认支付结果，详情请参见 异常处理。
         *          "code" => "10000",// *  String	是	-	网关返回码,详见文档	40004
         *          "msg" => "Success",// String	是	-	网关返回码描述,详见文档	Business Failed
         *          "trade_no" => "2013112011001004330000121536",// * 必选	64	支付宝交易号	2013112011001004330000121536
         *          "out_trade_no" => "6823789339978248",// 必选	64	商户订单号	6823789339978248
         *          "buyer_logon_id" => "159****5620",// 必选	100	买家支付宝账号	159****5620
         *          "settle_amount" => "88.88",// String	选填	11	结算币种订单金额	88.88
         *          "pay_currency" => "CNY",// String	选填	8	支付币种	CNY
         *          "pay_amount" => "580.04",// String	选填	11	支付币种订单金额	580.04
         *          "settle_trans_rate" => "1",// String	选填	32	结算币种兑换标价币种汇率	1
         *          "trans_pay_rate" => "6.5261",// String	选填	32	标价币种兑换支付币种汇率	6.5261
         *          "total_amount" => 120.88,// 必选	11	交易金额	120.88
         *           // String	选填	5
         *           // 标价币种, total_amount对应的币种单位。目前支持
         *           // 英镑：GBP、港币：HKD、美元：USD、新加坡元：SGD、日元：JPY、加拿大元：CAD、澳元：AUD、欧元：EUR、新西兰元：NZD、
         *           // 韩元：KRW、泰铢：THB、瑞士法郎：CHF、瑞典克朗：SEK、丹麦克朗：DKK、挪威克朗：NOK、马来西亚林吉特：MYR、印尼卢比：IDR、
         *           // 菲律宾比索：PHP、毛里求斯卢比：MUR、以色列新谢克尔：ILS、斯里兰卡卢比：LKR、俄罗斯卢布：RUB、阿联酋迪拉姆：AED、捷克克朗：CZK、
         *           // 南非兰特：ZAR、人民币：CNY
         *          "trans_currency" => "USD",
         *           // String	选填	8
         *           // 商户指定的结算币种，目前支持
         *           // 英镑：GBP、港币：HKD、美元：USD、新加坡元：SGD、日元：JPY、加拿大元：CAD、澳元：AUD、欧元：EUR、新西兰元：NZD、
         *           // 韩元：KRW、泰铢：THB、瑞士法郎：CHF、瑞典克朗：SEK、丹麦克朗：DKK、挪威克朗：NOK、马来西亚林吉特：MYR、印尼卢比：IDR、
         *           // 菲律宾比索：PHP、毛里求斯卢比：MUR、以色列新谢克尔：ILS、斯里兰卡卢比：LKR、俄罗斯卢布：RUB、阿联酋迪拉姆：AED、捷克克朗：CZK、
         *           // 南非兰特：ZAR、人民币：CNY
         *          "settle_currency" => "USD",
         *          "receipt_amount" => "88.88",// 必选	11	实收金额	88.88
         *          "buyer_pay_amount" => 8.88,// 可选	11	买家付款的金额	8.88
         *          "point_amount" => 8.12,// 可选	11	使用集分宝付款的金额	8.12
         *          "invoice_amount" => 12.5,// 可选	11	交易中可给用户开具发票的金额	12.50
         *          "gmt_payment" => "2014-11-27 15:45:57",// 必选	32	交易支付时间	2014-11-27 15:45:57
         *          // TradeFundBill	可选	0	交易支付使用的资金渠道。
         *          // 只有在签约中指定需要返回资金明细，或者入参的query_options中指定时才返回该字段信息。
         *          "fund_bill_list" => [
         *              [
         *                  // 支付渠道说明
         *                  // 支付渠道代码	支付渠道
         *                  // COUPON	支付宝红包
         *                  // ALIPAYACCOUNT	支付宝账户
         *                  // POINT	集分宝
         *                  // DISCOUNT	折扣券
         *                  // PCARD	预付卡
         *                  // MCARD	商家储值卡
         *                  // MDISCOUNT	商户优惠券
         *                  // MCOUPON	商户红包
         *                  // BANKCARD	银行卡
         *                  "fund_channel" => "ALIPAYACCOUNT",// String	必填	32	交易使用的资金渠道，详见 支付渠道列表	ALIPAYACCOUNT
         *                  "amount" => 10,// Price	必填	32	该支付工具类型所使用的金额	10
         *                  "real_amount" => 11.21// Price	可选	11	渠道实际付款金额	11.21
         *              ]
         *          ],
         *          "card_balance" => 98.23,// Price	选填	11	支付宝卡余额	98.23
         *          "store_name" => "证大五道口店",// 可选	512	发生支付交易的商户门店名称	证大五道口店
         *          "buyer_user_id" => "2088101117955611",// 必选	28	买家在支付宝的用户 ID	2088101117955611
         *          // 可选	4096	本次交易支付所使用的单品券优惠的商品优惠信息。 只有在query_options中指定时才返回该字段信息。
         *          //  [{"goods_id":"STANDARD1026181538","goods_name":"雪碧","discount_amount":"100.00","voucher_id":"2015102600073002039000002D5O"}]
         *          "discount_goods_detail" => "[{\"goods_id\":\"STANDARD1026181538\",\"goods_name\":\"雪碧\",\"discount_amount\":\"100.00\",\"voucher_id\":\"2015102600073002039000002D5O\"}]",
         *          // voucher_detail_list	VoucherDetail	可选	0	本交易支付时使用的所有优惠券信息。 只有在query_options中指定时才返回该字段信息。
         *          "voucher_detail_list" => [
         *              [
         *                  "id" => "2015102600073002039000002D5O",// String	必填	32	券id	2015102600073002039000002D5O
         *                  "name" => "XX超市5折优惠",// String	必填	64	券名称	XX超市5折优惠
         *                  // String	必填	32   券类型，如：
         *                  // ALIPAY_FIX_VOUCHER - 全场代金券
         *                  // ALIPAY_DISCOUNT_VOUCHER - 折扣券
         *                  // ALIPAY_ITEM_VOUCHER - 单品优惠券
         *                  // ALIPAY_CASH_VOUCHER - 现金抵价券
         *                  // ALIPAY_BIZ_VOUCHER - 商家全场券
         *                  // 注：不排除将来新增其他类型的可能，商家接入时注意兼容性避免硬编码
         *                  "type" => "ALIPAY_FIX_VOUCHER",
         *                  "amount" => 10,// Price	必填	8	优惠券面额，它应该会等于商家出资加上其他出资方出资	10.00
         *                  "merchant_contribute" => 9,// Price	可选	8	商家出资（特指发起交易的商家出资金额）	9.00
         *                  "other_contribute" => 1,// Price	可选	8	其他出资方出资金额，可能是支付宝，可能是品牌商，或者其他方，也可能是他们的一起出资	1.00
         *                  "memo" => "学生专用优惠",// String	可选	256	优惠券备注信息	学生专用优惠
         *                  "template_id" => "20171030000730015359000EMZP0",// String	可选	64	券模板id	20171030000730015359000EMZP0
         *                  "purchase_buyer_contribute" => 2.01,// Price	可选	8	如果使用的这张券是用户购买的，则该字段代表用户在购买这张券时用户实际付款的金额	2.01
         *                  "purchase_merchant_contribute" => 1.03,// Price	可选	8	如果使用的这张券是用户购买的，则该字段代表用户在购买这张券时商户优惠的金额	1.03
         *                  "purchase_ant_contribute" => 0.82// Price	可选	8	如果使用的这张券是用户购买的，则该字段代表用户在购买这张券时平台优惠的金额	0.82
         *              ]
         *          ],
         *          "advance_amount" => "88.8",// String	选填	11	先享后付2.0垫资金额,不返回表示没有走垫资，非空表示垫资支付的金额
         *          // String	选填	64	预授权支付模式，该参数仅在信用预授权支付场景下返回。信用预授权支付：CREDIT_PREAUTH_PAY
         *          "auth_trade_pay_mode" => "CREDIT_PREAUTH_PAY",
         *          "charge_amount" => "8.88",// String	选填	11	该笔交易针对收款方的收费金额； 默认不返回该信息，需与支付宝约定后配置返回；
         *          // String	选填	64
         *          // 费率活动标识，当交易享受活动优惠费率时，返回该活动的标识；
         *          // 默认不返回该信息，需与支付宝约定后配置返回；
         *          // 可能的返回值列表：
         *          // 蓝海活动标识：bluesea_1
         *          "charge_flags" => "bluesea_1",
         *          "settlement_id" => "2018101610032004620239146945",// String	选填	64 支付清算编号，用于清算对账使用；只在银行间联交易场景下返回该信息；
         *          // String	选填	512 商户传入业务信息，具体值要和支付宝约定
         *          // 将商户传入信息分发给相应系统，应用于安全，营销等参数直传场景
         *          // 格式为json格式
         *          "business_params" => "{\"data\":\"123\"}",
         *          "buyer_user_type" => "PRIVATE",// 可选	18	买家用户类型。CORPORATE:企业用户；PRIVATE:个人用户。	PRIVATE
         *          "mdiscount_amount" => "88.88",// 可选	11	商家优惠金额	88.88
         *          "discount_amount" => "88.88",// 可选	11	平台优惠金额	88.88
         *          // 可选	128	买家名称。买家为个人用户时为买家姓名；买家为企业用户时为企业名称。默认不返回该信息，需与支付宝约定后配置返回。	菜鸟网络有限公司
         *          "buyer_user_name" => "菜鸟网络有限公司"
         *      ],
         *      "sign" => "ERITJKEIJKJHKKKKKKKHJEREEEEEEEEEEE"
         *  ];
         *
         */
        return $result->$responseNode;
    }



    /**
     * --- 接口 alipay.trade.query(统一收单线下交易查询)
     *   https://opendocs.alipay.com/apis/api_1/alipay.trade.query
     *  该接口提供所有支付宝支付订单的查询，商户可以通过该接口主动查询订单状态，完成下一步的业务逻辑。 需要调用查询接口的情况：
     *  当商户后台、网络、服务器等出现异常，商户系统最终未接收到支付通知； 调用支付接口后，返回系统错误或未知交易状态情况； 调用alipay.trade.pay，
     *  返回INPROCESS的状态； 调用alipay.trade.cancel之前，需确认支付状态；
     *
     *  注意：预生成订单后，在用户付款前，订单查询会报   "sub_code": "ACQ.TRADE_NOT_EXIST"  ； "sub_msg": "交易不存在" --- 注意处理好这个逻辑【正常的逻辑，付款中】
     * @param array $config  接口相关的配置信息
     * @param array $apiParams  请求参数数组
     *   $apiParams = [
     *      'out_trade_no' => '20150320010101001',// 特殊可选	64 订单支付时传入的商户订单号,和支付宝交易号不能同时为空。trade_no,out_trade_no如果同时存在优先取trade_no
     *      'trade_no' => '2014112611001004680 073956707', // 特殊可选	64 支付宝交易号，和商户订单号不能同时为空
     *      'org_pid' => '2088101117952222', // 可选	16 银行间联模式下有用，其它场景请不要使用； 双联通过该参数指定需要查询的交易所属收单机构的pid;
     *      // String[]	可选	1024 查询选项，商户传入该参数可定制本接口同步响应额外返回的信息字段，数组格式。
     *      // 支持枚举如下：
     *      //        trade_settle_info：返回的交易结算信息，包含分账、补差等信息。
     *      //        fund_bill_list：交易支付使用的资金渠道。
     *      'query_options' => ["trade_settle_info", "fund_bill_list"],
     *  ];
     * @param string $app_auth_token 可选 服务商刷新令牌时必填  默认null 开发者代替商户发起请求时请务必带上 app_auth_token，否则支付宝将认为是本应用替自己发起的请求。请注意 app_auth_token 是 POST 请求参数，不是 biz_content 的子参数；
     * @return array 对象 请看 https://opendocs.alipay.com/apis/api_1/alipay.trade.query
     *  trade_no	String	必填	64	支付宝交易号	2013112011001004330000121536
     *  out_trade_no	String	必填	64	商家订单号	6823789339978248
     *  buyer_logon_id	String	必填	100	买家支付宝账号	159****5620
     *  trade_status	String	必填	32	交易状态：WAIT_BUYER_PAY（交易创建，等待买家付款）、TRADE_CLOSED（未付款交易超时关闭，或支付完成后全额退款）、TRADE_SUCCESS（交易支付成功）、TRADE_FINISHED（交易结束，不可退款）	TRADE_CLOSED
     *  total_amount	Price	必填	11	交易的订单金额，单位为元，两位小数。该参数的值为支付时传入的total_amount	88.88
     * @author zouyan(305463219@qq.com)
     */
    public static function getTradeQuery($config = [], $apiParams = [], $app_auth_token = null){
        //1、execute 使用
        $aop = static::getAop($config);// new \AopCertClient ();
        // 2、对于每一个接口对应的请求类，调用它的 setNeedEncrypt 方法，设置为 true 就可以了。
        static::setNeedEncrypt($request, $config, true);
        // if(static::isOpenEncrypt($config)) $request->setNeedEncrypt(true);

//        "{" .
//        "\"out_trade_no\":\"20150320010101001\"," .
//        "\"trade_no\":\"2014112611001004680 073956707\"," .
//        "\"org_pid\":\"2088101117952222\"," .
//        "      \"query_options\":[" .
//        "        \"trade_settle_info\"" .
//        "      ]" .
//        "  }"


        $authToken = null;// auth_token
        $appInfoAuthtoken = $app_auth_token;// null;// app_auth_token

        $request = new \AlipayTradeQueryRequest ();

        static::paramsArrToJson($apiParams, []);// 参数值需要转为json格式的参数--自动完成转换
        $request->setBizContent(json_encode($apiParams));
        $result = $aop->execute ( $request, $authToken, $appInfoAuthtoken);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";

        info('支付宝支付日志-统一收单线下交易查询:',[$apiParams,  $request, $result, $responseNode]);
        $resultCode = $result->$responseNode->code;

        static::judgeThrowsErr($result, $responseNode);// 判断接口返回，如果有错误，则抛出误错
//        if(!empty($resultCode)&&$resultCode == 10000){
//            echo "成功";
//        } else {
//            echo "失败";
//        }
        /***
         *
         *   $resultObj = [
         *      "alipay_trade_query_response" => [
         *          "code" => "10000",
         *          "msg" => "Success",
         *          "trade_no" => "2013112011001004330000121536",// String	必填	64	支付宝交易号
         *          "out_trade_no" => "6823789339978248",// String	必填	64	商家订单号
         *          "buyer_logon_id" => "159****5620",// String	必填	100	买家支付宝账号
         *          // String	必填	32
         *          // 交易状态：
         *          // WAIT_BUYER_PAY（交易创建，等待买家付款）、
         *          // TRADE_CLOSED（未付款交易超时关闭，或支付完成后全额退款）、
         *          // TRADE_SUCCESS（交易支付成功）、
         *          // TRADE_FINISHED（交易结束，不可退款）
         *          "trade_status" => "TRADE_CLOSED",
         *          "total_amount" => 88.88,// Price	必填	11	交易的订单金额，单位为元，两位小数。该参数的值为支付时传入的total_amount
         *          // String	选填	8
         *          // 标价币种，该参数的值为支付时传入的trans_currency，支持
         *          // 英镑：GBP、港币：HKD、美元：USD、新加坡元：SGD、日元：JPY、加拿大元：CAD、 澳元：AUD、欧元：EUR、新西兰元：NZD、韩元：KRW、
         *          //  泰铢：THB、瑞士法郎：CHF、瑞典克朗：SEK、丹麦克朗：DKK、挪威克朗：NOK、马来西亚林吉特：MYR、印尼卢比：IDR、菲律宾比索：PHP、
         *          //  毛里求斯卢比：MUR、以色列新谢克尔：ILS、斯里兰卡卢比：LKR、俄罗斯卢布：RUB、阿联酋迪拉姆：AED、捷克克朗：CZK、南非兰特：ZAR、
         *          //  人民币：CNY、新台币：TWD。当trans_currency 和 settle_currency 不一致时，trans_currency支持人民币：CNY、新台币：TWD
         *          "trans_currency" => "TWD",
         *          // String	选填	8
         *          // 订单结算币种，对应支付接口传入的settle_currency，支持
         *          // 英镑：GBP、港币：HKD、美元：USD、新加坡元：SGD、日元：JPY、加拿大元：CAD、澳元：AUD、欧元：EUR、新西兰元：NZD、韩元：KRW、
         *          // 泰铢：THB、瑞士法郎：CHF、瑞典克朗：SEK、丹麦克朗：DKK、挪威克朗：NOK、马来西亚林吉特：MYR、印尼卢比：IDR、菲律宾比索：PHP、
         *          // 毛里求斯卢比：MUR、以色列新谢克尔：ILS、斯里兰卡卢比：LKR、俄罗斯卢布：RUB、阿联酋迪拉姆：AED、捷克克朗：CZK、南非兰特：ZAR
         *          "settle_currency" => "USD",
         *          "settle_amount" => 2.96,// Price	选填	11	结算币种订单金额
         *          "pay_currency" => 1,// Price	选填	8	订单支付币种 CNY
         *          "pay_amount" => "8.88",// String	选填	11	支付币种订单金额	8.88
         *          "settle_trans_rate" => "30.025",// String	选填	11	结算币种兑换标价币种汇率	30.025
         *          "trans_pay_rate" => "0.264",// String	选填	11	标价币种兑换支付币种汇率	0.264
         *          "buyer_pay_amount" => 8.88,// Price	选填	11	买家实付金额，单位为元，两位小数。该金额代表该笔交易买家实际支付的金额，不包含商户折扣等金额	8.88
         *          "point_amount" => 10,// Price	选填	11	积分支付的金额，单位为元，两位小数。该金额代表该笔交易中用户使用积分支付的金额，比如集分宝或者支付宝实时优惠等	10
         *          "invoice_amount" => 12.11,// Price	选填	11	交易中用户支付的可开具发票的金额，单位为元，两位小数。该金额代表该笔交易中可以给用户开具发票的金额	12.11
         *          "send_pay_date" => "2014-11-27 15:45:57",// Date	选填	32	本次交易打款给卖家的时间	2014-11-27 15:45:57
         *          "receipt_amount" => "15.25",// String	选填	11	实收金额，单位为元，两位小数。该金额为本笔交易，商户账户能够实际收到的金额	15.25
         *          "store_id" => "NJ_S_001",// store_id	String	选填	32	商户门店编号	NJ_S_001
         *          "terminal_id" => "NJ_T_001",// terminal_id	String	选填	32	商户机具终端编号	NJ_T_001
         *          // TradeFundBill	必填		交易支付使用的资金渠道。
         *          // 只有在签约中指定需要返回资金明细，或者入参的query_options中指定时才返回该字段信息。
         *          "fund_bill_list" => [
         *              [
         *                  // String	必填	32	交易使用的资金渠道，详见 支付渠道列表
         *                  // 支付渠道说明
         *                  // 支付渠道代码	支付渠道
         *                  // COUPON	支付宝红包
         *                  // ALIPAYACCOUNT	支付宝账户
         *                  // POINT	集分宝
         *                  // DISCOUNT	折扣券
         *                  // PCARD	预付卡
         *                  // MCARD	商家储值卡
         *                  // MDISCOUNT	商户优惠券
         *                  // MCOUPON	商户红包
         *                  // BANKCARD	银行卡
         *                  "fund_channel" => "ALIPAYACCOUNT",
         *                  "amount" => 10,// Price	必填	32	该支付工具类型所使用的金额
         *                  "real_amount" => 11.21// Price	可选	11	渠道实际付款金额
         *              ]
         *          ],
         *          "store_name" => "证大五道口店",// String	选填	512	请求交易支付中的商户店铺的名称
         *          "buyer_user_id" => "2088101117955611",// String	必填	16	买家在支付宝的用户id
         *          "charge_amount" => "8.88",// String	选填	11	该笔交易针对收款方的收费金额； 默认不返回该信息，需与支付宝约定后配置返回；
         *          // String	选填	64
         *          // 费率活动标识，当交易享受活动优惠费率时，返回该活动的标识；
         *          // 默认不返回该信息，需与支付宝约定后配置返回；
         *          // 可能的返回值列表：
         *          // 蓝海活动标识：bluesea_1
         *          "charge_flags" => "bluesea_1",
         *          // settlement_id	String	选填	64
         *          //支付清算编号，用于清算对账使用；
         *          //只在银行间联交易场景下返回该信息；
         *          "settlement_id" => "2018101610032004620239146945",
         *          // trade_settle_info	TradeSettleInfo	选填
         *          // 返回的交易结算信息，包含分账、补差等信息。
         *          // 只有在query_options中指定时才返回该字段信息。
         *          "trade_settle_info" => [
         *              "trade_settle_detail_list" => [// TradeSettleDetail[]	可选	10	交易结算明细信息
         *                  [
         *                      "operation_type" => "replenish",// String	必填	32	结算操作类型。包含replenish、replenish_refund、transfer、transfer_refund等类型
         *                      "operation_serial_no" => "2321232323232",// String	可选	64	商户操作序列号。商户发起请求的外部请求号。
         *                      "operation_dt" => "2019-05-16 09:59:17",// Date	必填	32	操作日期
         *                      "trans_out" => "208811****111111",// String	可选	32	转出账号
         *                      "trans_in" => "208811****111111",// String	可选	32	转入账号
         *                      "amount" => 10// Price	必填	11	实际操作金额，单位为元，两位小数。该参数的值为分账或补差或结算时传入
         *                  ]
         *              ]
         *          ],
         *          // String	选填	64	预授权支付模式，该参数仅在信用预授权支付场景下返回。信用预授权支付：CREDIT_PREAUTH_PAY
         *          "auth_trade_pay_mode" => "CREDIT_PREAUTH_PAY",
         *          "buyer_user_type" => "PRIVATE",// String	选填	18	买家用户类型。CORPORATE:企业用户；PRIVATE:个人用户。
         *          "mdiscount_amount" => "88.88",// String	选填	11	商家优惠金额
         *          "discount_amount" => "88.88",// String	选填	11	平台优惠金额
         *          // String	选填	256	订单标题；
         *          // 只在间连场景下返回；
         *          "subject" => "Iphone6 16G",
         *          // String	选填	1000	订单描述;
         *          // 只在间连场景下返回；
         *          "body" => "Iphone6 16G",
         *          // String	选填	32
         *          // 间连商户在支付宝端的商户编号；
         *          // 只在间连场景下返回；
         *          "alipay_sub_merchant_id" => "2088301372182171",
         *          // String	选填	1024	交易额外信息，特殊场景下与支付宝约定返回。
         *          // json格式。
         *          "ext_infos" => "{\"action\":\"cancel\"}",
         *          // HbFqPayInfo	选填		若用户使用花呗分期支付，且商家开通返回此通知参数，则会返回花呗分期信息。json格式其它说明详见花呗分期信息说明。
         *          "hb_fq_pay_info" => [
         *              "user_install_num" => "3"// String	可选	5	用户使用花呗分期支付的分期数
         *          ]
         *      ],
         *      "sign" => "ERITJKEIJKJHKKKKKKKHJEREEEEEEEEEEE"
         *  ];
         *
         */
        return $result->$responseNode;
    }

    /**
     * --- 接口  alipay.trade.cancel(统一收单交易撤销接口)
     *   https://opendocs.alipay.com/apis/api_1/alipay.trade.cancel
     *  支付交易返回失败  或  支付系统超时，调用该接口撤销交易。
     *    如果此订单用户支付失败，支付宝系统会将此订单关闭；
     *    如果用户支付成功，支付宝系统会将此订单资金退还给用户。
     *  注意：只有发生支付系统超时或者支付结果未知时可调用撤销，其他正常支付的单如需实现相同功能请调用申请退款API。
     *        提交支付交易后调用【查询订单API】，没有明确的支付结果再调用【撤销订单API】。
     * @param array $config  接口相关的配置信息
     * @param array $apiParams  请求参数数组
     *   $apiParams = [
     *      'out_trade_no' => '20150320010101001',// String	特殊可选	64	原支付请求的商户订单号,和支付宝交易号不能同时为空	20150320010101001
     *      'trade_no' => '2014112611001004680 073956707', // String	特殊可选	64	支付宝交易号，和商户订单号不能同时为空	2014112611001004680073956707
     *  ];
     * @param string $app_auth_token 可选 服务商刷新令牌时必填  默认null 开发者代替商户发起请求时请务必带上 app_auth_token，否则支付宝将认为是本应用替自己发起的请求。请注意 app_auth_token 是 POST 请求参数，不是 biz_content 的子参数；
     * @return array 对象
     * @author zouyan(305463219@qq.com)
     */
    public static function tradeCancel($config = [], $apiParams = [], $app_auth_token = null){
        //1、execute 使用
        $aop = static::getAop($config);// new \AopCertClient ();
        // 2、对于每一个接口对应的请求类，调用它的 setNeedEncrypt 方法，设置为 true 就可以了。
        static::setNeedEncrypt($request, $config, true);
        // if(static::isOpenEncrypt($config)) $request->setNeedEncrypt(true);

//        "{" .
//        "\"out_trade_no\":\"20150320010101001\"," .
//        "\"trade_no\":\"2014112611001004680073956707\"" .
//        "  }"

        $authToken = null;// auth_token
        $appInfoAuthtoken = $app_auth_token;// null;// app_auth_token

        $request = new \AlipayTradeCancelRequest ();

        static::paramsArrToJson($apiParams, []);// 参数值需要转为json格式的参数--自动完成转换
        $request->setBizContent(json_encode($apiParams));
        $result = $aop->execute ( $request, $authToken, $appInfoAuthtoken);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";

        info('支付宝支付日志-alipay.trade.cancel(统一收单交易撤销接口):',[$apiParams,  $request, $result, $responseNode]);
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
         *   $resultObj = [
         *      "alipay_trade_cancel_response" => [
         *          "code" => "10000",
         *          "msg" => "Success",
         *          "trade_no" => "2013112011001004330000121536",// String	必填	64	支付宝交易号; 当发生交易关闭或交易退款时返回；
         *          "out_trade_no" => "6823789339978248",// String	必填	64	商户订单号	6823789339978248
         *          "retry_flag" => "N",// * String	必填	1	是否需要重试	N  retry_flag：是否需要重试，Y/N。
         *          // String	必填	10
         *          // 本次撤销触发的交易动作,接口调用成功且交易存在时返回。可能的返回值：
         *          // close：交易未支付，触发关闭交易动作，无退款； -- close：关闭交易，无退款 。
         *          // refund：交易已支付，触发交易退款动作；--- refund：产生了退款。
         *          // 未返回：未查询到交易，或接口调用失败；
         *          "action" => "close",
         *          "gmt_refund_pay" => "2016-11-27 15:45:57",// Date	选填	32 当撤销产生了退款时，返回退款时间； 默认不返回该信息，需与支付宝约定后配置返回；
         *          "refund_settlement_id" => "2018101610032004620239146945"// String	选填	64  当撤销产生了退款时，返回的退款清算编号，用于清算对账使用；只在银行间联交易场景下返回该信息；
         *      ],
         *      "sign" => "ERITJKEIJKJHKKKKKKKHJEREEEEEEEEEEE"
         *  ];
         *
         */
        return $result->$responseNode;
    }

    /**
     * --- 接口  alipay.trade.refund(统一收单交易退款接口)
     *   https://opendocs.alipay.com/apis/api_1/alipay.trade.refund
     *  当交易发生之后一段时间内，由于买家或者卖家的原因需要退款时，卖家可以通过退款接口将支付款退还给买家，
     *  支付宝将在收到退款请求并且验证成功之后，按照退款规则将支付款按原路退到买家帐号上。
     *  交易超过约定时间（签约时设置的可退款时间）的订单无法进行退款 支付宝退款支持单笔交易分多次退款，多次退款需要提交原支付订单的商户订单号和设置不同的退款单号。
     *  一笔退款失败后重新提交，要采用原来的退款单号。
     *  总退款金额不能超过用户实际支付金额
     *
     *  使用说明
     *  退款的途径按照支付途径原路返回，交易超过约定时间（签约时设置的可退款时间）的订单无法进行退款。
     *  支付渠道为花呗、余额等退款即时到账。银行卡的退款时间以银行退款时间为准，一般情况下 2 小时内可到账。
     *  商家也可以在商家中心（b.alipay.com）中退款。
     *  退款是否成功可以根据同步响应的 fund_change 参数来判断，返回值为 Y 则表示退款成功。
     *  退款接口会根据外部请求号 out_request_no 幂等返回，因此同一笔交易需要多次部分退款时，必须使用不同的 out_request_no。
     *
     * 重要入参说明
     *   out_trade_no：支付时传入的商户订单号，与 trade_no 必填一个。
     *  trade_no：支付时返回的支付宝交易号，与 out_trade_no 必填一个。
     *  out_request_no：本次退款请求流水号，部分退款时必传。
     *  refund_amount：本次退款金额。
     * @param array $config  接口相关的配置信息
     * @param array $apiParams  请求参数数组
     *   $apiParams = [
     *      'out_trade_no' => '20150320010101001',// * String	特殊可选	64	订单支付时传入的商户订单号，商家自定义且保证商家系统中唯一。与支付宝交易号 trade_no 不能同时为空。
     *      'trade_no' => '2014112611001004680073956707', // * String	特殊可选	64	支付宝交易号，和商户订单号 out_trade_no 不能同时为空。
     *      'refund_amount' => '200.12', // *  Price	必选	11	需要退款的金额，该金额不能大于订单金额,单位为元，支持两位小数
     *      // String	可选	8
     *      // 订单退款币种信息。支持
     *      // 英镑：GBP、港币：HKD、美元：USD、新加坡元：SGD、日元：JPY、加拿大元：CAD、澳元：AUD、欧元：EUR、新西兰元：NZD、
     *      // 韩元：KRW、泰铢：THB、瑞士法郎：CHF、瑞典克朗：SEK、丹麦克朗：DKK、挪威克朗：NOK、马来西亚林吉特：MYR、印尼卢比：IDR、
     *      // 菲律宾比索：PHP、毛里求斯卢比：MUR、以色列新谢克尔：ILS、斯里兰卡卢比：LKR、俄罗斯卢布：RUB、阿联酋迪拉姆：AED、捷克克朗：CZK、
     *      // 南非兰特：ZAR、人民币：CNY
     *      'refund_currency' => 'USD',
     *      'refund_reason' => '正常退款', // String	可选	256	退款原因说明，商家自定义。
     *      'out_request_no' => 'HZ01RF001', // * String	可选	64	标识一次退款请求，同一笔交易多次退款需要保证唯一，如需部分退款，则此参数必传。
     *      'operator_id' => 'OP001', // String	可选	30	商户的操作员编号
     *      'store_id' => 'NJ_S_001', // String	可选	32	商户门店编号，由商家自定义。需保证当前商户下唯一。
     *      'terminal_id' => 'NJ_S_001', // String	可选	32	商户的终端编号
     *      'goods_detail' => [ // GoodsDetail[]	可选		退款包含的商品列表信息，Json格式。
     *              'goods_id' => 'apple-01',// String	必填	32	商品的编号
     *              'alipay_goods_id' => '20010001',// String	可选	32	支付宝定义的统一商品编号
     *              'goods_name' => 'ipad',// String	必填	256	商品名称
     *              'quantity' => '1',// Number	必填	10	商品数量
     *              'price' => '2000',// Price	必填	9	商品单价，单位为元
     *              'goods_category' => '34543238',// String	可选	24	商品类目
     *              'categories_tree' => '124868003|126232002|126252004',// String	可选	128	商品类目树，从商品类目根节点到叶子节点的类目id组成，类目id值使用|分割
     *              'body' => '特价手机',// String	可选	1000	商品描述信息
     *              'show_url' => 'http://www.alipay.com/xxx.jpg',// String	可选	400	商品的展示地址
     *       ],
     *       // OpenApiRoyaltyDetailInfoPojo[]	可选 退分账明细信息。
     *          注：
     *          1.当面付无需传入退分账明细，系统自动按退款金额与订单金额的比率，从收款方和分账收入方退款，不支持指定退款金额与退款方。
     *          2.电脑网站支付，手机 APP 支付，手机网站支付产品，须在退款请求中明确是否退分账，从哪个分账收入方退，退多少分账金额；
     *                                         如不明确，默认从收款方退款，收款方余额不足退款失败。不支持系统按比率退款。
     *      'refund_royalty_parameters' => [
     *          [
     *              'royalty_type' => 'transfer', // String	可选	32 分账类型.; 普通分账为：transfer;     补差为：replenish; 为空默认为分账transfer;
     *              // // String	可选	16
     *              // 支出方账户。如果支出方账户类型为userId，本参数为支出方的支付宝账号对应的支付宝唯一用户号，以2088开头的纯16位数字；
     *              // 如果支出方类型为loginName，本参数为支出方的支付宝登录号；
     *              'trans_out' => '2088101126765726',
     *              'trans_out_type' => 'userId', // String	可选	64	支出方账户类型。userId表示是支付宝账号对应的支付宝唯一用户号;loginName表示是支付宝登录号；
     *              'trans_in_type' => 'userId', // String	可选	64	收入方账户类型。userId表示是支付宝账号对应的支付宝唯一用户号;cardAliasNo表示是卡编号;loginName表示是支付宝登录号；
     *              // String	必填	16
     *              // 收入方账户。如果收入方账户类型为userId，本参数为收入方的支付宝账号对应的支付宝唯一用户号，以2088开头的纯16位数字；
     *              // 如果收入方类型为cardAliasNo，本参数为收入方在支付宝绑定的卡编号；如果收入方类型为loginName，本参数为收入方的支付宝登录号；
     *              'trans_in' => '2088101126708402',
     *              'amount' => '0.1', // Price	可选	9	分账的金额，单位为元
     *              'amount_percentage' => '100', // Number	可选	3	分账信息中分账百分比。取值范围为大于0，少于或等于100的整数。
     *              'desc' => '分账给2088101126708402', // String	可选	1000	分账描述
     *          ]
     *       ],
     *      'org_pid' => '2088101117952222', // String	可选	16	银行间联模式下有用，其它场景请不要使用； 双联通过该参数指定需要退款的交易所属收单机构的pid;
     *      'query_options' => 'refund_detail_item_list', // String[]	可选	1024	查询选项，商户通过上送该参数来定制同步需要额外返回的信息字段，数组格式。支持：refund_detail_item_list：退款使用的资金渠道。

     *  ];
     * @param string $app_auth_token 可选 服务商刷新令牌时必填  默认null 开发者代替商户发起请求时请务必带上 app_auth_token，否则支付宝将认为是本应用替自己发起的请求。请注意 app_auth_token 是 POST 请求参数，不是 biz_content 的子参数；
     * @return array 对象
     * @author zouyan(305463219@qq.com)
     */
    public static function tradeRefund($config = [], $apiParams = [], $app_auth_token = null){
        //1、execute 使用
        $aop = static::getAop($config);// new \AopCertClient ();
        // 2、对于每一个接口对应的请求类，调用它的 setNeedEncrypt 方法，设置为 true 就可以了。
        static::setNeedEncrypt($request, $config, true);
        // if(static::isOpenEncrypt($config)) $request->setNeedEncrypt(true);

//        "{" .
//        "\"out_trade_no\":\"20150320010101001\"," .
//        "\"trade_no\":\"2014112611001004680073956707\"," .
//        "\"refund_amount\":200.12," .
//        "\"refund_currency\":\"USD\"," .
//        "\"refund_reason\":\"正常退款\"," .
//        "\"out_request_no\":\"HZ01RF001\"," .
//        "\"operator_id\":\"OP001\"," .
//        "\"store_id\":\"NJ_S_001\"," .
//        "\"terminal_id\":\"NJ_T_001\"," .
//        "      \"goods_detail\":[{" .
//        "        \"goods_id\":\"apple-01\"," .
//        "\"alipay_goods_id\":\"20010001\"," .
//        "\"goods_name\":\"ipad\"," .
//        "\"quantity\":1," .
//        "\"price\":2000," .
//        "\"goods_category\":\"34543238\"," .
//        "\"categories_tree\":\"124868003|126232002|126252004\"," .
//        "\"body\":\"特价手机\"," .
//        "\"show_url\":\"http://www.alipay.com/xxx.jpg\"" .
//        "        }]," .
//        "      \"refund_royalty_parameters\":[{" .
//        "        \"royalty_type\":\"transfer\"," .
//        "\"trans_out\":\"2088101126765726\"," .
//        "\"trans_out_type\":\"userId\"," .
//        "\"trans_in_type\":\"userId\"," .
//        "\"trans_in\":\"2088101126708402\"," .
//        "\"amount\":0.1," .
//        "\"amount_percentage\":100," .
//        "\"desc\":\"分账给2088101126708402\"" .
//        "        }]," .
//        "\"org_pid\":\"2088101117952222\"," .
//        "      \"query_options\":[" .
//        "        \"refund_detail_item_list\"" .
//        "      ]" .
//        "  }"

        $authToken = null;// auth_token
        $appInfoAuthtoken = $app_auth_token;// null;// app_auth_token

        $request = new \AlipayTradeRefundRequest ();

        static::paramsArrToJson($apiParams, []);// 参数值需要转为json格式的参数--自动完成转换
        $request->setBizContent(json_encode($apiParams));
        $result = $aop->execute ( $request, $authToken, $appInfoAuthtoken);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";

        info('支付宝支付日志-alipay.trade.refund(统一收单交易退款接口):',[$apiParams,  $request, $result, $responseNode]);
        $resultCode = $result->$responseNode->code;

        static::judgeThrowsErr($result, $responseNode);// 判断接口返回，如果有错误，则抛出误错
//        if(!empty($resultCode)&&$resultCode == 10000){
//            echo "成功";
//        } else {
//            echo "失败";
//        }
        /***
         *
         * 重要出参说明
         * refund_fee：该笔交易已退款的总金额。
         *  $resultObj = [
         *      "alipay_trade_refund_response" => [
         *          "code" => "10000",
         *          "msg" => "Success",
         *          "trade_no" => "支付宝交易号",// String	必填	64	2013112011001004330000121536	支付宝交易号
         *          "out_trade_no" => "6823789339978248",// String	必填	64	商户订单号	6823789339978248
         *          "buyer_logon_id" => "159****5620",// String	必填	100	用户的登录id	159****5620
         *          "fund_change" => "Y",// String	必填	1	本次退款是否发生了资金变化	Y
         *          "refund_fee" => 88.88,// * Price	必填	11	退款总金额	88.88
         *          "refund_currency" => "USD",// String	选填	8	退款币种信息	USD
         *          // TradeFundBill	选填		退款使用的资金渠道。
         *          // 只有在签约中指定需要返回资金明细，或者入参的query_options中指定时才返回该字段信息。
         *          "refund_detail_item_list" => [
         *              [
         *                  // tring	必填	32	交易使用的资金渠道，详见 支付渠道列表	ALIPAYACCOUNT
         *                  // 支付渠道说明
         *                  // 支付渠道代码	支付渠道
         *                  // COUPON	支付宝红包
         *                  // ALIPAYACCOUNT	支付宝账户
         *                  // POINT	集分宝
         *                  // DISCOUNT	折扣券
         *                  // PCARD	预付卡
         *                  // MCARD	商家储值卡
         *                  // MDISCOUNT	商户优惠券
         *                  // MCOUPON	商户红包
         *                  // BANKCARD	银行卡
         *                  "fund_channel" => "ALIPAYACCOUNT",
         *                  "bank_code" => "CEB",// String	可选	10	银行卡支付时的银行代码
         *                  "amount" => 10,// Price	必填	32	该支付工具类型所使用的金额
         *                  "real_amount" => 11.21,// Price	可选	11	渠道实际付款金额
         *                  // String	可选	32
         *                  // 渠道所使用的资金类型,目前只在资金渠道(fund_channel)是银行卡渠道(BANKCARD)的情况下才返回该信息(DEBIT_CARD: 借记卡, CREDIT_CARD:信用卡, MIXED_CARD:借贷合一卡)	DEBIT_CARD
         *                  "fund_type" => "DEBIT_CARD"
         *              ]
         *          ],
         *          "store_name" => "望湘园联洋店",// store_name	String	选填	512	交易在支付时候的门店名称
         *          "buyer_user_id" => "2088101117955611",// buyer_user_id	String	必填	28	买家在支付宝的用户id	2088101117955611
         *          "refund_preset_paytool_list" => [// PresetPayToolInfo	选填		退回的前置资产列表
         *              "amount" => [// Price[]	必填	32	前置资产金额	12.21
         *                  12.21
         *              ],
         *              // String	必填	32	前置资产类型编码，和收单支付传入的preset_pay_tool里面的类型编码保持一致。	盒马礼品卡:HEMA；抓猫猫红包:T_CAT_COUPON
         *              "assert_type_code" => "盒马礼品卡:HEMA；抓猫猫红包:T_CAT_COUPON"
         *          ],
         *          "refund_settlement_id" => "2018101610032004620239146945",// String	选填	64	退款清算编号，用于清算对账使用； 只在银行间联交易场景下返回该信息；	2018101610032004620239146945
         *          "present_refund_buyer_amount" => "88.88",// String	选填	11	本次退款金额中买家退款金额
         *          "present_refund_discount_amount" => "88.88",// String	选填	11	本次退款金额中平台优惠退款金额
         *          "present_refund_mdiscount_amount" => "88.88",// String	选填	11	本次退款金额中商家优惠退款金额
         *          "has_deposit_back" => "true"// String	选填	10	是否有银行卡冲退
         *      ],
         *      "sign" => "ERITJKEIJKJHKKKKKKKHJEREEEEEEEEEEE"
         *  ];
         *
         */
        return $result->$responseNode;
    }
}
