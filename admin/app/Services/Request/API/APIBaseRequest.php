<?php


namespace App\Services\Request\API;


class APIBaseRequest
{

    //  配置文件 public.apiRequestOptions 下标 ：如 ['headers' => ['Accept' => "application/vnd.myapp.v1+json"]]
    public static $optionsConfigKey = [];
    public static $apiConfigKey = '';// 请求api的常量配置下标  配置文件 public.apiConfig 下标
    public static $request_mode = 1;// 数据请求方式， 1 通过API获得数据； 2 访问本地数据库

    /**
     * 获得API常量配置数组
     *
     * @return array $apiConfigs 配置数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getAPIConfig(){
        $siteKey = static::$apiConfigKey ;
        $apiParams = config('public.apiConfig');
        $apiConfigs = $apiParams[$siteKey] ?? [];
        return $apiConfigs;
    }

    /**
     * 根据常量下标，获得API常量配置的具体值
     * @param string 常量配置下标
     * @return string $apiConfigs 配置的具体值
     * @author zouyan(305463219@qq.com)
     */
    public static function getAPIConfigByKey($key){
        $apiConfigs = static::getAPIConfig();
        $apiParamVal = $apiConfigs[$key] ?? '';
        return $apiParamVal;
    }

    /**
     * 获得请求options参数等
     *
     * @return array options参数等
     * @author zouyan(305463219@qq.com)
     */
    public static function getHeadersByConfig(){
        $siteKey = static::$optionsConfigKey ;
        $apiRequestOptions = config('public.apiRequestOptions');
        $sitesOptions = $apiRequestOptions[$siteKey] ?? [];
        static::resolveConfigOptions($sitesOptions);
        return $sitesOptions;
    }

    /**
     * 需要单独处理的，请继承重写此方法
     * 对请求options参数进行一些格式化处理
     *
     * @return array options参数等
     * @author zouyan(305463219@qq.com)
     */
    public static function  resolveConfigOptions(&$sitesOptions){
        return $sitesOptions;
    }

    /**
     * 获得请求地址-- 子类必须重写此方法
     *
     * @return string 接口地址
     * @author zouyan(305463219@qq.com)
     */
    public static function getUrl(){
        return static::getAPIConfigByKey('url');//config('public.apiUrl');
    }

}
