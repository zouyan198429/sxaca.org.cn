<?php
namespace App\Services\Request\API\Sites;
/**
 *  沪友API开放平台-- 开电子发票
 *  接口文档 http://api.hydzfp.com/index.php?s=/3&page_id=11
 *
 */

use App\Services\Request\API\APIBaseRequest;
use App\Services\Request\API\APIBasicRequest;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Support\Facades\Log;

/**
 *具体业务通用接口类--请求api--公有接口
 *  //extends APIBasicRequest  第三方其它的，不用继承此类
 */
class APIHYDZFPRequest extends APIBaseRequest
{
    // 配置文件 public.apiRequestOptions 下标 ：如 ['headers' => ['Accept' => "application/vnd.myapp.v1+json"]]
    public static $optionsConfigKey = 'APIHYDZFPROptions';
    public static $apiConfigKey = 'HYDZFPR';// 请求api的常量配置下标  配置文件 public.apiConfig 下标
    public static $request_mode = 1;// 数据请求方式， 1 通过API获得数据； 2 访问本地数据库

    /**
     * 获得请求地址-- 子类必须重写此方法
     *
     * @return string 接口地址
     * @author zouyan(305463219@qq.com)
     */
//    public static function getUrl(){
//        return static::getAPIConfigByKey('url');// config('public.apiConfig.url');
//    }

    /**
     * 获取接口数据
     *
     * @param string $method 请求方法前缀 如：open_access/buss/execClient.open ； open_access/buss/exec.open 或  open_access/access/token.open
     * @param array/object $params 参数数组/对象
     * @param array $urlParams url地址后面的参数数组 数据最终转换成-如:'?id='
     * @param string $type 请求类型 'GET'、'POST'、'PUT'、'PATCH'、'DELETE'
     * @author zouyan(305463219@qq.com)
     */
    protected static function getAPIRequest($method, $params = [], $urlParams = [], $type = 'POST')
    {
        $url = static::getUrl() . $method;
        // 生成带参数的测试get请求
        // $requestTesUrl = splicQuestAPI($url , $requestData);
        //return HttpRequest::HttpRequestApi($url, [], $requestData, 'GET');

        Log::info('电子发票接口日志 沪友  请求接口参数-->' . __FUNCTION__, [$url, $params, $urlParams, $type, static::getHeadersByConfig()]);

        $result = HttpRequest::sendHttpRequest($url, $params, $urlParams, $type, static::getHeadersByConfig());

        Log::info('电子发票接口日志 沪友  请求接口参数及返回数据-->' . __FUNCTION__, [$url, $params, $urlParams, $type, static::getHeadersByConfig(), $result]);

        $resultData = json_decode($result, true);

        $result = $resultData['result'] ?? 0;
        $msg = $resultData['msg'] ?? '返回数据错误!';
        $data = $resultData['rows'] ?? [];
        if ($result != 'SUCCESS'){
             throws('沪友接口错误:' . $msg);
        }
//        if(!is_array($data)){
//            $data = [];
//        }
        return $data;
    }

    /**
     * 获取access token
     *
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ["expire_in" => 7200, 'access_token' => 'FfJPQjgxQGa2y0Snuuc4Q94iQpce3A6x']
     */
    public static function getAccessToken($openid, $app_secret, $forceApi = false){
        $params = [
            'openid' => $openid,
            'app_secret' => $app_secret,
        ];

        $keyRedisPre = Tool::getProjectKey(1, ':', ':') . 'InvoiceHYDZFP' . ':' . $openid;
        $keyRedis = ':' . md5(json_encode($params));
        $operateRedis = 1;
        $cacheExpire = ceil(7200 / 5) * 4;

        if(!$forceApi){
            $accessTokenConfig = Tool::getRedis($keyRedisPre . $keyRedis, $operateRedis);
            Log::info('电子发票接口日志 沪友  获取access token，缓存中的数据-->' . __FUNCTION__, [$accessTokenConfig]);
            if(is_array($accessTokenConfig) && !empty($accessTokenConfig)){
                return $accessTokenConfig;
            }
        }

        $accessTokenConfig = static::getAPIRequest('open_access/access/token.open', $params, [], 'POST');

        Log::info('电子发票接口日志 沪友 获取access token，重新缓存数据-->' . __FUNCTION__, [$accessTokenConfig]);
        Tool::setRedis($keyRedisPre, $keyRedis, $accessTokenConfig, $cacheExpire , $operateRedis);

        return $accessTokenConfig;
    }


