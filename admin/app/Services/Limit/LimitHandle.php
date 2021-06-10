<?php
/*
 * 操作限制
 * 配置
 *
 *
 *
 */
namespace App\Services\Limit;

use App\Services\Redis\RedisString;
use App\Services\Tool;
use Carbon\Carbon;

class LimitHandle
{

    /**
     * --失败必须加锁
     * 执行操作，失败加锁，连续失败到某次，可以执行操作。
     *
     * @param string $doFun 具体执行什么操作--只会调用一次，返回值 true:成功；false:失败 ；参数 $infoConfig-单个限定配置， 通过use 引用传参到函数体内
     * @param string $doKey 操作的关键字--要求唯一  如：login
     * @param string $doName 操作的名称--如登录
     * @param array/string $limitConfig 限定配置信息 一维或二维数组 或json字符串
     * @return  mixed sting 具体错误(验证不通过)-暂无 ； throws 错误 true:操作都成功 ; false:有失败，但不处理--暂无
     * @author zouyan(305463219@qq.com)
     */
    public static function doLimit($doFun, $doKey = '', $doName = '', $limitConfig = []){
        // 如果是json字符串，则转为数组
        jsonStrToArr($limitConfig , 1, '配置参数有误！');
//
//        $data ='';
//        $aaa = '';
////        $doFun = function($infoConfig) use(&$data, &$aaa){
////            echo '$data=' .  $data . '<br/>';
////            echo '$aaa=' .  $aaa . '<br/>';
////            return true;
////        };
//        $lockingFun =  function($infoConfig, $lockMsg, $failednNum) use(&$data, &$aaa){
//            echo '$data=' .  $data . '<br/>';
//            echo '$aaa=' .  $aaa . '<br/>';
//            return true;
//        };
//        $lockedFun =  function(&$infoConfig, &$lockedInfo) use(&$data, &$aaa){
//            echo '$data=' .  $data . '<br/>';
//            echo '$aaa=' .  $aaa . '<br/>';
//            return true;
//        };
//        $limitingFun = function() use(&$data, &$aaa){
//            echo '$data=' .  $data . '<br/>';
//            echo '$aaa=' .  $aaa . '<br/>';
//            return true;
//        };
//        $limitedFun = function(&$infoConfig, &$lockSumInfoArr, &$sumLockedInfo) use(&$data, &$aaa){
//            echo '$data=' .  $data . '<br/>';
//            echo '$aaa=' .  $aaa . '<br/>';
//            return true;
//        };
//        $limitConfig = [
//            [
//                'cacheKey' => '1',// 整个二维数组中-唯一，作为缓存键的名称部分的
//                'unitTime' => 60 * 10,// 单位时间内 单位:秒，如10分钟，可以为零：代表所有时间内
////                'doFun' =>  $doFun,// 具体执行什么操作，返回值 true:成功；false:失败 ；参数 $infoConfig-单个限定配置， 通过use 引用传参到函数体内
//                'errNum' => 4,// 连续出错次数, 最小为1（> 0）
//                'lockTime' => 60,// 锁定时长，单位:秒; 如果值<0 ，则值为 unitTime 的值, 如果真为0(unitTime 也为 0时)，代表永久锁定
//                'lockingFun' => $lockingFun, // 每次锁定时执行的具体操作--不再执行后面代码；返回值：无；参数 $infoConfig-单个限定配置, $lockedInfo--锁的Redis数组, $failednNum --失败次数， 通过use 引用传参到函数体内
//                'lockedFun' => $lockedFun, // 每次已锁定状态时执行的具体操作--不再执行后面代码；返回值：无；参数 $infoConfig-单个限定配置, $lockedInfo--锁的Redis数组， 通过use 引用传参到函数体内
//                'lockNums' => [// 二维数组 为空，则不做累积封存限定--
//                    [
//                        'limitTime' => 60 * 50 ,// 限定时间内；单位:秒，如10分钟；如果值<0 ，则值为 unitTime 的值 0为不限时间
//                        'limitLockNum' => 5,// 连续锁定次数 必须 > 0,不然默认为1
//                        'limitLockTime' => 60,// 锁定执行的时长，单位:秒; 如果值<0 ，则值为 limitTime 的值, 如果真为0(limitTime 也为 0时)，代表永久执行
//                        'limitingFun' => $limitingFun,// 刚达到时，执行的操作，返回值 true:不执行后面代码--成功；false: 还要继续执行后面代码[默认]；错误(报错) 直接throws()；无参函数， 通过use 引用传参到函数体内
//                        'limitedFun' => $limitedFun,// 最前面判断，如果已达到时，执行的操作，返回值 1:不执行后面代码--成功；2默认 错误(报错) 原代码中直接throws()[默认]4: 还要继续执行后面代码；8 自定义方法中错误(报错) 直接throws()；参数 $infoConfig-单个限定配置, $lockSumInfoArr --单个累积配置, $sumLockedInfo--累积锁的Redis数组， 通过use 引用传参到函数体内
//                    ]
//                ],
//            ],
//        ];

        if(empty($limitConfig) || !is_array($limitConfig)) return throws('配置参数有误！');

        // 如果是一维数组,则转为二维数组
        Tool::isMultiArr($limitConfig, true);

        // 是否已经执行过操作函数
        $executedDdFun = false;// false:未执行 true:已执行

        // 相关的redis key
        // 记录连续出错的次数 key
        $redisKeyErrNum = $doKey . ':errNum';
        // 锁的redis  key
        $redisKeyLock = $doKey . ':locked';

        $tishiPre = $doName . '时:';

        $usedCacheKeys = [];// 已经用过的缓存key

        foreach($limitConfig as $k => $infoConfig){
            $cacheKey = $infoConfig['cacheKey'] ?? $k;// 整个二维数组中-唯一，作为缓存键的名称部分的
            $unitTime = $infoConfig['unitTime'];// 单位时间内 单位:秒，如10分钟 ，可以为零：代表所有时间内
            if(!is_numeric($unitTime) || $unitTime < 0) $unitTime = 0;
            // $doFun = $infoConfig['doFun'];// 具体执行什么操作，返回值 true:成功；false:失败 ；无参函数， 通过use 引用传参到函数体内
            $errNum = $infoConfig['errNum'];// 连续出错次数, 最小为1（> 0）
            if(!is_numeric($errNum) || $errNum <= 0) $errNum = 1;
            $lockTime = $infoConfig['lockTime'];// 锁定时长，单位:秒; 如果值<0 ，则值为 unitTime 的值, 如果真为0(unitTime 也为 0时)，代表永久锁定
            if(!is_numeric($lockTime) || $lockTime < 0) $lockTime = $unitTime;

            $lockingFun = $infoConfig['lockingFun'];// 每次锁定时执行的具体操作--不再执行后面代码；返回值：无；无参函数， 通过use 引用传参到函数体内
            $lockedFun = $infoConfig['lockedFun'];// 每次已锁定状态时执行的具体操作--不再执行后面代码；返回值：无；无参函数， 通过use 引用传参到函数体内
            $lockNums = $infoConfig['lockNums'];// // 二维数组 为空，则不做累积锁定限定
            static::getCacheKey($usedCacheKeys, $cacheKey, $k, $unitTime);


            // 记录连续出错的次数 key
            $redisKeyErrNumInfo = $redisKeyErrNum . ':' . $cacheKey;
            // 锁的redis  key
            // [ 'lockTime' => '锁定时间', 'unlockTime' => '解锁时间', 'lockExpire' => '锁定秒数', 'lockErrNum' => '连续失败次数']
            $redisKeyLockInfo = $redisKeyLock . ':' . $cacheKey;

            // $temErrArr = [];

            // 判断是否已经锁定
            $lockedInfo = Tool::getRedis($redisKeyLockInfo, 1);
            if($lockedInfo && RedisString::exists($redisKeyLockInfo)){
                $lockTimeTem = $lockedInfo['lockTime'];// 锁定时间 如：2020-06-01 09:23:34
                $unlockTimeTem = $lockedInfo['unlockTime'];// 解锁时间 如：2020-06-01 09:43:59 ；空值：无期限锁定
                $lockExpireTem = $lockedInfo['lockExpire'];// 锁定秒数 >0 或 0：无期限锁定
                $lockErrNumTem = $lockedInfo['lockErrNum'];// 连续失败次数 加锁时，连续失败次数
                // 每次锁定时执行的具体操作--不再执行后面代码；返回值：无；无参函数， 通过use 引用传参到函数体内
                if(is_callable($lockedFun)){
                    $lockedFun($infoConfig, $lockedInfo);
                }
                $temLockErrTxt = '';// {10分钟内}
                $temLockedBack = '';// {已锁定。}/ {锁定【20分钟2秒】，请稍后重试！【2020-06-01 09:43:59】！}
                // 锁定秒数 >0 或 0：无期限锁定
                if($unitTime > 0)  $temLockErrTxt =  Tool::formatSecondNum($unitTime) . '内';// 10分钟内
                if($lockExpireTem > 0)  {

                    $temLockedBack .= '锁定【' . Tool::formatSecondNum($lockExpireTem) . '】，请稍后';
                    if(strlen($unlockTimeTem) > 0) $temLockedBack .= '【' . $unlockTimeTem . '】';
                    $temLockedBack .= '重试！';
                }else{
                    $temLockedBack .= '已锁定。';
                }

                // old 登录时：在【2020-06-01 09:23:34】{10分钟内}已连续失败5次，{已锁定。}/ {锁定【20分钟2秒】，请稍后重试！【2020-06-01 09:43:59】！}
                // now 登录时：{10分钟内}已连续失败5次，{已锁定。}/ {锁定【20分钟2秒】，请稍后【2020-06-01 09:43:59】重试！！}
                throws($tishiPre . '' .  $temLockErrTxt . '已连续失败' . $lockErrNumTem . '次，' .$temLockedBack, 222);// 在【' . $lockTimeTem . '】
            }


            // 没有锁定，则执行操作
            $resultDo = true;
            if(!$executedDdFun && is_callable($doFun)){
                $executedDdFun = true;
                $resultDo = $doFun($infoConfig);
            }

            // 成功，则继续执行下一个判断，失败，则加上锁
            if($resultDo){
                // 释放各种Redis
                Tool::delRedis($redisKeyErrNumInfo);// -记录出错次数
                Tool::delRedis($redisKeyLockInfo);// -锁redis
                continue;// 继续下一个判断
            }

            // 已下是执行失败逻辑

            $currentTime = Carbon::now()->toDateTimeString();// date('Y-m-d H:i:s');//当前时间 2020-06-02 15:48:49

            // 连续失败的次数
            // $failednNum = 0;
            // $failednNum++;
            $failednNum = RedisString::numIncr($redisKeyErrNumInfo, $unitTime, 1, 1);
            // 设置连续失败次数

            if($failednNum < $errNum){// 失败次数还没有达到锁定
                $temTimeIn = ''; // {10分钟内}
                if($unitTime > 0) $temTimeIn = Tool::formatSecondNum($unitTime) . '内';
                // 连续7次失败，将会锁定20分钟！
                $temLockBackTxt = '连续' . $errNum . '次失败，将会锁定';
                if($lockTime > 0) $temLockBackTxt .= Tool::formatSecondNum($lockTime);
                $temLockBackTxt .= '！';
                 // old 登录时：在【2020-06-01 09:23:34】{10分钟内}已连续失败5次，连续7次失败，将会锁定｛20分钟｝！
                 // new 登录时： {10分钟内}已连续失败5次，连续7次失败，将会锁定｛20分钟｝！
                throws( $tishiPre . '' . $temTimeIn . '已连续失败' . $failednNum . '次，' .$temLockBackTxt,2222);// 在【' . $currentTime . '】
            }

            // 失败，超指定次数--加锁--清除缓存错误计数 -- 封存计数（并判断是否封存：如果封存-清除封存计数）
            $endTime = '';
            if($lockTime > 0) $endTime = (Carbon::now())->addSeconds($lockTime)->toDateTimeString();
            $lockMsg = [
                'lockTime' => $currentTime,// 锁定时间 如：2020-06-01 09:23:34
                'unlockTime' => $endTime,// 解锁时间 如：2020-06-01 09:43:59 ；空值：无期限锁定
                'lockExpire' => $lockTime,// 锁定秒数 >0 或 0：无期限锁定
                'lockErrNum' => $failednNum// 连续失败次数 加锁时，连续失败次数
            ];
            // 加锁
            Tool::setRedis('', $redisKeyLockInfo, $lockMsg, $lockTime, 1);

            if(is_callable($lockingFun)){
                $lockingFun($infoConfig, $lockMsg, $failednNum);
            }
            // 清除记错误次数Redis
            Tool::delRedis($redisKeyErrNumInfo);// -记录出错次数

            // 连续

            // old 登录时：在【2020-06-01 09:23:34】{10分钟内}已连续失败5次，被锁定｛20分钟，请稍后再试【' . $endTime . '】｝！
            // new 登录时：{10分钟内}已连续失败5次，被锁定｛20分钟，请稍后【' . $endTime . '】再试｝！
            $temlockingTimeIn = '';
            if($unitTime > 0) $temlockingTimeIn = Tool::formatSecondNum($unitTime) . '内';
            $temLockingBack = '被锁定';
            if($lockTime > 0 && strlen($endTime) > 0) $temLockingBack .= Tool::formatSecondNum($lockTime) . '请稍后【' . $endTime . '】再试';
            $temLockingBack .= '！';
            throws( $tishiPre . '' . $temlockingTimeIn . '已连续失败' . $failednNum . '次，' . $temLockingBack);// 在【' . $currentTime . '】
        }
        return true;
    }

