<?php


namespace App\Services\Wechat;


use App\Services\Request\API\HttpRequest;
use App\Services\Secure\AesDesCrypt;
use App\Services\Tool;
use Illuminate\Support\Facades\Log;

class BasicWechat
{
    public static $apiUrl = 'https://api.mch.weixin.qq.com';// 接口地址域名
    public static $certificateCacheSecond = 60 * 60 * 12;// 微信平台公钥缓存的时长，官方建议是：定期调用该接口，间隔时间小于12 小时 https://wechatpay-api.gitbook.io/wechatpay-api-v3/jie-kou-wen-dang/ping-tai-zheng-shu
    // 接口配置--样列，具体的会用数据来实现业务逻辑--测试、调试用
//    public static $apiConfig = [
//        'serial_no' => '56FC8****4D1A8',// 证书序列号
//        'mchid' => '16****235',// 商户号
//        'appid' => 'wx4138****4bcdac', // 公众号APPID
//        'api_secret' => '8IKzO****HNYHX',// API密钥
//        'apiv3_secret' => 'jlEKJZS****vTbCKmdaJyq',// APIv3密钥
//        'apiclient_key_path' => '/srv/www/cert/wechat/trm/apiclient_key.pem' ,// 商户API证书 私钥文件路径
//        'apiclient_cert_path' => '/srv/www/cert/wechat/trm/apiclient_cert.pem' ,// 商户API证书 公钥文件路径
//        'updated_at' => '2020-11-04 10:37:33',// 记录更新日期时间
//    ];

    // 数据格式
    // 使用 JSON 作为消息体的数据交换格式。请求须设置HTTP头部：
    // Content-Type: application/json
    // Accept: application/json
    // 图片上传API除外。

    // 参数兼容性
    // 当请求或应答中的JSON键值对的值为空（null）时，可以省略

    // 字符集
    // 微信支付API v3仅支持UTF-8字符编码的一个子集：使用一至三个字节编码的字符。也就是说，不支持Unicode辅助平面中的四至六字节编码的字符。

    // 日期格式  所有的日期对象，使用 ISO 8601 所定义的格式。
    // 示例 yyyy-MM-DDTHH:mm:ss.SSSZ
    // yyyy-MM-DDTHH:mm:ssZ
    // yyyy-MM-DDTHH:mm:ss.SSS+08:00
    // yyyy-MM-DDTHH:mm:ss+08:00

    // 请求的唯一标示
    // 微信支付给每个接收到的请求分配了一个唯一标示。请求的唯一标示包含在应答的HTTP头Request-ID中。当需要微信支付帮助时，请提供请求的唯一标示，以便我们更快的定位到具体的请求。

    // 错误信息
    // 使用HTTP状态码来表示请求处理的结果。
    // 处理成功的请求，如果有应答的消息体将返回200，若没有应答的消息体将返回204。
    // 已经被成功接受待处理的请求，将返回202。

    // 请求处理失败时，如缺少必要的入参、支付时余额不足，将会返回4xx范围内的错误码。
    // 请求处理时发生了微信支付侧的服务系统错误，将返回500/501/503的状态码。这种情况比较少见。

    // 错误码和错误提示
    // 当请求处理失败时，除了HTTP状态码表示错误之外，API将在消息体返回错误相应说明具体的错误原因。

    // 证书
    // 说明：证书必须与商户号相匹配且是有效的

    // 平台证书
    // 平台证书会周期性更换。商户应定时通过API下载新的证书。请参考我们的更新指引 ，不要依赖人工更新证书。

    // 声明所使用的证书
    // 某些情况下，将需要更新密钥对和证书。为了保证更换过程中不影响API的使用，请求和应答的HTTP头部中包括证书序列号 ，
    // 以声明签名或者加密所用的密钥对和证书。
    // 商户签名使用 商户私钥 ，证书序列号包含在请求HTTP头部的  Authorization的serial_no
    // 微信支付签名使用微信支付平台私钥，证书序列号包含在应答HTTP头部的Wechatpay-Serial
    // 商户上送敏感信息时使用微信支付平台公钥加密，证书序列号包含在请求HTTP头部的 Wechatpay-Serial

    // API v3密钥
    // 为了保证安全性，微信支付在 回调通知 和 平台证书下载接口 中，对关键信息进行了AES-256-GCM加密。API v3密钥是加密时使用的对称密钥。
    // APIv3密钥属于敏感信息，请妥善保管不要泄露，如果怀疑信息泄露，请重设密钥。


    // 签名
    // 微信支付API v3通过验证签名来保证请求的 真实性 和 数据的 完整性。

    // 请求签名
    // 商户需要使用自身的私钥对API URL、消息体等关键数据的组合进行SHA-256 with RSA签名。请求的签名信息通过HTTP头Authorization 传递，具体说明请见 签名生成指南。
    // 没有携带签名或者签名验证不通过的请求，都不会被执行，并返回401 Unauthorized 。

    // 应答签名
    // 对于签名验证成功的请求，微信支付API v3会使用微信支付的平台私钥对应答进行签名。签名的信息包含在HTTP头部中，具体说明请见 签名验证指南。

    // 请使用微信支付的公钥进行验签，它包含在微信支付平台证书中
    // 请对携带了签名的应答进行验签
    // 没有携带签名的成功应答（HTTP状态码为2xx），应认为是伪造或被篡改的应答

    // 签名验证
    // 商户可以按照下述步骤验证  应答  或者  回调  的签名 。
    // 如果验证商户的请求签名正确，微信支付会在应答的HTTP头部中包括应答签名。我们建议商户验证应答签名。
    // 同样的，微信支付会在回调的HTTP头部中包括回调报文的签名。商户必须 验证回调的签名，以确保回调是由微信支付发送。

    // 回调通知签名
    // 当调用商户的接口时，微信支付会使用微信支付的平台私钥对回调请求进行签名。签名的方法同应答签名的方式一致，商户必须使用微信支付公钥验证回调的签名。
    // 通知必须验证微信支付签名，避免被恶意攻击


    // 请求
          // HTTP头部
          // Content-Type: application/json   -- 图片上传API除外  Content-Type: application/json; charset=utf-8
          // Accept: application/json  -- 图片上传API除外

          // User-Agent
          // User Agent
          // HTTP协议要求发起请求的客户端在每一次请求中都使用HTTP头  User-Agent来标示自己。微信支付建议调用方选用以下两种方式的一种：
          // 1.使用HTTP客户端默认的 User-Agent。
          // 2.遵循HTTP协议，使用自身系统和应用的名称和版本等信息，组成自己独有的User-Agent。
          // 微信支付API v3很可能会拒绝处理无User-Agent 的请求。

          // 应答的语种
          // 微信支付API v3允许调用方声明应答中的错误描述使用的自然语言语种。如果有需要，设置请求的HTTP头Accept-Language。目前支持：
          // en  zh-CN  zh-HK  zh-TW
          // Accept-Language: zh-CN
          // 当不设置或者值不支持时，将使用简体中文（zh-CN）。

          // Authorization 请求的签名信息通过HTTP头Authorization 传递  没有携带签名或者签名验证不通过的请求，都不会被执行，并返回401 Unauthorized 。
          // Authorization的serial_no -- 商户签名使用  商户私钥  证书序列号
          // Wechatpay-Serial 可能有【敏感信息时有】 上送敏感信息时使用微信支付平台公钥加密，证书序列号包含在请求HTTP头部的 Wechatpay-Serial



    // 应答
          // 检查平台证书序列号
          // 微信支付的平台证书序列号位于HTTP头Wechatpay-Serial。验证签名前，请商户先检查序列号是否跟商户当前所持有的 微信支付平台证书的序列号一致。
          //          如果不一致，请重新获取证书。否则，签名的私钥和证书不匹配，将无法成功验证签名。

          // 构造验签名串
          // 首先，商户先从应答中获取以下信息
          // HTTP头Wechatpay-Timestamp 中的应答时间戳。
          // HTTP头Wechatpay-Nonce 中的应答随机串
          // 应答主体（response Body）
          // 然后，请按照以下规则构造应答的验签名串。签名串共有三行，行尾以\n 结束，包括最后一行。\n为换行符（ASCII编码值为0x0A）。
          // 若应答报文主体为空（如HTTP状态码为204 No Content），最后一行仅为一个\n换行符。

            // 应答时间戳\n
            // 应答随机串\n
            // 应答报文主体\n
            /**
             *
             *{
             *  "data": [
             *      {
             *          "serial_no": "5157F09EFDC096DE15EBE81A47057A7232F1B8E1",
             *          "effective_time": "2018-03-26T11:39:50+08:00",
             *          "expire_time": "2023-03-25T11:39:50+08:00",
             *          "encrypt_certificate": {
             *              "algorithm": "AEAD_AES_256_GCM",// 加密算法
             *              "nonce": "4de73afd28b6",// 加密使用的随机串初始化向量）
             *              "associated_data": "certificate",// 附加数据包（可能为空）
             *              "ciphertext": "..."// Base64编码后的密文
             *          }
             *      }
             *  ]
             *}
             *
             */
            // 获取应答签名
            // 微信支付的应答签名通过HTTP头Wechatpay-Signature传递。（注意，示例因为排版可能存在换行，实际数据应在一行）
            // 对 Wechat-Signature的字段值使用Base64进行解码，得到应答签名。
            // 某些代理服务器或CDN服务提供商，转发时会“过滤“微信支付扩展的HTTP头，导致应用层无法取到微信支付的签名信息。
            // 商户遇到这种情况时，我们建议尝试调整代理服务器配置，或者通过直连的方式访问微信支付的服务器和接收通知。

          // HTTP头
          // Request-ID  --唯一标示 当需要微信支付帮助时，请提供请求的唯一标示，以便我们更快的定位到具体的请求。Request-ID: e2762b10-b6b9-5108-a42c-16fe2422fc8a
          // Wechatpay-Serial 微信支付签名 证书序列号 微信支付平台私钥 Wechatpay-Serial: 5157F09EFDC096DE15EBE81A47057A7232F1B8E1
          // Wechatpay-Timestamp 中的应答时间戳。 Wechatpay-Timestamp: 1554209980
          // Wechatpay-Nonce HTTP头Wechatpay-Nonce 中的应答随机串 Wechatpay-Nonce: c5ac7061fccab6bf3e254dcf98995b8c
          // Wechatpay-Signature  应答签名 -- 使用Base64进行解码，得到应答签名。 Wechatpay-Signature: CtcbzwtQjN8rnOXItEBJ5...

          // Content-Language: zh-CN ---这个没有，只是看到有这个返回，暂时记录在此


          // 处理成功的请求，如果有应答的消息体将返回200，若没有应答的消息体将返回204。
          // 已经被成功接受待处理的请求，将返回202。
          // 请求处理失败时，如缺少必要的入参、支付时余额不足，将会返回4xx范围内的错误码。
          // 请求处理时发生了微信支付侧的服务系统错误，将返回500/501/503的状态码。这种情况比较少见。

          // HTTP状态码
            // 状态码	错误类型	一般的解决方案	典型错误码示例
            //* 200 - OK	处理成功	/	/
            //* 202 - Accepted	服务器已接受请求，但尚未处理	请使用原参数重复请求一遍	/
            //* 204 - No Content	处理成功，无返回Body	/	/
            //* 400 - Bad Request	协议或者参数非法	请根据接口返回的详细信息检查您的程序	PARAM_ERROR
            //* 401 - Unauthorized	签名验证失败	请检查签名参数和方法是否都符合签名算法要求	SIGN_ERROR
            //* 403 - Forbidden	权限异常	请开通商户号相关权限。请联系产品或商务申请	NO_AUTH
            //* 404 - Not Found	请求的资源不存在	请商户检查需要查询的id或者请求URL是否正确	ORDER_NOT_EXIST
            //* 429 - Too Many Requests	请求超过频率限制	请求未受理，请降低频率后重试	RATELIMIT_EXCEEDED
            //* 500 - Server Error	系统错误	按具体接口的错误指引进行重试	SYSTEM_ERROR
            //* 502 - Bad Gateway	服务下线，暂时不可用	请求无法处理，请稍后重试	SERVICE_UNAVAILABLE
            //* 503 - Service Unavailable	服务不可用，过载保护	请求无法处理，请稍后重试	SERVICE_UNAVAILABLE

