<?php


namespace App\Services\alipaySdk;

// require_once app_path('Library') . '/alipayWapPay/aop/AopClient.php';
use App\Services\Tool;

require_once 'aop/AopCertClient.php';

class BasicAlipay
{

    /**
     * --- 根据配置，实例化接口对象
     *
     * @param array $config  接口相关的配置信息
     *  $config = [
     *      'appCertPath' => "/srv/www/certFile/alipay/onlinePay/appCertPublicKey_2021002125656270.crt",// "应用证书路径（要确保证书文件可读），例如：/home/admin/cert/appCertPublicKey.crt";
     *      'alipayCertPath' => "/srv/www/certFile/alipay/onlinePay/alipayCertPublicKey_RSA2.crt",// "支付宝公钥证书路径（要确保证书文件可读），例如：/home/admin/cert/alipayCertPublicKey_RSA2.crt";
     *      'rootCertPath' => "/srv/www/certFile/alipay/onlinePay/alipayRootCert.crt",// "支付宝根证书路径（要确保证书文件可读），例如：/home/admin/cert/alipayRootCert.crt";
     *      'gatewayUrl' => 'https://openapi.alipay.com/gateway.do', // 可无此下标，默认 'https://openapi.alipay.com/gateway.do'
     *      'appId' => '2021002125656270',// '你的appid';
     *      'rsaPrivateKey' => '',// '你的应用私钥';
     *      'apiVersion' => '1.0',// 可无此下标，默认 '1.0'
     *      'signType' => 'RSA2',// 可无此下标，默认 'RSA2'
     *      'postCharset' => 'utf-8',// 可无此下标，默认 'utf-8'
     *      'format' => 'json',// 可无此下标，默认 'json'
     *      'isCheckAlipayPublicCert' => true,//是否校验自动下载的支付宝公钥证书，如果开启校验要保证支付宝根证书在有效期内 ; 可无此下标，默认 true
     *      // 1、调用这个 AlipayClient 构造方法
     *      // 加密密钥和类型---没有开启可以注释掉
     *      'encryptKey' => "PcH3Js0R2SaPe8uDvz94Qg==",// 加密密钥 // 可无此下标，默认 不设置
     *      'encryptType' => "AES",// 类型 // 可无此下标，默认 不设置
     *  ];
     * @return object 实例化接口对象
     * @author zouyan(305463219@qq.com)
     */
    public static function getAop($config = []){
        $aop = new \AopCertClient ();
        $appCertPath = $config['appCertPath'] ?? '';// "应用证书路径（要确保证书文件可读），例如：/home/admin/cert/appCertPublicKey.crt";
        $alipayCertPath = $config['alipayCertPath'] ?? '';// "支付宝公钥证书路径（要确保证书文件可读），例如：/home/admin/cert/alipayCertPublicKey_RSA2.crt";
        $rootCertPath = $config['rootCertPath'] ?? '';// "支付宝根证书路径（要确保证书文件可读），例如：/home/admin/cert/alipayRootCert.crt";

        $aop->gatewayUrl = $config['gatewayUrl'] ?? 'https://openapi.alipay.com/gateway.do';
        $aop->appId = $config['appId'] ?? '';// '2021002125656270';// '你的appid';
        $aop->rsaPrivateKey = $config['rsaPrivateKey'] ?? '';// '你的应用私钥';
        $aop->alipayrsaPublicKey = $aop->getPublicKey($alipayCertPath);//调用getPublicKey从支付宝公钥证书中提取公钥
        $aop->apiVersion = $config['apiVersion'] ?? '1.0';
        $aop->signType = $config['signType'] ?? 'RSA2';
        $aop->postCharset = $config['postCharset'] ?? 'utf-8';
        $aop->format = $config['format'] ?? 'json';
        $aop->isCheckAlipayPublicCert = $config['isCheckAlipayPublicCert'] ?? true;//是否校验自动下载的支付宝公钥证书，如果开启校验要保证支付宝根证书在有效期内
        $aop->appCertSN = $aop->getCertSN($appCertPath);//调用getCertSN获取证书序列号
        $aop->alipayRootCertSN = $aop->getRootCertSN($rootCertPath);//调用getRootCertSN获取支付宝根证书序列号

        // 1、调用这个 AlipayClient 构造方法：com.alipay.api.DefaultAlipayClient#DefaultAlipayClient(String, String, String, String, String, String, String, String, String)，最后两个参数分别传递 openhome 的 AES 密钥，和加密算法“AES”；
        // 加密密钥和类型---没有开启可以注释掉
        if(static::isOpenEncrypt($config)){
             $aop->encryptKey = $config['encryptKey'];// "PcH3Js0R2SaPe8uDvz94Qg==";
             $aop->encryptType = $config['encryptType'];// "AES";
        }

        return $aop;
    }

    /**
     * --- 根据配置，返回是否开启内容加密码
     *
     * @param array $config  接口相关的配置信息
     * @return boolean true:开启内容加密  ； false:未开启加密
     * @author zouyan(305463219@qq.com)
     */
    public static function isOpenEncrypt($config = []){

        if(isset($config['encryptKey']) && !empty($config['encryptKey']) && isset($config['encryptType']) && !empty($config['encryptType'])){
            return true;
        }
        return false;
    }


    /**
     * --- 根据设置，设置内容加密
     *
     * @param object $request  具体的API对象
     * @param array $config  接口相关的配置信息
     * @param array $needEncrypt 设置的值 true:加密内容【默认】； false：不加密内容
     * @return boolean true:开启内容加密  ； false:未开启加密
     * @author zouyan(305463219@qq.com)
     */
    public static function setNeedEncrypt(&$request, $config = [], $needEncrypt = true){
        // 2、对于每一个接口对应的请求类，调用它的 setNeedEncrypt 方法，设置为 true 就可以了。
        if(static::isOpenEncrypt($config) && method_exists($request,'setNeedEncrypt')){
            $request->setNeedEncrypt($needEncrypt);
        }
    }

    /**
     * --- 判断接口返回，如果有错误，则抛出误错
     *
     * @param object $result  接口返回对象
     * @param string $responseNode  接口结果下标 如 alipay_open_auth_token_app_response
     * @return null
     * @author zouyan(305463219@qq.com)
     */
    public static function judgeThrowsErr(&$result, $responseNode){
        $resultCode = $result->$responseNode->code;
        // 如果失败，则抛出错误
        if(!empty($resultCode) && $resultCode == 10000) {
            // echo "成功";
        }else{
           // echo "失败";
            throws($result->$responseNode->sub_msg . '【' . $result->$responseNode->sub_code . '】', $result->$responseNode->code);
        }
    }

    /**
     * --- 参数中需要数组需要转为json格式的参数，自动完成转换
     *
     * @param array $apiParams  源参数数组
     * @param array $jsoinFields  需要转换为json格式的参数数组，--一维数组
     * @return null
     * @author zouyan(305463219@qq.com)
     */
    public static function paramsArrToJson(&$apiParams, $jsonFields = []){
        return Tool::paramsArrToJson($apiParams, $jsonFields);
    }

    /**
     * --- 参数中需要json格式需要转为数组的参数，自动完成转换
     *
     * @param array $apiParams  源参数数组
     * @param array $jsoinFields  需要转换为json格式的参数数组，--一维数组
     * @return null
     * @author zouyan(305463219@qq.com)
     */
    public static function paramsJsonToArr(&$apiParams, $jsonFields = []){
        return Tool::paramsJsonToArr($apiParams, $jsonFields);
    }
}
