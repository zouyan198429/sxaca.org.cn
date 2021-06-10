<?php


namespace App\Services\Signer;


use App\Services\Redis\RedisString;
use App\Services\Tool;

class Signer
{
    /**
     * 获得计数器最新的值
     * @param string $cachePartKey  关键字 --缓存键中的标识不同的模块的；如 'orderRefund'
     * @param string $redisKey redis下标后部分
     * @param mixed $noCacheFun 没有redis缓存时的闭包函数 没有参数 ；返回当前已经使用的最大值--如果都没有使用过，则返回 0
     *  function(){
     *      $val = 0;
     *     // 读取已经使用的最大值
     *     $val =  ...;
     *     return $val
     * }
     * @return int 新的值
     */
    public static function getNewIncrVal($cachePartKey, $redisKey, $noCacheFun = null){
        // 计数器，缓存redis，自增，redis没有，则查表中最大值自增
        // 如果redis有值，则用redis的并自增1，
        // 如果redis没有值，则redis写0；然后读取数据库记录中的最大值，最大值+1，并保存到redis中
        // $redisKeyPre = 'idvalincr' . $cachePartKey . ':' . Tool::getProjectKey(1, ':', ':');
        // $redisKey = $objPathNameMD;
        $primaryKeyVal = Tool::lockDoSomething('limit:' . Tool::getUniqueKey([Tool::getProjectKey(1, ':', ':')
                , __CLASS__, __FUNCTION__, $cachePartKey, $redisKey]),
            function()  use(&$modelObj, &$redisKeyPre, &$redisKey, &$noCacheFun){//
                if(!RedisString::exists($redisKeyPre . $redisKey)){// 不存在
                    $usedMaxVal = 0;
                    if(is_callable($noCacheFun)){
                        $usedMaxVal = $noCacheFun();
                    }
                    // 一天更新一次缓存--还是缓存永久有效吧
                    $primaryKeyVal = RedisString::numIncr($redisKeyPre . $redisKey, 0, $usedMaxVal + 1 , 1);
                    // pr($primaryKeyVal);
                }else{// 存在
                    // pr(22);
                    $primaryKeyVal = RedisString::incr($redisKeyPre . $redisKey);
                }
                return $primaryKeyVal;
            }, function($errDo){
                // TODO
                $errMsg = '失败，请稍后重试!';
                if($errDo == 1) throws($errMsg);
                return $errMsg;
            }, true, 1, 2000, 2000);
        return $primaryKeyVal;
    }

    /**
     * 根所需要的数据类型 获得计数器最新的值--单条
     * @param string $cachePartKey  关键字 --缓存键中的标识不同的模块的；如 'orderRefund'
     * @param string $redisKey redis下标后部分
     * @param int $valType  主键id的值类型;
     *         2 或 256 自增  默认 256
     *         4一秒1  0000个   2【位】+6【位】+ 秒2【位】+自增数5【位】 = 15【位】 => 年【2位】+每年中第几分钟【60*24*365=525600 6位】+ 秒【2位】--长度15位
     *          8一分钟100 0000个   2【位】+6【位】+自增数6【位】 = 14【位】 => 年【2位】+每年中第几分钟【60*24*365=525600 6位】-- 长度 14位
     *          按年的天数~~~~~~~~~~~~~~~~直观年及年的第几天
     *         16 一秒1  0000个 年【2位】+ 日期[一年中的第几天 001-365] 3位 +每天中第几秒钟 一天中的第几秒   0-86400【5位】+自增数5【位】 --长度15位
     *          32 一分钟100 0000个 年【2位】+ 日期[一年中的第几天 001-365] 3位 +每天中时分钟 H时i分【4位】 +自增数6【位】 --长度15位
     *          按年月日的 分或秒~~~~~~~~~~~~~直观年月日
     *          64 一秒1  0000个 年【2位】+ 日期[月日] 4位 +每天中第几秒钟 一天中的第几秒   0-86400【5位】+自增数5【位】 --长度16位
     *         128 一分钟100 0000个 年【2位】+ 日期[月日] 4位 ++每天中时分钟 H时i分【4位】 +自增数6【位】 --长度16位
     * @param array $fixParams 前中后缀，默认都为空，不用填
     * [
     *  'prefix' => '',// 前缀[1-2位] 可填;可写业务编号等
     *  'midFix' => '',// 日期后中间缀[1-2位] 可填;适合用户编号里的后2位或其它等
     *  'backfix' => '',// 后缀[1-2位] 可填;备用
     * ]
     * @param mixed $noCacheFun 没有redis缓存时的闭包函数 没有参数 ；返回当前已经使用的最大值--如果都没有使用过，则返回 0
     *  function(){
     *      $val = 0;
     *     // 读取已经使用的最大值
     *     $val =  ...;
     *     return $val
     * }
     * @return int 新的值
     */
    public static function getNewValByType($cachePartKey, $redisKey, $valType = 256, $fixParams = [], $noCacheFun = null){
        $val = 0;
        if(in_array($valType, [2, 256])){
            $val = static::getNewIncrVal($cachePartKey, $redisKey, $noCacheFun);
        }else{
            $val = static::getNewVal($valType, $cachePartKey . $redisKey, $fixParams);
        }
        return $val;
    }