          // code：详细错误码
          // message：错误描述，使用易理解的文字表示错误的原因。
          // field: 指示错误参数的位置。当错误参数位于请求body的JSON时，填写指向参数的JSON Pointer 。当错误参数位于请求的url或者querystring时，填写参数的变量名。
          // value:错误的值
          // issue:具体错误原因
        // {
        //  "code": "PARAM_ERROR",
        //  "message": "参数错误",
        //  "detail": {
        //    "field": "/amount/currency",
        //    "value": "XYZ",
        //    "issue": "Currency code is invalid",
        //    "location" :"body"
        //  }
        //}

        // 应答主体（response Body）

    /**
     *  作用：微信的日期时间格式转为正常的日期时间格式
     *  $dateTime  yyyy-MM-DDTHH:mm:ss.SSSZ  ； yyyy-MM-DDTHH:mm:ssZ  ； yyyy-MM-DDTHH:mm:ss.SSS+08:00 ； yyyy-MM-DDTHH:mm:ss+08:00
     *  可以返回 yyy-MM-DD HH:mm:ss 或  yyy-MM-DD  或 HH:mm:ss
     */
    public static function getFormatDateTime($dateTime){
        // $formatDateTime = '';
        // $dateTime = '2021-01-04T13:19:42+08:00';
        $pattern = '/^(\d){4,4}(-(\d){1,2}){2,2}/';
        $num = preg_match($pattern, $dateTime, $matches);
        $ymd = $matches[0] ?? '';
        $pattern = '/(\d){1,2}(:(\d){1,2}){2,2}/';
        $num = preg_match($pattern, $dateTime, $matches);
        $hms = $matches[0] ?? '';

        if(!empty($ymd) && !empty($hms)){
            return $ymd . ' ' . $hms;
        }else if(!empty($ymd)){
            return $ymd;
        }else if(!empty($hms)) {
            return $hms;
        }
        return '';

    }

    /**
     * 如何在程序中加载证书
     * Read certificate from file
     * 只能读公钥文件，不能读私钥文件
     * @param string    $filepath     PEM encoded X.509 certificate file path
     *
     * @return resource|bool  X.509 certificate resource identifier on success or FALSE on failure
     *  resource(18) of type (OpenSSL X.509)
     */
//    public static function getCertificate($filepath)
//    {
//        return openssl_x509_read(file_get_contents($filepath));
//    }

    /**
     *  作用：产生随机字符串，不长于32位
     */
    public static function createNoncestr( $length = 32 )
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    /**
     * 根据公、私钥文件路径，获得公、私钥内容---内容加密缓存到redis
     * 适合于缓存有文件的公、私钥
     *
     * @param string $apiclient_key_path 商户API证书 私钥文件路径 '/srv/www/cert/wechat/trm/apiclient_key.pem'
     * @param string $updated_at 记录更新日期时间 2020-11-04 10:37:28
     * @param string $apiv3_secret APIv3密钥
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @param boolean $isReadOrCache 引用传参 是否需要重新读取可能还会缓存 true:重新读取可能还会缓存；false:不用重新读取[有缓存数据]
     * @param boolean $isReJudgeCache  在获得数据前，是否再读一次缓存并判断是否有效[有效则直接返回，不从源数据读了]--防击穿，以及雪崩，穿透 true:重新读取可缓存；false:不用重新读取，强制重新读源数据
     * @return string 公、私钥内容  // -----BEGIN PRIVATE KEY-----MIIEvwIBADANBgkqhkiG9w0BAQEFAASCB-----END PRIVATE KEY-----
     * @author zouyan(305463219@qq.com)
     */
    public static function getFileKeyContent($apiclient_key_path, $updated_at, $apiv3_secret, &$isOpenCache = true, &$isReadOrCache = false, $isReJudgeCache = true){
        // $isOpenCache = true;
        // $isReadOrCache = false;
        // $isReJudgeCache = true;
        $operateRedis = 3;
        $pre = Tool::getProjectKey(1 | 2 | 4, ':', ':') ;
        $requestData = Tool::readAndCacheData(function(&$isOpenCache) use(&$apiclient_key_path, &$updated_at, &$apiv3_secret){
            $fileContent = file_get_contents($apiclient_key_path);
            // 加密
            $fileContent = AesDesCrypt::AESEncrypt($fileContent, $apiv3_secret);
            Log::info('微信支付日志 通过密钥文件路径重新获取密钥文件内容并加密-->' . __FUNCTION__, [$apiclient_key_path, $updated_at, $apiv3_secret, $fileContent]);
            return $fileContent;

        }, function(&$cacheData, &$isReadOrCache, &$isOpenCache){
            Log::info('微信支付日志 通过密钥文件路径--缓存中读取的数据-->' . __FUNCTION__, [$cacheData, $isReadOrCache, $isOpenCache]);
        }, function(&$readData, &$isOpenCache){
            // Log::info('微信支付日志 通过密钥文件路径 --缓存数据前，对数据进行格式化处理-->' . __FUNCTION__, [$readData, $isOpenCache]);

        }, $pre, 'wechatApi:FileKey:'. md5($operateRedis . $apiclient_key_path . $apiv3_secret . $updated_at),
            static::$certificateCacheSecond, $operateRedis, $isOpenCache, $isReadOrCache, $isReJudgeCache, function(){
                // Log::info('微信支付日志 通过密钥文件路径 --缓存数据后，可进行一些操作-->' . __FUNCTION__, []);
            });

        if($requestData === false || $requestData === null) throws('获取数据失败，请稍后重试!');
        // 解密
        $requestData = AesDesCrypt::AESDecrypt($requestData, $apiv3_secret);
        // return openssl_get_privatekey($requestData);
        return $requestData;
    }

    /**
     * 获得私钥内容对象
     *
     * @param string $apiclient_key_path 商户API证书 私钥文件路径 '/srv/www/cert/wechat/trm/apiclient_key.pem'
     * @param string $updated_at 记录更新日期时间 2020-11-04 10:37:28
     * @param string $apiv3_secret APIv3密钥
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @param boolean $isReadOrCache 引用传参 是否需要重新读取可能还会缓存 true:重新读取可能还会缓存；false:不用重新读取[有缓存数据]
     * @param boolean $isReJudgeCache  在获得数据前，是否再读一次缓存并判断是否有效[有效则直接返回，不从源数据读了]--防击穿，以及雪崩，穿透 true:重新读取可缓存；false:不用重新读取，强制重新读源数据
     * @return mixed 私钥内容对象 编号 resource(19) of type (OpenSSL key)
     * @author zouyan(305463219@qq.com)
     */
    public static function getPrivateKeyContentResource($apiclient_key_path, $updated_at, $apiv3_secret, &$isOpenCache = true, &$isReadOrCache = false, $isReJudgeCache = true){
        $fileContent = static::getFileKeyContent($apiclient_key_path, $updated_at, $apiv3_secret, $isOpenCache, $isReadOrCache, $isReJudgeCache);
        return openssl_get_privatekey($fileContent);
    }

    // 获得公钥内容对象，参数同 上面的 getPrivateKeyContentResource 方法
    public static function getPublicKeyContentResource($apiclient_key_path, $updated_at, $apiv3_secret, &$isOpenCache = true, &$isReadOrCache = false, $isReJudgeCache = true){
        $fileContent = static::getFileKeyContent($apiclient_key_path, $updated_at, $apiv3_secret, $isOpenCache, $isReadOrCache, $isReJudgeCache);
        return openssl_get_publickey($fileContent);
    }

    /**
     * 微信平台公钥证书内容缓存相关配置
     *
     * @param string $mchid 商户号
     * @param string $wechatpaySerial 微信支付的平台证书序列号位于HTTP头Wechatpay-Serial。验证签名前，请商户先检查序列号是否跟商户当前所持有的 微信支付平台证书的序列号一致。
     * @param int $fileType 证书类型 1 公钥[默认] ； 2 私钥
     * @return array 配置一维数组  ['缓存前缀' , '缓存键' , '缓存数据保存的类型']
     * @author zouyan(305463219@qq.com)
     */
    public static function getKeyCacheConfig($mchid, $wechatpaySerial, $fileType = 1){

        $pre = Tool::getProjectKey(1 | 2 | 4, ':', ':') ;
        $operateRedis = 3;
        $key = 'wechatApi:ContentKey:' . $mchid . ':' . $wechatpaySerial . ':' . $fileType;
        return [$pre, $key, $operateRedis];

    }

    /**
     * 微信平台公钥证书内容缓存到redis
     *
     * @param string $mchid 商户号
     * @param string $wechatpaySerial 微信支付的平台证书序列号位于HTTP头Wechatpay-Serial。验证签名前，请商户先检查序列号是否跟商户当前所持有的 微信支付平台证书的序列号一致。
     * @param string $fileContent 内容  // -----BEGIN PRIVATE KEY-----MIIEvwIBADANBgkqhkiG9w0BAQEFAASCB-----END PRIVATE KEY-----
     * @param string $apiv3_secret APIv3密钥
     * @param int $fileType 证书类型 1 公钥[默认] ； 2 私钥
     * @return boolean 加密后的内容:缓存成功； false:缓存失败
     * @author zouyan(305463219@qq.com)
     */
    public static function setKeyContentCache($mchid, $wechatpaySerial, $fileContent, $apiv3_secret, $fileType = 1){
        // 为空，则不处理
        if(empty($wechatpaySerial) || empty($fileContent)) return false;
        // $pre = Tool::getProjectKey(1 | 2 | 4, ':', ':') ;
        // $operateRedis = 3;
        list($pre, $key, $operateRedis) = static::getKeyCacheConfig($mchid, $wechatpaySerial, $fileType);
        // 加密
        $fileContent = AesDesCrypt::AESEncrypt($fileContent, $apiv3_secret);
        // 定期调用该接口，间隔时间小于12 小时
        Tool::setRedis($pre, $key,  $fileContent, static::$certificateCacheSecond, $operateRedis);
        Log::info('微信支付日志 台公钥证书内容缓存到redis-->' . __FUNCTION__, [$wechatpaySerial, $fileContent, $apiv3_secret]);
        return $fileContent;// true;
    }

    /**
     * 获得微信平台公钥证书内容
     *
     * @param string $mchid 商户号
     * @param string $wechatpaySerial 微信支付的平台证书序列号位于HTTP头Wechatpay-Serial。验证签名前，请商户先检查序列号是否跟商户当前所持有的 微信支付平台证书的序列号一致。
     * @param string $apiv3_secret APIv3密钥
     * @param int $fileType 证书类型 1 公钥[默认] ； 2 私钥
     * @return string false ：失败 或  证书的内容 ： // -----BEGIN PRIVATE KEY-----MIIEvwIBADANBgkqhkiG9w0BAQEFAASCB-----END PRIVATE KEY-----
     * @author zouyan(305463219@qq.com)
     */
    public static function getKeyContentCache($mchid, $wechatpaySerial, $apiv3_secret, $fileType = 1){
        // $pre = Tool::getProjectKey(1 | 2 | 4, ':', ':') ;
        // $operateRedis = 3;

        list($pre, $key, $operateRedis) = static::getKeyCacheConfig($mchid, $wechatpaySerial, $fileType);
        $fileContent = Tool::getRedis($pre . $key, $operateRedis);
        if(!is_string($fileContent) || is_bool($fileContent) || empty($fileContent) )  return false;
        Log::info('微信支付日志 获取平台公钥证书内容缓存内容-->' . __FUNCTION__, [$wechatpaySerial, $fileContent, $apiv3_secret]);
        // 解密
        $fileContent = AesDesCrypt::AESDecrypt($fileContent, $apiv3_secret);
        return $fileContent;
    }

    // 微信平台当前使用的证书序列号
    /**
     * 微信平台公钥证书内容缓存相关配置
     *
     * @param string $mchid 商户号
     * @return array 配置一维数组  ['缓存前缀' , '缓存键' , '缓存数据保存的类型']
     * @author zouyan(305463219@qq.com)
     */
    public static function getKeySerialCacheConfig($mchid){

        $pre = Tool::getProjectKey(1 | 2 | 4, ':', ':') ;
        $operateRedis = 1;
        $key = 'wechatApi:KeySerial:' . $mchid;
        return [$pre, $key, $operateRedis];
    }