    /**
     * --失败必须加锁
     * 执行操作，失败加锁，连续失败到某次，可以执行操作。
     *
     * @param string $doFun 具体执行什么操作--只会调用一次，返回值 true:成功；false:失败 ；参数 $infoConfig-单个限定配置， 通过use 引用传参到函数体内
     * @param string $doKey 操作的关键字--要求唯一  如：login
     * @param string $doName 操作的名称--如登录
     * @param array/string $limitConfig 限定配置信息 一维或二维数组 或json字符串
     * @return  mixed sting 具体错误(验证不通过)-暂无 ； throws 错误 true:操作都成功 ; false:有失败，但不处理--暂无
     * @author zouyan(305463219@qq.com)
     */
    public static function doLimitBack($doFun, $doKey = '', $doName = '', $limitConfig = []){
        // 如果是json字符串，则转为数组
        jsonStrToArr($limitConfig , 1, '配置参数有误！');
//
//        $data ='';
//        $aaa = '';
////        $doFun = function($infoConfig) use(&$data, &$aaa){
////            echo '$data=' .  $data . '<br/>';
////            echo '$aaa=' .  $aaa . '<br/>';
////            return true;
////        };
//        $lockingFun =  function($infoConfig, $lockMsg, $failednNum) use(&$data, &$aaa){
//            echo '$data=' .  $data . '<br/>';
//            echo '$aaa=' .  $aaa . '<br/>';
//            return true;
//        };
//        $lockedFun =  function(&$infoConfig, &$lockedInfo) use(&$data, &$aaa){
//            echo '$data=' .  $data . '<br/>';
//            echo '$aaa=' .  $aaa . '<br/>';
//            return true;
//        };
//        $limitingFun = function() use(&$data, &$aaa){
//            echo '$data=' .  $data . '<br/>';
//            echo '$aaa=' .  $aaa . '<br/>';
//            return true;
//        };
//        $limitedFun = function(&$infoConfig, &$lockSumInfoArr, &$sumLockedInfo) use(&$data, &$aaa){
//            echo '$data=' .  $data . '<br/>';
//            echo '$aaa=' .  $aaa . '<br/>';
//            return true;
//        };
//        $limitConfig = [
//            [
//                'cacheKey' => '1',// 整个二维数组中-唯一，作为缓存键的名称部分的
//                'unitTime' => 60 * 10,// 单位时间内 单位:秒，如10分钟，可以为零：代表所有时间内
////                'doFun' =>  $doFun,// 具体执行什么操作，返回值 true:成功；false:失败 ；参数 $infoConfig-单个限定配置， 通过use 引用传参到函数体内
//                'errNum' => 4,// 连续出错次数, 最小为1（> 0）
//                'lockTime' => 60,// 锁定时长，单位:秒; 如果值<0 ，则值为 unitTime 的值, 如果真为0(unitTime 也为 0时)，代表永久锁定
//                'lockingFun' => $lockingFun, // 每次锁定时执行的具体操作--不再执行后面代码；返回值：无；参数 $infoConfig-单个限定配置, $lockedInfo--锁的Redis数组, $failednNum --失败次数， 通过use 引用传参到函数体内
//                'lockedFun' => $lockedFun, // 每次已锁定状态时执行的具体操作--不再执行后面代码；返回值：无；参数 $infoConfig-单个限定配置, $lockedInfo--锁的Redis数组， 通过use 引用传参到函数体内
//                'lockNums' => [// 二维数组 为空，则不做累积封存限定--
//                    [
//                        'limitTime' => 60 * 50 ,// 限定时间内；单位:秒，如10分钟；如果值<0 ，则值为 unitTime 的值 0为不限时间
//                        'limitLockNum' => 5,// 连续锁定次数 必须 > 0,不然默认为1
//                        'limitLockTime' => 60,// 锁定执行的时长，单位:秒; 如果值<0 ，则值为 limitTime 的值, 如果真为0(limitTime 也为 0时)，代表永久执行
//                        'limitingFun' => $limitingFun,// 刚达到时，执行的操作，返回值 true:不执行后面代码--成功；false: 还要继续执行后面代码[默认]；错误(报错) 直接throws()；无参函数， 通过use 引用传参到函数体内
//                        'limitedFun' => $limitedFun,// 最前面判断，如果已达到时，执行的操作，返回值 1:不执行后面代码--成功；2默认 错误(报错) 原代码中直接throws()[默认]4: 还要继续执行后面代码；8 自定义方法中错误(报错) 直接throws()；参数 $infoConfig-单个限定配置, $lockSumInfoArr --单个累积配置, $sumLockedInfo--累积锁的Redis数组， 通过use 引用传参到函数体内
//                    ]
//                ],
//            ],
//        ];

        if(empty($limitConfig) || !is_array($limitConfig)) return throws('配置参数有误！');

        // 如果是一维数组,则转为二维数组
        Tool::isMultiArr($limitConfig, true);

        // 是否已经执行过操作函数
        $executedDdFun = false;// false:未执行 true:已执行

        // 相关的redis key
        // 记录连续出错的次数 key
        $redisKeyErrNum = $doKey . ':errNum';
        // 锁的redis  key
        $redisKeyLock = $doKey . ':locked';
        // 累积锁的redis key
        $redisKeySumLock = $doKey . ':lockedSum';


        $tishiPre = $doName . '时:';

        $usedCacheKeys = [];// 已经用过的缓存key

        foreach($limitConfig as $k => $infoConfig){
            $cacheKey = $infoConfig['cacheKey'] ?? $k;// 整个二维数组中-唯一，作为缓存键的名称部分的
            $unitTime = $infoConfig['unitTime'];// 单位时间内 单位:秒，如10分钟 ，可以为零：代表所有时间内
            if(!is_numeric($unitTime) || $unitTime < 0) $unitTime = 0;
            // $doFun = $infoConfig['doFun'];// 具体执行什么操作，返回值 true:成功；false:失败 ；无参函数， 通过use 引用传参到函数体内
            $errNum = $infoConfig['errNum'];// 连续出错次数, 最小为1（> 0）
            if(!is_numeric($errNum) || $errNum <= 0) $errNum = 1;
            $lockTime = $infoConfig['lockTime'];// 锁定时长，单位:秒; 如果值<0 ，则值为 unitTime 的值, 如果真为0(unitTime 也为 0时)，代表永久锁定
            if(!is_numeric($lockTime) || $lockTime < 0) $lockTime = $unitTime;

            $lockingFun = $infoConfig['lockingFun'];// 每次锁定时执行的具体操作--不再执行后面代码；返回值：无；无参函数， 通过use 引用传参到函数体内
            $lockedFun = $infoConfig['lockedFun'];// 每次已锁定状态时执行的具体操作--不再执行后面代码；返回值：无；无参函数， 通过use 引用传参到函数体内
            $lockNums = $infoConfig['lockNums'];// // 二维数组 为空，则不做累积锁定限定
            static::getCacheKey($usedCacheKeys, $cacheKey, $k, $unitTime);


            // 记录连续出错的次数 key
            $redisKeyErrNumInfo = $redisKeyErrNum . ':' . $cacheKey;
            // 锁的redis  key
            // [ 'lockTime' => '锁定时间', 'unlockTime' => '解锁时间', 'lockExpire' => '锁定秒数', 'lockErrNum' => '连续失败次数']
            $redisKeyLockInfo = $redisKeyLock . ':' . $cacheKey;

            // 累积锁的redis key
            $redisKeySumLockInfo = $redisKeySumLock . ':' . $cacheKey;

            // $temErrArr = [];
            // 如果是json字符串，则转为数组
            jsonStrToArr($lockNums , 1, '配置参数[lockNums]有误！');
            if(!is_array($lockNums)) throws('配置参数[lockNums]有误！');
            // 如果是一维数组,则转为二维数组
            Tool::isMultiArr($lockNums, true);
            // 判断累积封存
            if(count($lockNums) > 0){
                foreach($lockNums as $lockKey => $lockSumInfoArr){
                    if(empty($lockSumInfoArr) || !is_array($lockSumInfoArr)) continue;
                    $sumLimitTime = $lockSumInfoArr['limitTime'];// 限定时间内；单位:秒，如10分钟；如果值<0 ，则值为 unitTime 的值 0为不限时间
                    $sumLimitLockNum = $lockSumInfoArr['limitLockNum'];// 连续锁定次数 必须 > 0,不然默认为1
                    $sumLimitLockTime = $lockSumInfoArr['limitLockTime'];// 锁定执行的时长，单位:秒; 如果值<=0 ，则值为 limitTime 的值, 如果真为0(limitTime 也为 0时)，代表永久执行
                    $sumLimitingFun = $lockSumInfoArr['limitingFun'];// 刚达到时，执行的操作，返回值 true:不执行后面代码--成功；false: 还要继续执行后面代码[默认]；错误(报错) 直接throws()；无参函数， 通过use 引用传参到函数体内
                    $sumLimitedFun = $lockSumInfoArr['limitedFun'];// 最前面判断，如果已达到时，执行的操作，返回值  1:不执行后面代码--成功；2默认 错误(报错) 原代码中直接throws()[默认]4: 还要继续执行后面代码；8 自定义方法中错误(报错) 直接throws()；无参函数， 通过use 引用传参到函数体内
                    if(!is_numeric($sumLimitTime) || $sumLimitTime < 0) $sumLimitTime = 0;
                    if(!is_numeric($sumLimitLockTime) || $sumLimitLockTime < 0) $sumLimitLockTime = $sumLimitTime;

                    if(!is_numeric($sumLimitLockNum) || $sumLimitLockNum <= 0) $sumLimitLockNum = 1;

                    // if(!is_callable($sumLimitingFun)) continue;// 没有要执行的操作
                    if(!is_callable($sumLimitedFun)) continue;// 没有要执行的操作

                    // 判断是否已经锁定
                    $temsSumLockInfoKey = $redisKeySumLockInfo . ':' . $lockKey;
                    $sumLockedInfo = Tool::getRedis($temsSumLockInfoKey, 1);

                    if($sumLockedInfo && RedisString::exists($temsSumLockInfoKey)) {
                        $lockTimeSumTem = $sumLockedInfo['lockTime'];// 锁定时间 如：2020-06-01 09:23:34
                        $unlockTimeSumTem = $sumLockedInfo['unlockTime'];// 解锁时间 如：2020-06-01 09:43:59 ；空值：无期限锁定
                        $lockExpireSumTem = $sumLockedInfo['lockExpire'];// 锁定秒数 >0 或 0：无期限锁定
                        $lockErrNumSumTem = $sumLockedInfo['lockErrNum'];// 连续失败次数 加锁时，连续失败次数
                        // 每次锁定时执行的具体操作--不再执行后面代码；返回值：无；无参函数， 通过use 引用传参到函数体内

                        //  1:不执行后面代码--成功；2默认 错误(报错) 原代码中直接throws()[默认]4: 还要继续执行后面代码；8 自定义方法中错误(报错) 直接throws()
                        $sumDoResult = 2;
                        if (is_callable($sumLimitedFun)) {
                            $sumDoResult = $sumLimitedFun($infoConfig, $lockSumInfoArr, $sumLockedInfo);
                        }
                        if($sumDoResult === 1 || $sumDoResult === '1') return true;
                        if($sumDoResult === 4 || $sumDoResult === '4') continue;

                        $temSumLockErrTxt = '';// {10分钟内}
                        $temSumLockedBack = '';// {已锁定。}/ {锁定【20分钟2秒】，请稍后重试！【2020-06-01 09:43:59】！}
                        // 锁定秒数 >0 或 0：无期限锁定
                        if($sumLimitTime > 0)  $temSumLockErrTxt =  Tool::formatSecondNum($sumLimitTime) . '内';// 10分钟内
                        if($lockExpireSumTem > 0)  {
                            $temSumLockedBack .= '封存【' . Tool::formatSecondNum($lockExpireSumTem) . '】，请稍后';
                            if(strlen($lockExpireSumTem) > 0) $temSumLockedBack .= '【' . $unlockTimeSumTem . '】';
                            $temSumLockedBack .= '重试！';
                        }else{
                            $temSumLockedBack .= '已封存。';
                        }

                        // old 登录时：在【2020-06-01 09:23:34】{10分钟内}已连续锁5次，{已封存。}/ {封存【20分钟2秒】，请稍后重试！【2020-06-01 09:43:59】！}
                        // now 登录时：{10分钟内}已连续锁5次，{已封存。}/ {封存【20分钟2秒】，请稍后【2020-06-01 09:43:59】重试！！}
                        throws($tishiPre . '' .  $temSumLockErrTxt . '已连续锁' . $lockErrNumSumTem . '次，' .$temSumLockedBack);// 在【' . $lockTimeSumTem . '】
                    }
                }
            }

            // 判断是否已经锁定
            $lockedInfo = Tool::getRedis($redisKeyLockInfo, 1);
            if($lockedInfo && RedisString::exists($redisKeyLockInfo)){
                $lockTimeTem = $lockedInfo['lockTime'];// 锁定时间 如：2020-06-01 09:23:34
                $unlockTimeTem = $lockedInfo['unlockTime'];// 解锁时间 如：2020-06-01 09:43:59 ；空值：无期限锁定
                $lockExpireTem = $lockedInfo['lockExpire'];// 锁定秒数 >0 或 0：无期限锁定
                $lockErrNumTem = $lockedInfo['lockErrNum'];// 连续失败次数 加锁时，连续失败次数
                // 每次锁定时执行的具体操作--不再执行后面代码；返回值：无；无参函数， 通过use 引用传参到函数体内
                if(is_callable($lockedFun)){
                    $lockedFun($infoConfig, $lockedInfo);
                }
                $temLockErrTxt = '';// {10分钟内}
                $temLockedBack = '';// {已锁定。}/ {锁定【20分钟2秒】，请稍后重试！【2020-06-01 09:43:59】！}
                // 锁定秒数 >0 或 0：无期限锁定
                if($unitTime > 0)  $temLockErrTxt =  Tool::formatSecondNum($unitTime) . '内';// 10分钟内
                if($lockExpireTem > 0)  {

                    $temLockedBack .= '锁定【' . Tool::formatSecondNum($lockExpireTem) . '】，请稍后';
                    if(strlen($unlockTimeTem) > 0) $temLockedBack .= '【' . $unlockTimeTem . '】';
                    $temLockedBack .= '重试！';
                }else{
                    $temLockedBack .= '已锁定。';
                }

                // old 登录时：在【2020-06-01 09:23:34】{10分钟内}已连续失败5次，{已锁定。}/ {锁定【20分钟2秒】，请稍后重试！【2020-06-01 09:43:59】！}
                // now 登录时：{10分钟内}已连续失败5次，{已锁定。}/ {锁定【20分钟2秒】，请稍后【2020-06-01 09:43:59】重试！！}
                throws($tishiPre . '' .  $temLockErrTxt . '已连续失败' . $lockErrNumTem . '次，' .$temLockedBack, 222);// 在【' . $lockTimeTem . '】
            }


            // 没有锁定，则执行操作
            $resultDo = true;
            if(!$executedDdFun && is_callable($doFun)){
                $executedDdFun = true;
                $resultDo = $doFun($infoConfig);
            }

            // 成功，则继续执行下一个判断，失败，则加上锁
            if($resultDo){
                // 释放各种Redis
                Tool::delRedis($redisKeyErrNumInfo);// -记录出错次数
                Tool::delRedis($redisKeyLockInfo);// -锁redis
                continue;// 继续下一个判断
            }

            // 已下是执行失败逻辑

            $currentTime = Carbon::now()->toDateTimeString();// date('Y-m-d H:i:s');//当前时间 2020-06-02 15:48:49

            // 连续失败的次数
            // $failednNum = 0;
            // $failednNum++;
            $failednNum = RedisString::numIncr($redisKeyErrNumInfo, $unitTime, 1, 1);
            // 设置连续失败次数

            if($failednNum < $errNum){// 失败次数还没有达到锁定
                $temTimeIn = ''; // {10分钟内}
                if($unitTime > 0) $temTimeIn = Tool::formatSecondNum($unitTime) . '内';
                // 连续7次失败，将会锁定20分钟！
                $temLockBackTxt = '连续' . $errNum . '次失败，将会锁定';
                if($lockTime > 0) $temLockBackTxt .= Tool::formatSecondNum($lockTime);
                $temLockBackTxt .= '！';
                // old 登录时：在【2020-06-01 09:23:34】{10分钟内}已连续失败5次，连续7次失败，将会锁定｛20分钟｝！
                // new 登录时： {10分钟内}已连续失败5次，连续7次失败，将会锁定｛20分钟｝！
                throws( $tishiPre . '' . $temTimeIn . '已连续失败' . $failednNum . '次，' .$temLockBackTxt,2222);// 在【' . $currentTime . '】
            }

            // 失败，超指定次数--加锁--清除缓存错误计数 -- 封存计数（并判断是否封存：如果封存-清除封存计数）
            $endTime = '';
            if($lockTime > 0) $endTime = (Carbon::now())->addSeconds($lockTime)->toDateTimeString();
            $lockMsg = [
                'lockTime' => $currentTime,// 锁定时间 如：2020-06-01 09:23:34
                'unlockTime' => $endTime,// 解锁时间 如：2020-06-01 09:43:59 ；空值：无期限锁定
                'lockExpire' => $lockTime,// 锁定秒数 >0 或 0：无期限锁定
                'lockErrNum' => $failednNum// 连续失败次数 加锁时，连续失败次数
            ];
            // 加锁
            Tool::setRedis('', $redisKeyLockInfo, $lockMsg, $lockTime, 1);

            if(is_callable($lockingFun)){
                $lockingFun($infoConfig, $lockMsg, $failednNum);
            }
            // 清除记错误次数Redis
            Tool::delRedis($redisKeyErrNumInfo);// -记录出错次数

            // 连续

            // old 登录时：在【2020-06-01 09:23:34】{10分钟内}已连续失败5次，被锁定｛20分钟，请稍后再试【' . $endTime . '】｝！
            // new 登录时：{10分钟内}已连续失败5次，被锁定｛20分钟，请稍后【' . $endTime . '】再试｝！
            $temlockingTimeIn = '';
            if($unitTime > 0) $temlockingTimeIn = Tool::formatSecondNum($unitTime) . '内';
            $temLockingBack = '被锁定';
            if($lockTime > 0 && strlen($endTime) > 0) $temLockingBack .= Tool::formatSecondNum($lockTime) . '请稍后【' . $endTime . '】再试';
            $temLockingBack .= '！';
            throws( $tishiPre . '' . $temlockingTimeIn . '已连续失败' . $failednNum . '次，' . $temLockingBack);// 在【' . $currentTime . '】
        }
        return true;
    }

