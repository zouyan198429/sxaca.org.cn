<?php

namespace App\Services\Request\API;

use App\Services\Redis\RedisString;
use App\Services\Tool;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\URL;
/**
 * 通用http请求
 */
class HttpRequest
{

    /**
     *  获取响应的hearder数组信息
     *
     * @param array $header  接口返回的header信息
     *  [
     *      "Server" => [
     *          "nginx"
     *      ],
     *      "Date" => [
     *          "Fri, 05 Feb 2021 05:11:15 GMT"
     *      ],
     *      "Content-Type" => [
     *          "application/json; charset=utf-8"
     *      ],
     *      "Content-Length" => [
     *          "2168"
     *      ],
     *      "Connection" => [
     *          "keep-alive"
     *      ],
     *      "Keep-Alive" => [
     *          "timeout=8"
     *      ],
     *      "Cache-Control" => [
     *          "no-cache, must-revalidate"
     *      ],
     *      "X-Content-Type-Options" => [
     *          "nosniff"
     *      ],
     *      "Request-ID" => [
     *          "08F3A6F38006101718A7B8B74C20F62528D7BF03-0"
     *      ],
     *      "Content-Language" => [
     *          "zh-CN"
     *      ],
     *      "Wechatpay-Nonce" => [
     *          "41ce8e57ec95f463e903e6423575ea3a"
     *      ],
     *      "Wechatpay-Signature" => [
     *          "fQPj2vjG7jRDAgvJ1/oqpAZIfVcB+RE2Xki/254ZwaauR7fBOhHSFJpOfSiS3nckSupxY3MGD015U8V7z/GB1svEqVIkUWA8Iz01tJnkUArn17WdfiNpcUWNfnRfE99Z9Cz37pv56NalFSw38FqlSGKHnOCDiwbnWu2OHJwWxv99iirrdzemfJT6h+XORykMMCEt9xY4JJBJY7jEENP+U+k/raVlSVIDhe4R9XBxv+yrkVxF90TTBdMniQcZfCdOj7oN4LaaVktbmntyfvDQtlv5KIKk7V+8umbBqNkH7+JnJypQsxeNcFDPiTZ8y6Y2Mhgr8CxEBN9uAhRT7sdWYA=="
     *      ],
     *      "Wechatpay-Timestamp" => [
     *          "1612501875"
     *      ],
     *      "Wechatpay-Serial" => [
     *          "35B4105DBFB51A3845213F8FF5F79413A6E48304"
     *      ]
     *  ]
     *
     * @return array 请求的 hearder 数组
     *   $extendParams = [
     *      'Accept-Language' => 'zh-CN',// 应答中的错误描述使用的自然语言语种。如果有需要，设置请求的HTTP头Accept-Language。目前支持：  en  zh-CN  zh-HK  zh-TW
     *      'Wechatpay-Serial' => 'aaaa',// 可能有【敏感信息时有】 上送敏感信息时使用微信支付平台公钥加密，证书序列号包含在请求HTTP头部的 Wechatpay-Serial
     *  ];
     * @author zouyan(305463219@qq.com)
     */
    public static function getHearderArr($header = []){
        $reHearders = [];
        foreach ($header as $name => $values) {
            // echo $name . ': ' . implode(', ', $values) . "\r\n";
            if(is_array($values))  $values = implode(', ', $values);
            $reHearders[$name] = $values;
        }
        return $reHearders;
    }

