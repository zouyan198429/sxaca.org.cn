<?php

// 工具类API

namespace App\Services\alipaySdk;


use Illuminate\Support\Facades\Log;

require_once 'aop/request/AlipayOpenAuthTokenAppRequest.php';
require_once 'aop/request/AlipayOpenAuthTokenAppQueryRequest.php';

class AlipayToolAPI extends BasicAlipay
{

    /**
     * --- 接口 换取应用授权令牌 或 刷新令牌 alipay.open.auth.token.app(换取应用授权令牌)
     *  https://opendocs.alipay.com/apis/api_9/alipay.open.auth.token.app
     * @param array $config  接口相关的配置信息
     * @param string $grantType 必选 授权方式。支持：
     * // 1.authorization_code，表示换取使用用户授权码code换取授权令牌access_token。
     * // 2.refresh_token，表示使用refresh_token刷新获取新授权令牌。
     * @param string $code 可选
     *  // 授权码，用户对应用授权后得到。本参数在 grant_type 为 authorization_code 时必填；为 refresh_token 时不填。4b203fe6c11548bcabd8da5bb087a83b
     * @param string $refresh_token 可选
     * // 授权码，用户对应用授权后得到。本参数在 grant_type 为 authorization_code 时必填；为 refresh_token 时不填。4b203fe6c11548bcabd8da5bb087a83b
     * @param string $app_auth_token 可选 服务商刷新令牌时必填  默认null 开发者代替商户发起请求时请务必带上 app_auth_token，否则支付宝将认为是本应用替自己发起的请求。请注意 app_auth_token 是 POST 请求参数，不是 biz_content 的子参数；
     * @return array 二维数组
     *   Array
     *  (
     *       [0] => stdClass Object
     *           (
     *          [app_auth_token] => 202101BB5e6768c83e7d40a5a51489014a144C42// 授权令牌信息
     *          [app_refresh_token] => 202101BB2ef0ce00fef643b9a8a5b7c90b7f7D42// 刷新令牌
     *          [auth_app_id] => 2021002125631695// 授权方应用的APPID，非第三方应用 APPID
     *          [expires_in] => 31536000// 有效期
     *          [re_expires_in] => 32140800// 刷新令牌有效期
     *          [user_id] => 2088041334900422// 支付宝用户标识
     *           )
     *
     *     )
     *
     *  )
     * @author zouyan(305463219@qq.com)
     */
    public static function getOpenAuthTokenApp($config = [], $grantType = 'authorization_code', $code = '', $refresh_token = '', $app_auth_token = null){
        //1、execute 使用
        $aop = static::getAop($config);// new \AopCertClient ();

        $request = new \AlipayOpenAuthTokenAppRequest ();
        // 2、对于每一个接口对应的请求类，调用它的 setNeedEncrypt 方法，设置为 true 就可以了。
        static::setNeedEncrypt($request, $config, true);
        // if(static::isOpenEncrypt($config)) $request->setNeedEncrypt(true);
//        "{" .
//        "\"grant_type\":\"authorization_code或者refresh_token\"," .
//        "\"code\":\"1cc19911172e4f8aaa509c8fb5d12F56\"," .
//        "\"refresh_token\":\"201509BBdcba1e3347de4e75ba3fed2c9abebE36\"" .
//        "  }"

        $apiParams = [
            'grant_type' => $grantType,
        ];
        if(!empty($code)) $apiParams['code'] = $code;
        if(!empty($refresh_token)){
            $apiParams['refresh_token'] = $refresh_token;

        }
        $authToken = null;// auth_token
        $appInfoAuthtoken = $app_auth_token;// null;// app_auth_token

        static::paramsArrToJson($apiParams, []);// 参数值需要转为json格式的参数--自动完成转换
        $request->setBizContent(json_encode($apiParams));
        $result = $aop->execute ( $request, $authToken, $appInfoAuthtoken);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        info('支付宝支付日志-换取应用授权令牌:',[$apiParams,  $request, $result, $responseNode]);
//        [alipay_open_auth_token_app_response] => stdClass Object
//        (
//            [code] => 40004
//            [msg] => Business Failed
//            [sub_code] => AUTH_CODE_NOT_EXIST
//            [sub_msg] => auth_code不存在
//        )
//
//        [alipay_cert_sn] => 8cf93869226c0c8651838f859f4991cc
//        [sign] => BZjMjE4d3hfh+zxqLAYI4WAKKXmRFsGftUvTFKZMcYUI+

//        [alipay_open_auth_token_app_response] => stdClass Object
//        (
//            [code] => 10000
//            [msg] => Success
//            [tokens] => Array
//                    (
//                         [0] => stdClass Object
//                             (
//                            [app_auth_token] => 202101BB5e6768c83e7d40a5a51489014a144C42
//                            [app_refresh_token] => 202101BB2ef0ce00fef643b9a8a5b7c90b7f7D42
//                            [auth_app_id] => 2021002125631695
//                            [expires_in] => 31536000
//                            [re_expires_in] => 32140800
//                            [user_id] => 2088041334900422
//                             )
//
//                       )
//
//                    )
//
//        [alipay_cert_sn] => 8cf93869226c0c8651838f859f4991cc
//        [sign] => jGbmpt3eNHNpXGmfODo+NyR/

        $resultCode = $result->$responseNode->code;
        // 如果失败，则抛出错误
//        if(!empty($resultCode)&&$resultCode == 10000){
//            echo "成功";
//        } else {
//            echo "失败";
//        }
        static::judgeThrowsErr($result, $responseNode);// 判断接口返回，如果有错误，则抛出误错
        /**
         *
         *   $resultObj = [
         *      "alipay_system_oauth_token_response" => [
         *          "user_id" => "2088102150527498",// String	必填	16	授权商户的user_id	2088102150527498
         *          "auth_app_id" => "2013121100055554",// String	必填	20	授权商户的appid	2013121100055554
         *          "app_auth_token" => "201509BBeff9351ad1874306903e96b91d248A36",// String	必填	40	应用授权令牌	201509BBeff9351ad1874306903e96b91d248A36
         *          "app_refresh_token" => "201509BBdcba1e3347de4e75ba3fed2c9abebE36",// String	必填	40	刷新令牌	201509BBdcba1e3347de4e75ba3fed2c9abebE36
         *          "expires_in" => "123456",// String	必填	16	该字段已作废，应用令牌长期有效，接入方不需要消费该字段	123456
         *          "re_expires_in" => "123456"// String	必填	16	刷新令牌的有效时间（从接口调用时间作为起始时间），单位到秒	123456
         *      ]
         *  ];
         *
         */
        if(isset($result->$responseNode->tokens)){// 批量授权能换取多个授权令牌 access_token
            return $result->$responseNode->tokens;
        }else{// 单个授权通过 alipay.open.auth.token.app 接口只能换取一个授权令牌 access_token
            return [
                (object) [
                      "app_auth_token" => $result->$responseNode->app_auth_token, // "202101BB5e6768c83e7d40a5a51489014a144C42",
                      "app_refresh_token" => $result->$responseNode->app_refresh_token, // "202101BB2ef0ce00fef643b9a8a5b7c90b7f7D42",
                      "auth_app_id" => $result->$responseNode->auth_app_id, // "2021002125631695",
                      "expires_in" => $result->$responseNode->expires_in, // "31536000",
                      "re_expires_in" => $result->$responseNode->re_expires_in, // "32140800",
                      "user_id" => $result->$responseNode->user_id, // "2088041334900422",
                ]
            ];
        }

    }

