<?php


namespace App\Services\Signer;

// 批理发号器 --不浪费【】
// 当待使用量< ,释放占用超时的，去掉真实使用的，并装满【批量生成】
use App\Services\Redis\RedisString;
use App\Services\Tool;

class BathSigner
{

    // 参数同下面的方法 getVals，只是 $valNums 可以 > $maxValNum：分批获取
    // --- 用这个，超过缓存中的数量，会分批获取并合并
    public static function getValsBath($redisKeyPre, $redisKey, $valNums = 1, $maxValNum = 10, $minAvailableNum = 3
        , $valType = 256, $fixParams = [], $overTime = 60, $noCacheFun = null, $judgeUsedFun = null){
        $cacheValArr = [];
        if($valNums <= $maxValNum){
            $cacheValArr = static::getVals($redisKeyPre, $redisKey, $valNums, $maxValNum, $minAvailableNum
                , $valType, $fixParams, $overTime, $noCacheFun, $judgeUsedFun);
        }else{
            $loop = ceil($valNums / $maxValNum);// 向上取整
            for($i = 0; $i < $loop; $i++){
                $valTemNums = $maxValNum;
                if(($i + 1) == $loop){// 最后一批
                    $valTemNums = $valNums - ($i * $maxValNum);
                }
                $temCacheValArr = static::getVals($redisKeyPre, $redisKey, $valTemNums, $maxValNum, $minAvailableNum
                    , $valType, $fixParams, $overTime, $noCacheFun, $judgeUsedFun);
                $cacheValArr = array_merge($cacheValArr, $temCacheValArr);
            }
        }
        return $cacheValArr;
    }

    /**
     * 获得可以使用的自增号值数组
     * ---不建议直接有这个方法，用上面的方法  getValsBath
     * @param string  $redisKeyPre 缓存键前缀
     * @param string $redisKey 缓存键
     * @param int $valNums 需要获得值的数量 默认 1 ; -- 不要超过 $maxValNum 参数
     * @param int $maxValNum  最大的可用记录数量 默认 10-- 缓存中最多可用的数量
     * @param int $minAvailableNum 记录可用数量最低数量，超过这个数就需要自动填满  默认 3---单次最多获取数量 -- 不要超过 $maxValNum 参数
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
     * @param int $overTime  占用的超时时间 --单位秒  默认:60秒
     * @param mixed $noCacheFun 没有redis缓存时的闭包函数 没有参数 ；返回当前已经使用的最大值--如果都没有使用过，则返回 0 ; 只有 $valType： 2 256 会用此方法
     *  function(){
     *      $val = 0;
     *     // 读取已经使用的最大值
     *     $val =  ...;
     *     return $val
     * }
     * @param mixed $judgeUsedFun 对占用的值，判断是否已经真的使用 参数 $judgeUsedValArr 格式 [值1，值2，值3,...]  返回值数组 ['值1' => true, '值2' => false,...] true代表已经使用 ；false 代表未使用
     * function($judgeUsedValArr){ return ['值1' => true, '值2' => false,...] ;}
     * @return array 获得的值数组 [值1，值2,...]
     */
   public static function getVals($redisKeyPre, $redisKey, $valNums = 1, $maxValNum = 10, $minAvailableNum = 3
       , $valType = 256, $fixParams = [], $overTime = 60, $noCacheFun = null, $judgeUsedFun = null){
       // 都不能<=0
       if($maxValNum <= 0 ) $maxValNum = 1;
       if($valNums <= 0) $valNums = 1;
       if($minAvailableNum <= 0) $minAvailableNum = 1;

       // 需要获得的数量及 最低保留数量  不能 > 最大缓存记录数量
       if($minAvailableNum > $maxValNum) $minAvailableNum = $maxValNum;
       if($valNums > $maxValNum) $valNums = $maxValNum;

       if(!is_numeric($overTime) || $overTime <= 0) $overTime = 10;

       $vals = Tool::lockDoSomething('limit:' . Tool::getUniqueKey([Tool::getProjectKey(1, ':', ':')
               , __CLASS__, __FUNCTION__, $redisKeyPre, $redisKey]),
           function()  use(&$redisKeyPre, &$redisKey, &$valNums, &$maxValNum, &$minAvailableNum, &$noCacheFun, &$judgeUsedFun
               , &$valType, &$cachePartKey, &$fixParams, &$overTime){//
               $operateRedis = 1;
               $usedMaxVal = 0;
               $cacheArr = [];
               $useVals = [];
               if(!RedisString::exists($redisKeyPre . $redisKey)){// 不存在--初始化缓存数据
                   if(in_array($valType, [2, 256]) && is_callable($noCacheFun)){
                       $usedMaxVal = $noCacheFun();
                   }
                   // 缓存追加新的值
                   static::appendNewCacheVal($cacheArr, $usedMaxVal + 1, $maxValNum, $valType, $redisKeyPre . $redisKey, $fixParams);
               }else{// 存在
                   $cacheArr = Tool::getRedis($redisKeyPre. $redisKey, $operateRedis);
                   $availableNum = static::getAvailableNum($cacheArr);
                   if($availableNum < $valNums || $availableNum <= $minAvailableNum){// 不够用 或 到设置的数量
                       // 对已经处理的进行移除或超时的进行回收【重复利用】
                       // 数量还不够，则追加新的值
                       $cacheFormatArr = Tool::arrUnderReset($cacheArr, 'status', 2, '_');// 转为参数代码为下标的数组
                       $cahceUsedArr = $cacheFormatArr[2] ?? [];// 已占用 的 记录
                       if(!empty($cahceUsedArr)){
                           $judgeUsedValArr = Tool::getArrFields($cahceUsedArr, 'val');
                           // 格式 ['val值' => true, 'val值' => false]-- true代表已经使用 ；false 代表未使用
                           $judgeUsedFormatArr = [];
                           if(is_callable($judgeUsedFun)){
                               $judgeUsedFormatArr = $judgeUsedFun($judgeUsedValArr);
                           }
                           $nowTime = date('Y-m-d H:i:s');
                           $hasChange = false;// 是否有改动 true:有改动 ；false:无改动
                           foreach ($cacheArr as $t_k => &$v){
                               $val = $v['val'];
                               if(!isset($judgeUsedFormatArr[$val])) continue;
                               $usedBoolean = $judgeUsedFormatArr[$val];
                               if($usedBoolean){
                                   unset($cacheArr[$t_k]);// 已使用，则清除掉
                                   $hasChange = true;
                                   continue;
                               }

                               // 下面都是待使用的

                               // 判断是否超时
                               $used_data_time = $v['used_data_time'];
                               // 开始时间
                               $begin_date_unix = judgeDate($used_data_time);
                               if($begin_date_unix === false){// 不是日期格式
                                   $v['status'] = 1;
                                   $v['used_data_time'] = null;
                                   $hasChange = true;
                                   continue;
                               }
                               // 超时
                               if(Tool::diffDate($used_data_time, $nowTime, 1, '时间', 1) > $overTime) {
                                   $v['status'] = 1;
                                   $v['used_data_time'] = null;
                                   $hasChange = true;
                                   continue;
                               }
                           }
                           if($hasChange){
                               // 再次获得可使用的数量及判断是否追加记录
                               $availableNum = static::getAvailableNum($cacheArr);
                               $cacheArr = array_values($cacheArr);
                           }
                       }

                       if($availableNum < $maxValNum){// 新加新的可用值--填满
                           // $usedMaxVal = $cacheArr[count($cacheArr) - 1]['val'];// 获得最大的值
                           $usedMaxVal = $cacheArr[count($cacheArr) - 1]['valint'] ?? 0;// 获得最大的值

                           // 缓存追加新的值
                           static::appendNewCacheVal($cacheArr, $usedMaxVal + 1, $maxValNum - $availableNum, $valType, $redisKeyPre . $redisKey, $fixParams);
                       }

                   }
               }
               $useVals = static::getWaitUseVals($cacheArr, $valNums);
               // 缓存数据
               Tool::setRedis($redisKeyPre, $redisKey, $cacheArr, 0 , $operateRedis);
               // return $useVals;
               return $useVals;
           }, function($errDo){
               // TODO
               $errMsg = '失败，请稍后重试!';
               if($errDo == 1) throws($errMsg);
               return $errMsg;
           }, true, 1, 2000, 2000);
       return $vals;
   }