    /**
     * 获取通用业务接口数据
     *
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  string  $serviceKey 应用接口service_key
     * @param  array  $data 业务请求数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return mixed 接口返回的与具体业务相关的数据
     */
    public static function getAPICommon($openid, $app_secret, $serviceKey, $data, $forceApi = false){
        $accessTokenConfig = static::getAccessToken($openid, $app_secret, $forceApi);
        $params = [
            'access_token' => $accessTokenConfig['access_token'],
            'serviceKey' => $serviceKey,
            'data' => $data,// json_encode($data)
        ];
        $result = static::getAPIRequest('open_access/buss/exec.open', $params, [], 'POST');

        return $result;

    }


    /**
     * 获取通用业务接口-base64数据
     *
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  string  $serviceKey 应用接口service_key
     * @param  array  $data 业务请求数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return mixed 接口返回的与具体业务相关的数据
     */
    public static function getAPIBase64($openid, $app_secret, $serviceKey, $data, $forceApi = false){
        $accessTokenConfig = static::getAccessToken($openid, $app_secret, $forceApi);
        $params = [
            'access_token' => $accessTokenConfig['access_token'],
            'serviceKey' => $serviceKey,
            'data' => base64_encode(json_encode($data)),
        ];
        $result = static::getAPIRequest('open_access/buss/execClient.open', $params, [], 'POST');

        return $result;

    }

    /**
     * 获取业务接口
     *
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  string  $serviceKey 应用接口service_key
     * @param  array  $data 业务请求数据
     * @param  int  $apiDataMode 业务请求数据的方式 0 使用配置文件配置的 [默认]；1：通用 ；2  base64数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return mixed 接口返回的与具体业务相关的数据
     */
    public static function getAPI($openid, $app_secret, $serviceKey, $data, $apiDataMode = 0, $forceApi = false){
        $result = [];
        if($apiDataMode == 0) $apiDataMode = static::getAPIConfigByKey('apiDataMode');
        switch($apiDataMode){
            case 2:// 2  base64数据
                $result = static::getAPIBase64($openid, $app_secret, $serviceKey, $data, $forceApi);
                break;
            default:// 1：通用[默认]
                $result = static::getAPICommon($openid, $app_secret, $serviceKey, $data, $forceApi);
                break;
        }
        return $result;

    }
    // *****************离线开票(扫码开票)*************************************************************************
    /**
     * E0001-获取离线开票authid
     *
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  string  $xsf_nsrsbh 销售方纳税人识别号
     * @param  int  $apiDataMode 业务请求数据的方式 0 使用配置文件配置的 [默认]；1：通用 ；2  base64数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ['authid' => '439658478412BB29E053...']
     */
    public static function getAuthid($openid, $app_secret, $xsf_nsrsbh, $apiDataMode = 0, $forceApi = false){
        $params = [
            'openid' => $openid,
            'app_secret' => $app_secret,
            'xsf_nsrsbh' => $xsf_nsrsbh,
        ];

        $keyRedisPre = Tool::getProjectKey(1, ':', ':') . 'InvoiceHYDZFP' . ':' . $openid . ':authid' . $xsf_nsrsbh;
        $keyRedis = ':' . md5(json_encode($params));
        $operateRedis = 1;
        $cacheExpire = ceil(7200 / 5) * 4;

        if(!$forceApi){
            $authidConfig = Tool::getRedis($keyRedisPre . $keyRedis, $operateRedis);
            Log::info('电子发票接口日志 沪友  获取authid，缓存中的数据-->' . __FUNCTION__, [$authidConfig]);
            if(is_array($authidConfig) && !empty($authidConfig)){
                return $authidConfig;
            }
        }

        $data = [
            "xsf_nsrsbh" => $xsf_nsrsbh,
        ];
        $authidConfig = static::getAPI($openid, $app_secret, 'offline_authId_getInfo', $data, $apiDataMode);
        Log::info('电子发票接口日志 沪友 获取authid，重新缓存数据-->' . __FUNCTION__, [$authidConfig]);
        Tool::setRedis($keyRedisPre, $keyRedis, $authidConfig, $cacheExpire , $operateRedis);

        return $authidConfig;
    }

}