    /**
     * 微信平台公钥证书序列号时间进行判断
     *
     * @param string $mchid 商户号
     * @return int 1:未生效 ；2已过期； 4 已生效; 8未知状态
     * @author zouyan(305463219@qq.com)
     */
    public static function judgeKeySerial($keySerial){
        // $returnStatus = 8;
        $effective_time = $keySerial['effective_time'] ?? '';// 生效时间 2021-01-04 13:19:42
        $expire_time = $keySerial['expire_time'] ?? '';// 过期时间 2026-01-03 13:19:42
        $dateTime = date('Y-m-d H:i:s');
        // 两个时间都不能为空
        $reslut = Tool::judgeBeginEndDate($effective_time, $expire_time,  1 | 2 | 256 , 2, $dateTime, '日期时间');

        // 出错 有效日期失效
        if($reslut !== true) return 8;

        // 未生效
        $diffTime = Tool::diffDate($effective_time, $dateTime, 2,  '生效时间', 2);
        if(!is_numeric($diffTime)) return 8;
        if($diffTime < 0) return 1;// 1:未生效

        // 已过期
        $diffTime = Tool::diffDate($dateTime, $expire_time, 2,  '过期时间', 2);
        if(!is_numeric($diffTime)) return 8;
        if($diffTime < 0) return 2;// 2已过期

        return 4 ;// 已生效;
    }

    /**
     * 微信平台公钥证书序列号缓存到redis
     *
     * @param string $mchid 商户号
     * @param array $keySerial 缓存的证书序列号信息数组
     *   $keySerial = [
     *      'effective_time' => $effective_time,// 生效时间 2021-01-04 13:19:42
     *      'expire_time' => $expire_time,// 过期时间 2026-01-03 13:19:42
     *      'serial_no' => $serial_no, // 证书序列号 35B4105DBFB51A3845213F8FF5F79413A6E48304
     *  ];
     * @return mixed 数组:缓存成功； false:缓存失败
     * @author zouyan(305463219@qq.com)
     */
    public static function setKeySerialCache($mchid, $keySerial = []){
        // 为空，则不处理
        if(empty($mchid) || empty($keySerial) || static::judgeKeySerial($keySerial) != 4) return false;
        // $pre = Tool::getProjectKey(1 | 2 | 4, ':', ':') ;
        // $operateRedis = 3;
        list($pre, $key, $operateRedis) = static::getKeySerialCacheConfig($mchid);
        // 定期调用该接口，间隔时间小于12 小时
        Tool::setRedis($pre, $key,  $keySerial, static::$certificateCacheSecond, $operateRedis);
        Log::info('微信支付日志 平台公钥证书号缓存到redis-->' . __FUNCTION__, [$mchid, $keySerial]);
        return $keySerial;
    }

    /**
     * 获得微信平台公钥证书序列号
     *
     * @param string $mchid 商户号
     * @return mixed false ：失败 或  数组：证书序列号相关数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getKeySerialCache($mchid){
        // $pre = Tool::getProjectKey(1 | 2 | 4, ':', ':') ;
        // $operateRedis = 3;

        list($pre, $key, $operateRedis) = static::getKeySerialCacheConfig($mchid);
        $keySerial = Tool::getRedis($pre . $key, $operateRedis);
        Log::info('微信支付日志 获取平台公钥证书序列号缓存内容-->' . __FUNCTION__, [$mchid, $keySerial]);
        if(!is_array($keySerial) || is_bool($keySerial) || empty($keySerial)  || static::judgeKeySerial($keySerial) != 4)  return false;
        return $keySerial;
    }

    /**
     * 微信平台公钥证书序列号缓存到redis--缓存有效的，比较最新生效的
     *
     * @param string $mchid 商户号
     * @param array $keySerial 缓存的证书序列号信息数组
     *   $keySerial = [
     *      'effective_time' => $effective_time,// 生效时间 2021-01-04 13:19:42
     *      'expire_time' => $expire_time,// 过期时间 2026-01-03 13:19:42
     *      'serial_no' => $serial_no, // 证书序列号 35B4105DBFB51A3845213F8FF5F79413A6E48304
     *  ];
     * @return mixed 数组:缓存成功或源缓存的数据； false:缓存失败
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceKeySerialCache($mchid, $keySerial = []){
        // 不是数组或空数组或未生效
        if(!is_array($keySerial) || empty($keySerial)  || static::judgeKeySerial($keySerial) != 4  )  return false;
        // 获得当前缓存的
        $cacheSerial = static::getKeySerialCache($mchid);
        // 没有缓存数据 或 非生效状态，重新缓存
        if(!is_array($cacheSerial) || is_bool($cacheSerial) || empty($cacheSerial)  || static::judgeKeySerial($cacheSerial) != 4 ){
            return static::setKeySerialCache($mchid, $keySerial);
        }
        // 有缓存数据，则与当前缓存的比较最新生效的
        $effective_time = $keySerial['effective_time'] ?? '';
        $expire_time = $keySerial['expire_time'] ?? '';
        $cache_effective_time = $cacheSerial['effective_time'] ?? '';
        $cache_expire_time = $cacheSerial['expire_time'] ?? '';
        $diffTime = Tool::diffDate($cache_effective_time, $effective_time, 2,  '过期时间', 2);
        if(!is_numeric($diffTime)) return false;
        if($diffTime >= 0){
            return static::setKeySerialCache($mchid, $keySerial);
        }
        return $cacheSerial;
    }

    /**
     * 生成v3 Authorization
     *
     * @param array $apiConfig  接口配置
     *  [
     *      'serial_no' => '56FC8CBC20C13345482454C25ACF531930F4D1A8',// 证书序列号
     *      'mchid' => '1605358235',// 商户号
     *      'appid' => 'wx41383f8b3f4bcdac', // 公众号APPID
     *      'api_secret' => '8IKzOOJJI81anzF4cXafxlHUzFDHNYHX',// API密钥
     *      'apiv3_secret' => 'jlEKJZSJBXfBE00vSQq2qvTbCKmdaJyq',// APIv3密钥
     *      'apiclient_key_path' => '/srv/www/cert/wechat/trm/apiclient_key.pem' ,// 商户API证书 私钥文件路径
     *      'updated_at' => '2020-11-04 10:37:28',// 记录更新日期时间
     *  ]
     * @param string $url 请求的绝对URL，并去除域名部分得到参与签名的URL。如果请求中有查询参数，URL末尾应附加有'?'和对应的查询字符串。 /v3/certificates
     * @param mixed $params 获取请求中的请求报文主体（request body）；如果是数组--方法内自动转为json
     *         请求方法为GET时，报文主体为空。
     *         当请求方法为POST或PUT时，请使用真实发送的JSON报文。
     *         图片上传API，请使用meta对应的JSON报文。
     *         对于下载证书的接口来说，请求报文主体是一个空串。
     * @param string $method 获取HTTP请求的方法（ GET,POST，PUT等
     * @param string $extendParams 其它扩展参数数组 一维数组
     *   $extendParams = [
     *      'Accept-Language' => 'zh-CN',// 应答中的错误描述使用的自然语言语种。如果有需要，设置请求的HTTP头Accept-Language。目前支持：  en  zh-CN  zh-HK  zh-TW
     *      'Wechatpay-Serial' => 'aaaa',// 可能有【敏感信息时有】 上送敏感信息时使用微信支付平台公钥加密，证书序列号包含在请求HTTP头部的 Wechatpay-Serial
     *  ];
     * @return array 请求的 hearder 数组
     * @author zouyan(305463219@qq.com)
     */
    public static function createAuthorization($apiConfig = [], $url = '', $params = '', $method = 'GET', $extendParams = []){
        if (!in_array('sha256WithRSAEncryption', openssl_get_md_methods(true))) {
            // throw new \RuntimeException("当前PHP环境不支持SHA256withRSA");
            throws("当前PHP环境不支持SHA256withRSA");
        }

        $url_parts = parse_url($url);
        $canonical_url = ($url_parts['path'] . (!empty($url_parts['query']) ? "?${url_parts['query']}" : ""));

        //私钥地址
        $mch_private_key = $apiConfig['apiclient_key_path'];// $this->mch_private_key;
        //商户号
        $merchant_id = $apiConfig['mchid'];// $this->mch_id;
        // 证书序列号
        $serial_no = $apiConfig['serial_no'];// 证书序列号
        //当前时间戳
        $timestamp =  time();
        //随机字符串
        $nonce = static::createNoncestr();
        //POST请求时 需要 转JSON字符串
        if(in_array($method, ['GET'])) $params = '';// 请求方法为GET时，报文主体为空。
        if(is_array($params)) $params = json_encode($params);// 如果是数组--方法内自动转为json

        $body =  $params;// $this->body ;
        $message = "{$method}\n".
            $canonical_url."\n".
            $timestamp."\n".
            $nonce."\n".
            $body."\n";

        //生成签名
        // openssl_sign($message, $raw_sign, openssl_get_privatekey(file_get_contents($mch_private_key)), 'sha256WithRSAEncryption');
        openssl_sign($message, $raw_sign, static::getPrivateKeyContentResource($apiConfig['apiclient_key_path'], $apiConfig['updated_at'], $apiConfig['apiv3_secret']), 'sha256WithRSAEncryption');
        $sign = base64_encode($raw_sign);
        //Authorization 类型
        $schema = 'WECHATPAY2-SHA256-RSA2048';
        //生成token
        $token = sprintf('mchid="%s",serial_no="%s",nonce_str="%s",timestamp="%d",signature="%s"', $merchant_id, $serial_no, $nonce, $timestamp, $sign);

//        $header = [
//            'Content-Type:application/json',
//            'Accept:application/json',
//            'User-Agent:*/*',
//            'Authorization: '.  $schema . ' ' . $token
//        ];
        $header = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => '*/*',
            'Authorization' => ' '.  $schema . ' ' . $token
        ];
        if(!empty($extendParams)) $header = array_merge($header, $extendParams);
        return $header;
    }

    // *******************************************
    // 获取平台证书及平台证书序列号，加密解密，个别V3接口需要用到。
    // 官方地址：https://wechatpay-api.gitbook.io/wechatpay-api-v3/qian-ming-zhi-nan-1/wei-xin-zhi-fu-ping-tai-zheng-shu-geng-xin-zhi-yin
    /**
     * 支付及服务 - 服务人员注册
     * 1. 获取平台证书序列号  serial_no与 商户支付证书不是一个
     * 2. 解密平台证书，拿到平台证书信息
     * 3. 加密请求参数时 需要用户 平台证书进行加密
     */