    /**
     * 缓存追加新的值
     * @param array  $cacheArr 缓存的值数组
     * @param int $startVal 新的起始值-- $valType 为自增时的启始值
     * @param int $valNum 需要新缓存的数量
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
     * @param string $cachePartKey  关键字 --缓存键中的标识不同的模块的；如 'orderRefund'
     * @param array $fixParams 前中后缀，默认都为空，不用填
     * [
     *  'prefix' => '',// 前缀[1-2位] 可填;可写业务编号等
     *  'midFix' => '',// 日期后中间缀[1-2位] 可填;适合用户编号里的后2位或其它等
     *  'backfix' => '',// 后缀[1-2位] 可填;备用
     * ]
     * @return null
     */
   public static function appendNewCacheVal(&$cacheArr, $startVal = 1, $valNum = 10, $valType = 256, $cachePartKey = '', $fixParams = []){
       $nowTime = date('Y-m-d H:i:s');
       for($i = 0; $i < $valNum; $i++){
           if(in_array($valType, [2,256])){
               $valint = $startVal + $i;
               $newVal = ($fixParams['prefix'] ?? '') . $valint . ($fixParams['backfix'] ?? '');
           }else{
               $newVal = Signer::getNewVal($valType, $cachePartKey, $fixParams);
               $valint = $newVal;
           }
           $newValArr = [
               'val' => $newVal,
               'valint' => $valint,
               'status' => 1, // 1待使用  2已占用
               'create_time' => $nowTime,// 创建时间
               'used_data_time' => null,// 占用时间
           ];
           array_push($cacheArr, $newValArr);
           // $cacheArr[$newVal] = $newValArr;
       }

   }

    /**
     * 获得需要使用的值数组，并修改值数组中对应的状态
     * @param array  $cacheArr 缓存的值数组
     * @param int $valNum 需要新缓存的数量
     * @return null
     */
   public static function getWaitUseVals(&$cacheArr, $valNum = 5){
       $vals = [];
       $nowTime = date('Y-m-d H:i:s');
       foreach($cacheArr as &$v){
           $status = $v['status'] ?? 2;
           if($status != 1) continue;
           $val = $v['val'];
           array_push($vals, $val);
           $v['status'] = 2;
           $v['used_data_time'] = $nowTime;
           $valNum--;
           if($valNum <= 0) break;
       }
       return $vals;
   }
    /**
     * 获得可用的数量
     * @param array  $cacheArr 缓存的值数组
     * @return int 可用的数量
     */
    public static function getAvailableNum(&$cacheArr){
        $availableNum = 0;
        foreach($cacheArr as &$v){
            $status = $v['status'] ?? 2;
            if($status != 1) continue;
            $availableNum++;
        }
        return $availableNum;
    }
}