    // 批量生成新的值，参数同上面的主法 getNewValByType；只是多了一个数量
    // @param int $valNums 需要获得值的数量 默认 1 ; -- 不要超过 $maxValNum 参数
    // 返回数组 --一维
    public static function getNewValArr($cachePartKey, $redisKey, $valType = 256, $valNums = 1, $fixParams = [], $noCacheFun = null){
        $reVals = [];
        for($i = 0; $i < $valNums; $i++){
            $newVal = static::getNewValByType($cachePartKey, $redisKey, $valType, $fixParams, $noCacheFun);
            array_push($reVals, $newVal);
        }
        return $reVals;
    }

    /**
     * 获得新的单个的值
     * @param int $valType  主键id的值类型;
     *         4一秒1  0000个   2【位】+6【位】+ 秒2【位】+自增数5【位】 = 15【位】 => 年【2位】+每年中第几分钟【60*24*365=525600 6位】+ 秒【2位】--长度15位
     *          8一分钟100 0000个   2【位】+6【位】+自增数6【位】 = 14【位】 => 年【2位】+每年中第几分钟【60*24*365=525600 6位】-- 长度 14位
     *          按年的天数~~~~~~~~~~~~~~~~直观年及年的第几天
     *         16 一秒1  0000个 年【2位】+ 日期[一年中的第几天 001-365] 3位 +每天中第几秒钟 一天中的第几秒   0-86400【5位】+自增数5【位】 --长度15位
     *          32 一分钟100 0000个 年【2位】+ 日期[一年中的第几天 001-365] 3位 +每天中时分钟 H时i分【4位】 +自增数6【位】 --长度15位
     *          按年月日的 分或秒~~~~~~~~~~~~~直观年月日
     *          64 一秒1  0000个 年【2位】+ 日期[月日] 4位 +每天中第几秒钟 一天中的第几秒   0-86400【5位】+自增数5【位】 --长度16位
     *         128 一分钟100 0000个 年【2位】+ 日期[月日] 4位 ++每天中时分钟 H时i分【4位】 +自增数6【位】 --长度16位
     * @param string $cachePartKey  关键字 --缓存键中的标识不同的模块的；如 'orderRefund'
     * @param array $fixParams 前中后缀，默认都为空，不用填
     * [
     *  'prefix' => '',// 前缀[1-2位] 可填;可写业务编号等
     *  'midFix' => '',// 日期后中间缀[1-2位] 可填;适合用户编号里的后2位或其它等
     *  'backfix' => '',// 后缀[1-2位] 可填;备用
     * ]
     * @return int 0 没有获取到， > 0 成功获取到
     */
    public static function getNewVal($valType = 0, $cachePartKey = '', $fixParams = []){
        $val = 0;

        switch($valType) {
            // 按时间生成 bigint类型
            // 年【2位】+每年中第几分钟【60*24*365=525600 6位】+ 秒【2位】--长度15位
            case 4:// 4一秒1  0000个   2【位】+6【位】+ 秒2【位】+自增数4【位】 = 16【位】
                $fixParams = array_merge($fixParams, [
                    // 'prefix' => '',// 前缀[1-2位] 可填;可写业务编号等
                    // 'midFix' => '',// 日期后中间缀[1-2位] 可填;适合用户编号里的后2位或其它等
                    // 'backfix' => '',// 后缀[1-2位] 可填;备用
                    'expireNums' => [],// redis设置缓存 ，在两个值之间时 - 二维 [[1,20,'缓存的秒数365 * 24 * 60 * 60'], [40,50,'缓存的秒数']]
                    'needNum' => 1 | 2 | 4 | 8,// 需要拼接的内容 1 年 2日期 4 自定义日期格式 8 自增的序号
                    'dataFormat' => 's', // needNum 值为 4时的日期格式  'YmdHis'
                ]);
                $val = Tool::makeOrder($cachePartKey , $fixParams, 5);
                break;
            case 8:// 8一分钟100 0000个   2【位】+6【位】+自增数6【位】 = 16【位】
                $fixParams = array_merge($fixParams, [
                    // 'prefix' => '',// 前缀[1-2位] 可填;可写业务编号等
                    // 'midFix' => '',// 日期后中间缀[1-2位] 可填;适合用户编号里的后2位或其它等
                    // 'backfix' => '',// 后缀[1-2位] 可填;备用
                    'expireNums' => [],// redis设置缓存 ，在两个值之间时 - 二维 [[1,20,'缓存的秒数365 * 24 * 60 * 60'], [40,50,'缓存的秒数']]
                    'needNum' => 1 | 2 | 8,// 需要拼接的内容 1 年 2日期 4 自定义日期格式 8 自增的序号
                    'dataFormat' => '', // needNum 值为 4时的日期格式  'YmdHis'
                ]);
                $val = Tool::makeOrder($cachePartKey , $fixParams, 6);
                break;
            case 16:// 一秒1  0000个 年【2位】+ 日期[一年中的第几天 001-365] 3位 +每天中第几秒钟 一天中的第几秒   0-86400【5位】+自增数5【位】 --长度15位
                $fixParams = array_merge($fixParams, [
                    // 'prefix' => '',// 前缀[1-2位] 可填;可写业务编号等
                    // 'midFix' => '',// 日期后中间缀[1-2位] 可填;适合用户编号里的后2位或其它等
                    // 'backfix' => '',// 后缀[1-2位] 可填;备用
                    'expireNums' => [],// redis设置缓存 ，在两个值之间时 - 二维 [[1,20,'缓存的秒数365 * 24 * 60 * 60'], [40,50,'缓存的秒数']]
                    'needNum' => 1 | 16 | 64 | 8,// 需要拼接的内容 1 年 2日期 4 自定义日期格式 8 自增的序号
                    'dataFormat' => '', // needNum 值为 4时的日期格式  'YmdHis'
                ]);
                $val = Tool::makeOrder($cachePartKey , $fixParams, 5);
                break;
            case 32:// 一分钟100 0000个 年【2位】+ 日期[一年中的第几天 001-365] 3位 +每天中时分钟 H时i分【4位】 +自增数6【位】 --长度15位
                $fixParams = array_merge($fixParams, [
                    // 'prefix' => '',// 前缀[1-2位] 可填;可写业务编号等
                    // 'midFix' => '',// 日期后中间缀[1-2位] 可填;适合用户编号里的后2位或其它等
                    // 'backfix' => '',// 后缀[1-2位] 可填;备用
                    'expireNums' => [],// redis设置缓存 ，在两个值之间时 - 二维 [[1,20,'缓存的秒数365 * 24 * 60 * 60'], [40,50,'缓存的秒数']]
                    'needNum' => 1 | 4 | 16 | 8,// 需要拼接的内容 1 年 2日期 4 自定义日期格式 8 自增的序号
                    'dataFormat' => 'Hi', // needNum 值为 4时的日期格式  'YmdHis'
                ]);
                $val = Tool::makeOrder($cachePartKey , $fixParams, 6);
                break;
            case 64:// 一秒1  0000个 年【2位】+ 日期[月日] 4位 +每天中第几秒钟 一天中的第几秒   0-86400【5位】+自增数5【位】 --长度16位
                $fixParams = array_merge($fixParams, [
                    //  'prefix' => '',// 前缀[1-2位] 可填;可写业务编号等
                    // 'midFix' => '',// 日期后中间缀[1-2位] 可填;适合用户编号里的后2位或其它等
                    // 'backfix' => '',// 后缀[1-2位] 可填;备用
                    'expireNums' => [],// redis设置缓存 ，在两个值之间时 - 二维 [[1,20,'缓存的秒数365 * 24 * 60 * 60'], [40,50,'缓存的秒数']]
                    'needNum' => 1 | 4 | 64 | 8,// 需要拼接的内容 1 年 2日期 4 自定义日期格式 8 自增的序号
                    'dataFormat' => 'md', // needNum 值为 4时的日期格式  'YmdHis'
                ]);
                $val = Tool::makeOrder($cachePartKey , $fixParams, 5);
                break;
            case 128:// 一分钟100 0000个 年【2位】+ 日期[月日] 4位 ++每天中时分钟 H时i分【4位】 +自增数6【位】 --长度16位
                $fixParams = array_merge($fixParams, [
                    // 'prefix' => '',// 前缀[1-2位] 可填;可写业务编号等
                    // 'midFix' => '',// 日期后中间缀[1-2位] 可填;适合用户编号里的后2位或其它等
                    // 'backfix' => '',// 后缀[1-2位] 可填;备用
                    'expireNums' => [],// redis设置缓存 ，在两个值之间时 - 二维 [[1,20,'缓存的秒数365 * 24 * 60 * 60'], [40,50,'缓存的秒数']]
                    'needNum' => 1 | 4 | 8,// 需要拼接的内容 1 年 2日期 4 自定义日期格式 8 自增的序号
                    'dataFormat' => 'mdHi', // needNum 值为 4时的日期格式  'YmdHis'
                ]);
                $val = Tool::makeOrder($cachePartKey , $fixParams, 6);
                break;
            default:
        }
        // pr($val);
        return $val;
    }
}