    /**
     * 调用速通接口服务方法-是什么内容，返回什么内容  数组 HTTP协议状态码 及 接口返回的内容
     *
     * @param string $url 请求地址
     * @param array/object $params 参数数组/对象
     * @param array $urlParams url地址后面的参数数组 数据最终转换成-如:'?id='
     * @param string $type 请求类型 'GET'、'POST'、'PUT'、'PATCH'、'DELETE'
     * @param array $options 其它默认参数：如 ['headers' => ['Accept' => "application/vnd.myapp.v1+json"]]
     * @return array
     * [
     *       'code' => $result->getStatusCode(), // HTTP协议状态码,
     *      'reason' => '',// OK// 原因短语(reason phrase)：
     *      'header' => [],// 获取头部信息；注意：下标的值还是一个数组
     *      'response_content' => '',// 接口返回的内容 -- 主要用这个，可能是json 格式的字符串
     *      'body' => object,// 获取html 返回的内容 对象
     *      'response' => object,// 响应对象
     *  ]
     */
    public static function sendHttpRequestUrl($url, $params = [], $urlParams = [], $type = 'POST', $options = [])
    {

        if (! empty($urlParams)) {
            $url .= '?' . http_build_query($urlParams);
        }

        // 9位自增编号（每天重置）
        // $number         = ToolsHelper::createSnDaily('BSuTong', 9);
        // $dataExchangeId = date('Ymd') . $number;
        // $logParams = self::getSimpleParams($params);

        // Yii::info([$dataExchangeId, $method, $logParams, $url], 'curl\BSuTongHttp\request');

        $http = new Client();

        switch ($type) {
            case 'POST':
                $option = array_merge($options, ['json' => $params]);
                $response = $http->post($url, $option);
                break;
            case 'GET':
                $response = $http->get($url, $options);
                break;
        }

//        if (200 != $response->getStatusCode()) {
//            throws('速通：请求失败 StatusCode: ' . $response->getStatusCode());
//        }

        // $ret = $response->getBody()->getContents();


        // Yii::info([$dataExchangeId, $method, $ret], 'curl\BSuTongHttp\response');

        // https://phpartisan.cn/news/45.html  [ laravel爬虫实战--基础篇 ] guzzle使用响应,获取header以及页面代码
        // 你可以获取这个响应的状态码和和原因短语(reason phrase)：
        // $code = $response->getStatusCode(); // 200
        // $reason = $response->getReasonPhrase(); // OK
        // 你可以从响应获取头信息(header)，更多信息直接参考就可以了：

        // 判断是否有请求头
        // if ($response->hasHeader('Content-Length')) {
        //    echo "It exists";
        // }
        // // 从请求头中获取一个参数.
        // echo $response->getHeader('Content-Length');
        // // 获取全部的请求头.
        // foreach ($response->getHeaders() as $name => $values) {
        //    echo $name . ': ' . implode(', ', $values) . "\r\n";
        // }
        // 使用getBody方法可以获取响应的主体部分(body)，主体可以当成一个字符串或流对象使用

        // $body = $response->getBody();
        // // Implicitly cast the body to a string and echo it
        // echo $body;
        // // Explicitly cast the body to a string
        // $stringBody = (string) $body;
        // // 获取body中的10个字节
        // $tenBytes = $body->read(10);
        // // 将正文的剩余内容作为字符串读取。
        // $remainingBytes = $body->getContents();

        // $client = new Client();
        // $response = $client->request('GET',"https://phpartisan.cn");
        // 获取头部信息
        // $header = $response->getHeaders();

        /**
         *  获取响应的hearder数组信息
         *
         * @ param array $header  接口返回的header信息
         *  [
         *      "Server" => [
         *          "nginx"
         *      ],
         *      "Date" => [
         *          "Fri, 05 Feb 2021 05:11:15 GMT"
         *      ],
         *      "Content-Type" => [
         *          "application/json; charset=utf-8"
         *      ],
         *      "Content-Length" => [
         *          "2168"
         *      ],
         *      "Connection" => [
         *          "keep-alive"
         *      ],
         *      "Keep-Alive" => [
         *          "timeout=8"
         *      ],
         *      "Cache-Control" => [
         *          "no-cache, must-revalidate"
         *      ],
         *      "X-Content-Type-Options" => [
         *          "nosniff"
         *      ],
         *      "Request-ID" => [
         *          "08F3A6F38006101718A7B8B74C20F62528D7BF03-0"
         *      ],
         *      "Content-Language" => [
         *          "zh-CN"
         *      ],
         *      "Wechatpay-Nonce" => [
         *          "41ce8e57ec95f463e903e6423575ea3a"
         *      ],
         *      "Wechatpay-Signature" => [
         *          "fQPj2vjG7jRDAgvJ1/oqpAZIfVcB+RE2Xki/254ZwaauR7fBOhHSFJpOfSiS3nckSupxY3MGD015U8V7z/GB1svEqVIkUWA8Iz01tJnkUArn17WdfiNpcUWNfnRfE99Z9Cz37pv56NalFSw38FqlSGKHnOCDiwbnWu2OHJwWxv99iirrdzemfJT6h+XORykMMCEt9xY4JJBJY7jEENP+U+k/raVlSVIDhe4R9XBxv+yrkVxF90TTBdMniQcZfCdOj7oN4LaaVktbmntyfvDQtlv5KIKk7V+8umbBqNkH7+JnJypQsxeNcFDPiTZ8y6Y2Mhgr8CxEBN9uAhRT7sdWYA=="
         *      ],
         *      "Wechatpay-Timestamp" => [
         *          "1612501875"
         *      ],
         *      "Wechatpay-Serial" => [
         *          "35B4105DBFB51A3845213F8FF5F79413A6E48304"
         *      ]
         *  ]
         */
        // 获取html
        // $body = $response->getBody();
        // 如果你想直接输出代码，可以取消如下注视
        // dd($header);
        // echo $body;
        // 转换为字符串
        // $stringBody = (string) $body
        // 从body中读取10字节
        // $tenBytes = $body->read(10);
        // 将body内容读取为字符串
        // $remainingBytes = $body->getContents();

        return [
            'code' => $response->getStatusCode(), // 200,// $response->getStatusCode(), // HTTP协议状态码,状态码
            'reason' => $response->getReasonPhrase(), // OK// 原因短语(reason phrase)：
            'header' => $response->getHeaders(),// 获取头部信息
            'response_content' => $response->getBody()->getContents(),// 接口返回的内容
            'body' => $response->getBody(),// 获取html
            'response' => $response,// 所有响应
        ];
    }

