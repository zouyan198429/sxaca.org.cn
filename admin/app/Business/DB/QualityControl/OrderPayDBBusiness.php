<?php
// 支付订单
namespace App\Business\DB\QualityControl;

use App\Services\alipaySdk\AlipayPayAPI;
use App\Services\DB\CommonDB;
use App\Services\pay\weixin\easyWechatPay;
use App\Services\Tool;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class OrderPayDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\OrderPay';
    public static $table_name = 'order_pay';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];

    /**
     * 订单完成支付
     *
     * @param int  $company_id 企业id
     * @param string  $pay_order_no 生成的支付订单号
     * @param string  $pay_no 支付单号(第三方)
     * @param array  $extendParams 扩展字段
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return null 错误码 10 -- 代表记录不存在或已处理---不用处理了 ; 11：已经是成功状态
     * @author zouyan(305463219@qq.com)
     */
    public static function finishPay($company_id, $pay_order_no, $pay_no = '', $extendParams = [], $operate_staff_id = 0, $modifAddOprate = 0){

        // 查询支付单
//        $payInfo = static::getDBFVFormatList(4, 1, ['pay_order_no' => $pay_order_no]);
//        if(empty($payInfo)) throws('订单支付记录不存在', 10);// return '订单支付记录不存在';// 1; //记录不存在
//
//        $status = $payInfo->pay_status;// 付款状态  状态1已关闭2付款中4成功8失败
//        // if(in_array($status, [1,4])) throws('已关闭或已支付成功', 10);// return '已关闭或已支付成功';//  return 1;// 已关闭或成功
//        if($status == 1) throws('已关闭', 10);
//        if($status == 4) throws('已支付成功', 11);
        $payInfo =  static::judgePayed($pay_order_no);

        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
        $operate_staff_id_history = 0;
        CommonDB::doTransactionFun(function() use(&$company_id, &$pay_order_no, &$pay_no, &$extendParams, &$operate_staff_id, &$modifAddOprate
            , &$payInfo, &$ownProperty, &$temNeedStaffIdOrHistoryId, &$operate_staff_id_history){
            $order_no = $payInfo['order_no'];
            // 收款订单财务流水
            OrdersDBBusiness::finishPay($company_id, $order_no, $pay_order_no, $operate_staff_id, $modifAddOprate);
            // 修改订单号
            $updateData = array_merge($extendParams, [
                'pay_no' => $pay_no,
                'pay_status' => 4,// 状态1已关闭2待确认4成功8失败
                'sure_time' => date('Y-m-d H:i:s'),// 付款时间
            ]);
            $saveQueryParams = Tool::getParamQuery(['pay_order_no' => $pay_order_no],[], []);
            static::save($updateData, $saveQueryParams);
        });

    }

    /**
     *  判断 订单是否已经 支付
     *
     * @param string  $pay_order_no 生成的支付订单号
     * @return null 数组一维 订单支付信息 ； 错误码 10 -- 代表记录不存在或已处理---不用处理了 ; 11：已经是成功状态
     * @author zouyan(305463219@qq.com)
     */
    public static function judgePayed($pay_order_no){

        // 查询支付单
        $payInfo = static::getDBFVFormatList(4, 1, ['pay_order_no' => $pay_order_no]);
        if(empty($payInfo)) throws('订单支付记录不存在', 10);// return '订单支付记录不存在';// 1; //记录不存在

        $status = $payInfo->pay_status;// 付款状态  状态1已关闭2付款中4成功8失败
        // if(in_array($status, [1,4])) throws('已关闭或已支付成功', 10);// return '已关闭或已支付成功';//  return 1;// 已关闭或成功
        if($status == 1) throws('已关闭', 10);
        if($status == 4) throws('已支付成功', 11);
        return $payInfo;
    }

    /**
     * 订单-支付--失败
     *
     * @param int  $company_id 企业id
     * @param string  $pay_order_no 生成的支付订单号
     * @param string  $pay_no 支付单号(第三方)--失败时，有可能为空值
     * @param array  $extendParams 扩展字段
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  null 错误码 10 -- 代表记录不存在或已处理---不用处理了; 11：已经是成功状态
     * @author zouyan(305463219@qq.com)
     */
    public static function failPay($company_id, $pay_order_no, $pay_no = '', $extendParams = [], $operate_staff_id = 0, $modifAddOprate = 0){

        // 查询支付单
        $payInfo = static::getDBFVFormatList(4, 1, ['pay_order_no' => $pay_order_no]);
        if(empty($payInfo)) throws('订单支付记录不存在', 10);// return '订单支付记录不存在';// 1; //记录不存在

        $status = $payInfo->pay_status;// 付款状态  状态1已关闭2付款中4成功8失败
        // if(in_array($status, [1,4])) throws('已关闭或已支付成功', 10);// return '已关闭或已支付成功';//  return 1;// 已关闭或成功
        if($status == 1) throws('已关闭', 10);
        if($status == 4) throws('已支付成功', 11);
        $pay_config_id = $payInfo->pay_config_id;
        // 判断收款配置
        $orderPayConfigInfo = OrderPayConfigDBBusiness::getDBFVFormatList(4, 1, ['id' => $pay_config_id]);

        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
        $operate_staff_id_history = 0;
        CommonDB::doTransactionFun(function() use(&$company_id, &$pay_order_no, &$pay_no, &$extendParams, &$operate_staff_id, &$modifAddOprate
            , &$payInfo, &$ownProperty, &$temNeedStaffIdOrHistoryId, &$operate_staff_id_history, &$orderPayConfigInfo){
            try {
                $pay_method = $payInfo->pay_method;
                // $pay_no = $payInfo->pay_no;
                switch($pay_method){
                    case 2:// 微信收款码【线上--网页生成】
                    case 16:// 微信收付款码【线上--扫码枪】
                        if(empty($orderPayConfigInfo)) throws('收款账号不存在');
                        $payKey = $orderPayConfigInfo['pay_key'];// 'banner';
                        $order_time = $payInfo->order_time;
                        $currentNow = Carbon::now()->toDateTimeString();
                        if(Tool::diffDate($order_time, $currentNow, 1, '时间', 2) > (60 * 5 + 2)) {
                            // 如果是微信支付，则关闭支付订单 注意：订单生成后不能马上调用关单接口，最短调用时间间隔为5分钟。

                            $app = app('wechat.payment.' . $payKey);
                            easyWechatPay::closeByOutTradeNumberExtend($app, $pay_order_no, function ($apiResult) {

                                Log::info('微信支付日志 关闭微信订单-->' . __FUNCTION__, [$apiResult]);
                            });
                        }
                        break;
                    default:
                        break;
                }
            } catch ( \Exception $e) {
                $errStr = $e->getMessage();
                $errCode = $e->getCode();
                // throws($errStr, $errCode);
                Log::info('微信支付日志 关闭微信订单失败-->' . __FUNCTION__, [$errStr, $errCode]);
            }


            // 修改订单号
            $updateData = array_merge($extendParams, [
                // 'pay_no' => $pay_no,
                'pay_status' => 8,// 状态1已关闭2待确认4成功8失败
                'sure_time' => date('Y-m-d H:i:s'),// 付款时间
            ]);
            if(!empty($pay_no)) $updateData['pay_no'] = $pay_no;
            $saveQueryParams = Tool::getParamQuery(['pay_order_no' => $pay_order_no],[], []);
            static::save($updateData, $saveQueryParams);
        });

    }

    /**
     * 支付回调--微信
     *
     * @param array $message 回调的参数
     *   {
     *      "appid": "wxcb82783fe211782f",
     *      "bank_type": "CFT",// 银行类型
     *      "cash_fee": "1",// 现金
     *      "fee_type": "CNY",// 币种
     *      "is_subscribe": "N",// 是否订阅
     *      "mch_id": "1527642191",
     *      "nonce_str": "5c8e67b1d9bc3",
     *      "openid": "owfFF4ydu2HmuvmSDS4goIoAIYEs",
     *      "out_trade_no": "119108029350007",
     *      "result_code": "SUCCESS",// 支付结果 FAIL:失败;SUCCESS:成功
     *      "return_code": "SUCCESS",// 表示通信状态: SUCCESS 成功
     *      "sign": "C6ACF2C7C8AF999048094ED2264F0ABC",
     *      "time_end": "20190317232919",// 交易时间
     *      "total_fee": "1",// 交易金额
     *      "trade_type": "JSAPI",// 交易类型
     *      "transaction_id": "4200000288201903177135850941"// 交易号
     *  }
     * @param array $queryMessage 商户订单号查询 结果
     *
     *商户订单号查询 结果
     *   {
     *       "return_code": "SUCCESS",
     *      "return_msg": "OK",
     *      "appid": "wxcb82783fe211782f",
     *      "mch_id": "1527642191",
     *      "nonce_str": "aA5oRYgVOf7osQv3",
     *      "sign": "DCD3A1790A8C4E1A4BBE2339E812AB3C",
     *      "result_code": "SUCCESS",
     *      "openid": "owfFF4ydu2HmuvmSDS4goIoAIYEs",
     *      "is_subscribe": "N",
     *      "trade_type": "JSAPI",
     *      "bank_type": "CFT",
     *      "total_fee": "1",
     *      "fee_type": "CNY",
     *      "transaction_id": "4200000288201903177135850941",
     *      "out_trade_no": "119108029350007",
     *      "attach": null,
     *      "time_end": "20190317232919",
     *      "trade_state": "SUCCESS",// 交易状态
     *      "cash_fee": "1",
     *      "trade_state_desc": "支付成功"
     *  }
     *
     * 交易状态
     *  SUCCESS—支付成功
     *  REFUND—转入退款
     *  NOTPAY—未支付
     *  CLOSED—已关闭
     *  REVOKED—已撤销（付款码支付）
     *  USERPAYING--用户支付中（付款码支付）
     *  PAYERROR--支付失败(其他原因，如银行返回失败)
     *  支付状态机请见下单API页面
     * @return  mixed string throws错误，请再通知我  正常返回 :不用通知我了
     * @author zouyan(305463219@qq.com)
     */
    public static function payWXNotify($message, $queryMessage){
        // 查询订单
        $out_trade_no = $message['out_trade_no'] ?? '';// 我方单号--与第三方对接用
        $out_trade_no = trim($out_trade_no);
        if(empty($out_trade_no)) throws('参数out_trade_no不能为空!');
        $transaction_id = $message['transaction_id'] ?? '';// 第三方单号[有则填]
        if(empty($transaction_id))  $transaction_id = $queryMessage['transaction_id'] ?? '';// 第三方单号[有则填]
        $transaction_id = trim($transaction_id);
        if(empty($transaction_id)) throws('参数transaction_id不能为空!');
        // pr($wrInfo->toArray());
        // pr($message);
        // pr($queryMessage);

//            if (!$order || $order->paid_at) { // 如果订单不存在 或者 订单已经支付过了
//                return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
//            }

//            ///////////// <- 建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////
//
        $returnStr = '';
        if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
            Tool::lockDoSomething('lock:' . Tool::getUniqueKey([Tool::getProjectKey(1 | 2 | 4, ':', ':'), __CLASS__, __FUNCTION__, $out_trade_no]),
                function()  use(&$returnStr, &$message, &$queryMessage, &$out_trade_no, &$transaction_id){//

                    CommonDB::doTransactionFun(function() use(&$returnStr, &$message, &$queryMessage, &$out_trade_no, &$transaction_id){
                        try {
                            if ($message['result_code'] === 'SUCCESS' && $queryMessage['trade_state'] === 'SUCCESS') {
                                static::finishPay(0, $out_trade_no, $transaction_id, [], 0, 0);

                                // 用户支付失败
                            } elseif ($message['result_code'] === 'FAIL') {
                                static::failPay(0, $out_trade_no, $transaction_id, [], 0, 0);
                            }
                        } catch ( \Exception $e) {
                            $errStr = $e->getMessage();
                            $errCode = $e->getCode();
                            if(in_array($errCode, [10,11])){
                                $returnStr = $errStr;
                                return $returnStr;
                            }else{
                                //                    throws('操作失败；信息[' . $e->getMessage() . ']');
                                throws($errStr, $errCode);
                            }
                            // throws($e->getMessage());
                        }

                    });
                }, function($errDo){
                    // TODO
//                $errMsg = '获得字段失败，请稍后重试!';
//                if($errDo == 1) throws($errMsg);
//                return $errMsg;
                   // return null;
                    throws('操作失败，请稍后重试!');
                }, true, 1, 2000, 2000);

            return $returnStr;

        } else {
//            return '通信失败，请稍后再通知我';// $fail('通信失败，请稍后再通知我');
            throws('通信失败，请稍后再通知我');
        }
        return '';