//    public function regguide($post ,$serial_no){
//
//        $url = static::$apiUrl . "/v3/smartguide/guides"; // https://api.mch.weixin.qq.com
//
//        $this->setBody( $post );
//        //生成V3请求 header认证信息
//        $header = static::createAuthorization( $url , 'POST' );
//
//        //增加平台证书序列号 ， 平台证书序列号方法 getCertificates()
//        $header[] = 'Wechatpay-Serial:' . $serial_no;
//        $data = $this->postXmlCurl(json_encode($post , JSON_UNESCAPED_UNICODE) ,  $url  , 30 , $header );
//        return json_decode($data , true);
//
//    }

    /**
     * 获取平台证书， 与商户证书不是一个内容
     */
    /**
     *  获取平台证书及平台证书序列号，加密解密，个别V3接口需要用到。
     *
     * @param array $apiConfig  接口配置
     *  [
     *      'serial_no' => '56FC8CBC20C13345482454C25ACF531930F4D1A8',// 证书序列号
     *      'mchid' => '1605358235',// 商户号
     *      'appid' => 'wx41383f8b3f4bcdac', // 公众号APPID
     *      'api_secret' => '8IKzOOJJI81anzF4cXafxlHUzFDHNYHX',// API密钥
     *      'apiv3_secret' => 'jlEKJZSJBXfBE00vSQq2qvTbCKmdaJyq',// APIv3密钥
     *      'apiclient_key_path' => '/srv/www/cert/wechat/trm/apiclient_key.pem' ,// 商户API证书 私钥文件路径
     *      'updated_at' => '2020-11-04 10:37:28',// 记录更新日期时间
     *  ]
     * @param string $requestWechatpaySerial 微信平台证书号 ；引用传值；可为空; 如果为空，则会优先取缓存中的，如果缓存没有或过期，则会调用平台证书列表自动获取并缓存
     * @param mixed $params 获取请求中的请求报文主体（request body）；如果是数组--方法内自动转为json
     *         请求方法为GET时，报文主体为空。
     *         当请求方法为POST或PUT时，请使用真实发送的JSON报文。--可用数组，会自动转为json
     *         图片上传API，请使用meta对应的JSON报文。
     *         对于下载证书的接口来说，请求报文主体是一个空串。
     * @param string $extendParams 其它扩展参数数组 一维数组
     *   $extendParams = [
     *      'Accept-Language' => 'zh-CN',// 应答中的错误描述使用的自然语言语种。如果有需要，设置请求的HTTP头Accept-Language。目前支持：  en  zh-CN  zh-HK  zh-TW
     *      'Wechatpay-Serial' => 'aaaa',// 可能有【敏感信息时有】 上送敏感信息时使用微信支付平台公钥加密，证书序列号包含在请求HTTP头部的 Wechatpay-Serial
     *  ];
     * @return array 请看方法中的 $body 的格式
     * @author zouyan(305463219@qq.com)
     */
    public static function getCertificates($apiConfig = [], &$requestWechatpaySerial = '', $params = '', $extendParams = []){

        $url = static::$apiUrl . "/v3/certificates";// https://api.mch.weixin.qq.com
        // $params = '';// [];
        /**
         * @param mixed $params 获取请求中的请求报文主体（request body）；如果是数组--方法内自动转为json
         *         请求方法为GET时，报文主体为空。
         *         当请求方法为POST或PUT时，请使用真实发送的JSON报文。--可用数组，会自动转为json
         *         图片上传API，请使用meta对应的JSON报文。
         *         对于下载证书的接口来说，请求报文主体是一个空串。
         *
         *
         */
        // 请求接口
        $extendParams['User-Agent'] = 'https://zh.wikipedia.org/wiki/User_agent';
        $apiResult = static::apiUrlRequest($apiConfig, $url, $params, 'GET', $extendParams);

        // 处理获得的公钥信息
        $responseContent = $apiResult['response_content'];// 返回的结果数据
        // 如果是json,则转为数组
        if (!isNotJson($responseContent)) $responseContent = json_decode($responseContent, true);

        // $body = '{
        //             "data":
        //                   [
        //                      {
        //                         "effective_time":"2021-01-04T13:19:42+08:00",// 有效时间
        //                         "encrypt_certificate":
        //                                       {
        //                                           "algorithm":"AEAD_AES_256_GCM",
        //                                           "associated_data":"certificate",// 附加数据包（可能为空）
        //                                           "ciphertext":"2Xi/y33Vyge0nks5oOLqVxcQpkufyUVhf7...xCtQ==",// Base64编码后的密文
        //                                           "nonce":"25e98ee1553c"// 加密使用的随机串初始化向量）
        //                                        },
        //                          "expire_time":"2026-01-03T13:19:42+08:00",// 过期时间
        //                          "serial_no":"35B4105DBFB51A3845213F8FF5F79413A6E48304"// 序列号
        //                          "aes_encrypt_certificate" // 公钥aes 加密后的数据---下面业务逻辑加上的
        //                          "aes_encrypt_key" // 公钥aes 加密的键--默认为apiv3_secret---下面业务逻辑加上的
        //                       }
        //                   ]
        //          }';

        // 先拿到当前的数据，好去验签
        $responseHeaders = HttpRequest::getHearderArr($apiResult['header']);
        $WechatpaySerial = $responseHeaders['Wechatpay-Serial'] ?? '';
        $apiv3_secret = $apiConfig['apiv3_secret'];
        // 根据证书接口/v3/certificates 返的数据，获取指定的证书序列号的证书内容
        $publicKeyContent = static::getCertificateContent($responseContent, $WechatpaySerial, $apiv3_secret);
//        foreach($responseContent['data'] as &$v){
//            $effective_time = $v['effective_time'];// 2021-01-04T13:19:42+08:00
//            $encrypt_certificate = $v['encrypt_certificate'];
//            $expire_time = $v['expire_time'];// 2026-01-03T13:19:42+08:00
//            $serial_no = $v['serial_no'];// 35B4105DBFB51A3845213F8FF5F79413A6E48304
//            if($WechatpaySerial != $serial_no) continue;
//            $publicKeyContent = static::decryptToString($apiConfig['apiv3_secret'] ,$encrypt_certificate['associated_data'], $encrypt_certificate['nonce'], $encrypt_certificate['ciphertext']);
//            break;
//            // 缓存数据
//            // $encryptPublicKey = static::setKeyContentCache($serial_no, $publicKeyContent, $apiConfig['apiv3_secret']);
//            // if($encryptPublicKey === false) throws('缓存平台公钥【'. $serial_no . '】失败');
//            // $v['aes_encrypt_certificate'] = $encryptPublicKey;
//            // $v['aes_encrypt_key'] = $apiConfig['apiv3_secret'];
//        }
        if(empty($publicKeyContent)) throws('获取平台证书及平台证书序列号，没有证书序列号对应的公钥');

        // 签名验证--接口返回的结果
        $certificates = [];// 如果验签时，有新的平台证书，则通过此参数传出，其它业务逻辑则可以使用【如存储】--格式请看 getCertificates 方法返回格式
        static::verifyResponse($apiConfig, $apiResult, $certificates, $extendParams, $publicKeyContent);

        // $responseContent = $apiResult['response_content'];// 返回的结果数据
        // 如果是json,则转为数组
        // if (!isNotJson($responseContent)) $responseContent = json_decode($responseContent, true);

        // 验签成功后，才能缓存数据
        foreach($responseContent['data'] as &$v){
            $effective_time = $v['effective_time'];// 2021-01-04T13:19:42+08:00// 生效时间
            $encrypt_certificate = $v['encrypt_certificate'];
            $expire_time = $v['expire_time'];// 2026-01-03T13:19:42+08:00// 过期时间
            $serial_no = $v['serial_no'];// 35B4105DBFB51A3845213F8FF5F79413A6E48304
            $publicKeyContent = static::decryptToString($apiConfig['apiv3_secret'] ,$encrypt_certificate['associated_data'], $encrypt_certificate['nonce'], $encrypt_certificate['ciphertext']);
            // 缓存数据
            $encryptPublicKey = static::setKeyContentCache($apiConfig['mchid'], $serial_no, $publicKeyContent, $apiConfig['apiv3_secret'], 1);
            if($encryptPublicKey === false) throws('缓存平台公钥【'. $serial_no . '】失败');
            // 微信平台公钥证书序列号缓存到redis--缓存有效的，比较最新生效的
            static::replaceKeySerialCache($apiConfig['mchid'], ['effective_time' => static::getFormatDateTime($effective_time),'expire_time' => static::getFormatDateTime($expire_time),'serial_no' => $serial_no]);
            $v['aes_encrypt_certificate'] = $encryptPublicKey;
            $v['aes_encrypt_key'] = $apiConfig['apiv3_secret'];
        }
        if(empty($requestWechatpaySerial)){
            // 获得当前缓存的
            $cacheSerial = static::getKeySerialCache($apiConfig['mchid']);
            if(is_array($cacheSerial) && !empty($cacheSerial) && static::judgeKeySerial($cacheSerial) == 4) $requestWechatpaySerial = $cacheSerial['serial_no'] ?? '';
            // 没有缓存数据 或 非生效状态，重新缓存
            //if(!is_array($cacheSerial) || is_bool($cacheSerial) || empty($cacheSerial)  || static::judgeKeySerial($cacheSerial) != 4 ){
            //   $requestWechatpaySerial = $WechatpaySerial;
            //}
            if(empty($requestWechatpaySerial)){
                $requestWechatpaySerial = $WechatpaySerial;// 还为空，则用微信回应此方法的
            }
        }
        return $responseContent;

    }

    // 根据证书接口/v3/certificates 返的数据，获取指定的证书序列号的证书内容
    public static function getCertificateContent($certificateData, $WechatpaySerial, $apiv3_secret){

        $publicKeyContent = '';
        foreach($certificateData['data'] as &$v){
            $effective_time = $v['effective_time'];// 2021-01-04T13:19:42+08:00
            $encrypt_certificate = $v['encrypt_certificate'];
            $expire_time = $v['expire_time'];// 2026-01-03T13:19:42+08:00
            $serial_no = $v['serial_no'];// 35B4105DBFB51A3845213F8FF5F79413A6E48304
            if($WechatpaySerial != $serial_no) continue;
            $publicKeyContent = static::decryptToString($apiv3_secret ,$encrypt_certificate['associated_data'], $encrypt_certificate['nonce'], $encrypt_certificate['ciphertext']);
            break;
            // 缓存数据
            // $encryptPublicKey = static::setKeyContentCache($serial_no, $publicKeyContent, $apiConfig['apiv3_secret']);
            // if($encryptPublicKey === false) throws('缓存平台公钥【'. $serial_no . '】失败');
            // $v['aes_encrypt_certificate'] = $encryptPublicKey;
            // $v['aes_encrypt_key'] = $apiConfig['apiv3_secret'];
        }
        return $publicKeyContent;
    }