    /**
     * 调用速通接口服务方法-是什么内容，返回什么内容
     *
     * @param string $url 请求地址
     * @param array/object $params 参数数组/对象
     * @param array $urlParams url地址后面的参数数组 数据最终转换成-如:'?id='
     * @param string $type 请求类型 'GET'、'POST'、'PUT'、'PATCH'、'DELETE'
     * @param array $options 其它默认参数：如 ['headers' => ['Accept' => "application/vnd.myapp.v1+json"]]
     * @return array/string
     */
    public static function sendHttpRequest($url, $params = [], $urlParams = [], $type = 'POST', $options = [])
    {

        if (! empty($urlParams)) {
            $url .= '?' . http_build_query($urlParams);
        }

        // 9位自增编号（每天重置）
        // $number         = ToolsHelper::createSnDaily('BSuTong', 9);
        // $dataExchangeId = date('Ymd') . $number;
        // $logParams = self::getSimpleParams($params);

        // Yii::info([$dataExchangeId, $method, $logParams, $url], 'curl\BSuTongHttp\request');

        $http = new Client();

        switch ($type) {
            case 'POST':
                $option = array_merge($options, ['json' => $params]);
                $result = $http->post($url, $option);
                break;
            case 'GET':
                $result = $http->get($url, $options);
                break;
        }

        if (200 != $result->getStatusCode()) {
            throws('速通：请求失败 StatusCode: ' . $result->getStatusCode());
        }

        $ret = $result->getBody()->getContents();

        // Yii::info([$dataExchangeId, $method, $ret], 'curl\BSuTongHttp\response');

        return $ret;
    }

