<?php
namespace App\Services\Redis;

use App\Services\Tool;
use Illuminate\Support\Facades\Redis;

class RedisString
{
    // set存数据 创建一个 key 并设置value
    // Redis::set('key','value');
    public static function set($key, $value){
        return Tool::lockDoSomething('Redis:set:' . $key,
            function()  use(&$key, &$value){//
                return  Redis::set($key, $value);
            }, function($errDo){
                // TODO
//                $errMsg = '获得字段失败，请稍后重试!';
//                if($errDo == 1) throws($errMsg);
//                return $errMsg;
                return null;
            }, false, 1, 2000, 2000);
    }

    public static function setexDo($key, $expire, $value){
        return Tool::lockDoSomething('Redis:setex:' . $key,
            function()  use(&$key, &$expire, &$value){//
                return Redis::setex($key, $expire, $value);
            }, function($errDo){
                // TODO
//                $errMsg = '获得字段失败，请稍后重试!';
//                if($errDo == 1) throws($errMsg);
//                return $errMsg;
                return null;
            }, false, 1, 2000, 2000);
    }


    // $expire 有效期 秒 <=0 长期有效
    // $value 需要保存的值，如果是对象或数组，则序列化
    public static function setex($key, $expire, $value){
        if(is_numeric($expire) && $expire > 0){
            return static::setexDo($key, $expire, $value);
        }else{
            return static::set($key, $value);
        }
    }

    // add操作,不会覆盖已有值
    // $redis->setnx('foo', 12) ;  // 返回 true ， 添加成功
    // true:新创建成功；false:已存在，他建设失败
    public static function setnx($key, $value){
        return Redis::setnx($key, $value);
    }

    // 不存在，则创建
    // $expire > 0，则同时设置或修改过期时间； <= 0 ：长期有效 --单位秒
    // true:新创建成功；false:已存在，他建设失败
    public static function setnxExpire($key, $expire, $value){
        if(static::setnx($key, $value))        {
            static::expire($key, $expire);  #设置过期时间
            return true;
        }else{
            return false;
        }
    }

    // 不存在则创建;存在，则更新有效期或值,
    // int 选填 $operateType 操作类型 1存在修改值 2存在修改有效期[默认]
    // true:更新成功；false:键不存在，更新有效期失败
    public static function forceSetnxExpire($key, $expire, $value, $operateType = 2){
        if(static::setnx($key, $value))        {
            static::expire($key, $expire);  #设置过期时间
            return true;
        }else{
            if(($operateType & 1) == 1) static::set($key, $value);
            if(($operateType & 2) == 2) static::expire($key, $expire);  #设置过期时间
            return true;
        }
    }

    // 存在，则更新有效期 或值
    // int 选填 $operateType 操作类型 1存在修改值 2存在修改有效期[默认]
    // true:更新成功；false:键不存在，更新有效期失败
    public static function existSetnxExpire($key, $expire, $value = '', $operateType = 2){
        if(static::exists($key)){
            if(($operateType & 1) == 1) static::set($key, $value);
            if(($operateType & 2) == 2) static::expire($key, $expire);  #设置过期时间
            return true;
        }else{
            return false;
        }
    }

    // 设置有效期
    public static function expire($key, $timeout){
        if(!is_numeric($timeout) || $timeout <= 0) return true;
        return Tool::lockDoSomething('Redis:expire:' . $key,
            function()  use(&$key, &$timeout){//
                return  Redis::expire($key, $timeout);
            }, function($errDo){
                // TODO
//                $errMsg = '获得字段失败，请稍后重试!';
//                if($errDo == 1) throws($errMsg);
//                return $errMsg;
                return null;
            }, false, 1, 2000, 2000);
    }

    /**
     * redis中不存在，则加入默认值。存在则自增
     * @param string $key 键---单个用户的标识 token
     * @param int $limitSecond 多少秒请求的限制 -单位：秒 -- 缓存时长
     * @param int $defaultNum 缓存不存在时，第一次赋值的默认值
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return mixed  具体数字:自增后的结果;  sting 具体错误 ； throws 错误
     */
    public static function numIncr($key, $limitSecond, $defaultNum = 1, $errDo = 1){
        $defaultNum = Tool::lockDoSomething('limit:numIncr' . $key,
            function()  use(&$defaultNum, &$key, &$limitSecond){//
                // redis中不存在，则加入。存在则自增
                if(!RedisString::setnxExpire($key, $limitSecond, $defaultNum)){
                    $defaultNum = RedisString::incr($key);
                }
                return $defaultNum;
            }, function($errDo){
                // TODO
                $errMsg = '失败，请稍后重试!';
                if($errDo == 1) throws($errMsg);
                return $errMsg;
            }, true, $errDo, 2000, 2000);
        return $defaultNum;
    }

    // incrby/incr/decrby/decr 对值的递增和递减

    // $redis->incr('foo') ;  // 返回 57，同时 foo 的值为 57
    public static function incr($key){
        return  Redis::incr($key);
    }

    // $redis->incrby('foo', 2 ) ;  // 返回 59，同时 foo 的值为 59
    public static function incrby($key, $incNum){
        return  Redis::incrby($key, $incNum);
    }