//    public static function aaa($apiConfig){
//        $hearders = [
//            'Request-ID' => '08F3A6F38006101718A7B8B74C20F62528D7BF03-0', // 微信支付给每个接收到的请求分配了一个唯一标示。请求的唯一标示包含在应答的HTTP头Request-ID中。当需要微信支付帮助时，请提供请求的唯一标示，以便我们更快的定位到具体的请求。
//            'Wechatpay-Nonce' => '41ce8e57ec95f463e903e6423575ea3a', // HTTP头Wechatpay-Nonce 中的应答随机串
//            // 微信支付的应答签名
//            'Wechatpay-Signature' => 'fQPj2vjG7jRDAgvJ1/oqpAZIfVcB+RE2Xki/254ZwaauR7fBOhHSFJpOfSiS3nckSupxY3MGD015U8V7z/GB1svEqVIkUWA8Iz01tJnkUArn17WdfiNpcUWNfnRfE99Z9Cz37pv56NalFSw38FqlSGKHnOCDiwbnWu2OHJwWxv99iirrdzemfJT6h+XORykMMCEt9xY4JJBJY7jEENP+U+k/raVlSVIDhe4R9XBxv+yrkVxF90TTBdMniQcZfCdOj7oN4LaaVktbmntyfvDQtlv5KIKk7V+8umbBqNkH7+JnJypQsxeNcFDPiTZ8y6Y2Mhgr8CxEBN9uAhRT7sdWYA==', // cccc
//            'Wechatpay-Timestamp' => '1612501875', // HTTP头Wechatpay-Timestamp 中的应答时间戳。
//            'Wechatpay-Serial' => '35B4105DBFB51A3845213F8FF5F79413A6E48304', // 微信支付签名 证书序列号 微信支付平台私钥
//        ];
//
//        $WechatpaySignature = $hearders['Wechatpay-Signature'];
//
//        $effective_time = '2021-01-04T13:19:42+08:00';
//        $expire_time = '2026-01-03T13:19:42+08:00';
//        $serial_no = '"35B4105DBFB51A3845213F8FF5F79413A6E48304"';
//
//        $ciphertext = '2Xi/y33Vyge0nks5oOLqVxcQpkufyUVhf7cCTGGFewKjQW36qNq9W+jmPKo3OGKhDag85l+Uvrmix0jS6eT3gYB2Qh5+o6DFOVpqF5VjQSsmjit/VD15VGlAkNDFexPgAhYB5Aj13chghyJxoWvmJ/0NLCK9RKfnuBj53JjY0f6I+05suFmUu6CA7gTmLYusn0jFwukBaE32UabrrgKD7nu/SZzULDhFOf/weJBgePHunbRLEaWUo40k2HNdE20BJ3s4FNlX0DYaMVFj2ZEUs45/fM4G3ZHXXAOjG7T3U2oMlsc9EYYphAVVoRfogXrpflzaP2KH60EzVuMxVo7+KcTsSyFzeoTmtbFKpwYvN2Ojf4r38HHgCbk91mFXvPJrIABFwNMhO5G8NcXeVibvKvNPD/yS7Q0bkW0OMn8SWxtG3Xd3nwZvt8MIpD34UdfG52e7pHauqZzKslLi/EKWeaVeq6igA/iZE4g+wpIZYtSevkHQKzhC1Gcf0pYm1qjIrml0kL/K09Kc1eR/cVCsPIVR/XIyq6q7CSpD8bL990nEv5MHs5YQlckeZ5g19HaLg69JW/CDyio4YrZ3nrsNujEbBe+xa9n6qkCLwjSlTZSpQvMIukoFiNkPHrfkYqa/6ugzt1UyruwNOcYq95P4oIbiK7b1FvF9qHqHl1Sw3rBl7Ox8XVJlMJqYFjGC1WYqebuSduNqrLuIEQOmLE10zqdWDWYG65ze5BhbsrmTmT9RbfBMFFts2WrigboMjIe0Cov874gOnDwEZrPdgpXZWgiykUla8FffOgeZsbTgB0hOUS0LPbwQ47bipFz/wDxngh1Xzqw5TEqJeSLz0dZFG1IU5dZkZ49/Tzwk9UWwnceKmzgCCrNkgfa77xjQT7jPez0Z787gf4gz5RMPFdtQ/mfq7dc9NzFy3okSBSp0YKuVQ9vhCq7s7lhTPvuj+C/VPbIojLGK1Khufg9tO9FKP7gPXmmTEauCPC8y3NmRlVANK943OKH0O6Br82h9+7TR93suhfzhsdik/btiPHY/ZQRN5sgV4S/Af2cKjAKkleo0tOeTf/BiChvj2sYaHnO38zw7eGXhJkIw7mOsa2pl9W24LD6sEfSxVK8x8mJNfQPct3zT4gZNNMEx3MBOqzHtqThw9ogTUhGS/5gFB3jyGKY4Sx/d1EVYRaGR/rORP5Yr6xFA0Qk8oyskvpMykXalupFkXmrMrY+zM2svQzr23qMGWkxI5DJkYuy22aESpCItULOqrk74p8UdKwj1LBS6nL/c+KCbHWnfu0gU3dGEVl+mckyJ+7Qnxd4C7Kx17m+ANR7M2b3Yb4i6h6Xy6ayrCy3EFyVlWcxIcilmDQ+fcLS+DHKNffIuiqEQ+wbS98MWnoQL+QWevn7EIAA8UhWNkyPxDnI9uOlZFQKAj3ZqhqvPcFzzrQVijzu0s7Op+FsvvWJ1Xv8e4YlB7vNMGhCNxjeAW9pXtKvQknoDaF3uaV9ozUQhNUJLuYhypzFt99pbWSNmv5Qp3Y+k63knUIH+TyCPofKZYUmwzN6D+M166wiLxhxmPYND1bH4vGzbt5JMzh41FRrClTN5LCq4EilNplMzoKcO/mTxJoLeTO2fxiPJxk/VbAuVvzZQ2jmVoMyn00xFidCUKT3NhZy6qCcziWqkPeDtkFaiDe3i4EDfBw70g7ymNVoT7VLgC9s7mSU7sg97JcH2k4o6W0PUCiX7s2Y/iLsd6BHWkI05zezzbbHFUDc6Yu+WnOqPEJf/ULi8ZI9AFK2HUH7Wc79CCn/7VL7M7N1X/B66q7nMew+FS7njHZN+g4UYsfS0fAk2X+7YsmdqMJlYKDkIjZtsbtshG0CGPVkjzlvWxrcKET4gAkvAiCxCtQ==';
//        $algorithm = 'AEAD_AES_256_GCM';
//        $associated_data = 'certificate';
//        $nonce = '25e98ee1553c';
//        $body = '{"data":[{"effective_time":"2021-01-04T13:19:42+08:00","encrypt_certificate":{"algorithm":"AEAD_AES_256_GCM","associated_data":"certificate","ciphertext":"2Xi/y33Vyge0nks5oOLqVxcQpkufyUVhf7cCTGGFewKjQW36qNq9W+jmPKo3OGKhDag85l+Uvrmix0jS6eT3gYB2Qh5+o6DFOVpqF5VjQSsmjit/VD15VGlAkNDFexPgAhYB5Aj13chghyJxoWvmJ/0NLCK9RKfnuBj53JjY0f6I+05suFmUu6CA7gTmLYusn0jFwukBaE32UabrrgKD7nu/SZzULDhFOf/weJBgePHunbRLEaWUo40k2HNdE20BJ3s4FNlX0DYaMVFj2ZEUs45/fM4G3ZHXXAOjG7T3U2oMlsc9EYYphAVVoRfogXrpflzaP2KH60EzVuMxVo7+KcTsSyFzeoTmtbFKpwYvN2Ojf4r38HHgCbk91mFXvPJrIABFwNMhO5G8NcXeVibvKvNPD/yS7Q0bkW0OMn8SWxtG3Xd3nwZvt8MIpD34UdfG52e7pHauqZzKslLi/EKWeaVeq6igA/iZE4g+wpIZYtSevkHQKzhC1Gcf0pYm1qjIrml0kL/K09Kc1eR/cVCsPIVR/XIyq6q7CSpD8bL990nEv5MHs5YQlckeZ5g19HaLg69JW/CDyio4YrZ3nrsNujEbBe+xa9n6qkCLwjSlTZSpQvMIukoFiNkPHrfkYqa/6ugzt1UyruwNOcYq95P4oIbiK7b1FvF9qHqHl1Sw3rBl7Ox8XVJlMJqYFjGC1WYqebuSduNqrLuIEQOmLE10zqdWDWYG65ze5BhbsrmTmT9RbfBMFFts2WrigboMjIe0Cov874gOnDwEZrPdgpXZWgiykUla8FffOgeZsbTgB0hOUS0LPbwQ47bipFz/wDxngh1Xzqw5TEqJeSLz0dZFG1IU5dZkZ49/Tzwk9UWwnceKmzgCCrNkgfa77xjQT7jPez0Z787gf4gz5RMPFdtQ/mfq7dc9NzFy3okSBSp0YKuVQ9vhCq7s7lhTPvuj+C/VPbIojLGK1Khufg9tO9FKP7gPXmmTEauCPC8y3NmRlVANK943OKH0O6Br82h9+7TR93suhfzhsdik/btiPHY/ZQRN5sgV4S/Af2cKjAKkleo0tOeTf/BiChvj2sYaHnO38zw7eGXhJkIw7mOsa2pl9W24LD6sEfSxVK8x8mJNfQPct3zT4gZNNMEx3MBOqzHtqThw9ogTUhGS/5gFB3jyGKY4Sx/d1EVYRaGR/rORP5Yr6xFA0Qk8oyskvpMykXalupFkXmrMrY+zM2svQzr23qMGWkxI5DJkYuy22aESpCItULOqrk74p8UdKwj1LBS6nL/c+KCbHWnfu0gU3dGEVl+mckyJ+7Qnxd4C7Kx17m+ANR7M2b3Yb4i6h6Xy6ayrCy3EFyVlWcxIcilmDQ+fcLS+DHKNffIuiqEQ+wbS98MWnoQL+QWevn7EIAA8UhWNkyPxDnI9uOlZFQKAj3ZqhqvPcFzzrQVijzu0s7Op+FsvvWJ1Xv8e4YlB7vNMGhCNxjeAW9pXtKvQknoDaF3uaV9ozUQhNUJLuYhypzFt99pbWSNmv5Qp3Y+k63knUIH+TyCPofKZYUmwzN6D+M166wiLxhxmPYND1bH4vGzbt5JMzh41FRrClTN5LCq4EilNplMzoKcO/mTxJoLeTO2fxiPJxk/VbAuVvzZQ2jmVoMyn00xFidCUKT3NhZy6qCcziWqkPeDtkFaiDe3i4EDfBw70g7ymNVoT7VLgC9s7mSU7sg97JcH2k4o6W0PUCiX7s2Y/iLsd6BHWkI05zezzbbHFUDc6Yu+WnOqPEJf/ULi8ZI9AFK2HUH7Wc79CCn/7VL7M7N1X/B66q7nMew+FS7njHZN+g4UYsfS0fAk2X+7YsmdqMJlYKDkIjZtsbtshG0CGPVkjzlvWxrcKET4gAkvAiCxCtQ==","nonce":"25e98ee1553c"},"expire_time":"2026-01-03T13:19:42+08:00","serial_no":"35B4105DBFB51A3845213F8FF5F79413A6E48304"}]}';
//
//
//        $WechatpayTimestamp = $hearders['Wechatpay-Timestamp'];
//        $WechatpayNonce = $hearders['Wechatpay-Nonce'];
//
//        $publicKeyContent = '';// 微信公钥内容   -----BEGIN CERTIFICATE-----\nMIID3DCCAsSgAwIBAgIUNbQQ\n-----END CERTIFICATE-----
//        $bodyArr = json_decode($body, true);
//        foreach($bodyArr['data'] as $v){
//            $effective_time = $v['effective_time'];// 2021-01-04T13:19:42+08:00
//            $encrypt_certificate = $v['encrypt_certificate'];
//            $expire_time = $v['expire_time'];// 2026-01-03T13:19:42+08:00
//            $serial_no = $v['serial_no'];// 35B4105DBFB51A3845213F8FF5F79413A6E48304
//            $publicKeyContent = static::decryptToString($apiConfig['apiv3_secret'] ,$encrypt_certificate['associated_data'], $encrypt_certificate['nonce'], $encrypt_certificate['ciphertext']);
//            // pr($publicKeyContent);
//
//
//        }
//
//        // pr($bodyArr);
//        // 验签
//
//        $message = "{$WechatpayTimestamp}\n".
//            $WechatpayNonce."\n".
//            $body."\n";
//
//        $pub_id = openssl_get_publickey($publicKeyContent);
//        // 成功返回1 ；失败返回0
//        $res = openssl_verify($message, base64_decode($WechatpaySignature), $pub_id, 'sha256WithRSAEncryption');
//        pr($res);
//
//        //生成签名
//        // openssl_sign($message, $raw_sign, openssl_get_privatekey(file_get_contents($mch_private_key)), 'sha256WithRSAEncryption');
//        openssl_sign($message, $raw_sign, static::getPrivateKeyContentResource($apiConfig['apiclient_key_path'], $apiConfig['updated_at'], $apiConfig['apiv3_secret']), 'sha256WithRSAEncryption');
//        $sign = base64_encode($raw_sign);
//    }

    /**
     *  通用接口处理方法
     *
     * @param mixed $doFun 成功获得数据后的业务逻  的闭包函数  function($responseContent, $certificates, $apiResult){  return ;} ，--如果成功，会返回此函数内的返回值
     *    参数
     *        $responseContent ：接口返回的数据，如果是json，则自动转为数组；
     *        $certificates ：如果验签时，有新的平台证书，则通过此参数传出，其它业务逻辑则可以使用【如存储】--格式请看 getCertificates 方法返回格式
     *        $apiResult ：接口返回的数据，-- 具体格式，请查看方法 apiUrlRequest
     *   其它参数都通过 use传入函数内 如：use(&$namespace, &$expireNums)
     * @param mixed $failFun 失败 的业务逻  的闭包函数  function($responseContent, $certificates, $apiResult){} ，--不需要返回值
     *    参数
     *        $responseContent ：接口返回的数据，如果是json，则自动转为数组；--失败，此参数一般不用，主要是输出参考
     *        $certificates ：如果验签时，有新的平台证书，则通过此参数传出，其它业务逻辑则可以使用【如存储】--格式请看 getCertificates 方法返回格式--失败时，此参数可能有值，如：业务需要更新数据表
     *        $apiResult ：接口返回的数据，-- 具体格式，请查看方法 apiUrlRequest --失败，此参数一般不用，主要是输出参考
     *   其它参数都通过 use传入函数内 如：use(&$namespace, &$expireNums)
     * @param array $apiConfig  接口配置
     *  [
     *      'serial_no' => '56FC8CBC20C13345482454C25ACF531930F4D1A8',// 证书序列号
     *      'mchid' => '1605358235',// 商户号
     *      'appid' => 'wx41383f8b3f4bcdac', // 公众号APPID
     *      'api_secret' => '8IKzOOJJI81anzF4cXafxlHUzFDHNYHX',// API密钥
     *      'apiv3_secret' => 'jlEKJZSJBXfBE00vSQq2qvTbCKmdaJyq',// APIv3密钥
     *      'apiclient_key_path' => '/srv/www/cert/wechat/trm/apiclient_key.pem' ,// 商户API证书 私钥文件路径
     *      'updated_at' => '2020-11-04 10:37:28',// 记录更新日期时间
     *  ]
     * @param string $url  接口url 格式 "/v3/certificates"; 或 https://api.mch.weixin.qq.com  当然，也可以 带 ? 参数
     * @param mixed $params 获取请求中的请求报文主体（request body）；如果是数组--方法内自动转为json
     *         请求方法为GET时，报文主体为空。
     *         当请求方法为POST或PUT时，请使用真实发送的JSON报文。--可用数组，会自动转为json
     *         图片上传API，请使用meta对应的JSON报文。
     *         对于下载证书的接口来说，请求报文主体是一个空串。
     * @param string $type 请求类型 'GET'、'POST'、'PUT'、'PATCH'、'DELETE'
     * @param string $extendParams 其它扩展参数数组 一维数组
     *   $extendParams = [
     *      'Accept-Language' => 'zh-CN',// 应答中的错误描述使用的自然语言语种。如果有需要，设置请求的HTTP头Accept-Language。目前支持：  en  zh-CN  zh-HK  zh-TW
     *      'Wechatpay-Serial' => 'aaaa',// 可能有【敏感信息时有】 上送敏感信息时使用微信支付平台公钥加密，证书序列号包含在请求HTTP头部的 Wechatpay-Serial
     *  ];
     * @return array 如果 成功 ：【有$doFun函数：返回此函数的返回值，无：则返回--接口返回的数据，如果是json，则自动转为数组 】；失败：对外抛出错误
     * @author zouyan(305463219@qq.com)
     */
    public static function commonAPIRequest($doFun, $failFun, $apiConfig = [], $url = '', $params = '', $type = 'POST', $extendParams = []){

        // $url = static::$apiUrl . "/v3/certificates";// https://api.mch.weixin.qq.com
        if(strpos($url, '://') === false) $url = static::$apiUrl . $url; // 没有域名，则拼接上域名
        // $params = '';// [];
        /**
         * @param mixed $params 获取请求中的请求报文主体（request body）；如果是数组--方法内自动转为json
         *         请求方法为GET时，报文主体为空。
         *         当请求方法为POST或PUT时，请使用真实发送的JSON报文。--可用数组，会自动转为json
         *         图片上传API，请使用meta对应的JSON报文。
         *         对于下载证书的接口来说，请求报文主体是一个空串。
         *
         *
         */
        // 请求接口
        $certificates = [];// 如果验签时，有新的平台证书，则通过此参数传出，其它业务逻辑则可以使用【如存储】--格式请看 getCertificates 方法返回格式
        $apiResult = [];
        $responseContent = [];
        try {
            $apiResult = static::apiUrlRequest($apiConfig, $url, $params, $type, $extendParams);

            // 签名验证--接口返回的结果
            // $certificates = [];// 如果验签时，有新的平台证书，则通过此参数传出，其它业务逻辑则可以使用【如存储】--格式请看 getCertificates 方法返回格式
            static::verifyResponse($apiConfig, $apiResult, $certificates, $extendParams);

            $responseContent = $apiResult['response_content'];// 返回的结果数据
            // 如果是json,则转为数组
            if (!isNotJson($responseContent)) $responseContent = json_decode($responseContent, true);
        } catch ( \Exception $e) {
            if(is_callable($failFun)){
                $failFun($responseContent, $certificates, $apiResult);
            }
            throws($e->getMessage(), $e->getCode());
        }

        if(is_callable($doFun)){
            return $doFun($responseContent, $certificates, $apiResult);
        }
         return $responseContent;

    }

    /**
     *  请求微信api接口
     *
     * @param array $apiConfig  接口配置
     *  [
     *      'serial_no' => '56FC8CBC20C13345482454C25ACF531930F4D1A8',// 证书序列号
     *      'mchid' => '1605358235',// 商户号
     *      'appid' => 'wx41383f8b3f4bcdac', // 公众号APPID
     *      'api_secret' => '8IKzOOJJI81anzF4cXafxlHUzFDHNYHX',// API密钥
     *      'apiv3_secret' => 'jlEKJZSJBXfBE00vSQq2qvTbCKmdaJyq',// APIv3密钥
     *      'apiclient_key_path' => '/srv/www/cert/wechat/trm/apiclient_key.pem' ,// 商户API证书 私钥文件路径
     *      'updated_at' => '2020-11-04 10:37:28',// 记录更新日期时间
     *  ]
     * @param string $url 请求url地址  "https://api.mch.weixin.qq.com/v3/certificates"; 如果有 ? 参数 直接拼接在 ?后面
     * @param mixed $params 获取请求中的请求报文主体（request body）；如果是数组--方法内自动转为json
     *         请求方法为GET时，报文主体为空。
     *         当请求方法为POST或PUT时，请使用真实发送的JSON报文。--可用数组，会自动转为json
     *         图片上传API，请使用meta对应的JSON报文。
     *         对于下载证书的接口来说，请求报文主体是一个空串。
     * @param string $type 请求类型 'GET'、'POST'、'PUT'、'PATCH'、'DELETE'
     * @param string $extendParams 其它扩展参数数组 一维数组
     *   $extendParams = [
     *      'Accept-Language' => 'zh-CN',// 应答中的错误描述使用的自然语言语种。如果有需要，设置请求的HTTP头Accept-Language。目前支持：  en  zh-CN  zh-HK  zh-TW
     *      'Wechatpay-Serial' => 'aaaa',// 可能有【敏感信息时有】 上送敏感信息时使用微信支付平台公钥加密，证书序列号包含在请求HTTP头部的 Wechatpay-Serial
     *  ];
     * @return array
     * [
     *       'code' => $result->getStatusCode(), // HTTP协议状态码,
     *      'reason' => '',// OK// 原因短语(reason phrase)：
     *      'header' => [],// 获取头部信息；注意：下标的值还是一个数组
     *      'response_content' => '',// 接口返回的内容 -- 主要用这个，可能是json 格式的字符串
     *      'body' => object,// 获取html 返回的内容 对象
     *      'response' => object,// 响应对象
     *  ]
     * @author zouyan(305463219@qq.com)
     */
    public static function apiUrlRequest($apiConfig, $url, $params = [], $type = 'GET', $extendParams = []){

        //生成V3请求 header认证信息
        $header = static::createAuthorization($apiConfig, $url, $params, $type, $extendParams);
        // $header['User-Agent'] = 'https://zh.wikipedia.org/wiki/User_agent';
        $apiResult = HttpRequest::sendHttpRequestUrl($url, $params, [], $type, ['headers' => $header]);
        Log::info('微信支付日志 请求接口信息-->' . __FUNCTION__, [$url, $params, $type, $extendParams, $header, $apiResult]);
        $responseCode = $apiResult['code'];
        $responseReason = $apiResult['reason'];
        $responseHeaders = HttpRequest::getHearderArr($apiResult['header']);
        /**
         *
         * $hearders = [
         *   'Request-ID' => '08F3A6F38006101718A7B8B74C20F62528D7BF03-0', // 微信支付给每个接收到的请求分配了一个唯一标示。请求的唯一标示包含在应答的HTTP头Request-ID中。当需要微信支付帮助时，请提供请求的唯一标示，以便我们更快的定位到具体的请求。
         *   'Wechatpay-Nonce' => '41ce8e57ec95f463e903e6423575ea3a', // HTTP头Wechatpay-Nonce 中的应答随机串
         *   'Wechatpay-Signature' => 'fQPj2vjG7jRDAgvJ1/oqpAZI...A==', // 微信支付的应答签名
         *   'Wechatpay-Timestamp' => '1612501875', // HTTP头Wechatpay-Timestamp 中的应答时间戳。
         *   'Wechatpay-Serial' => '35B4105DBFB51A3845213F8FF5F79413A6E48304', // 微信支付签名 证书序列号 微信支付平台私钥
         *  ];
         *
         */
        $responseContent = $apiResult['response_content'];

        $RequestID = $responseHeaders['Request-ID'] ?? '';
        if(empty($RequestID)) throws('微信接收唯一标示不存在或不能为空');
        $WechatpaySignature = $responseHeaders['Wechatpay-Signature'] ?? '';
        if(empty($WechatpaySignature)) throws('微信签名不存在或不能为空');
        $WechatpayTimestamp = $responseHeaders['Wechatpay-Timestamp'] ?? '';
        if(empty($WechatpayTimestamp)) throws('应答时间戳不存在或不能为空');
        $WechatpayNonce = $responseHeaders['Wechatpay-Nonce'] ?? '';
        if(empty($WechatpayNonce)) throws('应答随机串不存在或不能为空');
        $WechatpaySerial = $responseHeaders['Wechatpay-Serial'] ?? '';
        if(empty($WechatpaySerial)) throws('微信证书序列号不存在或不能为空');

        // 对HTTP状态码 进行判断
        // 处理成功的请求，如果有应答的消息体将返回200，若没有应答的消息体将返回204。
        // 已经被成功接受待处理的请求，将返回202。

        // 请求处理失败时，如缺少必要的入参、支付时余额不足，将会返回4xx范围内的错误码。
        // 请求处理时发生了微信支付侧的服务系统错误，将返回500/501/503的状态码。这种情况比较少见。

        // HTTP状态码
        // 状态码  错误类型    一般的解决方案 典型错误码示例
        //* 200 - OK    处理成功    /   /
        //* 202 - Accepted  服务器已接受请求，但尚未处理  请使用原参数重复请求一遍    /
        //* 204 - No Content    处理成功，无返回Body    /   /

        //* 400 - Bad Request   协议或者参数非法    请根据接口返回的详细信息检查您的程序  PARAM_ERROR
        if($responseCode == 400) throws('400 - Bad Request   协议或者参数非法    请根据接口返回的详细信息检查您的程序  PARAM_ERROR');
        //* 401 - Unauthorized  签名验证失败  请检查签名参数和方法是否都符合签名算法要求   SIGN_ERROR
        if($responseCode == 401) throws('401 - Unauthorized  签名验证失败  请检查签名参数和方法是否都符合签名算法要求   SIGN_ERROR');
        //* 403 - Forbidden 权限异常    请开通商户号相关权限。请联系产品或商务申请   NO_AUTH
        if($responseCode == 403) throws('403 - Forbidden 权限异常    请开通商户号相关权限。请联系产品或商务申请   NO_AUTH');
        //* 404 - Not Found 请求的资源不存在    请商户检查需要查询的id或者请求URL是否正确 ORDER_NOT_EXIST
        if($responseCode == 404) throws('404 - Not Found 请求的资源不存在    请商户检查需要查询的id或者请求URL是否正确 ORDER_NOT_EXIST');
        //* 429 - Too Many Requests 请求超过频率限制    请求未受理，请降低频率后重试  RATELIMIT_EXCEEDED
        if($responseCode == 429) throws('429 - Too Many Requests 请求超过频率限制    请求未受理，请降低频率后重试  RATELIMIT_EXCEEDED');
        //* 500 - Server Error  系统错误    按具体接口的错误指引进行重试  SYSTEM_ERROR
        if($responseCode == 500) throws('500 - Server Error  系统错误    按具体接口的错误指引进行重试  SYSTEM_ERROR');
        //* 502 - Bad Gateway   服务下线，暂时不可用  请求无法处理，请稍后重试    SERVICE_UNAVAILABLE
        if($responseCode == 502) throws('502 - Bad Gateway   服务下线，暂时不可用  请求无法处理，请稍后重试    SERVICE_UNAVAILABLE');
        //* 503 - Service Unavailable   服务不可用，过载保护  请求无法处理，请稍后重试    SERVICE_UNAVAILABLE
        if($responseCode == 503) throws('503 - Service Unavailable   服务不可用，过载保护  请求无法处理，请稍后重试    SERVICE_UNAVAILABLE');

        return $apiResult;
    }


    /**
     *  签名验证--接口返回的结果
     *
     * @param array $apiConfig  接口配置
     *  [
     *      'serial_no' => '56FC8CBC20C13345482454C25ACF531930F4D1A8',// 证书序列号
     *      'mchid' => '1605358235',// 商户号
     *      'appid' => 'wx41383f8b3f4bcdac', // 公众号APPID
     *      'api_secret' => '8IKzOOJJI81anzF4cXafxlHUzFDHNYHX',// API密钥
     *      'apiv3_secret' => 'jlEKJZSJBXfBE00vSQq2qvTbCKmdaJyq',// APIv3密钥
     *      'apiclient_key_path' => '/srv/www/cert/wechat/trm/apiclient_key.pem' ,// 商户API证书 私钥文件路径
     *      'updated_at' => '2020-11-04 10:37:28',// 记录更新日期时间
     *  ]
     * @param array $apiResult
     * [
     *       'code' => $result->getStatusCode(), // HTTP协议状态码,
     *      'reason' => '',// OK// 原因短语(reason phrase)：
     *      'header' => [],// 获取头部信息；注意：下标的值还是一个数组
     *      'response_content' => '',// 接口返回的内容 -- 主要用这个，可能是json 格式的字符串
     *      'body' => object,// 获取html 返回的内容 对象
     *      'response' => object,// 响应对象
     *  ]
     * @param array $certificates 如果验签时，有新的平台证书，则通过此参数传出，其它业务逻辑则可以使用【如存储】--格式请看 getCertificates 方法返回格式
     *    证书的内容 // -----BEGIN PRIVATE KEY-----MIIEvwIBADANBgkqhkiG9w0BAQEFAASCB-----END PRIVATE KEY-----
     *         默认为空：从hearders中的Wechatpay-Serial 值 从缓存中 获取
     *         具体值：用传入的--来验签--主要是  获取平台证书列表 https://api.mch.weixin.qq.com/v3/certificates 接口用，  先要验签，才能缓存数据
     * @param string $extendParams 其它扩展参数数组 一维数组
     *   $extendParams = [
     *      'Accept-Language' => 'zh-CN',// 应答中的错误描述使用的自然语言语种。如果有需要，设置请求的HTTP头Accept-Language。目前支持：  en  zh-CN  zh-HK  zh-TW
     *      'Wechatpay-Serial' => 'aaaa',// 可能有【敏感信息时有】 上送敏感信息时使用微信支付平台公钥加密，证书序列号包含在请求HTTP头部的 Wechatpay-Serial
     *  ];
     * @param string $publicKeyContent
     * @return null
     * @author zouyan(305463219@qq.com)
     */
    public static function verifyResponse($apiConfig, $apiResult, &$certificates = [], $extendParams = [], $publicKeyContent = ''){

        $responseCode = $apiResult['code'];
        $responseReason = $apiResult['reason'];
        $responseHeaders = HttpRequest::getHearderArr($apiResult['header']);
        /**
         *
         * $hearders = [
         *   'Request-ID' => '08F3A6F38006101718A7B8B74C20F62528D7BF03-0', // 微信支付给每个接收到的请求分配了一个唯一标示。请求的唯一标示包含在应答的HTTP头Request-ID中。当需要微信支付帮助时，请提供请求的唯一标示，以便我们更快的定位到具体的请求。
         *   'Wechatpay-Nonce' => '41ce8e57ec95f463e903e6423575ea3a', // HTTP头Wechatpay-Nonce 中的应答随机串
         *   'Wechatpay-Signature' => 'fQPj2vjG7jRDAgvJ1/oqpAZI...A==', // 微信支付的应答签名
         *   'Wechatpay-Timestamp' => '1612501875', // HTTP头Wechatpay-Timestamp 中的应答时间戳。
         *   'Wechatpay-Serial' => '35B4105DBFB51A3845213F8FF5F79413A6E48304', // 微信支付签名 证书序列号 微信支付平台私钥
         *  ];
         *
         */
        $responseContent = $apiResult['response_content'];

        $RequestID = $responseHeaders['Request-ID'] ?? '';
        if(empty($RequestID)) throws('微信接收唯一标示不存在或不能为空');
        $WechatpaySignature = $responseHeaders['Wechatpay-Signature'] ?? '';
        if(empty($WechatpaySignature)) throws('微信签名不存在或不能为空');
        $WechatpayTimestamp = $responseHeaders['Wechatpay-Timestamp'] ?? '';
        if(empty($WechatpayTimestamp)) throws('应答时间戳不存在或不能为空');
        $WechatpayNonce = $responseHeaders['Wechatpay-Nonce'] ?? '';
        if(empty($WechatpayNonce)) throws('应答随机串不存在或不能为空');
        $WechatpaySerial = $responseHeaders['Wechatpay-Serial'] ?? '';
        if(empty($WechatpaySerial)) throws('微信证书序列号不存在或不能为空');

        // 证书的内容// -----BEGIN PRIVATE KEY-----MIIEvwIBADANBgkqhkiG9w0BAQEFAASCB-----END PRIVATE KEY-----
        // $certificates = [];
        if(empty($publicKeyContent)){
            $publicKeyContent = static::getRetryKeyContent($apiConfig, $WechatpaySerial, 1, $certificates, $extendParams);
//            $publicKeyContent = static::getKeyContentCache($apiConfig['mchid'], $WechatpaySerial, $apiConfig['apiv3_secret'], 1);
//            Log::info('微信支付日志 通过缓存获得平台公钥证书信息-->' . __FUNCTION__, [$WechatpaySerial, $publicKeyContent]);
//            if($publicKeyContent == false){// 失败,则说明没有公钥证书，重新调一次接口去获取并缓存公钥证书
//                $certificates = static::getCertificates($apiConfig, '', $extendParams);
//
//                // 根据证书接口/v3/certificates 返的数据，获取指定的证书序列号的证书内容
//                $apiv3_secret = $apiConfig['apiv3_secret'];
//                $publicKeyContent = static::getCertificateContent($certificates, $WechatpaySerial, $apiv3_secret);
//                Log::info('微信支付日志 通过平台证书列表接口获取信息信息-->' . __FUNCTION__, [$WechatpaySerial, $certificates, $publicKeyContent]);
//                if(empty($publicKeyContent)) throws('获取平台证书及平台证书序列号，没有证书序列号对应的公钥!');
//            }
        }else{

        }
        if(static::verify($responseContent, $WechatpayTimestamp, $WechatpayNonce, $WechatpaySignature, $publicKeyContent) != 1){
            Log::info('微信支付日志 验签失败-->' . __FUNCTION__, [$WechatpaySerial, $apiResult, $publicKeyContent]);
            throws('验签失败！');
        }
        // return $certificates;
    }


    /**
     *  获得证书内容，从缓存优先，如果缓存没有，则从证书接口重新获取
     *
     * @param array $apiConfig  接口配置
     *  [
     *      'serial_no' => '56FC8CBC20C13345482454C25ACF531930F4D1A8',// 证书序列号
     *      'mchid' => '1605358235',// 商户号
     *      'appid' => 'wx41383f8b3f4bcdac', // 公众号APPID
     *      'api_secret' => '8IKzOOJJI81anzF4cXafxlHUzFDHNYHX',// API密钥
     *      'apiv3_secret' => 'jlEKJZSJBXfBE00vSQq2qvTbCKmdaJyq',// APIv3密钥
     *      'apiclient_key_path' => '/srv/www/cert/wechat/trm/apiclient_key.pem' ,// 商户API证书 私钥文件路径
     *      'updated_at' => '2020-11-04 10:37:28',// 记录更新日期时间
     *  ]
     * @param string $WechatpaySerial 微信平台证书号 ；引用传值；可为空; 如果为空，则会优先取缓存中的，如果缓存没有或过期，则会调用平台证书列表自动获取并缓存
     * @param int $fileType 证书类型 1 公钥[默认] ； 2 私钥
     * @param array $certificates 如果验签时，有新的平台证书，则通过此参数传出，其它业务逻辑则可以使用【如存储】--格式请看 getCertificates 方法返回格式
     *    证书的内容 // -----BEGIN PRIVATE KEY-----MIIEvwIBADANBgkqhkiG9w0BAQEFAASCB-----END PRIVATE KEY-----
     *         默认为空：从hearders中的Wechatpay-Serial 值 从缓存中 获取
     *         具体值：用传入的--来验签--主要是  获取平台证书列表 https://api.mch.weixin.qq.com/v3/certificates 接口用，  先要验签，才能缓存数据
     * @param string $extendParams 其它扩展参数数组 一维数组
     *   $extendParams = [
     *      'Accept-Language' => 'zh-CN',// 应答中的错误描述使用的自然语言语种。如果有需要，设置请求的HTTP头Accept-Language。目前支持：  en  zh-CN  zh-HK  zh-TW
     *      'Wechatpay-Serial' => 'aaaa',// 可能有【敏感信息时有】 上送敏感信息时使用微信支付平台公钥加密，证书序列号包含在请求HTTP头部的 Wechatpay-Serial
     *  ];
     * @return string 证书内容 或 throws 错误
     * @author zouyan(305463219@qq.com)
     */
    public static function getRetryKeyContent($apiConfig, &$WechatpaySerial = '', $fileType = 1, &$certificates = [], $extendParams = []){
        $publicKeyContent = false;
        if(!empty($WechatpaySerial)) $publicKeyContent = static::getKeyContentCache($apiConfig['mchid'], $WechatpaySerial, $apiConfig['apiv3_secret'], $fileType);
        Log::info('微信支付日志 通过缓存获得平台公钥证书信息-->' . __FUNCTION__, [$WechatpaySerial, $publicKeyContent]);
        if($publicKeyContent == false){// 失败,则说明没有公钥证书，重新调一次接口去获取并缓存公钥证书
            $certificates = static::getCertificates($apiConfig, $WechatpaySerial, '', $extendParams);

            // 根据证书接口/v3/certificates 返的数据，获取指定的证书序列号的证书内容
            $apiv3_secret = $apiConfig['apiv3_secret'];
            $publicKeyContent = static::getCertificateContent($certificates, $WechatpaySerial, $apiv3_secret);
            Log::info('微信支付日志 通过平台证书列表接口获取信息信息-->' . __FUNCTION__, [$WechatpaySerial, $certificates, $publicKeyContent]);
            if(empty($publicKeyContent)) throws('获取平台证书及平台证书序列号，没有证书序列号对应的公钥!');
        }
        return $publicKeyContent;
    }

    /**
     *  签名验证
     *
     * @param string $responseContent  响应的内容
     * @param string $WechatpayTimestamp 中的应答时间戳 1612501875
     * @param string $WechatpayNonce HTTP头Wechatpay-Nonce 中的应答随机串  41ce8e57ec95f463e903e6423575ea3a
     * @param string $WechatpaySignature 微信支付的应答签名
     * @param string $publicKeyContent 证书的内容// -----BEGIN PRIVATE KEY-----MIIEvwIBADANBgkqhkiG9w0BAQEFAASCB-----END PRIVATE KEY-----
     * @return int 成功返回1 ；失败返回0
     * @author zouyan(305463219@qq.com)
     */
    public static function verify($responseContent, $WechatpayTimestamp, $WechatpayNonce, $WechatpaySignature, $publicKeyContent)
    {

        // 验签
        $body = $responseContent;
        $message = "{$WechatpayTimestamp}\n".
            $WechatpayNonce."\n".
            $body."\n";

        $pub_id = openssl_get_publickey($publicKeyContent);
        // 成功返回1 ；失败返回0
        $res = openssl_verify($message, base64_decode($WechatpaySignature), $pub_id, 'sha256WithRSAEncryption');
        return $res;
    }


    /**
     * V3加密- --根据公钥路径- 公钥加密
     * @param string $str 需要加密的数据
     * @param string $apiclient_cert_path 商户API证书 私钥文件路径 '/srv/www/cert/wechat/trm/apiclient_cert.pem'
     * @param string $updated_at 记录更新日期时间 2020-11-04 10:37:28
     * @param string $apiv3_secret APIv3密钥
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @param boolean $isReadOrCache 引用传参 是否需要重新读取可能还会缓存 true:重新读取可能还会缓存；false:不用重新读取[有缓存数据]
     * @param boolean $isReJudgeCache  在获得数据前，是否再读一次缓存并判断是否有效[有效则直接返回，不从源数据读了]--防击穿，以及雪崩，穿透 true:重新读取可缓存；false:不用重新读取，强制重新读源数据
     * @return string  返回加密后的数据
     * @author zouyan(305463219@qq.com)
     */
    public static function getEncryptPublicFile($str, $apiclient_cert_path, $updated_at, $apiv3_secret, &$isOpenCache = true, &$isReadOrCache = false, $isReJudgeCache = true){
        $fileContent = static::getFileKeyContent($apiclient_cert_path, $updated_at, $apiv3_secret, $isOpenCache, $isReadOrCache, $isReJudgeCache);
        return static::encryptPublicStr($str, $fileContent);
    }

    /**
     * V3加密- --根据证书号- 公钥加密
     * @param array $apiConfig  接口配置
     *  [
     *      'serial_no' => '56FC8CBC20C13345482454C25ACF531930F4D1A8',// 证书序列号
     *      'mchid' => '1605358235',// 商户号
     *      'appid' => 'wx41383f8b3f4bcdac', // 公众号APPID
     *      'api_secret' => '8IKzOOJJI81anzF4cXafxlHUzFDHNYHX',// API密钥
     *      'apiv3_secret' => 'jlEKJZSJBXfBE00vSQq2qvTbCKmdaJyq',// APIv3密钥
     *      'apiclient_key_path' => '/srv/www/cert/wechat/trm/apiclient_key.pem' ,// 商户API证书 私钥文件路径
     *      'updated_at' => '2020-11-04 10:37:28',// 记录更新日期时间
     *  ]
     * @param string $str 需要加密的数据
     * @param string $WechatpaySerial 微信平台证书号 ；引用传值；可为空; 如果为空，则会优先取缓存中的，如果缓存没有或过期，则会调用平台证书列表自动获取并缓存
     * @param mixed $finallyFun 对可能调用获得证书接口 的业务逻  的闭包函数  function($certificates){} ，--不需要返回值
     *    参数
     *        $certificates ：如果验签时，有新的平台证书，则通过此参数传出，其它业务逻辑则可以使用【如存储】--格式请看 getCertificates 方法返回格式--失败时，此参数可能有值，如：业务需要更新数据表
     * @param string $extendParams 其它扩展参数数组 一维数组
     *   $extendParams = [
     *      'Accept-Language' => 'zh-CN',// 应答中的错误描述使用的自然语言语种。如果有需要，设置请求的HTTP头Accept-Language。目前支持：  en  zh-CN  zh-HK  zh-TW
     *      'Wechatpay-Serial' => 'aaaa',// 可能有【敏感信息时有】 上送敏感信息时使用微信支付平台公钥加密，证书序列号包含在请求HTTP头部的 Wechatpay-Serial
     *  ];
     * @return string  返回加密后的数据
     * @author zouyan(305463219@qq.com)
     */
    public static function getEncryptPublic($apiConfig, $str, &$WechatpaySerial = '', $finallyFun = null, $extendParams = []){
        $certificates = [];
        if(empty($WechatpaySerial)){// 为空，则优先从缓存获取
            // 获得当前缓存的
            $cacheSerial = static::getKeySerialCache($apiConfig['mchid']);
            if(is_array($cacheSerial) && !empty($cacheSerial) && static::judgeKeySerial($cacheSerial) == 4) $WechatpaySerial = $cacheSerial['serial_no'] ?? '';
        }
        try {
            $fileContent = static::getRetryKeyContent($apiConfig, $WechatpaySerial, 1, $certificates, $extendParams);
        } catch ( \Exception $e) {
            throws($e->getMessage(), $e->getCode());
        }finally{
            if(is_callable($finallyFun)){
                $finallyFun($certificates);
            }
        }
        return static::encryptPublicStr($str, $fileContent);
    }

    /**
     * V3加密- --根据公钥的内容，加密数据
     *
     * @param string $str 需要加密的数据
     * @param string $public_key 公钥的内容
     * @return string  返回加密后的数据
     * @author zouyan(305463219@qq.com)
     */
    public static function encryptPublicStr($str, $public_key){

        $encrypted = '';

        if (openssl_public_encrypt($str,$encrypted, $public_key,OPENSSL_PKCS1_OAEP_PADDING)) {
            //base64编码
            $sign = base64_encode($encrypted);
        } else {
            // throw new Exception('encrypt failed');
            throws('encrypt failed');
        }
        return $sign;
    }


    /**
     * V3加密-- 公钥加密
     *  开发者应当使用微信支付平台证书中的公钥，对上送的敏感信息进行加密。这样只有拥有私钥的微信支付才能对密文进行解密，从而保证了信息的机密性。
     */
