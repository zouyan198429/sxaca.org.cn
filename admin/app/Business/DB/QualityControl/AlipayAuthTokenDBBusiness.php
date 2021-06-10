<?php
// 支付宝授权及令牌
namespace App\Business\DB\QualityControl;

use App\Services\alipaySdk\AlipayToolAPI;
use App\Services\DB\CommonDB;
use App\Services\Tool;

/**
 *
 */
class AlipayAuthTokenDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\AlipayAuthToken';
    public static $table_name = 'alipay_auth_token';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];


    /**
     * 支付宝授权
     *
     * @param int  $company_id 企业id
     * @param array $authData  支付宝返回的授权信息
     *   $authData = [
     *       'app_id' => $app_id,// 开发者应用的 AppId
     *       'source' => $source,// 授权类型；如：alipay_app_auth
     *      'application_type' => $application_type,// 应用类型；多个用,号分隔； MOBILEAPP (移动应用)，WEBAPP（网页应用），PUBLICAPP（生活号），TINYAPP（小程序），ARAPP（AR应用）;
     *      'app_auth_code' => $app_auth_code,// 应用授权码
     *      'state' => $state,// 商户自定义参数；便于开发者识别是哪个商户的授权。
     *  ];
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  array 一维数组 收款帐号信息
     * @author zouyan(305463219@qq.com)
     */
    public static function alipayAuth($company_id, $authData = [], $operate_staff_id = 0, $modifAddOprate = 0){

        $app_id = $authData['app_id'];// 开发者应用的 AppId 2021002124696736
        $source = $authData['source'];// 授权类型；如：alipay_app_auth alipay_app_auth
        $application_type = $authData['application_type'];// $application_type,// 应用类型；多个用,号分隔； MOBILEAPP (移动应用)，WEBAPP（网页应用），PUBLICAPP（生活号），TINYAPP（小程序），ARAPP（AR应用）; WEBAPP,MOBILEAPP
        $app_auth_code = $authData['app_auth_code'];// 应用授权码 P78879f6aa936405c94a4118224d1a20
        $state = $authData['state'];// state 对应的值 在授权过程中，建议在拼接授权 URL 的时候，开发者可增加自己的一个自定义信息（即 URL 拼接规则中的 state 参数），便于开发者识别是哪个商户的授权。
        // 获得收款帐号信息
        $orderPayConfigInfo = OrderPayConfigDBBusiness::getDBFVFormatList(4, 1, ['id' => $state]
            , false, '', []);
        if(empty($orderPayConfigInfo)) throws('收款帐号记录不存在！');
        $orderPayConfigInfo = $orderPayConfigInfo->toArray();
        if(!in_array($orderPayConfigInfo['alipay_auth_status'], [2]) ) throws('收款帐号非未授权！');

        // 使用 app_auth_code 换取 app_auth_token
        // 应用授权的 app_auth_code 是唯一的；app_auth_code 使用一次后失效，单个授权的有效期为一天（从生成 app_auth_code 开始的 24 小时）未被使用自动过期；批量授权的有效期为 10 分钟。
        // 调用支付宝接口
        $alipayConfig = config('public.alipayConfig.APIConfig');
        $apiTokens = AlipayToolAPI::getOpenAuthTokenApp($alipayConfig, 'authorization_code', $app_auth_code);
        $authAppIds = [];
        $appAuthTokens = [];
        foreach($apiTokens as $temInfoObj){
            array_push($authAppIds, $temInfoObj->auth_app_id);
            array_push($appAuthTokens, $temInfoObj->app_auth_token);
        }


        $alipayAuthContent = [];
        // 查询某个应用授权AppAuthToken的授权信息
        foreach($appAuthTokens as $temAppAuthToken){
            $resultQuery = AlipayToolAPI::getOpenAuthTokenAppQuery($alipayConfig, $temAppAuthToken);
            $auth_methods = $resultQuery['auth_methods'];
            if(is_array($auth_methods)) $auth_methods = json_encode($auth_methods);
            $temContentInfo = [
                'pay_config_id' => $orderPayConfigInfo['id'], // 收款帐号配置id
                'user_id' => $resultQuery['user_id'], // 授权商户的user_id
                'auth_app_id' => $resultQuery['auth_app_id'], // 授权商户的appid
                'app_auth_token' => $temAppAuthToken, // 应用授权令牌
                'expires_in' => $resultQuery['expires_in'], // 应用令牌长期有效；该字段已作废
                'auth_methods' => $auth_methods, // 当前app_auth_token的授权接口列表
                // 'auth_start' => $resultQuery['auth_start'], // 授权生效时间
                // 'auth_end' => $resultQuery['auth_end'], // 授权失效时间
                'status' => $resultQuery['status'], // 状态：valid：有效状态；invalid：无效状态
            ];
            if(isset($resultQuery['auth_start']) &&  judgeDate($resultQuery['auth_start']) !== false) $temContentInfo['auth_start'] = $resultQuery['auth_start'];
            if(isset($resultQuery['auth_end']) &&  judgeDate($resultQuery['auth_end']) !== false) $temContentInfo['auth_end'] = $resultQuery['auth_end'];
            array_push($alipayAuthContent, $temContentInfo);

        }
        // 获得支付宝授权及令牌信息-正起作用的【唯一的一条】，也可能没有记录
        $alipayAuthTokenRunList = static::getDBFVFormatList(1, 1, ['auth_app_id' => $authAppIds, 'operate_status' =>2 ]// 'pay_config_id' => $orderPayConfigInfo['id'],
            , false, '', []);
        $alipayAuthTokenRunFormatList = Tool::arrUnderReset($alipayAuthTokenRunList, 'auth_app_id', 2, '_');



        CommonDB::doTransactionFun(function() use(&$orderPayConfigInfo, &$company_id, &$authData, &$operate_staff_id, &$modifAddOprate, &$app_id, &$source, &$application_type
            , &$app_auth_code, &$state, &$alipayAuthTokenRunList, &$alipayAuthTokenRunFormatList, &$apiTokens, &$authAppIds, &$alipayAuthContent){
            foreach($apiTokens as $temTokenInfoObj){
                $auth_app_id = $temTokenInfoObj->auth_app_id;
                $alipayAuthTokenInfo = [
                    'pay_config_id' => $orderPayConfigInfo['id'],// 收款帐号配置id
                    'app_id' => $app_id,// 开发者应用的 AppId
                    'source' => $source,// 授权类型；如：alipay_app_auth
                    'application_type' => $application_type,// 应用类型；多个用,号分隔； MOBILEAPP (移动应用)，WEBAPP（网页应用），PUBLICAPP（生活号），TINYAPP（小程序），ARAPP（AR应用）;
                    'app_auth_code' => $app_auth_code,// 应用授权码
                    'state' => $state,// 商户自定义参数；便于开发者识别是哪个商户的授权。
                    'user_id' => $temTokenInfoObj->user_id ,// 授权商户的user_id
                    'auth_app_id' => $temTokenInfoObj->auth_app_id,// 授权商户的appid
                    'app_auth_token' => $temTokenInfoObj->app_auth_token,// 应用授权令牌
                    'app_refresh_token' => $temTokenInfoObj->app_refresh_token ,// 刷新令牌
                    'expires_in' => $temTokenInfoObj->expires_in,// 应用令牌长期有效；该字段已作废
                    're_expires_in' => $temTokenInfoObj->re_expires_in ,// 刷新令牌的有效时间，单位到秒；刷新后老的refresh_token会在一段时间后失效
                    'operate_status' => 2,// 操作状态(1待换取令牌2已换取令牌4作废【已刷新令牌】)
                    'p_auth_token_id' => 0,// 父令牌id
                ];
                $alipayAuthTokenId = 0;
                static::replaceById($alipayAuthTokenInfo, $company_id, $alipayAuthTokenId, $operate_staff_id, $modifAddOprate);
            }

            if(!empty($alipayAuthTokenRunList)){
                // 更新状态为已作废
                $mainIds = Tool::getArrFields($alipayAuthTokenRunList, 'id');
                $saveQueryParams = Tool::getParamQuery(['id' => $mainIds],[], []);
                static::save(['operate_status' => 4], $saveQueryParams);

                // 支付宝授权内容 改为 失效
                // $mainAppAuthTokens = Tool::getArrFields($alipayAuthTokenRunList, 'app_auth_token');
                $mainAuthAppIds = Tool::getArrFields($alipayAuthTokenRunList, 'auth_app_id');
                $saveQueryParams = Tool::getParamQuery(['auth_app_id' => $mainAuthAppIds, 'status' => 'valid'],[], []);
                AlipayAuthContentDBBusiness::save(['status' => 'invalid'], $saveQueryParams);

                // 改为未授权
                $payConfigIds = Tool::getArrFields($alipayAuthTokenRunList, 'pay_config_id');
                $saveQueryParams = Tool::getParamQuery(['id' => $payConfigIds],[], []);
                OrderPayConfigDBBusiness::save(['alipay_auth_status' => 2], $saveQueryParams);
            }
            // 更新状态为支付宝授权已授权    (1已授权2未授权)
            $saveInfo = ['alipay_auth_status' => 1];
            // $orderPayConfigInfo['alipay_auth_status'] = 1;
            $orderPayConfigInfo = array_merge($orderPayConfigInfo, $saveInfo);
            $saveQueryParams = Tool::getParamQuery(['id' => $orderPayConfigInfo['id']],[], []);
            OrderPayConfigDBBusiness::save($saveInfo, $saveQueryParams);
            // 创建或更新 支付宝授权内容
            foreach($alipayAuthContent as $temContentInfo){
                $searchConditon = [
                    'app_auth_token' => $temContentInfo['app_auth_token'],
                ];
                $contentObj = null;
                AlipayAuthContentDBBusiness::updateOrCreate($contentObj, $searchConditon, $temContentInfo );
            }
        });
        return $orderPayConfigInfo;

    }

    /**
     * 支付宝刷新授权令牌
     *
     * @param int  $company_id 企业id
     * @param int $id  支付宝授权及令牌id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  array 一维数组 ['新的应用授权令牌']
     * @author zouyan(305463219@qq.com)
     */
    public static function alipayRefeshAuth($company_id, $id, $operate_staff_id = 0, $modifAddOprate = 0)
    {

        // 获得令牌信息
        $alipayAuthTokenInfo = static::getDBFVFormatList(4, 1, ['id' => $id]// , 'operate_status' => 2
            , false, '', []);
        if(empty($alipayAuthTokenInfo)) throws('支付宝授权及令牌记录不存在！');
        $alipayAuthTokenInfo = $alipayAuthTokenInfo->toArray();
        if(!in_array($alipayAuthTokenInfo['operate_status'], [2]) ) throws('支付宝授权及令牌非已换取令牌！');
        $appRefreshToken = $alipayAuthTokenInfo['app_refresh_token'];
        $appAuthToken = $alipayAuthTokenInfo['app_auth_token'];
        $payConfigId = $alipayAuthTokenInfo['pay_config_id'];

        // 获得收款帐号信息
        $orderPayConfigInfo = OrderPayConfigDBBusiness::getDBFVFormatList(4, 1, ['id' => $payConfigId]
            , false, '', []);
        if(empty($orderPayConfigInfo)) throws('收款帐号记录不存在！');
        $orderPayConfigInfo = $orderPayConfigInfo->toArray();
        if(!in_array($orderPayConfigInfo['alipay_auth_status'], [1]) ) throws('收款帐号非已授权！');

        // 使用 app_auth_code 换取 app_auth_token
        // 应用授权的 app_auth_code 是唯一的；app_auth_code 使用一次后失效，单个授权的有效期为一天（从生成 app_auth_code 开始的 24 小时）未被使用自动过期；批量授权的有效期为 10 分钟。
        // 调用支付宝接口
        $alipayConfig = config('public.alipayConfig.APIConfig');
        $apiTokens = AlipayToolAPI::getOpenAuthTokenApp($alipayConfig, 'refresh_token', '', $appRefreshToken, null);
        $authAppIds = [];
        $appAuthTokens = [];
        foreach($apiTokens as $temInfoObj){
            array_push($authAppIds, $temInfoObj->auth_app_id);
            array_push($appAuthTokens, $temInfoObj->app_auth_token);
        }

        $alipayAuthContent = [];
        // 查询某个应用授权AppAuthToken的授权信息
        foreach($appAuthTokens as $temAppAuthToken){
            $resultQuery = AlipayToolAPI::getOpenAuthTokenAppQuery($alipayConfig, $temAppAuthToken);
            $auth_methods = $resultQuery['auth_methods'];
            if(is_array($auth_methods)) $auth_methods = json_encode($auth_methods);
            $temContentInfo = [
                'pay_config_id' => $orderPayConfigInfo['id'], // 收款帐号配置id
                'user_id' => $resultQuery['user_id'], // 授权商户的user_id
                'auth_app_id' => $resultQuery['auth_app_id'], // 授权商户的appid
                'app_auth_token' => $temAppAuthToken, // 应用授权令牌
                'expires_in' => $resultQuery['expires_in'], // 应用令牌长期有效；该字段已作废
                'auth_methods' => $auth_methods, // 当前app_auth_token的授权接口列表
                // 'auth_start' => $resultQuery['auth_start'], // 授权生效时间
                // 'auth_end' => $resultQuery['auth_end'], // 授权失效时间
                'status' => $resultQuery['status'], // 状态：valid：有效状态；invalid：无效状态
            ];
            if(isset($resultQuery['auth_start']) &&  judgeDate($resultQuery['auth_start']) !== false) $temContentInfo['auth_start'] = $resultQuery['auth_start'];
            if(isset($resultQuery['auth_end']) &&  judgeDate($resultQuery['auth_end']) !== false) $temContentInfo['auth_end'] = $resultQuery['auth_end'];
            array_push($alipayAuthContent, $temContentInfo);

        }

        // 新加记录并作废前记录
        CommonDB::doTransactionFun(function() use(&$orderPayConfigInfo, &$company_id, &$operate_staff_id, &$modifAddOprate,
            &$alipayAuthTokenInfo, &$apiTokens, &$authAppIds, &$alipayAuthContent){
            foreach($apiTokens as $temTokenInfoObj){
                $auth_app_id = $temTokenInfoObj->auth_app_id;
                $temAlipayAuthTokenInfo = [
                    'pay_config_id' => $orderPayConfigInfo['id'],// 收款帐号配置id
                    'app_id' => $alipayAuthTokenInfo['app_id'],// 开发者应用的 AppId
                    'source' => $alipayAuthTokenInfo['source'],// 授权类型；如：alipay_app_auth
                    'application_type' => $alipayAuthTokenInfo['application_type'],// 应用类型；多个用,号分隔； MOBILEAPP (移动应用)，WEBAPP（网页应用），PUBLICAPP（生活号），TINYAPP（小程序），ARAPP（AR应用）;
                    'app_auth_code' => $alipayAuthTokenInfo['app_auth_code'],// 应用授权码
                    'state' => $alipayAuthTokenInfo['state'],// 商户自定义参数；便于开发者识别是哪个商户的授权。
                    'user_id' => $temTokenInfoObj->user_id ,// 授权商户的user_id
                    'auth_app_id' => $temTokenInfoObj->auth_app_id,// 授权商户的appid
                    'app_auth_token' => $temTokenInfoObj->app_auth_token,// 应用授权令牌
                    'app_refresh_token' => $temTokenInfoObj->app_refresh_token ,// 刷新令牌
                    'expires_in' => $temTokenInfoObj->expires_in,// 应用令牌长期有效；该字段已作废
                    're_expires_in' => $temTokenInfoObj->re_expires_in ,// 刷新令牌的有效时间，单位到秒；刷新后老的refresh_token会在一段时间后失效
                    'operate_status' => 2,// 操作状态(1待换取令牌2已换取令牌4作废【已刷新令牌】)
                    'p_auth_token_id' => $alipayAuthTokenInfo['id'],// 父令牌id
                ];
                $alipayAuthTokenId = 0;
                static::replaceById($temAlipayAuthTokenInfo, $company_id, $alipayAuthTokenId, $operate_staff_id, $modifAddOprate);
            }

            // 更新状态为已作废
            $saveQueryParams = Tool::getParamQuery(['id' => $alipayAuthTokenInfo['id']],[], []);
            static::save(['operate_status' => 4], $saveQueryParams);

            // 支付宝授权内容 改为 失效
            $saveQueryParams = Tool::getParamQuery(['auth_app_id' => $temTokenInfoObj->auth_app_id, 'status' => 'valid'],[], []);
            AlipayAuthContentDBBusiness::save(['status' => 'invalid'], $saveQueryParams);

            // 更新状态为支付宝授权已授权    (1已授权2未授权)
            $saveInfo = ['alipay_auth_status' => 1];
            // $orderPayConfigInfo['alipay_auth_status'] = 1;
            $saveQueryParams = Tool::getParamQuery(['id' => $orderPayConfigInfo['id']],[], []);
            OrderPayConfigDBBusiness::save($saveInfo, $saveQueryParams);

            // 创建或更新 支付宝授权内容
            foreach($alipayAuthContent as $temContentInfo){
                $searchConditon = [
                    'app_auth_token' => $temContentInfo['app_auth_token'],
                ];
                $contentObj = null;
                AlipayAuthContentDBBusiness::updateOrCreate($contentObj, $searchConditon, $temContentInfo );
            }
        });
        return $appAuthTokens;
    }

    /**
     * 对360天未刷新的进行刷新处理--每天跑一次
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public static function autoRefeshAuth()
    {
        $dateTime =  date('Y-m-d H:i:s');
        // 读取所有未开始的
//        $queryParams = [
//            'where' => [
//                ['status', 1],
//                ['join_begin_date', '<=', $dateTime],
//            ],
//             'select' => ['id' ]
//        ];
        $endTime = Tool::addMinusDate($dateTime, ['-360 day'], 'Y-m-d H:i:s', 1, '时间');
        $queryParams = Tool::getParamQuery(['operate_status' => 2], [
            'sqlParams' =>[
                'select' =>['id' ]
                ,
                'where' => [
                    ['created_at', '<=', $endTime]
                ]
            ]
        ], []);
        $dataList = static::getAllList($queryParams, [])->toArray();

        if(!empty($dataList)){
            $company_id = 0;
            foreach($dataList as $info){
                static::alipayRefeshAuth($company_id, $info['id'], 0, 0);
            }
        }
    }
}