    public static function decr($key){
        return  Redis::decr($key);
    }

    public static function decrby($key, $decNum){
        return  Redis::decrby($key, $decNum);
    }


    // get命令用于获取指定 key 的值,key不存在,返回null,如果key储存的值不是字符串类型，返回一个错误。
    // var_dump(Redis::get('key'));
    public static function get($key){
        return Redis::get($key);
    }

    // del 删除 成功删除返回 true, 失败则返回 false
    // $judgeExist 是否判断键是否存在 true:判断存在  false:不判断存在
    // Redis::del('key');
    public static function del($key, $judgeExist = true){
        if($judgeExist && !static::exists($key)) return true;
        return Tool::lockDoSomething('Redis:del:' . $key,
            function()  use(&$key){//
                return Redis::del($key);
            }, function($errDo){
                // TODO
//                $errMsg = '获得字段失败，请稍后重试!';
//                if($errDo == 1) throws($errMsg);
//                return $errMsg;
                return null;
            }, false, 1, 2000, 2000);

    }

    // 存在，则删除
    // true:删除成功；false:键不存在，或删除失败
    public static function existDel($key){
        if(static::exists($key)){
            return static::del($key, false);  #设置过期时间
        }else{
            return false;
        }
    }

    // mset存储多个 key 对应的 value
    //     $array= array(
    //         'user1'=>'张三',
    //         'user2'=>'李四',
    //         'user3'=>'王五'
    //     );
    // redis::mset($array); // 存储多个 key 对应的 value
    public static function mset($array){
        redis::mset($array); // 存储多个 key 对应的 value
    }

    // Mget返回所有(一个或多个)给定 key 的值,给定的 key 里面,key 不存在,这个 key 返回特殊值 nil
    // var_dump(redis::mget (array_keys( $array))); //获取多个key对应的value
    public static function mget($keysArr){
        return redis::mget($keysArr); //获取多个key对应的value
    }

    // Strlen 命令用于获取指定 key 所储存的字符串值的长度。当 key存储不是字符串，返回错误。
    // var_dump(redis::strlen('key'));
    public static function strlen($key){
        return redis::strlen($key);
    }

    // substr 获取第一到第三位字符
    // var_dump(Redis::substr('key',0,2));
    public static function substr($key, $beginUbound, $endUbound){
        return Redis::substr($key, $beginUbound, $endUbound);
    }

    // 根据键名模糊搜索
    // var_dump(Redis::keys('use*'));//模糊搜索
    public static function keys($keyWord){
        return Redis::keys($keyWord);//模糊搜索
    }

    // 获取缓存时间
    // Redis::ttl('str2');//获取缓存时间
    public static function ttl($key){
        return Redis::ttl($key);//获取缓存时间
    }

    // exists检测是否存在某值
    // Redis::exists ( 'foo' ) ; //true
    public static function exists($key){
        return Redis::exists($key) ; //true
    }

    // ~~~~~~~~~~~~~~结合运用~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    /**
     * 保存redis值-json/序列化保存
     * @param string 必填 $pre 前缀
     * @param string $key 键 null 自动生成
     * @param string 选填 $value 需要保存的值，如果是对象或数组，则序列化
     * @param int 选填 $expire 有效期 秒 <=0 长期有效
     * @param int 选填 $operate 操作 1 转为json 2 序列化 3 不转换
     * @return $key
     * @author zouyan(305463219@qq.com)
     */
    public static function setRedis($pre = '', $key = null, $value = '', $expire = 0, $operate = 1)
    {
        if( (!is_string($key) && !is_numeric($key)) || strlen($key) <= 0)  $key = Tool::createUniqueNumber(25);
        $key = $pre . $key;
        // 序列化保存
        try{
            $value = Tool::dataFormat($value, $operate);
//            switch($operate){
//                case 1:
//                    if(is_array($value)){
//                        $value = json_encode($value);
//                    }
//                    break;
//                case 2:
//                    $value = serialize($value);
//                    break;
//                default:
//                    break;
//            }
            if(is_numeric($expire) && $expire > 0){
                // Redis::setex($key, $expire, $value);
                static::setex($key, $expire, $value);
            }else{
                // Redis::set($key, $value);
                static::set($key, $value);
            }
        } catch ( \Exception $e) {
            throws('redis[' . $key . ']保存失败；信息[' . $e->getMessage() . ']');
        }
        return $key;
    }

    /**
     * 获得key的redis值
     * @param string $key 键
     * @param int 选填 $operate 操作 1 转为json 2 序列化 3 不转换
     * @return $value ; false失败
     * @author zouyan(305463219@qq.com)
     */
    public static function getRedis($key, $operate = 1)
    {
        $value = static::get($key);// Redis::get($key);
        if(is_bool($value) || is_null($value)){//string或BOOL 如果键不存在，则返回 FALSE。否则，返回指定键对应的value值。
            return false;
        }
        return Tool::dataResolv($value, $operate);
//        switch($operate){
//            case 1:
//                if (!Tool::isNotJson($value)) {
//                    $value = json_decode($value, true);
//                }
//                break;
//            case 2:
//                $value = unserialize($value);
//                break;
//            default:
//                break;
//        }
//        return $value;

    }


}