//    public function getEncrypt($str){
//        //$str是待加密字符串
//        $public_key_path = '证书地址'; //看情况使用证书， 个别接口证书 使用的是 平台证书而不是 api证书
//        $public_key = file_get_contents($public_key_path);
//        $encrypted = '';
//
//        if (openssl_public_encrypt($str,$encrypted, $public_key,OPENSSL_PKCS1_OAEP_PADDING)) {
//            //base64编码
//            $sign = base64_encode($encrypted);
//        } else {
//            // throw new Exception('encrypt failed');
//            throws('encrypt failed');
//        }
//        return $sign;
//    }

    /**
     * V3解密-- 私钥解密
     *  微信支付使用 商户证书中的公钥对下行的敏感信息进行加密。开发者应使用商户私钥对下行的敏感信息的密文进行解密。
     */
//    public function getDecrypt($sign){
//        //$str是待加密字符串
//        $public_key_path = '证书地址'; //看情况使用证书， 个别接口证书 使用的是 平台证书而不是 api证书
//        $public_key = file_get_contents($public_key_path);
//        $decrypted = '';
//
//        //base64解码
//        // $sign = base64_decode($sign);
//        if (openssl_private_decrypt(base64_decode($sign),$decrypted, $public_key,OPENSSL_PKCS1_OAEP_PADDING)) {
//        } else {
//            // throw new Exception('encrypt failed');
//            throws('encrypt failed');
//        }
//        return $decrypted;
//    }

    /**
     * V3解密- --根据私钥路径- 私钥解密
     * @param string $strEncrypt 需要解密的数据
     * @param string $apiclient_key_path 商户API证书 公钥文件路径 '/srv/www/cert/wechat/trm/apiclient_cert.pem'
     * @param string $updated_at 记录更新日期时间 2020-11-04 10:37:28
     * @param string $apiv3_secret APIv3密钥
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @param boolean $isReadOrCache 引用传参 是否需要重新读取可能还会缓存 true:重新读取可能还会缓存；false:不用重新读取[有缓存数据]
     * @param boolean $isReJudgeCache  在获得数据前，是否再读一次缓存并判断是否有效[有效则直接返回，不从源数据读了]--防击穿，以及雪崩，穿透 true:重新读取可缓存；false:不用重新读取，强制重新读源数据
     * @return string  返回解密后的数据
     * @author zouyan(305463219@qq.com)
     */
    public static function getDencryptPrivateFile($strEncrypt, $apiclient_key_path, $updated_at, $apiv3_secret, &$isOpenCache = true, &$isReadOrCache = false, $isReJudgeCache = true){
        $fileContent = static::getFileKeyContent($apiclient_key_path, $updated_at, $apiv3_secret, $isOpenCache, $isReadOrCache, $isReJudgeCache);
        return static::dencryptPrivateStr($strEncrypt, $fileContent);
    }

    /**
     * V3解密- --根据证书号- 私钥解密
     *  用不上，因为不会用到微信平台私钥
     * @param string $strEncrypt 需要解密的数据
     * @param string $mchid 商户号
     * @param string $WechatpaySerial 证书号
     * @param string $apiv3_secret APIv3密钥
     * @return string  返回解密后的数据
     * @author zouyan(305463219@qq.com)
     */
    public static function getDencryptPrivate($strEncrypt, $mchid, $WechatpaySerial, $apiv3_secret){
        $fileContent = static::getKeyContentCache($mchid, $WechatpaySerial, $apiv3_secret, 2);
        return static::dencryptPrivateStr($strEncrypt, $fileContent);
    }

    /**
     * V3解密- --根据证书号- 私钥解密
     * 用不上，因为不会用到微信平台私钥
     * @param array $apiConfig  接口配置
     *  [
     *      'serial_no' => '56FC8CBC20C13345482454C25ACF531930F4D1A8',// 证书序列号
     *      'mchid' => '1605358235',// 商户号
     *      'appid' => 'wx41383f8b3f4bcdac', // 公众号APPID
     *      'api_secret' => '8IKzOOJJI81anzF4cXafxlHUzFDHNYHX',// API密钥
     *      'apiv3_secret' => 'jlEKJZSJBXfBE00vSQq2qvTbCKmdaJyq',// APIv3密钥
     *      'apiclient_key_path' => '/srv/www/cert/wechat/trm/apiclient_key.pem' ,// 商户API证书 私钥文件路径
     *      'updated_at' => '2020-11-04 10:37:28',// 记录更新日期时间
     *  ]
     * @param string $strEncrypt 需要解密的数据
     * @param string $WechatpaySerial 证书号
     * @param mixed $finallyFun 对可能调用获得证书接口 的业务逻  的闭包函数  function($certificates){} ，--不需要返回值
     *    参数
     *        $certificates ：如果验签时，有新的平台证书，则通过此参数传出，其它业务逻辑则可以使用【如存储】--格式请看 getCertificates 方法返回格式--失败时，此参数可能有值，如：业务需要更新数据表
     * @param string $extendParams 其它扩展参数数组 一维数组
     *   $extendParams = [
     *      'Accept-Language' => 'zh-CN',// 应答中的错误描述使用的自然语言语种。如果有需要，设置请求的HTTP头Accept-Language。目前支持：  en  zh-CN  zh-HK  zh-TW
     *      'Wechatpay-Serial' => 'aaaa',// 可能有【敏感信息时有】 上送敏感信息时使用微信支付平台公钥加密，证书序列号包含在请求HTTP头部的 Wechatpay-Serial
     *  ];
     * @return string  返回解密后的数据
     * @author zouyan(305463219@qq.com)
     */