    /**
     * --人工或手动或管理人员解除锁定--清空锁缓存
     *
     * @param string $doKey 操作的关键字--要求唯一  如：login
     * @param array/string $limitConfig 限定配置信息 一维或二维数组 或json字符串
     * @return  mixed sting 具体错误(验证不通过) ； throws 错误 true:操作都成功 ; false:有失败，但不处理
     * @author zouyan(305463219@qq.com)
     */
    public static function clearLimit($doKey = '', $limitConfig = []){
        // 如果是json字符串，则转为数组
        jsonStrToArr($limitConfig , 1, '配置参数有误！');

        if(empty($limitConfig) || !is_array($limitConfig)) return throws('配置参数有误！');

        // 如果是一维数组,则转为二维数组
        Tool::isMultiArr($limitConfig, true);

        // 相关的redis key
        // 记录连续出错的次数 key
        $redisKeyErrNum = $doKey . ':errNum';
        // 锁的redis  key
        $redisKeyLock = $doKey . ':locked';
        $usedCacheKeys = [];// 已经用过的缓存key
        foreach($limitConfig as $k => $infoConfig){
            $cacheKey = $infoConfig['cacheKey'] ?? $k;// 整个二维数组中-唯一，作为缓存键的名称部分的
            $unitTime = $infoConfig['unitTime'];// 单位时间内 单位:秒，如10分钟 ，可以为零：代表所有时间内
//            if(!is_numeric($unitTime) || $unitTime <= 0) $unitTime = 0;
//            // $doFun = $infoConfig['doFun'];// 具体执行什么操作，返回值 true:成功；false:失败 ；无参函数， 通过use 引用传参到函数体内
//            $errNum = $infoConfig['errNum'];// 连续出错次数, 最小为1（> 0）
//            if(!is_numeric($errNum) || $errNum <= 0) $errNum = 1;
//            $lockTime = $infoConfig['lockTime'];// 锁定时长，单位:秒; 如果值<0 ，则值为 unitTime 的值, 如果真为0(unitTime 也为 0时)，代表永久锁定
//            if(!is_numeric($lockTime) || $lockTime <= 0) $lockTime = $unitTime;

//            $lockingFun = $infoConfig['lockingFun'];// 每次锁定时执行的具体操作--不再执行后面代码；返回值：无；无参函数， 通过use 引用传参到函数体内
//            $lockedFun = $infoConfig['lockedFun'];// 每次已锁定状态时执行的具体操作--不再执行后面代码；返回值：无；无参函数， 通过use 引用传参到函数体内
//            $lockNums = $infoConfig['lockNums'];// // 二维数组 为空，则不做累积锁定限定
            static::getCacheKey($usedCacheKeys, $cacheKey, $k, $unitTime);

            // 记录连续出错的次数 key
            $redisKeyErrNumInfo = $redisKeyErrNum . ':' . $cacheKey;
            // 锁的redis  key
            // [ 'lockTime' => '锁定时间', 'unlockTime' => '解锁时间', 'lockExpire' => '锁定秒数', 'lockErrNum' => '连续失败次数']
            $redisKeyLockInfo = $redisKeyLock . ':' . $cacheKey;

            // 释放各种Redis
            Tool::delRedis($redisKeyErrNumInfo);// -记录出错次数
            Tool::delRedis($redisKeyLockInfo);// -锁redis
        }
        return true;
    }