    /**
     * --- 接口 查询某个应用授权AppAuthToken的授权信息 alipay.open.auth.token.app.query(查询某个应用授权AppAuthToken的授权信息)
     * https://opendocs.alipay.com/apis/api_9/alipay.open.auth.token.app.query
     * @param array $config  接口相关的配置信息
     * @param string $app_auth_token 可选 服务商刷新令牌时必填  默认null 开发者代替商户发起请求时请务必带上 app_auth_token，否则支付宝将认为是本应用替自己发起的请求。请注意 app_auth_token 是 POST 请求参数，不是 biz_content 的子参数；
     * @param boolean $isISV  是否是服务商调用 true:服务商查询下面的商户的用； false:非服务商查询自己的【默认】
     * @return array  一维数组
     *  'user_id' => $result->user_id,// 授权商户的user_id  2088102150527498
     *  'auth_app_id' => $result->auth_app_id,// 授权商户的appid 2013121100055554
     *  'expires_in' => $result->expires_in,// 应用授权令牌失效时间，单位到秒 31536000
     *  'auth_methods' => $result->auth_methods,// 一维数组 -当前app_auth_token的授权接口列表  "alipay.open.auth.token.app.query","alipay.system.oauth.token","alipay.open.auth.token.app"
     *  'auth_start' => $result->auth_start,// 授权生效时间 2015-11-03 01:59:57 -- 有可能没有此下标
     *  'auth_end' => $result->auth_end,// 授权失效时间  2016-11-03 01:59:57 -- 有可能没有此下标
     *  'status' => $result->status,// valid：有效状态；invalid：无效状态  valid
     * @author zouyan(305463219@qq.com)
     */
    public static function getOpenAuthTokenAppQuery($config = [], $app_auth_token = null)// , $isISV = false
    {
        //1、execute 使用
        $aop = static::getAop($config);// new \AopCertClient ();
        $request = new \AlipayOpenAuthTokenAppQueryRequest ();
        // 2、对于每一个接口对应的请求类，调用它的 setNeedEncrypt 方法，设置为 true 就可以了。
        static::setNeedEncrypt($request, $config, true);
        // if(static::isOpenEncrypt($config)) $request->setNeedEncrypt(true);
//        "{" .
//        "\"app_auth_token\":\"201509BBeff9351ad1874306903e96b91d248A36\"" .
//        "  }"
        $apiParams = [
            'app_auth_token' => $app_auth_token,
        ];
        $authToken = null;// auth_token
        $appInfoAuthtoken = null;// app_auth_token
//        if($isISV){
//             // $appInfoAuthtoken = $app_auth_token;
//        }

        static::paramsArrToJson($apiParams, []);// 参数值需要转为json格式的参数--自动完成转换
        $request->setBizContent(json_encode($apiParams));
        $result = $aop->execute ( $request, $authToken, $appInfoAuthtoken);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        info('支付宝支付日志-查询某个应用授权AppAuthToken的授权信息:',[$apiParams,  $request, $result, $responseNode]);
        $resultCode = $result->$responseNode->code;
        static::judgeThrowsErr($result, $responseNode);// 判断接口返回，如果有错误，则抛出误错

//        if(!empty($resultCode)&&$resultCode == 10000){
//            echo "成功";
//        } else {
//            echo "失败";
//        }
//        [alipay_open_auth_token_app_query_response] => stdClass Object
//            (
//                [code] => 10000
//                [msg] => Success
//                [auth_app_id] => 2021002125631695// String	必填		授权商户的appid
//                [auth_end] => 2022-01-26 10:34:19// Date	必填		授权失效时间
//                [auth_methods] => Array// String	必填	4000	当前app_auth_token的授权接口列表
//                    (
//                    [0] => alipay.open.mini.version.audit.rejected
//                    [1] => alipay.open.auth.appauth.cancelled
//                    [2] => alipay.ebpp.invoice.apply.status.changed
//                    [3] => alipay.ebpp.invoice.merchant.enterstatus.changed
//                    [4] => ant.merchant.expand.shop.save.rejected
//                    [5] => alipay.open.auth.userauth.cancelled
//                    [6] => alipay.trade.settle.success
//                    )
//                [auth_start] => 2021-01-26 10:34:19 // Date	必填		授权生效时间
//                [expires_in] => 31536000// Number	必填		应用授权令牌失效时间，单位到秒
//                [status] => valid// String	必填		valid：有效状态；invalid：无效状态	valid
//                [user_id] => 20880// String	必填		授权商户的user_id

        return [
            'user_id' => $result->$responseNode->user_id,// 授权商户的user_id  2088102150527498
            'auth_app_id' => $result->$responseNode->auth_app_id,// 授权商户的appid 2013121100055554
            'expires_in' => $result->$responseNode->expires_in,// 应用授权令牌失效时间，单位到秒 31536000
            'auth_methods' => $result->$responseNode->auth_methods ?? [],// 当前app_auth_token的授权接口列表  "alipay.open.auth.token.app.query","alipay.system.oauth.token","alipay.open.auth.token.app"
            'auth_start' => $result->$responseNode->auth_start ?? null,//  授权生效时间 2015-11-03 01:59:57 -- 有可能没有此下标
            'auth_end' => $result->$responseNode->auth_end ?? null,// 授权失效时间  2016-11-03 01:59:57 -- 有可能没有此下标
            'status' => $result->$responseNode->status,// valid：有效状态；invalid：无效状态  valid
        ];
    }
}