//    public static function getDencryptPrivate($apiConfig, $strEncrypt, $WechatpaySerial, $finallyFun, $extendParams = []){
//        $certificates = [];
//        try {
//            $fileContent = static::getRetryKeyContent($apiConfig, $WechatpaySerial, 2, $certificates, $extendParams);
//        } catch ( \Exception $e) {
//            throws($e->getMessage(), $e->getCode());
//        }finally{
//            if(is_callable($finallyFun)){
//                $finallyFun($certificates);
//            }
//        }
//        return static::dencryptPrivateStr($strEncrypt, $fileContent);
//    }

    /**
     * V3解密- --根据私钥的内容，解密数据
     *
     * @param string $strEncrypt 需要解密的数据
     * @param string $private_key  私钥的内容
     * @return string  返回解密后的数据
     * @author zouyan(305463219@qq.com)
     */
    public static function dencryptPrivateStr($strEncrypt, $private_key){

        $decrypted = '';

        //base64解码
        // $sign = base64_decode($sign);
        if (openssl_private_decrypt(base64_decode($strEncrypt),$decrypted, $private_key,OPENSSL_PKCS1_OAEP_PADDING)) {
        } else {
            // throw new Exception('encrypt failed');
            throws('encrypt failed');
        }
        return $decrypted;
    }


    /**
     * Decrypt AEAD_AES_256_GCM ciphertext  V3签名解密
     * @param stingr    $aesKey             V3签名
     * @param string    $associatedData     AES GCM additional authentication data // 附加数据包（可能为空）
     * @param string    $nonceStr           AES GCM nonce // 加密使用的随机串初始化向量）
     * @param string    $ciphertext         AES GCM cipher text // Base64编码后的密文
     *
     * @return string|bool      Decrypted string on success or FALSE on failure
     */
    public static function decryptToString($aesKey ,$associatedData, $nonceStr, $ciphertext)
    {

        if (strlen($aesKey) != 32 ) {
            // throw new InvalidArgumentException('无效的ApiV3Key，长度应为32个字节');
            throws('无效的ApiV3Key，长度应为32个字节');
        }

        $ciphertext = \base64_decode($ciphertext , true);
        if (strlen($ciphertext) <= 16) {
            return false;
        }


        // ext-sodium (default installed on >= PHP 7.2)
        if(function_exists('\sodium_crypto_aead_aes256gcm_is_available') && \sodium_crypto_aead_aes256gcm_is_available() ){
            return \sodium_crypto_aead_aes256gcm_decrypt($ciphertext, $associatedData, $nonceStr, $aesKey);
        }

        // ext-libsodium (need install libsodium-php 1.x via pecl)
        if(function_exists('\Sodium\crypto_aead_aes256gcm_is_available') && \Sodium\crypto_aead_aes256gcm_is_available()){

            return \Sodium\crypto_aead_aes256gcm_decrypt($ciphertext, $associatedData, $nonceStr, $aesKey);
        }

        // PHP >= 7.1
        if(PHP_VERSION_ID >= 70100 && in_array('aes-256-gcm', \openssl_get_cipher_methods()) ){
            $ctext = substr($ciphertext, 0, -16);
            $authTag = substr($ciphertext, -16);
            return \openssl_decrypt($ctext, 'aes-256-gcm', $aesKey, \OPENSSL_RAW_DATA, $nonceStr,$authTag, $associatedData);
        }

        // throw new \RuntimeException('AEAD_AES_256_GCM需要PHP 7.1以上或者安装libsodium-php');
        throws('AEAD_AES_256_GCM需要PHP 7.1以上或者安装libsodium-php');
    }


}