    /**
     * 调用速通接口服务方法-是什么内容，返回什么内容
     *
     * @param string $url 请求地址
     * @param array/object $params 参数数组/对象
     * @param array $urlParams url地址后面的参数数组 数据最终转换成-如:'?id='
     * @param string $type 请求类型 'GET'、'POST'、'PUT'、'PATCH'、'DELETE'
     * @param array $options 其它默认参数：如 ['headers' => ['Accept' => "application/vnd.myapp.v1+json"]]
     * @return array array 正常数据
     */
    public static function HttpRequestApi($url, $params = [], $urlParams = [], $type = 'POST', $options = [])
    {
        $result = self::sendHttpRequest($url, $params, $urlParams, $type, $options);

        $resultData = json_decode($result, true);
        $code = $resultData['code'] ?? 0;
        $msg = $resultData['msg'] ?? '返回数据错误!';
        $data = $resultData['data'] ?? [];
        if ($code == 0){
            throws($msg);
        }

        return $data;
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~签名~~~~~~~~相关的~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    /**
     * @brief 使用HMAC-SHA1算法生成oauth_signature签名值 二进制的base64
     *
     * @param $key 密钥
     * @param $str 源串
     *
     * @return 签名值
     */
    public static function getSignature($str, $key)
    {
        $signature = "";
        if (function_exists('hash_hmac')) {
            $signature = base64_encode(hash_hmac("sha1", $str, $key, true));
        } else {
            $blocksize = 64;
            $hashfunc = 'sha1';
            if (strlen($key) > $blocksize) {
                $key = pack('H*', $hashfunc($key));
            }
            $key = str_pad($key, $blocksize, chr(0x00));
            $ipad = str_repeat(chr(0x36), $blocksize);
            $opad = str_repeat(chr(0x5c), $blocksize);
            $hmac = pack(
                'H*', $hashfunc(
                    ($key ^ $opad) . pack(
                        'H*', $hashfunc(
                            ($key ^ $ipad) . $str
                        )
                    )
                )
            );
            $signature = base64_encode($hmac);
        }
        return $signature;
    }

    /**
     * 生成签名---基本方法
     *
     * @param string $signUbound 签名下标名称
     * @param array $params 需要请求的参数数组--注意含有appid   ;如果是生成时，注更新时间 timestamp = time();
     *
     *  [
     *      'appid'=>5288971,
     *      'menu'=>'客户服务列表',
     *      'lat'=>21.223,
     *      'lng'=>131.334,
     *      'timestamp'=>time()
     *  ]
     * @param string $appsecret 密匙
     * @param mixed $securePosition 签名拼接位置
     *                1 用下标加入排序---不在此 方法内处理，在params参数传入;
     *                2     指定下标key加入参数的最后 ;--如果下标已存在，则会先删除，再追加到最后
     *                下标名称  是下标名称 ----加入参数的最后 ;--如果下标已存在，则会先删除，再追加到最后
     *               3 直接拼接到参数字符最后台;
     *               4 直接拼接到参数字符最前台;
     *               5 直接拼接到参数字符最后台和最前面;
     * @param int $secureType 签名类型1 md5 ; 2 sha1 ; 3 hash_hmac-- md5;4 hash_hmac-- sha1;5 hash_hmac-- sha256
     * @param boolean $paramsurlsafe 加密前的字符-是否进行urldecode转换 true:转换;false:不转换[默认] -
     *                     - 如果是在准备调接口时，可能前面对参数urlencode,所以现在要urldecode还原数据：true;
     *                        如果是获得接口的[调用获得]，一般都已经自动处理过：false
     * @param boolean $base64 加密后的字符-是否进行base64转换 true:转换;false:不转换[默认]
     * @param boolean $urlsafe 加密后的字符-是否进行url传输转换 true:转换;false:不转换[默认] -
     *                     - 如果生成是为了调接口：true; 如果是为了验证：false
     * @param array $urlParams url地址后面的参数数组 数据最终转换成-如:'?id='
     * @return string  签名
     */
    public static function buildSign($signUbound = 'sign', $params = [], $appsecret = '', $securePosition = 1, $secureType = 1, $paramsurlsafe = FALSE, $base64 = FALSE, $urlsafe = FALSE)
    {
//        if (isset($params['sign'])) {
//            unset($params['sign']);
//        }
        if(strlen($signUbound) <= 0)  $signUbound = 'sign';
        if (strlen($signUbound) > 0 && isset($params[$signUbound])) {
            unset($params[$signUbound]);
        }

        // 加到最后，所以不参与到排序中，排完序后面再加上
        if($securePosition == 2) {
            if(isset($params['key'])) unset($params['key']);
        }
        if(!is_numeric($securePosition) && strlen($securePosition) > 0) {
            if(isset($params[$securePosition])) unset($params[$securePosition]);
        }
        // $params['timestamp'] = time();

        ksort($params);

        // $str .= '&key=' . $appsecret;
        if($securePosition == 2) {
            if(isset($params['key'])) unset($params['key']);
            $params['key'] = $appsecret;
        }
        if(!is_numeric($securePosition) && strlen($securePosition) > 0) {
            if(isset($params[$securePosition])) unset($params[$securePosition]);
            $params[$securePosition] = $appsecret;
        }

        // 注意：这里会自动urlencode操作，所以下面要解码urldecode过来
        $str = urldecode(http_build_query($params));
        // 如果是获得接口的[调用获得]，一般都已经自动处理过。
        // 如果是在准备调接口时，可能前面对参数urlencode,所以现在要urldecode还原数据
//        $paramsurlsafe && $str = urldecode($str);

        switch ($securePosition)
        {
            case 1:// 1 用下标加入排序---不在此 方法内处理，在params参数传入;
                break;
            case 2://  2 指定下标加入参数的最后 ;
                break;
            case 3:// 3 直接拼接到参数字符最后台;
                $str .= $appsecret;
                break;
            case 4://4 直接拼接到参数字符最前台;
                $str = $appsecret . $str;
                break;
            case 5:// 5 直接拼接到参数字符最后台和最前面;
                $str = $appsecret . $str . $appsecret;
                break;
            default:
                break;
        }

        // 签名类型1 md5 ; 2 sha1 ; 3 hash_hmac
        switch ($secureType)
        {
            case 1:// 1 md5 ;
                $str = md5($str);
                break;
            case 2://  2 sha1 ;
                $str = sha1($str);
                break;
            case 3:// 3 hash_hmac-- md5
                $str = hash_hmac("md5", $str, $appsecret, true);// 原始二进制数据
                break;
            case 4:// 4 hash_hmac-- sha1
                $str = hash_hmac("sha1", $str, $appsecret, true);// 原始二进制数据
                break;
            case 5:// 5 hash_hmac-- sha256
                $str = hash_hmac("sha256", $str, $appsecret, true);// 原始二进制数据
                break;
            default:
                $str = md5($str);
                break;
        }
        $base64 && $str = base64_encode($str);

        $urlsafe && $str = urlencode($str);
        return $str;
    }

    /**
     * 验证签名
     * @param array $otherParams 其它参数
     *    $otherParams = [
     *        'paramsurlsafe' => false, boolean 加密前的字符-是否进行urldecode转换 true:转换;false:不转换[默认] -
     *        'urlsafe' => false,boolean  加密后的字符-是否进行url传输转换 true:转换;false:不转换[默认] -
     *    ];
     * @param array $params 待验证数据
     * @param string $signUbound 签名下标名称
     * @param string $appsecret 密匙
     * @param mixed $securePosition 签名拼接位置
     *                1 用下标加入排序---不在此 方法内处理，在params参数传入;
     *                2     指定下标key加入参数的最后 ;--如果下标已存在，则会先删除，再追加到最后
     *                下标名称  是下标名称 ----加入参数的最后 ;--如果下标已存在，则会先删除，再追加到最后
     *               3 直接拼接到参数字符最后台;
     *               4 直接拼接到参数字符最前台;
     *               5 直接拼接到参数字符最后台和最前面;
     * @param int $secureType 签名类型1 md5 ; 2 sha1 ; 3 hash_hmac-- md5;4 hash_hmac-- sha1;5 hash_hmac-- sha256
     * @param boolean $base64 加密后的字符-是否进行base64转换 true:转换;false:不转换[默认]
     * @return boolean
     */
    public static function verifySign($otherParams = [],$params = [], $signUbound = 'sign', $appsecret = '', $securePosition = 1, $secureType = 1, $base64 = FALSE)
    {
        if(strlen($signUbound) <= 0)  $signUbound = 'sign';
        $sign = $params[$signUbound] ?? '';// $params['sign']

        $paramsurlsafe = $otherParams['paramsurlsafe'] ?? false;
        if(!is_bool($paramsurlsafe)) $paramsurlsafe = false;

        $urlsafe = $otherParams['urlsafe'] ?? false;
        if(!is_bool($urlsafe)) $urlsafe = false;
//        return $sign == static::buildSign($signUbound, $params, $appsecret, $securePosition, $secureType,  FALSE, $base64, FALSE);
        return $sign == static::buildSign($signUbound, $params, $appsecret, $securePosition, $secureType,  $paramsurlsafe, $base64, $urlsafe);
    }

    /**
     * 创建签名--生成用来提交/访问接口用
     * @param array $otherParams 其它参数
     *    $otherParams = [
     *        'timestamp' => 'timestamp',// 时间戳 下标,没有或值为空，则不需要
     *        'nonceStr' => 'noncestr',// （防重放） 下标,没有或值为空，则不需要
     *        'mix' => 0,// （防重放） 生成随机数的最小数 , 都小于0，则不生成随机数
     *        'max' => 1000,// （防重放） 生成随机数的最大数
     *        'secureTypeArr' => [],// （防重放）加密的参数，请看 createNonce方法中参数说明
     *        'paramsurlsafe' => false, boolean 加密前的字符-是否进行urldecode转换 true:转换;false:不转换[默认] -
     *        'urlsafe' => false,boolean  加密后的字符-是否进行url传输转换 true:转换;false:不转换[默认] -
     *    ];
     * @param array $params 待验证数据
     * @param string $signUbound 签名下标名称
     * @param string $appsecret 密匙
     * @param mixed $securePosition 签名拼接位置
     *                1 用下标加入排序---不在此 方法内处理，在params参数传入;
     *                2     指定下标key加入参数的最后 ;--如果下标已存在，则会先删除，再追加到最后
     *                下标名称  是下标名称 ----加入参数的最后 ;--如果下标已存在，则会先删除，再追加到最后
     *               3 直接拼接到参数字符最后台;
     *               4 直接拼接到参数字符最前台;
     *               5 直接拼接到参数字符最后台和最前面;
     * @param int $secureType 签名类型1 md5 ; 2 sha1 ; 3 hash_hmac-- md5;4 hash_hmac-- sha1;5 hash_hmac-- sha256
     * @param boolean $base64 加密后的字符-是否进行base64转换 true:转换;false:不转换[默认]
     * @return boolean
     */
    public static function createSign($otherParams = [], &$params = [], $signUbound = 'sign', $appsecret = '', $securePosition = 1, $secureType = 1, $base64 = FALSE)
    {
        $timestamp = $otherParams['timestamp'] ?? '';
        if(is_string($timestamp) && strlen($timestamp) > 0) $params[$timestamp] = time();// $params['timestamp'] = time();

        // url()->current();// 返回不带查询字符串(不带参数)的URL：http://laravel.demo/hello
        // url()->full();//包含查询字符串：http://laravel.demo/hello?kk=111

        $nonceStr = $otherParams['nonceStr'] ?? '';
        if(is_string($nonceStr) && strlen($nonceStr) > 0){
            $mix = $otherParams['mix'] ?? -1;
            if(!is_numeric($mix)) $mix = -1;

            $max = $otherParams['max'] ?? -1;
            if(!is_numeric($max)) $max = -1;

            $secureTypeArr = $otherParams['secureTypeArr'] ?? [];
            if(!is_array($secureTypeArr)) $secureTypeArr = [];
//            $secureTypeArr = [];//['md5' => []];

//            $params['nonceStr'] = static::createNonce('',0, 10000, $secureTypeArr);
            $params[$nonceStr] = static::createNonce('',$mix, $max, $secureTypeArr);
        }

        $paramsurlsafe = $otherParams['paramsurlsafe'] ?? false;
        if(!is_bool($paramsurlsafe)) $paramsurlsafe = false;

        $urlsafe = $otherParams['urlsafe'] ?? false;
        if(!is_bool($urlsafe)) $urlsafe = false;

//        return static::buildSign($signUbound, $params, $appsecret, $securePosition, $secureType,  true, $base64, true);
//        return static::buildSign($signUbound, $params, $appsecret, $securePosition, $secureType,  false, $base64, false);
        return static::buildSign($signUbound, $params, $appsecret, $securePosition, $secureType,  $paramsurlsafe, $base64, $urlsafe);
    }

    /**
     * 验证时间戳
     * @param int $timestamp
     * @param int $valid_time 有效时长(单位：秒) 默认：1分钟
     * @return boolean true:未超时/有效;  false:超时/过期
     */
    public static function isValidTimestamp($timestamp, $valid_time = 1 * 60)
    {
        return ($timestamp + $valid_time) >= time();// 函数返回自 Unix 纪元（January 1 1970 00:00:00 GMT）起的当前时间的秒数
    }

    /**
     * 验证随机数（防重放）
     * @param $nonce string
     * @param int $valid_time 有效时长(单位：秒) 默认：1分钟
     * @return boolean true:有效的；false:已经请求过的--重复提交
     */
    public static function isValidNonce($nonce = '', $valid_time = 1 * 60)
    {
        // 没有随机数
        if( (!is_string($nonce) && !is_numeric($nonce)) || strlen($nonce) <=0) return false;

        // $operate = 3;
        $redisKeyPre = Tool::getProjectKey(1, ':', ':') . 'nonceStr:' . $nonce . ':';

        // 返回当前页面的地址:http://a.com/platforms ---不带?后面的参数
        // 返回不带查询字符串(不带参数)的URL：http://laravel.demo/hello
        $currentUrl = URL::current();
        $redisKey =  md5($currentUrl);

        // 获得缓存值
//        $nonceStr = RedisString::getRedis($redisKeyPre . $redisKey, $operate);// Tool::getRedis($redisKeyPre . $redisKey, $operate);
//
//        // 已经存在缓存中
//        if( ( is_string($nonceStr)  || is_numeric($nonceStr) ) && strlen($nonceStr) > 0) return false;
        // 缓存中不存在，则缓存一小段时间
        if(!is_numeric($valid_time) || $valid_time <= 0 ) $valid_time = 60;// 1分钟

        $cacheVal = time();//1;// date('Y-m-d H:i:s');

        // 新建失败,则说明已存在
        if(!RedisString::setnxExpire($redisKeyPre . $redisKey, $valid_time, $cacheVal)) return false;
//        RedisString::setRedis($redisKeyPre, $redisKey, $cacheVal, $valid_time , $operate);// Tool::setRedis($redisKeyPre, $redisKey, $cacheVal, $valid_time , $operate);
        return true;
    }

    /**
     * 生成md5随机数（防重放）
     * @param string $nonce 重放字符，为空则为 time()
     * @param array 需要执行的加密操作,下标顺序代表执行顺序
     *   $secureTypeArr = [
     *        'md5' => [],// md5加密
     *        'sha1' => [],// sha1加密
     *       'hmac-md5' => ['key' => '', 'raw_output' => false],// sha1加密
     *       'hmac-sha1' => ['key' => '', 'raw_output' => false],// sha1加密
     *       'hmac-sha256' => ['key' => '', 'raw_output' => false],// sha1加密
     *   ];
     *   // 都可能会有的参数,下标顺序代表执行顺序
     *   [
     *        'operates' => ['base64','strtoupper','strtolower','urlencode', 'urldecode'],
     *   ];
     * @return string
     */
    public static function createNonce($nonce = '',$mix = 0, $max = 10000, $secureTypeArr = [])
    {
        return Tool::createNonce($nonce, $mix, $max, $secureTypeArr);
    }

    /**
     * 一分钟限制请求100次--注意只能针对单个用户限,（根据ip等限不现实,只能对具体某一用户来限）
     * @param string $key 键---单个用户的标识 token
     * @param int $limitSecond 多少秒请求的限制 -单位：秒
     * @param int $maxLimit 最多请求次数
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return boolean 结果 是否合法请求 true:未超过数量;  sting 具体错误 ； throws 错误   ----false:已超过数量
     */
//    public static function limit($key = '', $limitSecond = 60, $maxLimit = 100, $errDo = 1){
//        // $maxNum = $redis->incr($key);
//        // if($maxNum <= 1){
//        //    $redis->expire($key,60);
//        // }else if($maxNum > 100){
//        //    return false;
//        // }
//
//        // 返回当前页面的地址:http://a.com/platforms ---不带?后面的参数
//        // 返回不带查询字符串(不带参数)的URL：http://laravel.demo/hello
//        $currentUrl = URL::current() ?? '';
//        $redisKey =  md5($currentUrl . '-' . $key);
//
//        $maxNum = 1;

//        $maxNum = Tool::lockDoSomething('limit:' . $redisKey,
//            function()  use(&$key, &$limitSecond, &$maxNum){//
//                // redis中不存在，则加入。存在则自增
//                if(!RedisString::setnxExpire($key, $limitSecond, $maxNum)){
//                    $maxNum = RedisString::incr($key);
//                }
//                return $maxNum;
//        }, function($errDo){
//                // TODO
//                $errMsg = '获得字段失败，请稍后重试!';
//                if($errDo == 1) throws($errMsg);
//                return $errMsg;
//            }, true, $errDo, 2000, 2000);
//
//        if($maxNum > $maxLimit){
//            $errMsg = '请求次数超限!';
//            if($errDo == 1) throws($errMsg);
//            return $errMsg;
//            // return false;
//        }
//        return true;
//    }

}