//
//            $order->save(); // 保存订单

//            return true; // 返回处理完成
    }

    /**
     * 生成付款码后，用户扫码支付对回调内容进行验证
     *
     * @param array $notifyParams 回调的参数数组
     *   //  必有 out_trade_no 、 total_amount、 trade_status、notify_id ；  可能有 seller_id
     * @return   true 成功 或 throws 有误
     * @author zouyan(305463219@qq.com)
     */
    public static function alipayNotifyJudge($notifyParams = []){
        // 需要严格按照如下描述校验通知数据的正确性：
        // 商户需要验证该通知数据中的 out_trade_no 是否为商户系统中创建的订单号。
        // 判断 total_amount 是否确实为该订单的实际金额（即商户订单创建时的金额）。
        // 校验通知中的 seller_id（或者seller_email) 是否为 out_trade_no 这笔单据的对应的操作方（有的时候，一个商户可能有多个 seller_id/seller_email）。
        // 上述有任何一个验证不通过，则表明本次通知是异常通知，务必忽略。在上述验证通过后商户必须根据支付宝不同类型的业务通知，正确的进行不同的业务处理，并且过滤重复的通知结果数据。在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
        // 注意：
        // 状态 TRADE_SUCCESS 的通知触发条件是商户签约的产品支持退款功能的前提下，买家付款成功。
        // 交易状态 TRADE_FINISHED 的通知触发条件是商户签约的产品不支持退款功能的前提下，买家付款成功；或者，商户签约的产品支持退款功能的前提下，交易已经成功并且已经超过可退款期限。
        if(!isset($notifyParams['out_trade_no'])) throws('参数【out_trade_no】不存在！');
        if(!isset($notifyParams['total_amount'])) throws('参数【total_amount】不存在！');
        if(!isset($notifyParams['trade_status'])) throws('参数【trade_status】不存在！');
        if(!isset($notifyParams['notify_id'])) throws('参数【notify_id】不存在！');
        if(!isset($notifyParams['trade_no'])) throws('参数【trade_no】不存在！');
        $out_trade_no = $notifyParams['out_trade_no'] ?? '';
        $total_amount = $notifyParams['total_amount'] ?? '0';
        $seller_id = $notifyParams['seller_id'] ?? '0';// 可能存在
        $trade_status = $notifyParams['trade_status'] ?? '';
        $notify_id = $notifyParams['notify_id'] ?? '';
        $trade_no = $notifyParams['trade_no'] ?? '';
        // 获取订单支付信息
        $orderPayInfo = static::getDBFVFormatList(4, 1, ['pay_order_no' => $out_trade_no]);
        if(empty($orderPayInfo)) throws('【' . $trade_no . '】订单支付记录不存在！');
        $pay_status = $orderPayInfo['pay_status'];// 状态1已关闭2待确认4成功8失败
        if(in_array($pay_status, [1, 8])) return true;//throws('订单支付已关闭或支付失败！');
        if($pay_status == 4) return true;// 支付成功
        // if($pay_status != 2)  throws('订单支付非待确认状态！');
        $pay_price = bcdiv($orderPayInfo['pay_price'], '100', 2);// 支付费用
        if(bccomp($total_amount, $pay_price, 2) != 0) throws('【' . $trade_no . '】订单支付金额不匹配！');
        $pay_config_id = $orderPayInfo['pay_config_id'];

        $alipayAuthTokenInfo = AlipayAuthTokenDBBusiness::getDBFVFormatList(4, 1, ['pay_config_id' => $pay_config_id, 'operate_status' => 2]);
        if(empty($alipayAuthTokenInfo)) throws('【' . $trade_no . '】此收款帐号支付宝未授权，请先授权才能使用！');
        $tokeyUserId = $alipayAuthTokenInfo['user_id'];
        if(isset($notifyParams['seller_id']) && $seller_id != $tokeyUserId) throws('卖家支付宝用户号不匹配！');

        $judgeNotifyId = AlipayPayAPI::judgeAlipayNotify(config('public.alipayConfig.judgeNotifyIdUrl'), $seller_id, $notify_id);
        if( $judgeNotifyId != 'true') throws('【' . $trade_no . '】通知校验 ID验证失败 【' . $judgeNotifyId . '】！');

        $order_no = $orderPayInfo['order_no'];
        $pay_order_no = $out_trade_no;
        $alipayConfig = config('public.alipayConfig.APIConfig');
        try {
            static::payAlipayJudgeThirdQuery($order_no, $pay_order_no, $alipayConfig, $alipayAuthTokenInfo, $orderPayInfo);
        } catch ( \Exception $e) {
            $errStr = $e->getMessage();
            $errCode = $e->getCode();
            throws('【' . $trade_no . '】' . $errStr, $errCode);
        }
        return true;
    }

    /**
     * 支付脚本或查询支付是否成功【生成付款码，用户扫码支付场景】--微信 --还有支付宝扫码支付
     *  $order_no 订单号
     *  $pay_order_no  订单付款单号
     *  返回值 1:支付成功  2: 支付失败 3：其它状态 或 throws 有误或 暂时没有支付结果
     * */
    public static function payWXJudge($order_no, $pay_order_no){

        // 获取订单信息
        $orderInfo = OrdersDBBusiness::getDBFVFormatList(4, 1, ['order_no' => $order_no]);
        if(empty($orderInfo)) throws('订单记录不存在！');
        // 获取订单支付信息
        $orderPayInfo = static::getDBFVFormatList(4, 1, ['pay_order_no' => $pay_order_no]);
        if(empty($orderPayInfo)) throws('订单支付记录不存在！');
        $pay_status = $orderPayInfo['pay_status'];// 状态1已关闭2待确认4成功8失败
        if(in_array($pay_status, [1, 8])) throws('订单支付已关闭或支付失败！');
        if($pay_status == 4) return 1;// 支付成功
        if($pay_status != 2)  throws('订单支付非待确认状态！');
        // 调用新加或修改接口
        $pay_config_id = $orderPayInfo['pay_config_id'];
        $payConfigInfo = OrderPayConfigDBBusiness::getDBFVFormatList(4, 1, ['id' => $pay_config_id]);
        if(empty($payConfigInfo)) throws('订单支付收款账号记录不存在！');
        // 微信收款码【线上--网页生成】 2       支付宝收款码【线上--网页生成】 4
        // 微信收付款码【线上--扫码枪】 16    支付宝收付款码【线上--扫码枪】  64
        // 微信收款码【个人-燕】   8             支付宝收款码【个人-燕】   32
        $pay_method = $orderPayInfo['pay_method'];
        if(in_array($pay_method, [4, 64])){// 支付宝支付
            $alipayAuthTokenInfo = AlipayAuthTokenDBBusiness::getDBFVFormatList(4, 1, ['pay_config_id' => $pay_config_id, 'operate_status' => 2]);
            if(empty($alipayAuthTokenInfo)) return 2;// throws('此收款帐号支付宝未授权，请先授权才能使用！');
            $alipayConfig = config('public.alipayConfig.APIConfig');
            return static::payAlipayJudgeThirdQuery($order_no, $pay_order_no, $alipayConfig, $alipayAuthTokenInfo, $orderPayInfo);
        }else{// 微信支付
            $pay_key = $payConfigInfo['pay_key'];
            return static::payWXJudgeThirdQuery($order_no, $pay_order_no, $pay_key, $orderPayInfo);
        }

    }

    /**
     *  注意，确定是已支生成支付订单，但是还没有改变支付结果的记录才能调用此方法--会修改支付订单状态
     * 支付脚本或查询支付是否成功【生成付款码，用户扫码支付场景】--支付宝
     *  $order_no 订单号
     *  $pay_order_no  订单付款单号
     *   $alipayConfig 支付宝配置信息
     *   $alipayAuthTokenInfo 支付宝授权及令牌详情
     *   $order_pay_info 支付单详情--一维数组
     *  返回值 1:支付成功  2: 支付失败 3：其它状态 或 throws 有误或 暂时没有支付结果
     * */
    public static function payAlipayJudgeThirdQuery($order_no, $pay_order_no, $alipayConfig, $alipayAuthTokenInfo, $order_pay_info = []){
        // 查询订单信息
        return Tool::lockDoSomething('lock:' . Tool::getUniqueKey([Tool::getProjectKey(1 | 2 | 4, ':', ':'),  __CLASS__, __FUNCTION__, $order_no, $pay_order_no]),
            function()  use(&$order_no, &$pay_order_no, &$alipayConfig, &$alipayAuthTokenInfo, &$order_pay_info){//

                // 统一收单线下交易查询
                $apiParams = [
                    'out_trade_no' => $pay_order_no, // '20150320010101001'
                ];
                $app_auth_token = $alipayAuthTokenInfo['app_auth_token'] ?? null;// "202101BBa54ffe4585f54301a73361d3c9fb8A42";
                try{
                    $alipayPayInfoObj = AlipayPayAPI::getTradeQuery($alipayConfig, $apiParams, $app_auth_token);
                } catch ( \Exception $e) {
                    $errStr = $e->getMessage();
                    $errCode = $e->getCode();
                    // 注意：预生成订单后，在用户付款前，订单查询会报   "sub_code": "ACQ.TRADE_NOT_EXIST"  ； "sub_msg": "交易不存在" --- 注意处理好这个逻辑【正常的逻辑，付款中】
                    if(strpos($errStr, 'ACQ.TRADE_NOT_EXIST') !== false){// 交易不存在

                        // 如果开启支付超过10分钟，则作失败处理
                        $order_time = $order_pay_info['order_time'];
                        $currentNow = Carbon::now()->toDateTimeString();
                        if(Tool::diffDate($order_time, $currentNow, 1, '时间', 2) > (60 * 5 + 1)){
                            $transaction_id = '';
                            static::failPay(0, $pay_order_no, $transaction_id, [], 0, 0);
                            return 2;
                        }
                        return 3;
                    }
                    throws($errStr, $errCode);
                }
                $trade_no = $alipayPayInfoObj->trade_no;// String	必填	64	支付宝交易号	2013112011001004330000121536
                $transaction_id = $trade_no;
                $buyer_logon_id = $alipayPayInfoObj->buyer_logon_id;// String	必填	100	买家支付宝账号	159****5620
                // 必填	32	交易状态：
                //  WAIT_BUYER_PAY（交易创建，等待买家付款）、
                //  TRADE_CLOSED（未付款交易超时关闭，或支付完成后全额退款）、
                //  TRADE_SUCCESS（交易支付成功）、
                //  TRADE_FINISHED（交易结束，不可退款）
                //  TRADE_CLOSED
                $trade_status = $alipayPayInfoObj->trade_status;
                $total_amount = $alipayPayInfoObj->total_amount;// Price	必填	11	交易的订单金额，单位为元，两位小数。该参数的值为支付时传入的total_amount	88.88

                try {
                    switch($trade_status){
                        case 'TRADE_SUCCESS':// 交易支付成功—支付成功
                        case 'TRADE_FINISHED': // （交易结束，不可退款）
                            static::finishPay(0, $pay_order_no, $transaction_id, [], 0, 0);
                            return 1;
                            break;
                        // case 'TRADE_CLOSED':// 未付款交易超时关闭，或支付完成后全额退款）
                            break;
                        case 'WAIT_BUYER_PAY':// （交易创建，等待买家付款）—未支付
                            // 如果开启支付超过10分钟，则作失败处理
                            $order_time = $order_pay_info['order_time'];
                            $currentNow = Carbon::now()->toDateTimeString();
                            if(Tool::diffDate($order_time, $currentNow, 1, '时间', 2) > (60 * 5 + 1)){
                                static::failPay(0, $pay_order_no, $transaction_id, [], 0, 0);
                                return 2;
                            }
                            break;
                        case 'TRADE_CLOSED': // TRADE_CLOSED—已关闭
                            static::failPay(0, $pay_order_no, $transaction_id, [], 0, 0);
                            return 2;
                            break;
                        default:
                            break;
                    }
                } catch ( \Exception $e) {
                    $errStr = $e->getMessage();
                    $errCode = $e->getCode();
                    // 10 -- 代表记录不存在或已处理---不用处理了 ; 11：已经是成功状态
                    // 返回值 1:支付成功  2: 支付失败 3：其它状态 或 throws 有误或 暂时没有支付结果
                    if(in_array($errCode, [11])){
                        // $returnStr = $errStr;
                        return 1;// $returnStr;
                    }else if(in_array($errCode, [10])){
                        return 2;
                    }else{
                        //                    throws('操作失败；信息[' . $e->getMessage() . ']');
                        throws($errStr, $errCode);
                    }
                    // throws($e->getMessage());
                }
                return 3;
            }, function($errDo){
                // TODO
//                            $errMsg = '获得字段失败，请稍后重试!';
//                            if($errDo == 1) throws($errMsg);
//                            return $errMsg;
//                            // return null;
                throws('操作失败，请稍后重试!');
            }, true, 1, 2000, 2000);
    }

    /**
     *  注意，确定是已支生成支付订单，但是还没有改变支付结果的记录才能调用此方法--会修改支付订单状态
     * 支付脚本或查询支付是否成功【生成付款码，用户扫码支付场景】--微信
     *  $order_no 订单号
     *  $pay_order_no  订单付款单号
     *   $pay_key 支付配置的key
     *   $order_pay_info 支付单详情--一维数组
     *  返回值 1:支付成功  2: 支付失败 3：其它状态 或 throws 有误或 暂时没有支付结果
     * */
    public static function payWXJudgeThirdQuery($order_no, $pay_order_no, $pay_key, $order_pay_info = []){
        $app = app('wechat.payment.' . $pay_key);
        return easyWechatPay::queryByOutTradeNumberExtend($app, $pay_order_no, function ($trade_state, $resultWX) use(&$order_no, &$pay_order_no,
            &$pay_key, &$order_pay_info){

            /** 交易状态
            SUCCESS—支付成功
            REFUND—转入退款
            NOTPAY—未支付
            CLOSED—已关闭
            REVOKED—已撤销（付款码支付）
            USERPAYING--用户支付中（付款码支付）
            PAYERROR--支付失败(其他原因，如银行返回失败)
            支付状态机请见下单API页面
             */
            // 如trade_state不为 SUCCESS，则只返回out_trade_no（必传）和attach（选传）。
            $transaction_id = $resultWX['transaction_id'] ?? '';// 第三方单号[有则填]，---注意未支付成功不会返回此字段
            return Tool::lockDoSomething('lock:' . Tool::getUniqueKey([Tool::getProjectKey(1 | 2 | 4, ':', ':'), __CLASS__, __FUNCTION__, $order_no, $pay_order_no]),
                function()  use(&$pay_order_no, &$transaction_id, &$trade_state, &$order_pay_info){//

                    try {
                        switch($trade_state){
                            case 'SUCCESS':// SUCCESS—支付成功
                                static::finishPay(0, $pay_order_no, $transaction_id, [], 0, 0);
                                return 1;
                                break;
                            case 'REFUND':// REFUND—转入退款
                                break;
                            case 'NOTPAY':// NOTPAY—未支付
                            case 'USERPAYING':// USERPAYING--用户支付中（付款码支付）
                                // 如果开启支付超过10分钟，则作失败处理
                                $order_time = $order_pay_info['order_time'];
                                $currentNow = Carbon::now()->toDateTimeString();
                                if(Tool::diffDate($order_time, $currentNow, 1, '时间', 2) > (60 * 5 + 1)){
                                    static::failPay(0, $pay_order_no, $transaction_id, [], 0, 0);
                                    return 2;
                                }
                                break;
                            case 'CLOSED':// CLOSED—已关闭
                            case 'REVOKED':// REVOKED—已撤销（付款码支付）
                            case 'PAYERROR':// PAYERROR--支付失败(其他原因，如银行返回失败)
                                static::failPay(0, $pay_order_no, $transaction_id, [], 0, 0);
                                return 2;
                                break;
                            default:
                                break;
                        }
                    } catch ( \Exception $e) {
                        $errStr = $e->getMessage();
                        $errCode = $e->getCode();
                        // 10 -- 代表记录不存在或已处理---不用处理了 ; 11：已经是成功状态
                        // 返回值 1:支付成功  2: 支付失败 3：其它状态 或 throws 有误或 暂时没有支付结果
                        if(in_array($errCode, [11])){
                            // $returnStr = $errStr;
                            return 1;// $returnStr;
                        }else if(in_array($errCode, [10])){
                            return 2;
                        }else{
                            //                    throws('操作失败；信息[' . $e->getMessage() . ']');
                            throws($errStr, $errCode);
                        }
                        // throws($e->getMessage());
                    }
                    return 3;
                }, function($errDo){
                    // TODO
//                            $errMsg = '获得字段失败，请稍后重试!';
//                            if($errDo == 1) throws($errMsg);
//                            return $errMsg;
//                            // return null;
                    throws('操作失败，请稍后重试!');
                }, true, 1, 2000, 2000);
        }, 1);
    }

    // 脚本去跑页面生成收款码，用户扫码付款的脚本--查询并修改订单状态
    public static function autoRunWXPayResult(){
        // 读取所有不知道付款结果的
        $queryParams = Tool::getParamQuery(['pay_type' => 1 ,'pay_status' => 2, 'pay_method' => [2, 16, 4, 64]], [], []);//
        $dataList = static::getAllList($queryParams, [])->toArray();

        Log::info('微信支付日志 支付宝支付日志 自动脚本，生成的收款码主动请求不知道是否付款成功的订单-->' . __FUNCTION__, [$dataList]);
        if(!empty($dataList)){

            $orderNoArr = Tool::getArrFields($dataList, 'order_no');
            $orderList = OrdersDBBusiness::getDBFVFormatList(1, 1, ['order_no' => $orderNoArr]);
            // 按订单号格式化数据
            $formatOrderList = Tool::arrUnderReset($orderList, 'order_no', 1, '_');

            $payConfigIdsArr = Tool::getArrFields($dataList, 'pay_config_id');
            $payConfigList = OrderPayConfigDBBusiness::getDBFVFormatList(1, 1, ['id' => $payConfigIdsArr]);
            // 按id格式化数据
            $formatPayConfigList = Tool::arrUnderReset($payConfigList, 'id', 1, '_');

            $alipayAuthTokenList = AlipayAuthTokenDBBusiness::getDBFVFormatList(1, 1, ['pay_config_id' => $payConfigIdsArr, 'operate_status' => 2]);
            $alipayAuthTokenFormatList = Tool::arrUnderReset($alipayAuthTokenList, 'pay_config_id', 1, '_');

            foreach($dataList as $orderPayInfo){
                $order_no = $orderPayInfo['order_no'];
                $pay_order_no = $orderPayInfo['pay_order_no'];
                $pay_method = $orderPayInfo['pay_method'];
                try{
                    // 获取订单信息
                    $orderInfo = $formatOrderList[$order_no] ?? [];// OrdersDBBusiness::getDBFVFormatList(4, 1, ['order_no' => $order_no]);
                    if(empty($orderInfo)) throws('订单记录不存在！');
                    $pay_status = $orderPayInfo['pay_status'];// 状态1已关闭2待确认4成功8失败
                    if(in_array($pay_status, [1, 8])) throws('订单支付已关闭或支付失败！');
                    if($pay_status == 4) continue;// 支付成功
                    if($pay_status != 2)  throws('订单支付非待确认状态！');
                    // 调用新加或修改接口
                    $pay_config_id = $orderPayInfo['pay_config_id'];
                    $payConfigInfo = $formatPayConfigList[$pay_config_id] ?? [] ;// OrderPayConfigDBBusiness::getDBFVFormatList(4, 1, ['id' => $pay_config_id]);
                    if(empty($payConfigInfo)) throws('订单支付收款账号记录不存在！');
                    if(in_array($pay_method, [4, 64])){// 支付宝支付
                        $alipayAuthTokenInfo = $alipayAuthTokenFormatList[$pay_config_id] ?? [];
                        if(empty($alipayAuthTokenInfo)) continue;// throws('此收款帐号支付宝未授权，请先授权才能使用！');
                        $alipayConfig = config('public.alipayConfig.APIConfig');
                         static::payAlipayJudgeThirdQuery($order_no, $pay_order_no, $alipayConfig, $alipayAuthTokenInfo, $orderPayInfo);
                    }else {// 微信支付
                        $pay_key = $payConfigInfo['pay_key'];
                        static::payWXJudgeThirdQuery($order_no, $pay_order_no, $pay_key, $orderPayInfo);
                    }
                } catch ( \Exception $e) {
                    Log::info('微信支付日志 支付宝支付日志 自动脚本，生成的收款码主动请求不知道是否付款成功的订单-错误->' . __FUNCTION__, [$e->getMessage(), $e->getCode()]);
                    // throws($e->getMessage(), $e->getCode());
                }
            }
        }
    }
}