    /**
     * --获得缓存的唯一下标key值,并加入已使用数组
     *
     * @param array $usedCacheKeys 一维数组，已经使用过的 key ，用来判断是否重了
     * @param string $cacheKey 当前的Key
     * @param int $k 当前记录的下标
     * @param int $unitTime  单位时间内 单位:秒，如10分钟 ，可以为零：代表所有时间内
     * @return  null
     * @author zouyan(305463219@qq.com)
     */
    public static function getCacheKey(&$usedCacheKeys, &$cacheKey, $k, $unitTime){

        if(in_array($cacheKey, $usedCacheKeys)){
            if(!in_array($k, $usedCacheKeys)){
                $cacheKey = $k;
            }else if(!in_array($cacheKey . $k, $usedCacheKeys)){
                $cacheKey = $cacheKey . $k;
            }else if(!in_array( $k . $cacheKey, $usedCacheKeys)){
                $cacheKey = $k . $cacheKey;
            }else if(!in_array( $cacheKey . $k . $unitTime, $usedCacheKeys)){
                $cacheKey = $cacheKey . $k . $unitTime;
            }else if(!in_array( $cacheKey . $unitTime . $k, $usedCacheKeys)){
                $cacheKey = $cacheKey . $unitTime . $k;
            }else{
                throws('缓存下标已存在！请重新设置！');
            }

        }
        array_push($usedCacheKeys, $cacheKey);
    }


}
