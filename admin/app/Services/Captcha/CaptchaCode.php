<?php
namespace App\Services\Captcha;
use App\Services\Tool;

class CaptchaCode
{
    public static $captcha_pre = "captcha:";// 缓存皱起前缀
    public static $captcha_expire = 60 * 3;// 过期时间 3分钟
    public static $captcha_operate = 3;// 操作 1 转为json 2 序列化 3 不转换

    /**
     * 生成验证码
     *
     * @param string $requestClass [暂不使用，以后再看] 调用此方法的类名或其它唯一的信息[最好加上用户的id或ip]，主要作为key来限止调用次数的
     * @param string $config
     * @param int $captcha_expire 过期时间
     * @return  array
     *  $captchaParams =  [
     *      "sensitive" => true,
     *      "key" => "$2y$10$4UTAMBN0hd1V6wP3bVmbhu/PQf/y9Mz6FhFJ/VtU8CkwmRkBF8/cy",// 验证码的hash值
     *      "img" => "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHgAAAAkCAYAAABCKP5eAAAA",// base64后的图片
     *  ];
     * @author zouyan(305463219@qq.com)
     */
    public static function createCodeAPI(string $requestClass = '', string $config = 'default', $captcha_expire = 180){
        if(!is_numeric($captcha_expire) || $captcha_expire <= 0 ) $captcha_expire = static::$captcha_expire;
        $captchaParams = app('captcha')->create($config, true);
        $captchaKey = $captchaParams['key'];
        // 缓存起来
        Tool::setRedis(static::getRedisKeyPre(), $captchaKey, 1, $captcha_expire , static::$captcha_operate);
        return $captchaParams;
    }

    /**
     * 生成验证码--校验
     *
     * @param string $captcha_code 验证码
     * @param string $captcha_key 缓存key
     * @param boolean $del_cache 通过验证是否删除缓存 true:删除：false:不删除
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return  mixed sting 具体错误(验证不通过) ； throws 错误 true:验证通过 ;
     * @author zouyan(305463219@qq.com)
     */
    public static function captchaCheckAPI($captcha_code = '', $captcha_key = '', $del_cache = false, $errDo = 1){
        if(is_string($captcha_code) && strlen($captcha_code) <= 0){
            $errMsg = '验证码不能为空！';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }
        if(!captcha_api_check($captcha_code, $captcha_key)) {
            $errMsg = '验证码错误！';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }
        if(Tool::getRedis(static::getRedisKeyPre() . $captcha_key, static::$captcha_operate) != 1){
            $errMsg = '验证码已过期，请刷新重试!！';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }
        // 验证通过删除缓存
        if($del_cache) Tool::delRedis(static::getRedisKeyPre() . $captcha_key);
        return true;
    }

    /**
     * 获得redis key前缀
     *
     * @return  string 缓存前缀
     * @author zouyan(305463219@qq.com)
     */
    public static function getRedisKeyPre(){
        return Tool::getProjectKey(1, ':', ':') . static::$captcha_pre;
    }
}
