<?php
// 通用工具服务类-- 操作数据库
namespace App\Services\DB;

use App\Services\Redis\RedisString;
use App\Services\Signer\BathSigner;
use App\Services\Signer\Signer;
use App\Services\Tool;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * 通用工具服务类-- 操作数据库
 */
class CommonDB
{

    // 主键值字符串  主键字段1的值1 小分隔符 主键字段1的值2 ... 大分隔符  主键字段2的值1 小分隔符 主键字段2的值2 ...
    public static $bigSplit = '@@!';// $bigSplit 主键值大分隔符
    public static $smallSplit = ',';// 主键值小分隔符
    public static $transactionNum = 0;// 正在进行的事务的数量：进入事务自+1,完成一个事务自-1
    public static $transactionHasErr = false;// 事务是否有错，true:有错 ；false:无错
    public static $changeDBTables = [];// 事务中操作过的数据表及单条记录
// 格式 二维的
//        [
//            'tableObj' => Tool::copyObject($modelObj),
//            'params' => [$operateType, $recordData, 1]
//        ]

    //写一些通用方法
    /*
    public static function test(){
        echo 'aaa';die;
    }
    */

    // 所有的事务处理方法--会先加上锁
    // 参数看 doTransactionFun 方法
    // $lockKeyArr 具体调用方的特别关键锁数组 --一维数组 [ __CLASS__, __FUNCTION__, ..其它锁关键数据.]---此参数为空，则不使用锁
    public static function doLockTransactionFun($doFun, $lockKeyArr = [], $default_result = '', $rollBackFun = '', $finallyFun = ''){

        if(empty($lockKeyArr) || !is_array($lockKeyArr)) return static::doTransactionFun($doFun, $default_result, $rollBackFun, $finallyFun);

        $lockKey = Tool::getUniqueKey(array_merge([Tool::getProjectKey(1, ':', ':')], $lockKeyArr));
        return Tool::lockDoSomething('lock:' . $lockKey,
            function()  use(&$doFun, &$default_result, &$rollBackFun, &$finallyFun){//
                return static::doTransactionFun($doFun, $default_result, $rollBackFun, $finallyFun);
            }, function($errDo){
                // TODO
                $errMsg = '失败，请稍后重试!';
                if($errDo == 1) throws($errMsg);
                return $errMsg;
            }, true, 1, 2000, 2000);
    }

    /**
     * 所有的事务处理方法
     *
     * @param mixed $doFun 事务需要执行的匿名函数--无参数
     * @param mixed $default_result 默认返回值
     * @param mixed $rollBackFun 事务失败需要执行的匿名函数--无参数
     * @param mixed $finallyFun 事务无论成功失败都需要执行的匿名函数--无参数
     * @return mixed 可以返回匿名函数返回的值
     * @author zouyan(305463219@qq.com)
     */
    public static function doTransactionFun($doFun, $default_result = '', $rollBackFun = '', $finallyFun = ''){
        $return_result = $default_result;
        DB::beginTransaction();
        try {
          if(static::$transactionNum == 0){
              static::$transactionHasErr = false;
              static::$changeDBTables = [];
          }
          static::$transactionNum++;
            // 对缓存中的数据处理下---判断缓存是否失效
            if(is_callable($doFun)){
                $return_result = $doFun();
            }
            DB::commit();
        } catch ( \Exception $e) {
            static::$transactionHasErr = true;
            DB::rollBack();
            $errStr = $e->getMessage();
            $errCode = $e->getCode();
            if(is_callable($rollBackFun)){
                $rollBackFun();
            }
            throws($errStr, $errCode);
            // throws($e->getMessage());
        } finally {
            static::$transactionNum--;
            // 事务有错且已经到最外层事务---重新更新刚才处理过的数据缓存
            if(static::$transactionNum < 0){
                static::$transactionNum = 0;
                // static::$changeDBTables = [];
            }
            if(static::$transactionHasErr && static::$transactionNum == 0){
                // 重新更新缓存时间
                // echo '执行重新更新缓存时间：' . json_encode(static::$changeDBTables);
//                foreach(static::$changeDBTables as $k => $v){
//                    $tableObj = $v['tableObj'];
//                    $params = $v['params'];
//                    if(is_object($tableObj)){
//                        $tableObj->operateDbUpdateTimeCache(...$params);
//                    }
//                }
//                static::$changeDBTables = [];// 操作完--置空
                  $while_no = 1;
                  // 一次失败，则会重试2次
                  while(!empty(static::$changeDBTables) && $while_no <=3){
                      static::rollBackUpdateTimeCache();
                      $while_no++;
                  }
                  // 还有没执行的，则打个错误日志并发个错误邮件
                  if(!empty(static::$changeDBTables)){
                      Log::error('事务失败[回滚]重新更新记录缓存时间失败日志',static::$changeDBTables);
                  }
                  static::$changeDBTables = [];// 操作完--置空
            }
            if(is_callable($finallyFun)){
                $finallyFun();
            }
        }
        return $return_result;
    }

    // 事务回退时，重新更新操作过的记录的缓存时间
    public static function rollBackUpdateTimeCache(){
        // 重新更新缓存时间
        // echo '执行重新更新缓存时间：' . json_encode(static::$changeDBTables);
        foreach(static::$changeDBTables as $k => $v){
            try {
                $tableObj = $v['tableObj'];
                $params = $v['params'];
                if(is_object($tableObj)){
                    $tableObj->operateDbUpdateTimeCache(...$params);
                }
                unset(static::$changeDBTables[$k]);
            } catch ( \Exception $e) {// 错误捕获，但是不处理--为了重试2次
                // throws($e->getMessage(), $e->getCode());
                // throws($e->getMessage());
            }
        }
        // static::$changeDBTables = [];// 操作完--置空
    }



    // 根据数据模型名称，反回对象
    public static function getObjByModelName($modelName, &$modelObj = null){
        if (! is_object($modelObj)) {
            $className = "App\\Models\\" . $modelName;
            if (!class_exists($className)) {
                throws('参数[Model_name]不正确！');
            }
            $modelObj = new $className();
        }
        return $modelObj;
    }


    /**
     * 解析sql条件
     *
     * @param object &$tbObj
     * @param string $params array || json string
     * @author zouyan(305463219@qq.com)
     */
    public static function resolveSqlParams(&$tbObj ,$params = [])
    {
        if (empty($params) ) {
            return $tbObj;
        }
        if (jsonStrToArr($params , 2, '') === false){
            return $tbObj;
        }
        foreach($params as $key => $param){
            switch($key){
                case 'select':   // 使用一维数组
                    // 查询（Select）--
                    // select('name', 'email as user_email')->get();
                    // ->select(['id','company_id','phonto_name']);
                    if (! empty($param)){
                        $tbObj = $tbObj->select($param);
                    }
                    break;
                case 'addSelect': // 单个字段的字符
                    // 添加一个查询列到已存在的 select 子句，可以使用 addSelect 方法
                    // addSelect('age')->get();
                    if ( (! empty($param)) && is_string($param)){
                        $tbObj = $tbObj->addSelect($param);
                    }
                    break;
                case 'distinct': // 空字符
                    // distinct 方法允许你强制查询返回不重复的结果集
                    // $users = DB::table('users')->distinct()->get();
                    // 或指定具体的字段 ->distinct('id')  参数$param为 字段名称
                    if(!empty($param)){
                        $tbObj = $tbObj->distinct($param);
                    }else{
                        $tbObj = $tbObj->distinct();
                    }
                case 'where': //使用如下的二维数组.注意，如果是=,第二个参数可以不需要
                    /*[
                            ['status', '=', '1'],
                            ['subscribed', '<>', '1'],
                            ['id', '&' , '16=16'],
                        ]
                    */
                    // Where 子句
                    // ->where('id', '&', '16=16')
                    // ->where('votes', '=', 100)
                    // ->where('votes', 100)
                    /* ->where([
                        ['status', '=', '1'],
                            ['subscribed', '<>', '1'],
                        ])
                    */
                    if ( (! empty($param)) && is_array($param)){
                        $tbObj = $tbObj->where($param);
                    }
                    break;
                case 'orWhere':// orWhere  子句
                    if ( (! empty($param)) && is_array($param)){
                        $tbObj = $tbObj->orWhere($param);
                    }
                    break;
                case 'whereDate': // 同where
                    // whereDate 方法用于比较字段值和日期
                    // ->whereDate('created_at', '2016-10-10')
                    if ( (! empty($param)) && is_array($param)){
                        $tbObj = $tbObj->whereDate($param);
                    }
                    break;
                case 'whereMonth':// 同where
                    // whereMonth 方法用于比较字段值和一年中的指定月份
                    // ->whereMonth('created_at', '10')
                    if ( (! empty($param)) && is_array($param)){
                        $tbObj = $tbObj->whereMonth($param);
                    }
                    break;
                case 'whereDay':// 同where
                    // whereDay 方法用于比较字段值和一月中的指定日期
                    // ->whereDay('created_at', '10')
                    if ( (! empty($param)) && is_array($param)){
                        $tbObj = $tbObj->whereDay($param);
                    }
                    break;
                case 'whereYear':// 同where
                    // whereYear 方法用于比较字段值和指定年
                    // ->whereYear('created_at', '2017')
                    if ( (! empty($param)) && is_array($param)){
                        $tbObj = $tbObj->whereYear($param);
                    }
                    break;
                case 'whereTime':// 同where
                    // whereTime 方法用于比较字段值和指定时间
                    // ->whereTime('created_at', '=', '11:20')
                    if ( (! empty($param)) && is_array($param)){
                        $tbObj = $tbObj->whereTime($param);
                    }
                    break;
                case 'whereBetween':// 数组 [1, 100]
                    // whereBetween  子句
                    // ->whereBetween('votes', [1, 100])
                    if ( (! empty($param)) && is_array($param)){
                        foreach($param as $betweenField => $rangeValsArr){
                            if(!is_array($rangeValsArr) || count($rangeValsArr) != 2) continue;
                            $tbObj = $tbObj->whereBetween($betweenField, $rangeValsArr);
                        }
                    }
                    break;
                case 'whereNotBetween':// 数组 [1, 100]
                    // whereNotBetween  子句
                    // ->whereNotBetween('votes', [1, 100])
                    if ( (! empty($param)) && is_array($param)){
                        foreach($param as $betweenField => $rangeValsArr){
                            if(!is_array($rangeValsArr) || count($rangeValsArr) != 2) continue;
                            $tbObj = $tbObj->whereNotBetween($betweenField, $rangeValsArr);
                        }
                    }
                    break;
                case 'whereIn': // 数组 [1, 2, 3] 二维数组 [ [字段名=>[多个字段值]],....]
                    // whereIn  子句
                    // ->whereIn('id', [1, 2, 3])
                    if ( (! empty($param)) && is_array($param)){
                        foreach($param as $field => $vals){
                            $tbObj = $tbObj->whereIn($field,$vals);
                        }
                    }

                    break;
                case 'whereNotIn':// 数组 [1, 2, 3] 二维数组 [ [字段名=>[多个字段值]],....]
                    // whereNotIn  子句
                    // ->whereNotIn('id', [1, 2, 3])
//                    if ( (! empty($param)) && is_array($param)){
//                        $tbObj = $tbObj->whereNotIn($param);
//                    }
                    if ( (! empty($param)) && is_array($param)){
                        foreach($param as $field => $vals){
                            $tbObj = $tbObj->whereNotIn($field,$vals);
                        }
                    }
                    break;
                case 'whereNull': // 字段字符 一维数组 ['字段名1',...]
                    // whereNull 方法验证给定列的值为 NULL
                    // ->whereNull('updated_at')
//                    if ( (! empty($param)) && is_string($param)){
//                        $tbObj = $tbObj->whereNull($param);
//                    }
                    if ( (! empty($param)) && is_array($param)){
                        foreach($param as $field ){
                            $tbObj = $tbObj->whereNull($field);
                        }
                    }
                    break;
                case 'whereNotNull':// 字段字符  一维数组 ['字段名1',...]
                    // whereNotNull 方法验证给定列的值不是 NULL
                    // ->whereNotNull('updated_at')
//                    if ( (! empty($param)) && is_string($param)){
//                        $tbObj = $tbObj->whereNotNull($param);
//                    }
                    if ( (! empty($param)) && is_array($param)){
                        foreach($param as $field ){
                            $tbObj = $tbObj->whereNotNull($field);
                        }
                    }
                    break;
                case 'whereColumn':// 同where -二维数组
                    // whereColumn 方法用于验证两个字段是否相等
                    // ->whereColumn('first_name', 'last_name')
                    // 还可以传递一个比较运算符到该方法
                    // ->whereColumn('updated_at', '>', 'created_at')
                    // 还可以传递多条件数组到 whereColumn 方法，这些条件通过 and 操作符进行连接
                    /*
                        ->whereColumn([
                            ['first_name', '=', 'last_name'],
                            ['updated_at', '>', 'created_at']
                        ])
                     */
                    if ( (! empty($param)) && is_array($param)){
                        $tbObj = $tbObj->whereColumn($param);
                    }
                    break;
                case 'orderBy':// 一维数组 ['name'=>'desc','name'=>'desc']
                    // orderBy 的第一个参数应该是你希望排序的字段，第二个参数控制着排序的方向 —— asc 或 desc
                    // ->orderBy('name', 'desc')
                    // ->orderBy('name', 'desc')
                    if ( (! empty($param)) && is_array($param)){
                        foreach($param as $orderField => $orderType){
                            $tbObj = $tbObj->orderBy($orderField,$orderType);
                        }

                    }
                    break;
                case 'latest':
                    // latest 和 oldest 方法允许你通过日期对结果进行排序，默认情况下，结果集根据 created_at 字段进行排序，或者，你可以按照你想要排序的字段作为字段名传入
                    // ->latest()
                    $tbObj = $tbObj->latest();
                    break;
                case 'oldest'://
                    $tbObj = $tbObj->oldest();
                    break;
                case 'inRandomOrder':// inRandomOrder 方法可用于对查询结果集进行随机排序，比如，你可以用该方法获取一个随机用户
                    $tbObj = $tbObj->inRandomOrder();
                    break;
                case 'groupBy':// 字段字符 或 一维数组 ['字段一','字段二']
                    // groupBy / having-对结果集进行分组
                    /*
                    ->groupBy('account_id')
                    ->having('account_id', '>', 100)
                    */
                    if ( (! empty($param)) && is_string($param)){
                        $tbObj = $tbObj->groupBy($param);
                    }else if(is_array($param)){
                        foreach($param as $groupField ){
                            $tbObj = $tbObj->groupBy($groupField);
                        }
                    }
                    break;
                case 'having':// 一维数组 [$havingField,$havingOperator,$havingValue]
                    if ( (! empty($param)) && is_array($param)){
                        $havingField = $param[0] ?? '';
                        $havingOperator = $param[1] ?? '';
                        $havingValue = $param[2] ?? '';
                        $tbObj = $tbObj->having($havingField, $havingOperator,$havingValue);
                    }
                    break;
                case 'skip': // 数字
                    // skip / take-限定查询返回的结果集的数目
                    // ->skip(10)->take(5)
                    if ( (! empty($param)) && is_numeric($param)){
                        $tbObj = $tbObj->skip($param);
                    }
                    break;
                case 'take':// 数字
                    if ( (! empty($param)) && is_numeric($param)){
                        $tbObj = $tbObj->take($param);
                    }
                    break;
                case 'limit':// 数字
                    // 为替代方法，还可以使用 limit 和 offset 方法
                    /*  ->offset(10)
                        ->limit(5)
                    */
                    if ( (! empty($param)) && is_numeric($param)){
                        $tbObj = $tbObj->limit($param);
                    }
                    break;
                case 'offset':// 数字
                    if ( (! empty($param)) && is_numeric($param)){
                        $tbObj = $tbObj->offset($param);
                    }
                    break;
                case 'find':// 单个数字 或 数组
                    // find 和 first 获取单个记录
                    // App\Flight::find(1);
                    // App\Flight::find([1, 2, 3]);
                    if ( (! empty($param))){
                        $tbObj = $tbObj->find($param);
                    }
                    break;
                case 'first':
                    // ->first();
                    $tbObj = $tbObj->first();
                    break;
                case 'findOrFail':// 单个数字 或 数组
                    // findOrFail 和 firstOrFail方法会获取查询到的第一个结果。不过，如果没有任何查询结果，Illuminate\Database\Eloquent\ModelNotFoundException 异常将会被抛出
                    if ( (! empty($param))){
                        $tbObj = $tbObj->findOrFail($param);
                    }
                    break;
                case 'firstOrFail':// bbb  子句
                    $tbObj = $tbObj->firstOrFail();
                    break;
                case 'value': // 字段名
                    // 不需要完整的一行，可以使用 value 方法从结果中获取单个值，该方法会直接返回指定列的值
                    // ->value('email');
                    if ( (! empty($param)) && is_string($param)){
                        $tbObj = $tbObj->value($param);
                    }
                    break;
                case 'pluck':// 字符 '字段名'或 ['字段名'] 或  ['别名'=>'字段名']
                    // 获取包含单个列值的数组，可以使用 pluck 方法
                    /*
                    $titles = DB::table('roles')->pluck('title');

                    foreach ($titles as $title) {
                        echo $title;
                    }

                    列值指定自定义键
                    ->pluck('title', 'name');

                    */
                    if ( (! empty($param)) && is_array($param)){
                        foreach($param as $k => $v){
                            if(is_string($k)){
                                $tbObj = $tbObj->pluck($v,$k);
                            }else{
                                $tbObj = $tbObj->pluck($v);
                            }
                        }
                    }else if( (! empty($param)) && is_string($param)){
                        $tbObj = $tbObj->pluck($param);
                    }

                    break;
                case 'count':// 获取聚合结果-  count, max, min, avg 和 sum
                    // ->count();
                    $tbObj = $tbObj->count();
                    break;
                case 'max':// 字段名
                    // ->max('price')
                    if ( (! empty($param)) && is_string($param)){
                        $tbObj = $tbObj->max($param);
                    }
                    break;
                case 'avg':// 字段名
                    // ->avg('price');
                    if ( (! empty($param)) && is_string($param)){
                        $tbObj = $tbObj->avg($param);
                    }
                    break;
                case 'sum':// bbb  子句
                    $tbObj = $tbObj->sum();
                    break;
                case 'exists':// 判断记录是否存在- exists 或 doesntExist 方法
                    // return DB::table('orders')->where('finalized', 1)->exists();
                    $tbObj = $tbObj->exists();
                    break;
                case 'doesntExist':// bbb  子句
                    // return DB::table('orders')->where('finalized', 1)->doesntExist();
                    $tbObj = $tbObj->doesntExist();
                    break;
                default:
            }

        }
        return $tbObj;
    }

    /**
     * 解析表关系
     *
     * @param object &$tbObj
     * @param string $relations array || json string
     * @return object
     * @author zouyan(305463219@qq.com)
     */
    public static function resolveRelations(&$tbObj ,$relations = [])
    {
        if (empty($relations) || empty($tbObj)) {
            return $tbObj;
        }

        if (jsonStrToArr($relations , 2, '') === false){
            return $tbObj;
        }


        // 层关系
        $tbObj->load($relations);
        return $tbObj;
    }

    /**
     * 获得记录-根据条件
     *
     * @param object $modelObj 当前模型对象
     * @param array $queryParams 查询条件   有count下标则是查询数量--是否是查询总数
     * @param array $relations 要查询的与其它表的关系
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @return object 数据对象
     * @author zouyan(305463219@qq.com)
     */
    public static function getList(&$modelObj, $queryParams, $relations, $isOpenCache = true){

        if(isset($queryParams['count']) ){// 查询总数
            $requestData = static::resolveSqlParams($modelObj, $queryParams);
        }else{
            // #####开始使用缓存数据功能#######开始#################################################################

            // 查询要获取的字段数组 主要用来判断要请求的字段是否包含排除字段 一维数组
            $select = $queryParams['select'] ?? [];// static::getCacheFields($modelObj, 1 | 2 | 4)
            // 缓存类型 1 单条记录缓存 2 块级缓存[默认]
            $cacheType = 2;
            // 缓存时对数据的操作  1 转为json 2 序列化 ; 3 不转换  ；如果是对象或数组请用2
            $operateRedis = 2;
            // 会作为缓存键的前缀标识   调用的方法名  一般用这个 __FUNCTION__
            $redisKeyPre = __FUNCTION__;
            // 会作为缓存键的参数数组- 相关的参数, 会md5(json_encode())
            $paramsRedisKey = [__CLASS__, __FUNCTION__, $queryParams];
            // 操作类型 1先读取缓存 ；2 获得数据前，是否再读一次缓存并判断是否有效[有效则直接返回，不从源数据读了]--防击穿，以及雪崩，穿透
            $operateType = 1 | 2;

            $requestData = static::getDataCache($modelObj,
                function(&$isOpenCache) use(&$modelObj, &$queryParams) {// 重新读取数据 注意一定要有返回值[缓存的就是这个值]
                    // 可以缓存时，对读取的字段，加入缓存相关的字段 --- 注意： 确定需要缓存才加入要缓存的字段
                    if($isOpenCache && isset($queryParams['select']) && !empty($queryParams['select'])){
                        $queryParams['select'] = array_merge($queryParams['select'], static::getCacheFields($modelObj, 1 | 2 | 4));
                    }

                    // 真正获得数据的代码
                    static::resolveSqlParams($modelObj, $queryParams);
                    $requestData = $modelObj->get();

                    // 返回
                    return $requestData;
                }, $redisKeyPre, $paramsRedisKey, $cacheType, $operateRedis, $queryParams, $select, $isOpenCache, $operateType);

            // #####开始使用缓存数据功能#######结束#################################################################

//            static::resolveSqlParams($modelObj, $queryParams);
//            $requestData = $modelObj->get();

            // 查询关系参数
            static::resolveRelations($requestData, $relations);
            // $requestData->load($relations);
        }
        return $requestData;
    }

    /**
     * 获得model所有记录--分批获取[推荐]
     *
     * @param object $modelObj 当前模型对象
     * @param array $queryParams 查询条件   有count下标则是查询数量--是否是查询总数
     * @param array $relations 要查询的与其它表的关系
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @return mixed 数据对象
     * @author zouyan(305463219@qq.com)
     */
    public static function getAllModelDatas(&$modelObj, $queryParams, $relations, $isOpenCache = true){
        /*
        $queryParams = [
            'where' => [
                // ['id', '1'],
                // ['phonto_name', 'like', '%知的标题1%']
            ],
            'orderBy' => ['id'=>'desc','company_id'=>'asc'],
        ];
        $relations = ['siteResources','CompanyInfo.proUnits.proRecords','CompanyInfo.companyType'];
        */
        // 有count下标则是查询数量--是否是查询总数
        if(isset($queryParams['count'])){
            if (isset($queryParams['count'])) unset($queryParams['count']);
            if (isset($queryParams['limit'])) unset($queryParams['limit']);
            if (isset($queryParams['offset'])) unset($queryParams['offset']);
            if (isset($queryParams['take'])) unset($queryParams['take']);
            if (isset($queryParams['skip'])) unset($queryParams['skip']);
            if (isset($queryParams['orderBy'])) {
                $limitParams['orderBy'] = $queryParams['orderBy'];
                unset($queryParams['orderBy']);
            }
            // 获得总数量

            // #####开始使用缓存数据功能#######开始#################################################################

            // 查询要获取的字段数组 主要用来判断要请求的字段是否包含排除字段 一维数组
            $select = $queryParams['select'] ?? [];// static::getCacheFields($modelObj, 1 | 2 | 4)
            // 缓存类型 1 单条记录缓存 2 块级缓存[默认]
            $cacheType = 2;
            // 缓存时对数据的操作  1 转为json 2 序列化 ; 3 不转换  ；如果是对象或数组请用2
            $operateRedis = 2;
            // 会作为缓存键的前缀标识   调用的方法名  一般用这个 __FUNCTION__
            $redisKeyPre = __FUNCTION__ . ':count';
            // 会作为缓存键的参数数组- 相关的参数, 会md5(json_encode())
            $paramsRedisKey = [__CLASS__, __FUNCTION__, $queryParams];
            // 操作类型 1先读取缓存 ；2 获得数据前，是否再读一次缓存并判断是否有效[有效则直接返回，不从源数据读了]--防击穿，以及雪崩，穿透
            $operateType = 1 | 2;

            $recordCount = static::getDataCache($modelObj,
                function(&$isOpenCache) use(&$modelObj, &$queryParams) {// 重新读取数据 注意一定要有返回值[缓存的就是这个值]
                    // 可以缓存时，对读取的字段，加入缓存相关的字段 --- 注意： 确定需要缓存才加入要缓存的字段
                    if($isOpenCache && isset($queryParams['select']) && !empty($queryParams['select'])){
                        $queryParams['select'] = array_merge($queryParams['select'], static::getCacheFields($modelObj, 1 | 2 | 4));
                    }

                    // 真正获得数据的代码
                    static::resolveSqlParams($modelObj, $queryParams);
                    $recordCount = $modelObj->count();

                    // 返回
                    return $recordCount;
                }, $redisKeyPre, $paramsRedisKey, $cacheType, $operateRedis, $queryParams, $select, $isOpenCache, $operateType);

            // #####开始使用缓存数据功能#######结束#################################################################
//            $recordCount = $modelObj->count();
            return $recordCount;
        }

        $limit = $queryParams['limit'] ?? 0;
        $offset = $queryParams['offset'] ?? 0;
        $isChunk = true;// 是否分批获取 true 分批获取，false:直接获取
        if($limit > 0 || $offset > 0){
            $isChunk = false;
        }

        $isChunk = false;// 暂时关闭批量获取功能

        if(!$isChunk || ($isChunk && empty($relations))){
            // #####开始使用缓存数据功能#######开始#################################################################

            // 查询要获取的字段数组 主要用来判断要请求的字段是否包含排除字段 一维数组
            $select = $queryParams['select'] ?? [];// static::getCacheFields($modelObj, 1 | 2 | 4)
            // 缓存类型 1 单条记录缓存 2 块级缓存[默认]
            $cacheType = 2;
            // 缓存时对数据的操作  1 转为json 2 序列化 ; 3 不转换  ；如果是对象或数组请用2
            $operateRedis = 2;
            // 会作为缓存键的前缀标识   调用的方法名  一般用这个 __FUNCTION__
            $redisKeyPre = __FUNCTION__ . ':dataList';
            // 会作为缓存键的参数数组- 相关的参数, 会md5(json_encode())
            $paramsRedisKey = [__CLASS__, __FUNCTION__, $queryParams];
            // 操作类型 1先读取缓存 ；2 获得数据前，是否再读一次缓存并判断是否有效[有效则直接返回，不从源数据读了]--防击穿，以及雪崩，穿透
            $operateType = 1 | 2;

            $requestData = static::getDataCache($modelObj,
                function(&$isOpenCache) use(&$modelObj, &$queryParams, &$isChunk, &$relations) {// 重新读取数据 注意一定要有返回值[缓存的就是这个值]
                    // 可以缓存时，对读取的字段，加入缓存相关的字段 --- 注意： 确定需要缓存才加入要缓存的字段
                    if($isOpenCache && isset($queryParams['select']) && !empty($queryParams['select'])){
                        $queryParams['select'] = array_merge($queryParams['select'], static::getCacheFields($modelObj, 1 | 2 | 4));
                    }

                    // 真正获得数据的代码
                    if ($isChunk) {// 在处理大量数据集合时能够有效减少内存消耗
                        // 查询条件
                        static::resolveSqlParams($modelObj, $queryParams);
                        $requestData = collect([]);
                        $modelObj->chunk(500, function ($flights) use (&$requestData, $relations) {
                            // static::resolveRelations($flights, $relations);
                            // $flights->load('siteResources');

                            $requestData= $requestData->concat($flights);
                            /*
                              foreach ($flights as $flight) {
                                  //
                              }
                            */
                        });
                    } else {
                        // 查询条件
                        static::resolveSqlParams($modelObj, $queryParams);
                        $requestData = $modelObj->get();
                        // 查询关系参数
                        // static::resolveRelations($requestData, $relations);
                        // $requestData->load($relations);

                        //return $infos;
                    }
                    // return $requestData;

                    // 返回
                    return $requestData;
                }, $redisKeyPre, $paramsRedisKey, $cacheType, $operateRedis, $queryParams, $select, $isOpenCache, $operateType);
            // #####开始使用缓存数据功能#######结束#################################################################
            // 不分断的，可以获得关系操作
            if(!$isChunk ) static::resolveRelations($requestData, $relations);
            return $requestData;
        }

        // 查询条件---有关系且分批获取
        static::resolveSqlParams($modelObj, $queryParams);
        $requestData = collect([]);
        $modelObj->chunk(500, function ($flights) use (&$requestData, $relations) {
            static::resolveRelations($flights, $relations);
            // $flights->load('siteResources');

            $requestData= $requestData->concat($flights);
            /*
              foreach ($flights as $flight) {
                  //
              }
            */
        });
        return $requestData;
        // 没有缓存之前的代码
//        if ($isChunk) {// 在处理大量数据集合时能够有效减少内存消耗
//            // 查询条件
//            static::resolveSqlParams($modelObj, $queryParams);
//            $requestData = collect([]);
//            $modelObj->chunk(500, function ($flights) use (&$requestData, $relations) {
//                 static::resolveRelations($flights, $relations);
//                // $flights->load('siteResources');
//
//                $requestData= $requestData->concat($flights);
//                /*
//                  foreach ($flights as $flight) {
//                      //
//                  }
//                */
//            });
//        } else {
//            // 查询条件
//            static::resolveSqlParams($modelObj, $queryParams);
//            $requestData = $modelObj->get();
//            // 查询关系参数
//             static::resolveRelations($requestData, $relations);
//            // $requestData->load($relations);
//
//            //return $infos;
//        }
//        return $requestData;
    }


    /**
     * 获得指定条件的多条数据
     *
     * @param int 选填 $page 当前页page [默认1]
     * @param int 选填 $pagesize 每页显示的数量 [默认10]
     * @param int 选填 $total 总记录数,优化方案：传<=0传重新获取总数[默认0];=-5:只统计条件记录数量，不返回数据
     * @param string 选填 $queryParams 条件数组/json字符 ;--如果有下标 limit >0 ： 每页显示数量  ； offset  >= 0：当前的偏移量 【($page-1) * $pagesize】，则会优先使用
     * @param string 选填 $relations 关系数组/json字符
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @return array 数据
     *    $listData = [
     *       'pageSize' => $pagesize,
     *       'page' => $page,
     *       'total' => $total,
     *       'totalPage' => ceil($total/$pagesize),
     *        'dataList' => $requestData,
     *   ];
     * @author zouyan(305463219@qq.com)
     */
    public static function getModelListDatas(&$modelObj,  $page = 1, $pagesize = 10, $total = 0, $queryParams = [], $relations = [], $isOpenCache = true){
//        $primaryKey = static::exeMethod($modelObj, 'getKeyName', '');
//        vd($primaryKey);
        // 有优先的改变每页显示数量下标
        if(isset($queryParams['limit']) && is_numeric($queryParams['limit']) && $queryParams['limit'] > 0) {
            $pagesize = $queryParams['limit'];
        }

        // 偏移量
        $offset = ($page-1) * $pagesize;

        // 有优先的改变偏移量下标
        if(isset($queryParams['offset']) && is_numeric($queryParams['offset']) && $queryParams['offset'] >= 0) {// 存在偏移量下标
            $offset = $queryParams['offset'];
            // 当前页数
            $page = ceil($offset / $pagesize);
//        }else{// 不存在偏移量下标
//            $offset = ($page-1) * $pagesize;// --重新计算偏移量
        }

        $limitParams = [
            'limit' => $pagesize,
            // 'take' => $pagesize,
            'offset' => $offset,
            // 'skip' => $offset,
        ];
        /*
        $queryParams = [
            'where' => [
                ['id', '>', '0'],
                //  ['phonto_name', 'like', '%知的标题1%']
            ],
            'orderBy' => ['id'=>'desc','company_id'=>'asc'],
            // 'limit' => $pagesize,
            // 'take' => $pagesize,
            // 'offset' => $offset,
            // 'skip' => $offset,
        ];
        $relations = ['siteResources','CompanyInfo.proUnits.proRecords','CompanyInfo.companyType'];
        */
        $needDataList = true;
        if ($total <= 0){ // 需要获得总页数
            if($total == -5){
                $needDataList = false;
            }
            if (isset($queryParams['limit'])) unset($queryParams['limit']);
            if (isset($queryParams['offset'])) unset($queryParams['offset']);
            if (isset($queryParams['take'])) unset($queryParams['take']);
            if (isset($queryParams['skip'])) unset($queryParams['skip']);
            if (isset($queryParams['orderBy'])) {
                $limitParams['orderBy'] = $queryParams['orderBy'];
                unset($queryParams['orderBy']);
            }

            // 获得总数量
//            // #####开始使用缓存数据功能#######开始#################################################################
            $limitParams = array_merge($queryParams,$limitParams);
            $modelObjCount = Tool::copyObject($modelObj);

            // 查询要获取的字段数组 主要用来判断要请求的字段是否包含排除字段 一维数组
            $select = $queryParams['select'] ?? [];// static::getCacheFields($modelObjCount, 1 | 2 | 4)
            // 缓存类型 1 单条记录缓存 2 块级缓存[默认]
            $cacheType = 2;
            // 缓存时对数据的操作  1 转为json 2 序列化 ; 3 不转换  ；如果是对象或数组请用2
            $operateRedis = 2;
            // 会作为缓存键的前缀标识   调用的方法名  一般用这个 __FUNCTION__
            $redisKeyPre = __FUNCTION__ . ':total';
            // 会作为缓存键的参数数组- 相关的参数, 会md5(json_encode())
            $paramsRedisKey = [__CLASS__, __FUNCTION__, $queryParams];
            // 操作类型 1先读取缓存 ；2 获得数据前，是否再读一次缓存并判断是否有效[有效则直接返回，不从源数据读了]--防击穿，以及雪崩，穿透
            $operateType = 1 | 2;

            $resutTotal = static::getDataCache($modelObjCount,
                function(&$isOpenCache) use(&$modelObjCount, &$queryParams) {// 重新读取数据 注意一定要有返回值[缓存的就是这个值]
                    // 可以缓存时，对读取的字段，加入缓存相关的字段 --- 注意： 确定需要缓存才加入要缓存的字段
                    if($isOpenCache && isset($queryParams['select']) && !empty($queryParams['select'])){
                        $queryParams['select'] = array_merge($queryParams['select'], static::getCacheFields($modelObjCount, 1 | 2 | 4));
                    }

                    // 真正获得数据的代码
                    static::resolveSqlParams($modelObjCount, $queryParams);
                    $total = $modelObjCount->count();
//
//                    // 返回
                    return $total;
                }, $redisKeyPre, $paramsRedisKey, $cacheType, $operateRedis, $queryParams, $select, $isOpenCache, $operateType);
            $total = $resutTotal;
            // #####开始使用缓存数据功能#######结束#################################################################
            // 获得总数量
//            static::resolveSqlParams($modelObj, $queryParams);
//            $total = $modelObj->count();
        } else {
            $limitParams = array_merge($queryParams,$limitParams);
        }
        $requestData = [];
        if($needDataList) {
            // 获得数据
            // #####开始使用缓存数据功能#######开始#################################################################

            // 查询要获取的字段数组 主要用来判断要请求的字段是否包含排除字段 一维数组
            $select = $limitParams['select'] ?? [];// static::getCacheFields($modelObj, 1 | 2 | 4)
            // 缓存类型 1 单条记录缓存 2 块级缓存[默认]
            $cacheType = 2;
            // 缓存时对数据的操作  1 转为json 2 序列化 ; 3 不转换  ；如果是对象或数组请用2
            $operateRedis = 2;
            // 会作为缓存键的前缀标识   调用的方法名  一般用这个 __FUNCTION__
            $redisKeyPre = __FUNCTION__ . ':dataList';
            // 会作为缓存键的参数数组- 相关的参数, 会md5(json_encode())
            $paramsRedisKey = [__CLASS__, __FUNCTION__, $limitParams];
            // 操作类型 1先读取缓存 ；2 获得数据前，是否再读一次缓存并判断是否有效[有效则直接返回，不从源数据读了]--防击穿，以及雪崩，穿透
            $operateType = 1 | 2;

            $requestData = static::getDataCache($modelObj,
                function(&$isOpenCache) use(&$modelObj, &$limitParams) {// 重新读取数据 注意一定要有返回值[缓存的就是这个值]
                    // 可以缓存时，对读取的字段，加入缓存相关的字段 --- 注意： 确定需要缓存才加入要缓存的字段
                    if($isOpenCache && isset($limitParams['select']) && !empty($limitParams['select'])){
                        $limitParams['select'] = array_merge($limitParams['select'], static::getCacheFields($modelObj, 1 | 2 | 4));
                    }

                    // 真正获得数据的代码
                    static::resolveSqlParams($modelObj, $limitParams);
                    $requestData = $modelObj->get();

                    // 返回
                    return $requestData;
                }, $redisKeyPre, $paramsRedisKey, $cacheType, $operateRedis, $limitParams, $select, $isOpenCache, $operateType);

            // #####开始使用缓存数据功能#######结束#################################################################
            // 获得数据
//            static::resolveSqlParams($modelObj, $limitParams);
//            $requestData = $modelObj->get();
            // 获得关联系关系
            static::resolveRelations($requestData, $relations);
        }
        $listData = [
            'pageSize' => $pagesize,
            'page' => $page,
            'total' => $total,
            'totalPage' => ceil($total/$pagesize),
            'dataList' => $requestData,
        ];
        return $listData;
    }

    /**
     * 根据id获得详情
     *
     * @param object $modelObj 模型对象
     * @param string $id 主键值字符串  主键字段1的值1 小分隔符 主键字段1的值2 ... 大分隔符  主键字段2的值1 小分隔符 主键字段2的值2 ...
     * @param array  $selectParams 需要获得的字段-一维数组
     * @param string/array $relations 关系
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @return  array 详情数据--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoById(&$modelObj, $id, $selectParams, $relations = '', $isOpenCache = true){
        if($isOpenCache){
            $primaryKey = static::getPrimaryKey($modelObj);//  缓存键的主键字段-- 一维数组,也可能是空
            if(!empty($primaryKey)){
                $paramsPrimaryVals = static::getPrimaryFVArr($modelObj, $id, static::$bigSplit, static::$smallSplit, $primaryKey);// [$primaryKey => $id];

                $requestData = static::getInfoByCache($modelObj, $paramsPrimaryVals, $selectParams, $relations, $isOpenCache);
                return $requestData;
            }
        }

        // 强制从数据库中获取
        if (!empty($selectParams) && is_array($selectParams))  $modelObj = $modelObj::select($selectParams);

        $requestData = $modelObj->find($id);
        // 查询关系参数
        static::resolveRelations($requestData, $relations);
        return $requestData;
    }

    /**
     * 单条记录缓存处理
     * 根据主键缓存或次缓存，获得数据--参数为空，则返回空数组
     *  cacheDb:U:m:{email值}_{email值}  -> {id值}
     * @param object $modelObj 当前模型对象
     * @param array $paramsPrimaryVals 主键或主键相关缓存字段及值 刚好[字段不能多]用上缓存，不然就用不了缓存 [ '字段1' => '字段1的值','字段2' => ['字段2的值1', '字段2的值2']] ;为空，则返回空数组--注意字段是刚好[主键或主字段]，不能有多,顺序无所谓
     * @param array $select 查询要获取的字段数组 一维数组
     * @param array $relations 关系
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @return  mixed 获得的单 条记录对象
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoByPrimaryFields(&$modelObj, $paramsPrimaryVals = [], $select = [], $relations = [], $isOpenCache = true)
    {
        return static::getInfoByCache($modelObj, $paramsPrimaryVals, $select, $relations, $isOpenCache);
    }

    // 根据条件，获得单条记录数据 1:返回一维数组,>1 返回二维数组
    //  $pagesize 每页显示的数量 [默认1]
    /**
     * 根据id获得详情
     *
     * @param object $modelObj 模型对象
     * @param int $pagesize 获得的数量
     * @param array  $queryParams 条件
     * @param string/array $relations 关系
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @return  array 详情数据--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoByQuery(&$modelObj, $pagesize = 1, $queryParams = [], $relations = [], $isOpenCache = true){
        $listData = static::getModelListDatas($modelObj,  1, $pagesize, $pagesize, $queryParams, $relations, $isOpenCache);
        $dataList = $listData['dataList'] ?? [];
        if($pagesize > 1) return $dataList;
        return $dataList[0] ?? [];
    }

    /**
     * 根据条件，查询单条记录--从数据库---此方法为块级缓存
     * @param object $modelObj 模型对象
     * @param array $fieldVals 不能为空，为空，则返回空数组； 查询的字段及值 ['字段1' => '字段1的值', '字段2' => ['字段2的值1', '字段2的值2']]
     * @param array $select 需要指定的字段 -一维数组；为空代表所有字段
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @return  null 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoByFieldVals(&$modelObj, $fieldVals = [], $select = [], $isOpenCache = true){
        // if(empty($fieldVals)) return [];
        $queryParams = static::getGueryParams($fieldVals, $select);
        if(empty($queryParams)) return [];

        // 查询记录
        return static::getInfoByQuery($modelObj, 1, $queryParams, [], $isOpenCache);
    }


    public static function del(&$modelObj, $queryParams){
        $selFunName = __FUNCTION__;
        return static::doTransactionFun(function() use(&$modelObj, &$queryParams, &$selFunName){
            // 对同步的表进行相同的操作
            static::doDoingSync(0, static::getDoingObj($modelObj), $selFunName, ['{OBJ}', $queryParams], 4, 1);

            // 获得是否开通缓存
            $cachePower = static::getCachePowerNum($modelObj);
            // 开通缓存，则更新缓存时间信息
            if($cachePower > 0){
                $modelObjCopy = Tool::copyObject($modelObj);
                static::updateTimeByQuery($modelObjCopy, $queryParams, 4, static::getCacheFields($modelObjCopy, 1 | 2 | 4), false, [], 2);
            }

            // 查询条件
            static::resolveSqlParams($modelObj, $queryParams);
            $requestData = $modelObj->delete();
            return $requestData;
        });
    }

    // 根据id，删除记录 id,单条记录或 多条[,号分隔] 或一维数组
    // @param string $fieldName 查询的字段名--表中的
    // @param string $valsSeparator 如果是多值字符串，多个值的分隔符;默认逗号 ,
    // @param array $excludeVals 需要除外的参数值--不加入查询条件 [0, '0', ''] --默认；  ['']
    public static function delByIds(&$modelObj, $id, $fieldName = 'id', $valsSeparator = ',', $excludeVals = [0, '0', '']){
        return static::doTransactionFun(function() use(&$modelObj, &$id, &$fieldName, &$valsSeparator, &$excludeVals){

            // 如果是数组，则转为字符串
            // if(is_array($id)) $id = implode(',', $id);
            $queryParams =[// 查询条件参数
                'where' => [
                    // ['id', $id],
                    // ['company_id', $company_id]
                ]
            ];
            Tool::appendParamQuery($queryParams, $id, $fieldName, $excludeVals, $valsSeparator, false);
    //        if (strpos($id, ',') === false) { // 单条
    //            if(!isset($queryParams['where'])) $queryParams['where'] = [];
    //            array_push($queryParams['where'],['id', $id]);
    //        }else{
    //            $queryParams['whereIn']['id'] = explode(',',$id);
    //        }
            return static::del($modelObj, $queryParams);
        });

    }

    // $synces  格式 [ '关系方法名' =>[关系id,...],...可多个....]
    public static function sync(&$modelObj, $id, $synces){
        $requestData = $modelObj->find($id);
        // 同步修改关系 TODO ；以后改为事务
        $successRels = [
            'success' => [],
            'fail' => [],
        ];
//        DB::beginTransaction();
//        try {
//          DB::commit();
//        } catch ( \Exception $e) {
//            DB::rollBack();
//            $errStr = $e->getMessage();
//            $errCode = $e->getCode();
//            throws($errStr, $errCode);
//            // throws($e->getMessage());
//        }
        static::doTransactionFun(function() use(&$synces, &$requestData, &$successRels){
            foreach($synces as $rel => $relIds){
                try {
                    $requestData->{$rel}()->sync($relIds);
                    array_push($successRels['success'],$relIds);
                } catch ( \Exception $e) {
                    // DB::rollBack();
                    array_push($successRels['fail'],[ 'ids'=> $relIds,'msg'=>$e->getMessage() ]);
                    throws('同步关系[' . $rel . ']失败；信息[' . $e->getMessage() . ']');
                    // throws($e->getMessage());

                }
            }
        });
        return $successRels;
    }


    // $detaches 可多个 ;格式 [ '关系方法名' =>关系id数组[1,2,3] 或空数组[](全部移除), ...]
    public static function detach(&$modelObj, $id, $detaches){
        $requestData = $modelObj->find($id);
        // 同步修改关系 TODO ；以后改为事务
        $successRels = [
            'success' => [],
            'fail' => [],
        ];
//        DB::beginTransaction();
//        try {
//          DB::commit();
//        } catch ( \Exception $e) {
//            DB::rollBack();
//            $errStr = $e->getMessage();
//            $errCode = $e->getCode();
//            throws($errStr, $errCode);
//            // throws($e->getMessage());
//        }
        static::doTransactionFun(function() use(&$detaches, &$requestData, &$successRels){
            foreach($detaches as $rel => $relIds){
                try {
                    if(empty($relIds)){
                        $requestData->{$rel}()->detach();
                    }else{
                        $requestData->{$rel}()->detach($relIds);
                    }
                    array_push($successRels['success'],$rel);
                } catch ( \Exception $e) {
                    // DB::rollBack();
                    array_push($successRels['fail'],[$rel =>$e->getMessage() ]);
                    throws('移除关系[' . $rel . ']失败；信息[' . $e->getMessage() . ']');
                    // throws($e->getMessage());

                }
            }
            // DB::commit();
        });
        return $successRels;
    }

    //创建对象
    // 保存一个新的模型。该方法返回被插入的模型实例。但是，在此之前，你需要指定模型的 fillable 或 guarded 属性
    // @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
    public  static function create(&$modelObj, $dataParams, $isOpenCache = true){
        $selFunName = __FUNCTION__;
        return static::doTransactionFun(function() use(&$modelObj, &$dataParams, &$isOpenCache, &$selFunName){

            $modelObjArr = static::getDoingObj($modelObj);
            // 主要确保主键值一致;如果需要生成id，则生成
            static::setPrimaryKeyVal($modelObj, $dataParams, '', false, $isOpenCache);

            // 验证数据
            $judgeType = 2;// $judgeType 验证类型 1 普通数据验证--[默认] ; 2 新建数据验证 ；4 修改数据验证
            static::judgeDBDataThrowErr($modelObj, $judgeType, $dataParams, [], 1);

            // 获得是否开通缓存
            $cachePower = $isOpenCache ? static::getCachePowerNum($modelObj) : 0;
            $modelObjCopy = null;
            if($cachePower > 0){
                $modelObjCopy = Tool::copyObject($modelObj);
            }

            $infoObj = $modelObj->create($dataParams);
            // 开通缓存，则更新缓存时间信息
            if($cachePower > 0){
                // $modelObjCopy = Tool::copyObject($modelObj);
                static::updateTimeByData($modelObjCopy, $infoObj, 1, false, []);
            }

            // 对同步的表进行相同的操作
            if(!empty($modelObjArr)){// 主要确保主键值一致
                // 获得主键字段---默认为id
                $primaryKey = static::exeMethod($modelObj, 'getKeyName', []);
                $primaryVal = '';// 主键值
                if(!empty($primaryKey))  $primaryVal = $infoObj[$primaryKey] ?? '';
                // 有主键值  且  (数据不存在主键值   或  （数据存在主键值 ，但是 数据中的主键值 不等于  主键值）)
                if(!empty($primaryVal) && ( (!isset($dataParams[$primaryKey])) || (isset($dataParams[$primaryKey]) && $dataParams[$primaryKey] != $primaryVal))){
                    $dataParams[$primaryKey] = $primaryVal;
                }
                static::doDoingSync(0, $modelObjArr, $selFunName, ['{OBJ}', $dataParams, $isOpenCache], 1, 1);
            }
            return $infoObj;
        });
    }

    /**
     * 批量会拆分为一个一个新加数据, 返回 成功true:失败:false
     * 注：这里是基类，比量新加时，有缓存时，用这个，无缓存还是用insertData方法;所以都应该用 insertData方法[自动选择基类]
     *     需要返回新加对象的可以用这个
     * @param object $modelObj 当前模型对象
     * @param array $dataParams 一维或二维数组
     * @param int $reType 返回类型 1  成功true:失败:false ； 2：返回新创建的对象 一维[单条]或二维[多条]
     * @return mixed boolean 返回成功true:失败:false; array/object 新创建的对象 一维[单条]或二维[多条]
     * @author zouyan(305463219@qq.com)
     */
    public static function eachAddData(&$modelObj, $dataParams, $reType = 1){
        $requestData = false;
        $dataArr = [];// 新的数据对象--二维数组
        // 获得是否开通缓存
        $cachePower = static::getCachePowerNum($modelObj);
        $modelObjCopy = null;
        if($cachePower > 0){
            $modelObjCopy = Tool::copyObject($modelObj);
        }

        $isMulti = Tool::isMultiArr($dataParams, true);


        // 主要确保主键值一致;如果需要生成id，则生成
        static::setPrimaryKeyVal($modelObj, $dataParams, '', false, false);// 为了批量生成，提高效率

//        DB::beginTransaction();
//        try {
//
//        } catch ( \Exception $e) {
//            DB::rollBack();
//            $errStr = $e->getMessage();
//            $errCode = $e->getCode();
//            throws($errStr, $errCode);
//            // throws($e->getMessage());
//        }
//        DB::commit();

        static::doTransactionFun(function() use(&$dataParams, &$modelObj, &$requestData, &$dataArr){
            $errMsg = '';
            try {
                foreach($dataParams as $dataInfo){
                    $modelObjInfo = Tool::copyObject($modelObj);
                    $resultCreateObj = static::create($modelObjInfo, $dataInfo, false);
                    if(!$resultCreateObj){
                        $requestData = false;
                        break;
                    }else{
                        $dataArr[] = $resultCreateObj;
                        $requestData = true;
                    }
                }
            } catch ( \Exception $e) {
                $requestData = false;
                $errMsg = $e->getMessage();
                // throws('保存失败；信息[' . $e->getMessage() . ']');
                // throws($e->getMessage());
            }finally{
                if(!$requestData){
                    // DB::rollBack();
                    throws('保存失败；信息[' . $errMsg . ']');
                }
                // if($requestData) DB::commit();
            }
        });


        if(!$isMulti) $dataArr = $dataArr[0] ?? [];

        // 开通缓存，则更新缓存时间信息
        if($cachePower > 0){
            // $modelObjCopy = Tool::copyObject($modelObj);
            static::updateTimeByData($modelObjCopy, $dataArr, 1, false, []);
        }

        if( ($reType & 1) == 1) return $requestData;
        return $dataArr;
    }

    // 批量新加-data只能返回成功true:失败:false  -- 前置主要用这个，是否用缓存由内部代码确定
    public static function insertData(&$modelObj, $dataParams){
        // 验证数据
        $judgeType = 2;// $judgeType 验证类型 1 普通数据验证--[默认] ; 2 新建数据验证 ；4 修改数据验证
        static::judgeDBDataThrowErr($modelObj, $judgeType, $dataParams, [], 1);
        $requestData = false;

        // 获得是否开通缓存
        $cachePower = static::getCachePowerNum($modelObj);
        // 开通缓存，则更新缓存时间信息
        if($cachePower > 0){
            $requestData = static::eachAddData($modelObj, $dataParams, 1);
        }else{
//            DB::beginTransaction();
//            try {
//              DB::commit();
//            } catch ( \Exception $e) {
//                DB::rollBack();
//                $errStr = $e->getMessage();
//                $errCode = $e->getCode();
//                throws($errStr, $errCode);
//                // throws($e->getMessage());
//            }

            $modelObjArr = static::getDoingObj($modelObj);
            // 主要确保主键值一致;如果需要生成id，则生成
            $forceIncr = false;// 如果 数据模型的 主键id的值类型 为  1自增id时 ：是否通过直接读取表中当前的最大主键值来补充数据中的主键；true：是： false:不用处理数据中的主键值
            if(!empty($modelObjArr)) $forceIncr = true;
            static::setPrimaryKeyVal($modelObj, $dataParams, '', $forceIncr, false);// 为了批量生成，提高效率

            static::doTransactionFun(function() use(&$modelObj, &$dataParams, &$requestData, &$modelObjArr){
                $errMsg = '';
                try {

                    $requestData = $modelObj->insert($dataParams);//一维或二维数组;只返回true:成功;false：失败
                    // $requestData =$modelObj->insertGetId($dataParams,'id');//只能是一维数组，返回id值
                    // 对同步的表进行相同的操作
                    static::doDoingSync(0, $modelObjArr, 'insert', [$dataParams], 1, 4);

                } catch ( \Exception $e) {
                    $requestData = false;
                    $errMsg = $e->getMessage();
                    // DB::rollBack();
                    // throws('保存失败；信息[' . $e->getMessage() . ']');
                    // throws($e->getMessage());

                }finally{
                    if(!$requestData){
                        // DB::rollBack();
                        throws('保存失败；信息[' . $errMsg . ']');
                    }
                    // if($requestData) DB::commit();
                }
            });

        }
        return $requestData;
    }

    /**
     * 批量新加--返回新加的主键值-一维数组
     *
     * @param object $modelObj 对象
     * @param array $dataParams 需要新的数据-- 二维数组
     * @param string $primaryKey 默认自增列被命名为 id，如果你想要从其他“序列”获取ID
     * @return array 返回新加的主键值-一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function insertGetId(&$modelObj, $dataParams, $primaryKey = 'id'){
        if(empty($primaryKey)) $primaryKey = 'id';

        // 验证数据
        $judgeType = 2;// $judgeType 验证类型 1 普通数据验证--[默认] ; 2 新建数据验证 ；4 修改数据验证
        static::judgeDBDataThrowErr($modelObj, $judgeType, $dataParams, [], 1);

        $requestData = false;
        $dataArr = [];// 新的数据对象--二维数组,只有开通缓存时才会用到
        // 获得是否开通缓存
        $cachePower = static::getCachePowerNum($modelObj);
        $modelObjCopy = null;
        if($cachePower > 0){
            $modelObjCopy = Tool::copyObject($modelObj);
        }

        $isMulti = Tool::isMultiArr($dataParams, true);

        $modelObjArr = static::getDoingObj($modelObj);
        $forceIncr = false;// 如果 数据模型的 主键id的值类型 为  1自增id时 ：是否通过直接读取表中当前的最大主键值来补充数据中的主键；true：是： false:不用处理数据中的主键值
        if(!empty($modelObjArr) && $cachePower <= 0) $forceIncr = true;
        // 主要确保主键值一致;如果需要生成id，则生成
        static::setPrimaryKeyVal($modelObj, $dataParams, $primaryKey, $forceIncr, false);// 为了批量生成，提高效率

        $newIds = [];
//        DB::beginTransaction();
//
//        try {
//          DB::commit();
//        } catch ( \Exception $e) {
//            DB::rollBack();
//            $errStr = $e->getMessage();
//            $errCode = $e->getCode();
//            throws($errStr, $errCode);
//            // throws($e->getMessage());
//        }

        static::doTransactionFun(function() use(&$dataParams, &$cachePower, &$modelObj, &$modelObjCopy, &$primaryKey
            , &$isMulti, &$newIds, &$requestData, &$dataArr, &$modelObjArr){

            // 保存记录
            $errMsg = '';
            try {
                foreach($dataParams as $info){
                    $newId = 0;
                    // 开通缓存，则更新缓存时间信息
                    if($cachePower > 0){
                        $modelObjInfo = Tool::copyObject($modelObj);
                        $resultCreateObj = static::create($modelObjInfo, $info, false);
                        if(!$resultCreateObj){
                            // $errMsg = '';
                            $requestData = false;
                            break;
                        }else{
                            $newId = $resultCreateObj->{$primaryKey};
                            $dataArr[] = $resultCreateObj;
                            $requestData = true;
                        }
                    }else {

                        $newId = $modelObj->insertGetId($info, $primaryKey);//只能是一维数组，返回id值
                        $requestData = true;
                        // 对同步的表进行相同的操作
                        static::doDoingSync(0, $modelObjArr, 'insertGetId', [$info, $primaryKey], 1, 4);

                    }
                    // array_push($newIds,$newId);
                    $newIds[] = $newId;
                }
            } catch ( \Exception $e) {
                $requestData = false;
                $errMsg = $e->getMessage();
                // DB::rollBack();
                // throws('保存失败；信息[' . $e->getMessage() . ']');
                // throws($e->getMessage());

            }finally{
                if(!$requestData){
//                    DB::rollBack();
                    throws('保存失败；信息[' . $errMsg . ']');
                }
//                if($requestData) DB::commit();
            }

            // 开通缓存，则更新缓存时间信息
            if($cachePower > 0){
                if(!$isMulti) $dataArr = $dataArr[0] ?? [];
                // $modelObjCopy = Tool::copyObject($modelObj);
                static::updateTimeByData($modelObjCopy, $dataArr, 1, false, []);
            }
        });

        return $newIds;
    }


    // 通过id修改记录
    public static function saveById(&$modelObj, $dataParams, $id){
        // 没有要修改的参数，直接返回成功
        if(empty($dataParams)) return true;

        $modelObjArr = static::getDoingObj($modelObj);
        $selFunName = __FUNCTION__;

        return static::doTransactionFun(function() use(&$modelObj, &$dataParams, &$id, &$modelObjArr, &$selFunName){

            // 验证数据
            $judgeType = 4;// $judgeType 验证类型 1 普通数据验证--[默认] ; 2 新建数据验证 ；4 修改数据验证
            static::judgeDBDataThrowErr($modelObj, $judgeType, $dataParams, [], 1);

            // 获得是否开通缓存
            $cachePower = static::getCachePowerNum($modelObj);
            $modelObjCopy = null;
            if($cachePower > 0){
                $modelObjCopy = Tool::copyObject($modelObj);
            }

            // $requestData = $modelObj->find($id);
            $modelObj = $modelObj->find($id);
            if(empty($modelObj) || !is_object($modelObj)) throws('记录[' . $id . ']不存在！');

            // 如果数据没有变化，则不用执行修改操作
            $dataInfo = (is_object($modelObj) )  ? $modelObj->toArray() : $modelObj;
            // 获得真正需要更新的数据
            // $diffUpdateData = array_diff_assoc($dataParams, $dataInfo);
            $dataParams = array_diff_assoc($dataParams, $dataInfo);
            // 没有需要更新的数据，直接返回成功
            // if(empty($diffUpdateData)) return true;
            if(empty($dataParams)) return true;


            // 更新数据前，如果有更新缓存字段，则需要先更新缓存时间
            if($cachePower > 0) {
                $dataCacheArr = $dataInfo;// is_object($modelObj) ? $modelObj->toArray() : $modelObj;
                static::updateTimeByData($modelObjCopy, $dataCacheArr, 2, true, $dataParams);
            }

            foreach($dataParams as $field => $val){
                // $requestData->{$field} = $val;
                $modelObj->{$field} = $val;
            }
            // $result = $requestData->save();

            $result = $modelObj->save();

    //        foreach(static::getDoingObj($modelObj) as $v){
    //            static::{__FUNCTION__}($v['obj'], $dataParams, $id);
    //        }

            // 开通缓存，则更新缓存时间信息
            if($cachePower > 0){
                $dataCacheArr = is_object($modelObj) ? $modelObj->toArray() : $modelObj;
                // $modelObjCopy = Tool::copyObject($modelObj);
                static::updateTimeByData($modelObjCopy, $dataCacheArr, 2, false, []);
            }

            // 对同步的表进行相同的操作
            static::doDoingSync($id, $modelObjArr, $selFunName, ['{OBJ}', $dataParams, $id], 2, 1);

            return $result;
        });
    }

    // 通过id修改记录--只修改同步的表的操作
    public static function saveDoingSyncById(&$modelObj, $dataParams, $id){

        // 没有要修改的参数，直接返回成功
        if(empty($dataParams)) return true;

        $modelObjArr = static::getDoingObj($modelObj);
        if(empty($modelObjArr)) return true;
        $selFunName = __FUNCTION__;

        return static::doTransactionFun(function() use(&$modelObj, &$dataParams, &$id, &$modelObjArr, &$selFunName){
            // 对同步的表进行相同的操作
            static::doDoingSync($id, $modelObjArr, 'saveById', ['{OBJ}', $dataParams, $id], 2, 1);
        });
    }

    /**
     * 批量修改设置-- 根据主键
     *
     * @param string $Model_name model名称  'QualityControl\AbilityType'
     * @param string $primaryKey 主键字段,默认为id
     * @param string $dataParams 主键及要修改的字段值 一维/二维数组 数组/json字符
     * @return array
     * @author zouyan(305463219@qq.com)
     */
    public static function batchSave($modelName, $dataParams = [], $primaryKey = 'id'){
        if(empty($primaryKey)) $primaryKey = 'id';

        $className = "App\\Models\\" .$modelName;
        if (! class_exists($className )) {
            throws('参数[Model_name]不正确！');
        }

        // 验证数据
        $modelObj = null;
        static::getObjByModelName($modelName, $modelObj);
        $judgeType = 4;// $judgeType 验证类型 1 普通数据验证--[默认] ; 2 新建数据验证 ；4 修改数据验证
        static::judgeDBDataThrowErr($modelObj, $judgeType, $dataParams, [], 1);

        // 获得是否开通缓存
        $cachePower = static::getCachePowerNum($modelObj);
        $modelObjCopy = null;
        if($cachePower > 0){
            $modelObjCopy = Tool::copyObject($modelObj);
        }

        $modelObjArr = static::getDoingObj($modelObj);
        $selFunName = __FUNCTION__;

        $isMulti = Tool::isMultiArr($dataParams, true);

        $successRels = [
            'success' => [],
            'fail' => [],
        ];
//        DB::beginTransaction();
//        try {
//          DB::commit();
//        } catch ( \Exception $e) {
//            DB::rollBack();
//            $errStr = $e->getMessage();
//            $errCode = $e->getCode();
//            throws($errStr, $errCode);
//            // throws($e->getMessage());
//        }
        static::doTransactionFun(function() use(&$dataParams, &$primaryKey, &$className, &$successRels, &$cachePower, &$modelObj, &$modelObjCopy, &$modelObjArr, &$selFunName){

            foreach($dataParams as $info){
                // 保存记录
                $id = $info[$primaryKey] ?? '';
                try {
                    $temObj = $className::find($id);
                    if(empty($temObj) || !is_object($temObj)) throws('记录[' . $id . ']不存在！');
                    // 如果数据没有变化，则不用执行修改操作
                    $dataInfo = (is_object($temObj) )  ? $temObj->toArray() : $temObj;

                    unset($info[$primaryKey]);
                    if(empty($info))  continue;

                    // 获得真正需要更新的数据
                    // $diffUpdateData = array_diff_assoc($info, $dataInfo);
                    $info = array_diff_assoc($info, $dataInfo);
                    // 没有需要更新的数据，直接返回成功
                    // if(empty($diffUpdateData)) return true;
                    if(empty($info)) continue;

                    // 更新数据前，如果有更新缓存字段，则需要先更新缓存时间
                    if($cachePower > 0) {
                        $dataCacheArr = is_object($temObj) ? $temObj->toArray() : $temObj;
                        static::updateTimeByData($modelObjCopy, $dataCacheArr, 2, true, $info);
                    }

                    foreach($info as $field => $val){
                        $temObj->{$field} = $val;
                    }
                    $res = $temObj->save();
                    array_push($successRels['success'],[$id => $res]);

                    // 开通缓存，则更新缓存时间信息
                    if($cachePower > 0){
                        $dataCacheArr = is_object($temObj) ? $temObj->toArray() : $temObj;
                        // $modelObjCopy = Tool::copyObject($modelObj);
                        static::updateTimeByData($modelObjCopy, $dataCacheArr, 2, false, []);
                    }

                } catch ( \Exception $e) {
                    array_push($successRels['fail'],[ 'id'=> $id,'msg'=>$e->getMessage() ]);
                    throws('修改[' . $id . ']失败；信息[' . $e->getMessage() . ']');
                    // throws($e->getMessage());
                }
            }
            // 对同步的表进行相同的操作
            static::doDoingSync(0, $modelObjArr, $selFunName, ['{TMODEL}', $dataParams, $primaryKey], 2, 1);
        });
        return $successRels;
    }

    // 按条件修改
    // $dataParams --一维数组
    // @param boolean $isCacheDataByCache 读取缓存数据时,是否从缓存优先 true:有缓存优先使用缓存[默认]；false：不使用缓存,强制从数据表读取
    public static function updateQuery(&$modelObj, $dataParams, $queryParams, $isCacheDataByCache = true){

        $modelObjArr = static::getDoingObj($modelObj);
        $selFunName = __FUNCTION__;

        return static::doTransactionFun(function() use(&$modelObj, &$dataParams, &$queryParams, &$isCacheDataByCache, &$modelObjArr, &$selFunName){

            // 验证数据
            $judgeType = 4;// $judgeType 验证类型 1 普通数据验证--[默认] ; 2 新建数据验证 ；4 修改数据验证
            static::judgeDBDataThrowErr($modelObj, $judgeType, $dataParams, [], 1);

            // 更新数据并更新缓存，通过查询条件
            $requestData = static::updateAndCacheByQuery($modelObj,
                function (&$isReturnFunRes, &$cachePower, &$dataCacheObj, &$dataCacheArr) use(&$modelObj, &$queryParams, &$dataParams){
                // 开启缓存; 且有要修改的数据时， ---可以进行判断是否还要 闭包函数 返回的结果，不再往下执行
                // 是否还要 闭包函数 返回的结果，不再往下执行
    //            if( $cachePower > 0 && empty($dataCacheArr)){
    //                $isReturnFunRes = true;
    //                return 0;
    //            }

                // 查询条件
                static::resolveSqlParams($modelObj, $queryParams);
                $requestData = $modelObj->update($dataParams);
                return $requestData;
            }, $queryParams, false, $dataParams, true, $isCacheDataByCache);
             if(is_bool($requestData) && $requestData === true) $requestData = 0;// 没有修改数据，返回0

            // 对同步的表进行相同的操作
            static::doDoingSync(0, $modelObjArr, $selFunName, ['{OBJ}', $dataParams, $queryParams, $isCacheDataByCache], 2, 1);
            return $requestData;
        });
    }

    // 按条件修改--只对同步的表进行相同的操作
    // $dataParams --一维数组
    // @param boolean $isCacheDataByCache 读取缓存数据时,是否从缓存优先 true:有缓存优先使用缓存[默认]；false：不使用缓存,强制从数据表读取
    public static function updateDoingSyncQuery(&$modelObj, $dataParams, $queryParams, $isCacheDataByCache = true){

        $modelObjArr = static::getDoingObj($modelObj);
        if(empty($modelObjArr)) return true;
        $selFunName = __FUNCTION__;

        return static::doTransactionFun(function() use(&$modelObj, &$dataParams, &$queryParams, &$isCacheDataByCache, &$modelObjArr, &$selFunName){
            // 对同步的表进行相同的操作
            static::doDoingSync(0, $modelObjArr, 'updateQuery', ['{OBJ}', $dataParams, $queryParams, $isCacheDataByCache], 2, 1);
            return true;
        });
    }

    //自增自减,通过条件-data操作的行数
    // @param boolean $isCacheDataByCache 读取缓存数据时,是否从缓存优先 true:有缓存优先使用缓存[默认]；false：不使用缓存,强制从数据表读取
    public static function saveDecInc(&$modelObj, $incDecField, $incDecVal = 1, $incDecType = 'inc', $queryParams = [], $modifFields = [], $isCacheDataByCache = true){

        $modelObjArr = static::getDoingObj($modelObj);
        $selFunName = __FUNCTION__;

        return static::doTransactionFun(function() use(&$modelObj, &$incDecField, &$incDecVal, &$incDecType
            , &$queryParams, &$modifFields, &$isCacheDataByCache, &$modelObjArr, &$selFunName){

            // 对同步的表进行相同的操作
            static::doDoingSync(0, $modelObjArr, $selFunName, ['{OBJ}', $incDecField, $incDecVal, $incDecType
                , $queryParams, $modifFields, $isCacheDataByCache], 2, 1);

            // 缓存字段判断相关的
            $dataParams = $modifFields;
            $dataParams[$incDecField] = null;
            // 更新数据并更新缓存，通过查询条件
            $requestData = static::updateAndCacheByQuery($modelObj, function (&$isReturnFunRes, &$cachePower, &$dataCacheObj, &$dataCacheArr) use(&$modelObj, &$queryParams, &$incDecType, &$modifFields, &$incDecField, &$incDecVal ){
                // 开启缓存; 且有要修改的数据时， ---可以进行判断是否还要 闭包函数 返回的结果，不再往下执行
                // 是否还要 闭包函数 返回的结果，不再往下执行
//            if( $cachePower > 0 && empty($dataCacheArr)){
//                $isReturnFunRes = true;
//                return 0;
//            }

                // 查询条件
                static::resolveSqlParams($modelObj, $queryParams);

                $operate = 'decrement'; // 减
                if($incDecType == 'inc'){
                    $operate = 'increment';// 增
                }
                if(is_array($modifFields) && (!empty($modifFields))){
                    $requestData = $modelObj->{$operate}($incDecField, $incDecVal,$modifFields);
                }else{
                    $requestData = $modelObj->{$operate}($incDecField, $incDecVal);
                }

                return $requestData;
            }, $queryParams, false, $dataParams, false, $isCacheDataByCache);
            return $requestData;
        });
    }

    // 只对 对同步的表进行操作
    //自增自减,通过条件-data操作的行数
    // @param boolean $isCacheDataByCache 读取缓存数据时,是否从缓存优先 true:有缓存优先使用缓存[默认]；false：不使用缓存,强制从数据表读取
    public static function saveDoingSyncDecInc(&$modelObj, $incDecField, $incDecVal = 1, $incDecType = 'inc', $queryParams = [], $modifFields = [], $isCacheDataByCache = true){

        $modelObjArr = static::getDoingObj($modelObj);
        if(empty($modelObjArr)) return true;
        $selFunName = __FUNCTION__;

        return static::doTransactionFun(function() use(&$modelObj, &$incDecField, &$incDecVal, &$incDecType
            , &$queryParams, &$modifFields, &$isCacheDataByCache, &$modelObjArr, &$selFunName){

            // 对同步的表进行相同的操作
            static::doDoingSync(0, $modelObjArr, 'saveDecInc', ['{OBJ}', $incDecField, $incDecVal, $incDecType, $queryParams, $modifFields, $isCacheDataByCache], 2, 1);

        });
    }

    /**
     * 批量修改设置
     *
     * @param string $dataParams 主键及要修改的字段值 二维数组 数组/json字符 ;
     *
     *    $dataParams = [
     *       [
     *           'Model_name' => 'model名称',
     *           'primaryVal' => '主键字段值',
     *           'incDecType' => '增减类型 inc 增 ;dec 减[默认]',
     *           'incDecField' => '增减字段',
     *           'incDecVal' => '增减值',
     *           'modifFields' => '修改的其它字段 -没有，则传空数组',
     *       ],
     *   ];
     * @return array
     * @author zouyan(305463219@qq.com)
     */
    public static function saveDecIncBatchByPrimaryKey($dataParams ){

        $successRels = [];

//        DB::beginTransaction();
//
//
//        try {
//          DB::commit();
//        } catch ( \Exception $e) {
//            DB::rollBack();
//            $errStr = $e->getMessage();
//            $errCode = $e->getCode();
//            throws($errStr, $errCode);
//            // throws($e->getMessage());
//        }

        static::doTransactionFun(function() use(&$dataParams, &$successRels){

            foreach($dataParams as $info){
                try {
                    $primaryVal = $info['primaryVal'] ?? '';
                    if(empty($primaryVal)){
                        throws('参数[primaryVal]不能为空！');
                    }
                    // 获得对象
                    $modelName = $info['Model_name'] ?? '';
                    Tool::judgeEmptyParams('Model_name', $modelName);

                    // 增减字段
                    $incDecField = $info['incDecField'] ?? '';
                    Tool::judgeEmptyParams('incDecField', $incDecField);

                    // 增减类型 inc 增 ;dec 减[默认]
                    $incDecType = $info['incDecType'] ?? 'dec';
                    // 增减值
                    $incDecVal = $info['incDecVal'] ?? '';
                    if(!is_numeric($incDecVal)){
                        throws('参数[incDecVal]必须是数字!');
                    }

                    // 修改的其它字段 -没有，则传空数组json
                    $modifFields = $info['modifFields'] ?? [];
                    // jsonStrToArr($modifFields , 1, '参数[modifFields]格式有误!');

                    $res = static::saveInfoDecIncBatchByPrimaryKey($modelName, $incDecField, $primaryVal, $incDecType, $incDecVal, $modifFields);
                    array_push($successRels,$res);


                } catch ( \Exception $e) {
                    throws('保存[' . $primaryVal . ']失败；信息[' . $e->getMessage() . ']', $e->getCode());
                    // throws($e->getMessage());
                }
            }
        });
        return $successRels;
    }


    /**
     * 单个修改自增自减
     *
     * @param string $modelName model名称   QualityControl\StaffDoing
     * @param string $primaryVal 主键字段值
     * @param string $incDecField 增减字段
     * @param string $incDecType 增减类型 inc 增 ;dec 减[默认]
     * @param string $incDecVal 增减值 默认1
     * @param mixed $modifFields 修改的其它字段 -没有，则传空数组
     * @return int 返回操作成功的数量 如：1
     * @author zouyan(305463219@qq.com)
     */
    public static function saveInfoDecIncBatchByPrimaryKey($modelName, $incDecField = '', $primaryVal = '', $incDecType = 'dec', $incDecVal = 1, $modifFields = []){
        $res = 0;
        $selFunName = __FUNCTION__;
        static::doTransactionFun(function() use(&$modelName, &$incDecField, &$primaryVal, &$incDecType, &$incDecVal, &$modifFields, &$res, &$selFunName){
            if(empty($primaryVal)){
                throws('参数[primaryVal]不能为空！');
            }
            // 获得对象
            // $modelName = $info['Model_name'] ?? '';
            Tool::judgeEmptyParams('Model_name', $modelName);

            $className = "App\\Models\\" .$modelName;
            if (! class_exists($className )) {
                throws('参数[Model_name]不正确！');
            }

            // 增减字段
            Tool::judgeEmptyParams('incDecField', $incDecField);

            // 增减值
            if(!is_numeric($incDecVal)){
                throws('参数[incDecVal]必须是数字!');
            }

            // 更新缓存需要用到
            $modelObj = new $className();

            $modelObjArr = static::getDoingObj($modelObj);
            // 对同步的表进行相同的操作
            static::doDoingSync(0, $modelObjArr, $selFunName, ['{TMODEL}', $incDecField, $primaryVal, $incDecType, $incDecVal, $modifFields], 2, 1);

            // 修改的其它字段 -没有，则传空数组json
            // $modifFields = $info['modifFields'] ?? [];
            // jsonStrToArr($modifFields , 1, '参数[modifFields]格式有误!');


            // 保存记录
            $operate = 'decrement'; // 减
            if($incDecType == 'inc'){
                $operate = 'increment';// 增
            }

            // 获得是否开通缓存
            $cachePower = static::getCachePowerNum($modelObj);
            $modelObjCopy = null;
            if($cachePower > 0){
                $modelObjCopy = Tool::copyObject($modelObj);
            }
            $temDataParams = (is_array($modifFields) && (!empty($modifFields))) ? $modifFields : [];
            $temDataParams[$incDecField] = null;


            $temObj = $className::find($primaryVal);

            // 更新数据前，如果有更新缓存字段，则需要先更新缓存时间
            if($cachePower > 0) {
                $dataCacheArr = is_object($temObj) ? $temObj->toArray() : $temObj;
                static::updateTimeByData($modelObjCopy, $dataCacheArr, 2, true, $temDataParams);
            }

            if(is_array($modifFields) && (!empty($modifFields))){
                $res = $temObj->{$operate}($incDecField, $incDecVal,$modifFields);
            }else{
                // 返回操作成功的数量 如：1
                $res = $temObj->{$operate}($incDecField, $incDecVal);
            }
            // array_push($successRels,$res);

            // 开通缓存，则更新缓存时间信息
            if($cachePower > 0){
                $dataCacheArr = is_object($temObj) ? $temObj->toArray() : $temObj;
                // $modelObjCopy = Tool::copyObject($modelObj);
                static::updateTimeByData($modelObjCopy, $dataCacheArr, 2, false, []);
            }

            // 清空对象
            $temObj = null;
            $modelObj = null;
            $modelObjCopy = null;
        });

        return $res;
    }

    /**
     * 需要使用历史字段时，获得历史id
     *
     * @param object $mainObj 主表对象
     * @param mixed $primaryVal 主表对象主键值
     * @param object $historyObj 历史表对象
     * @param string $historyTable 历史表名字
     * @param array $historySearch 历史表查询字段[一维数组][一定要包含主表id的] +  版本号(不用传，自动会加上) 格式 ['字段1'=>'字段1的值','字段2'=>'字段2的值' ... ]
     * @param array $ignoreFields 忽略都有的字段中，忽略历史表中的记录 [一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]
     * @return int 历史表id
     * @author zouyan(305463219@qq.com)
     */
    public static function getHistory(&$mainObj, $primaryVal, &$historyObj, $HistoryTableName, $historySearch, $ignoreFields ){

        // 获得是否开通缓存
        $cachePower = static::getCachePowerNum($mainObj);

        $modelObjCopy = null;
        if($cachePower > 0){
            $modelObjCopy = Tool::copyObject($mainObj);
        }

        $modelObjDoingSyncCopy = Tool::copyObject($mainObj);
        // 获得主键字段---默认为id
        $primaryKey = static::exeMethod($mainObj, 'getKeyName', []);

        // 获得员操作员工信息
        // $mainObj = $mainObj::find($primaryVal);
        $mainObj = static::getInfoById($mainObj, $primaryVal, [], '', true);// $mainObj::find($primaryVal);
        if(empty($mainObj)){
            throws("原记录[" . $primaryVal  . "] 不存在");
        }

        $versionNum = $mainObj->version_num;
        // 获得所有字段
        $historyColumns = static::getDbFieldsByName($historyObj, $HistoryTableName,1);// Schema::getColumnListing($HistoryTableName);


        // 历史表需要保存的字段
        $historyData = [];// 要保存的历史记录
        $historySearchConditon = [];// 历史表查询字段
        $ignoreFields = array_merge($ignoreFields,['id', 'updated_at', $primaryKey]);// , 'version_history_id', 'version_num_history'
        foreach($historyColumns as $field){
            if(isset($mainObj->$field) && !in_array($field,$ignoreFields) ){
                $historyData[$field] = $mainObj->$field;
            }
            // 去掉不在历史表中的历史表查询字段
            if(isset($historySearch[$field])){
                $historySearchConditon[$field] = $historySearch[$field] ;
            }
        }
        if(isset($mainObj->updated_at)){// 记录历史表记录是主表的修改时间
            $historyData['created_at'] = $mainObj->updated_at;
        }

        // 查询加入版本号
        $historySearchConditon["version_num"] = $versionNum;

        // ~~~~~~~~~~判断~当前主记录版本号是否有必要+1（主记录数据有改动，但没有更新版本号，这里进行纠正版本号）~~开始~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // 判断主记录版本号，是否应该加1
        $diffArr = []; // 记录不同的字段及值
        $historyObjCopy = Tool::copyObject($historyObj);

        $queryParamsHistory = static::getGueryParams($historySearchConditon, []);
        $historyInfoObj = static::getInfoByQuery($historyObjCopy, 1, $queryParamsHistory, [], true);
        // 有历史记录,需要对比，再确定是否更新版本
        if(!empty($historyInfoObj)){
            $ignoreHistoryFields = $ignoreFields;

            // 忽略的比较字段
            $ignoreHistoryFields = array_merge($ignoreHistoryFields,['id', 'created_at', 'updated_at', 'version_num', 'version_history_id', 'version_num_history']);// 'staff_id' // , 'operate_staff_id', 'operate_staff_id_history'

            // 比较字段
            foreach($historyColumns as $field){
                if( in_array($field,$ignoreHistoryFields) ) continue;
                if($mainObj->$field != $historyInfoObj->$field){ // 字段值不同
                    $diffArr[$field] = [$mainObj->$field,$historyInfoObj->$field];
                }
            }
            if(empty($diffArr)){// 优化，如果历史表记录没有变化，则不用后继的处理--直接返回
                $historyObj = $historyInfoObj;
                return $historyInfoObj->{$primaryKey};
            }
            if(!empty($diffArr)){// 有不同的值，则需要版本号+1

                $mainObj->version_num++;

                static::doTransactionFun(function() use(&$cachePower, &$modelObjCopy, &$mainObj, &$modelObjDoingSyncCopy, &$primaryKey, $primaryVal){

                    // 开通缓存，则更新缓存时间信息
                    if($cachePower > 0){
                        $modelCacheObjCopy = Tool::copyObject($modelObjCopy);
                        $dataCacheArr = is_object($mainObj) ? $mainObj->toArray() : $mainObj;
                        static::updateTimeByData($modelCacheObjCopy, $dataCacheArr, 2, false, []);
                    }

                    $mainObj->save();

                    // 对同步的表进行相同的操作
                    $queryDoingSyncParams = [
                        'where' => [
                             [$primaryKey, $primaryVal],
                        ]
                    ];
                    static::saveDoingSyncDecInc($modelObjDoingSyncCopy, 'version_num', 1, 'inc',$queryDoingSyncParams, [], false);


                });

                $versionNum++;// $versionNum += 1;
                // 查询加入版本号
                $historySearchConditon["version_num"] = $versionNum;
                $historyData['version_num'] = $versionNum;
            }
        }
        // ~~~~~~~~~~判断~~~结束~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        return static::doTransactionFun(function() use(&$historyObj, &$historySearchConditon, &$historyData, &$cachePower
            , &$mainObj, &$modelObjCopy, &$modelObjDoingSyncCopy, &$primaryVal, &$primaryKey){

            // 查找历史表当前版本
            static::firstOrCreate($historyObj, $historySearchConditon, $historyData );

            // $historyObj = $historyObj::firstOrCreate($historySearchConditon, $historyData);
            $version_history_id = $historyObj->{$primaryKey};// id;
            // 如果主表有当前历史记录id,且值不等于最新的历史记录id,则更新主表当前历史记录id值
            if(isset($mainObj->version_history_id) && $mainObj->version_history_id != $version_history_id){
                $dataDoingSyncInfo = [] ;

                $mainObj->version_history_id = $version_history_id;
                $dataDoingSyncInfo['version_history_id'] = $version_history_id;
                if(isset($mainObj->version_num_history)){
                    $mainObj->version_num_history = $historyObj->version_num ;
                    $dataDoingSyncInfo['version_num_history'] = $historyObj->version_num;
                }

                // 开通缓存，则更新缓存时间信息--更新缓存优先
                if($cachePower > 0){
                    // $mainObjCopyCache = Tool::copyObject($modelObjCopy);
                    $dataCacheArr = is_object($mainObj) ? $mainObj->toArray() : $mainObj;
                    static::updateTimeByData($modelObjCopy, $dataCacheArr, 2, false, []);
                }

                $mainObj->save();

                // 对同步的表进行相同的操作
                static::saveDoingSyncById($modelObjDoingSyncCopy, $dataDoingSyncInfo, $primaryVal);


            }
            return $version_history_id;// $historyObj->id ;
        });
    }

    /**
     * 对比主表和历史表是否相同，相同：不更新版本号，不同：版本号+1
     *
     * @param object $mainObj 主表对象
     * @param mixed $primaryVal 主表对象主键值
     * @param object $historyObj 历史表对象
     * @param string $HistoryTableName 历史表名字
     * @param array $historySearch 历史表查询字段[一维数组][一定要包含主表id的值] +  版本号(不用传，自动会加上) 格式 ['字段1'=>'字段1的值','字段2'=>'字段2的值' ... ]
     * @param array $ignoreFields 忽略都有的字段中，忽略历史中的记录 [一维数组] - 必须会有 [历史表中对应主表的id字段] 格式 ['字段1','字段2' ... ]
     * @param int $forceIncVersion 如果需要主表版本号+1,是否更新主表 1 更新 ;0 不更新
     * @return array 不同字段的内容 数组 [ '字段名' => ['原表中的值','历史表中的值']]; 空数组：不用版本号+1;非空数组：版本号+1
     * @author zouyan(305463219@qq.com)
     */
    public static function compareHistoryOrUpdateVersion(&$mainObj, $primaryVal, &$historyObj, $HistoryTableName, $historySearch, $ignoreFields, $forceIncVersion = 1)
    {
        // 获得是否开通缓存
        $cachePower = static::getCachePowerNum($mainObj);
        $modelObjCopy = null;
        if($cachePower > 0){
            $modelObjCopy = Tool::copyObject($mainObj);
        }

        $modelObjDoingSyncCopy = Tool::copyObject($mainObj);
        // 获得主键字段---默认为id
        $primaryKey = static::exeMethod($mainObj, 'getKeyName', []);


        $diffArr = []; // 记录不同的字段及值

        // 获得员操作员工信息
        // $mainObj = $mainObj::find($primaryVal);
        $mainObj = static::getInfoById($mainObj, $primaryVal, [], '', true);// $mainObj::find($primaryVal);
        if(empty($mainObj)){
            throws("原记录[" . $primaryVal  . "] 不存在");
        }
        $versionNum = $mainObj->version_num;// 当前记录版本号

        // 获得所有字段-历史表
        $historyColumns = static::getDbFieldsByName($historyObj, $HistoryTableName,1);// Schema::getColumnListing($HistoryTableName);

        // 过滤查询条件中不在字段中的
        $historySearchConditon = [];
        // 去掉不在历史表中的历史表查询字段
        foreach($historyColumns as $field){
            if(isset($historySearch[$field])){
                $historySearchConditon[$field] = $historySearch[$field] ;
            }
        }
        // 查询条件加上版本号
        $historySearchConditon["version_num"] = $versionNum;

//        // 查询条件转为二维数组
//        $where = [];
//        foreach($historySearchConditon as $k => $v){
//            $where[] = [$k ,"=" , $v];
//        }
//
//        // 查找当前版本在历史表中的记录
//        $historyObj = $historyObj::where($where)->limit(1)->get();
//        $historyInfoObj = $historyObj[0] ?? [] ;

        $queryParamsHistory = static::getGueryParams($historySearchConditon, []);
        $historyInfoObj = static::getInfoByQuery($historyObj, 1, $queryParamsHistory, [], true);
        if(empty($historyInfoObj)) return $diffArr;// 没有历史记录,不用更新版本

        // 忽略的比较字段
        $ignoreFields = array_merge($ignoreFields, ['id', 'created_at', 'updated_at', 'version_num', 'version_history_id', 'version_num_history', $primaryKey]);//, 'version_history_id', 'version_num_history' , 'staff_id', 'operate_staff_id_history'

        // 比较字段
        foreach($historyColumns as $field){
            if( in_array($field,$ignoreFields) ) continue;
            if($mainObj->$field != $historyInfoObj->$field){ // 字段值不同
                $diffArr[$field] = [$mainObj->$field,$historyInfoObj->$field];
            }
        }
        if(!empty($diffArr)){// 有不同的值，则需要版本号+1
            if ($forceIncVersion) {
                $mainObj->version_num++;

                static::doTransactionFun(function() use(&$cachePower, &$modelObjCopy, &$mainObj, &$modelObjDoingSyncCopy, &$primaryKey, $primaryVal){

                    // 开通缓存，则更新缓存时间信息
                    if($cachePower > 0){
                        $dataCacheArr = is_object($mainObj) ? $mainObj->toArray() : $mainObj;
                        static::updateTimeByData($modelObjCopy, $dataCacheArr, 2, false, []);
                    }
                    $mainObj->save();

                    // 对同步的表进行相同的操作
                    $queryDoingSyncParams = [
                        'where' => [
                            [$primaryKey, $primaryVal],
                        ]
                    ];
                    static::saveDoingSyncDecInc($modelObjDoingSyncCopy, 'version_num', 1, 'inc',$queryDoingSyncParams, [], false);
                });

            }
            return $diffArr;
        }
        return $diffArr;
    }

    /**
     * 查找记录,或创建新记录[没有找到] - $searchConditon +  $updateFields 的字段,
     *
     * @param object $mainObj 主表对象
     * @param array $searchConditon 查询字段[一维数组]
     * @param array $updateFields 表中还需要保存的记录 [一维数组] -- 新建表时会用
     * @return object $mainObj 表对象[一维]
     * @author zouyan(305463219@qq.com)
     */
    public static function firstOrCreate(&$mainObj, $searchConditon, $updateFields )
    {
        // 验证数据
        $judgeType = 4;// $judgeType 验证类型 1 普通数据验证--[默认] ; 2 新建数据验证 ；4 修改数据验证
        $dataParams = array_merge($searchConditon, $updateFields);
        static::judgeDBDataThrowErr($mainObj, $judgeType, $dataParams, [], 1);
        // 源操作语句
//        $mainObj = $mainObj::firstOrCreate($searchConditon, $updateFields);
//        return $mainObj;

        // 如果没有记录，新加记录时用
        $modelObjCopy = Tool::copyObject($mainObj);
        // 先查询，有直接返回对象。
        $queryParams = static::getGueryParams($searchConditon, []);
        $infoObj = static::getInfoByQuery($mainObj, 1, $queryParams, [], true);
        if(!empty($infoObj) && is_object($infoObj)){
            $mainObj = $infoObj;// 注：一定要把新对象重新给引用 $mainObj 改对象
            return $infoObj;
        }

        // 没有查到，则新加并返回对象
        $infoObj = static::create($modelObjCopy, $dataParams, true);
        $mainObj = $infoObj;// 注：一定要把新对象重新给引用 $mainObj 改对象
        return $infoObj;
    }

    /**
     * 已存在则更新，否则创建新模型--持久化模型，所以无需调用 save()- $searchConditon +  $updateFields 的字段,
     *
     * @param object $mainObj 主表对象
     * @param array $searchConditon 查询字段[一维数组]
     * @param array $updateFields 表中还需要保存的记录 [一维数组] -- 新建表时会用
     * @param boolean $isCacheDataByCache 读取缓存数据时,是否从缓存优先 true:有缓存优先使用缓存[默认]；false：不使用缓存,强制从数据表读取
     * @return mixed object $mainObj 表对象[一维]
     * @author zouyan(305463219@qq.com)
     */
    public static function updateOrCreate(&$mainObj, $searchConditon, $updateFields, $isCacheDataByCache = true)
    {
        // 验证数据
        $judgeType = 4;// $judgeType 验证类型 1 普通数据验证--[默认] ; 2 新建数据验证 ；4 修改数据验证
        $dataParams = array_merge($searchConditon, $updateFields);
        static::judgeDBDataThrowErr($mainObj, $judgeType, $dataParams, [], 1);

        // 源操作语句
//        $mainObj = $mainObj::updateOrCreate($searchConditon, $updateFields);
//        return $mainObj;

        // 如果没有记录，新加记录时用
        $modelObjCopy = Tool::copyObject($mainObj);
        $modelObjDoingSyncCopy = Tool::copyObject($mainObj);
        // 先查询，有直接返回对象。
        $queryParams = static::getGueryParams($searchConditon, []);
        $infoObj = static::getInfoByQuery($mainObj, 1, $queryParams, [], true);
        // 有记录
        if(!empty($infoObj) && is_object($infoObj)){
            // 如果数据没有变化，则不用执行修改操作
            $dataInfo = (is_object($infoObj) )  ? $infoObj->toArray() : $infoObj;
            // 获得真正需要更新的数据
            // $diffUpdateData = array_diff_assoc($updateFields, $dataInfo);
            $updateFields = array_diff_assoc($updateFields, $dataInfo);
            // 没有需要更新的数据，直接返回成功
            // if(empty($diffUpdateData)) return true;
            // 对比数据，如果有变动，则
            if(!empty($updateFields)){
                static::doTransactionFun(function() use(&$modelObjCopy, &$infoObj, &$updateFields
                    , &$queryParams, &$isCacheDataByCache, &$modelObjDoingSyncCopy){

                    // 更新数据并更新缓存，通过查询条件-- 这里有个问题是：如果记录存在，也会更新记录时间，使缓存失败，--所以不用
                    $infoObj = static::updateAndCacheByQuery($modelObjCopy, function (&$isReturnFunRes, &$cachePower
                        , &$dataCacheObj, &$dataCacheArr) use(&$infoObj, &$updateFields){
                        // 如果查询的数量大于1，则有问题
                        if($cachePower > 0 && count($dataCacheArr) > 1){
                            $isReturnFunRes = true;
                            throws('不可更新多条记录！');//  . json_encode($updateFields) . json_encode($dataCacheArr)
                        }

                        foreach($updateFields as $t_field => $t_fv){
                            $infoObj->{$t_field} = $t_fv;
                        }
                        $infoObj->save();
                        return $infoObj;
                    }, $queryParams, false, $updateFields, true, $isCacheDataByCache);
                    // 对同步的表进行相同的操作
                    static::updateDoingSyncQuery($modelObjDoingSyncCopy, $updateFields, $queryParams, $isCacheDataByCache);
                });

            }
            $mainObj = $infoObj;// 注：一定要把新对象重新给引用 $mainObj 改对象
            return $infoObj;
        }

        // 没有查到，则新加并返回对象
        $infoObj = static::create($modelObjCopy, $dataParams, true);
        $mainObj = $infoObj;// 注：一定要把新对象重新给引用 $mainObj 改对象
        return $infoObj;
    }


    // 获得表的主键字段 缓存键的主键字段-- 一维数组,也可能是空
    public static function getPrimaryKey(&$modelObj)
    {
        // $primaryKey = $modelObj->getKeyName();
        $primaryKey = $modelObj->getCachePrimaryFields();
        return $primaryKey;
    }

    /**
     * 获得缓存表结构的时长，单位秒
     *
     * @return int $cacheExpire
     * @author zouyan(305463219@qq.com)
     */
    public static function getDBCacheExpire(){
        $cacheExpire = config('public.DBDataCache.tableFieldsCacheExpire', 60);
        if(!is_numeric($cacheExpire) || $cacheExpire <= 0) $cacheExpire = 60;
        return $cacheExpire;
    }

    /**
     * 获得表字段的md5值
     *
     * @param object $modelObj 当前模型对象
     * @param string $table_name 历史表名字
     * @param string $database_model_dir_name 对应的数据库模型目录名称
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return string 表的字段md5值
     * @author zouyan(305463219@qq.com)
     */
    public static function getDbFieldsMd5(&$modelObj, $table_name, $database_model_dir_name = '', $errDo = 1){
//        $dbKey = Tool::getProjectKey(64, ':', ':');
//        $keyRedisPre = $dbKey . 'dbfields:';
//        if(strlen($database_model_dir_name) > 0 ) $keyRedisPre .= $database_model_dir_name . ':';
        $keyRedisPre = static::getKeyRedisPre($database_model_dir_name);

        $fieldsMd5 = Tool::getRedis($keyRedisPre . $table_name . ':md5', 3);
        // 如果失败，则重新缓存并再次获取
        if(empty($fieldsMd5) || strlen($fieldsMd5) <= 0){
            static::getDbFields($modelObj, $table_name, $database_model_dir_name, $errDo);
            $fieldsMd5 = Tool::getRedis($keyRedisPre . $table_name . ':md5', 3);
        }
        return $fieldsMd5;
    }

    /**
     * 根据表名，获得表字段数组--直接获取，无缓存
     *   注：不要直接使用此方法[会有查库的行为]，可以使用getDbFields 方法 会使用缓存
     *
     * @param string $table_name 历史表名字
     * @return array 表的字段数组-一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getTableFields($table_name){
        // 获得所有字段
        $tableFields = Schema::getColumnListing($table_name);
        $tableFields = array_map("strtolower", $tableFields);// 都转为小写
        return $tableFields;
    }

    /**
     * 根据表名，获得表字段数组
     *
     * @param object $modelObj 当前模型对象
     * @param string $table_name 表名字
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return mixed 表的字段数组-一维数组   sting 具体错误 ； throws 错误
     * @author zouyan(305463219@qq.com)
     */
    public static function getDbFieldsByName(&$modelObj, $table_name, $errDo = 1){
        $database_model_dir_name = static::getAttr($modelObj, 'modelPath', 1);
        return static::getDbFields($modelObj, $table_name, $database_model_dir_name, $errDo);
    }

    /**
     * 根据表名，获得表字段数组
     *
     * @param object $modelObj 当前模型对象
     * @param string $table_name 历史表名字
     * @param string $database_model_dir_name 对应的数据库模型目录名称
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return mixed 表的字段数组-一维数组   sting 具体错误 ； throws 错误
     * @author zouyan(305463219@qq.com)
     */
    public static function getDbFields(&$modelObj, $table_name, $database_model_dir_name = '', $errDo = 1){
        $operateRedis = 1;
        $cacheExpire = static::getDBCacheExpire();

        /*
         *
        $dbKey = Tool::getProjectKey(64, ':', ':');
        $keyRedisPre = $dbKey . 'dbfields:';
        if(strlen($database_model_dir_name) > 0 ) $keyRedisPre .= $database_model_dir_name . ':';

        $forceReCache = false;// 是否强制重新缓存 true:强制缓存 ; false:不强制缓存
        // 获得版本号
        $versionRedisKey = $table_name . ':version';
        $version = Tool::getRedis($keyRedisPre . $versionRedisKey, 3);
        if($version === false || ($version != config('public.DBDataCache.version', 0))){
            $forceReCache = true;
            Tool::setRedis($keyRedisPre, $versionRedisKey, config('public.DBDataCache.version', 0), 0 , 3);
        }
         *
         */

        $keyRedisPre = '';
        // $forceReCache 是否强制重新缓存 true:强制缓存;有变动[不可用缓存，重新缓存] ; false:不强制缓存;无变动[缓存有效可用]
        $forceReCache = static::getDBChangeStatus($keyRedisPre, $database_model_dir_name, $table_name);

        // 判断是否具体的数据库表模型结构有改动
        if(!$forceReCache) $forceReCache = static::getDBModelChangeStatus($modelObj, $keyRedisPre, $database_model_dir_name, $table_name);

        $tableFields = [];
        if(!$forceReCache){
            $tableFields = Tool::getRedis($keyRedisPre . $table_name, $operateRedis);
            // 缓存时间>0才会从缓存获取，不然实时获取并缓存
            if(is_array($tableFields) && !empty($tableFields)) return $tableFields;
        }

        $redisKey =  md5($keyRedisPre . '-' . $table_name);
        $tableFields = Tool::lockDoSomething('dbfields:' . $redisKey,
            function()  use(&$table_name, &$keyRedisPre, &$tableFields, &$cacheExpire, &$operateRedis){//
                // 获得所有字段
                // $tableFields = Schema::getColumnListing($table_name);
                // $tableFields = array_map("strtolower", $tableFields);// 都转为小写
                $tableFields = static::getTableFields($table_name);
                // 重新缓存
                Tool::setRedis($keyRedisPre, $table_name, $tableFields, $cacheExpire , $operateRedis);

                // 缓存表字段的md5值
                Tool::setRedis($keyRedisPre, $table_name . ':md5', md5(json_encode($tableFields)), 0 , 3);
                return $tableFields;
            }, function($errDo) use(&$keyRedisPre, &$table_name, &$operateRedis ){
                // TODO
                // 再次查看是否有缓存
                $tableFields = Tool::getRedis($keyRedisPre . $table_name, $operateRedis);
                if(is_array($tableFields) && !empty($tableFields)) return $tableFields;

                $errMsg = '获得字段失败，请稍后重试!';
                if($errDo == 1) throws($errMsg);
                return $errMsg;
            }, true, $errDo, 2000, 2000);

        if(!is_array($tableFields) || empty($tableFields)){
            $errMsg = '获得字段失败，请稍后重试!!';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }
        return $tableFields;
    }

    /**
     * 获得属性
     *
     * @param object $modelObj 对象
     * @param string $attrName 属性名称[静态或动态]--注意静态时不要加$ ,与动态属性一样 如 attrTest
     * @param int $isStatic 是否静态属性 0：动态属性 1 静态属性
     * @return string 属性值
     * @author zouyan(305463219@qq.com)
     */
    public static function getAttr(&$modelObj, $attrName, $isStatic = 0){
        return Tool::getAttr($modelObj, $attrName, $isStatic);
//        if ( !property_exists($modelObj, $attrName)) {
//            throws("未定义[" . $attrName  . "] 属性");
//        }
//        // 静态
//        if($isStatic == 1) return $modelObj::${$attrName};
//        return $modelObj->{$attrName};
    }

    /**
     * 调用模型方法
     *  模型中方法定义:注意参数尽可能给默认值
     *    public function aaa($aa = [], $bb = []){
     *       echo $this->getTable() . '<BR/>';
     *       print_r($aa);
     *       echo  '<BR/>';
     *       print_r($bb);
     *       echo  '<BR/>';
     *        echo 'aaaaafunction';
     *   }
     * @param object $modelObj 对象
     * @param string $methodName 方法名称
     * @param array $params 参数数组 没有参数:[]  或 [第一个参数, 第二个参数,....];
     * @return mixed 返回值
     * @author zouyan(305463219@qq.com)
     */
    public static function exeMethod(&$modelObj, $methodName, $params = []){
        return Tool::exeMethod($modelObj, $methodName, $params);
//        if(!method_exists($modelObj,$methodName)){
//            throws("未定义[" . $methodName  . "] 方法");
//        }
//        return $modelObj->{$methodName}(...$params);
    }

    /**
     * 获得 id=> 键值对 或 查询的数据
     *
     * @param array $kv ['key' => 'id字段', 'val' => 'name字段'] 或 [] 返回查询的数据
     * @param array $select 需要获得的字段数组   ['id', 'area_name'] ; 参数 $kv 不为空，则此参数可为空数组
     * @param array $queryParams 查询条件
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @author zouyan(305463219@qq.com)
     */
    public static function getKeyVals(&$modelObj, $kv = [], $select =[], $queryParams = [], $isOpenCache = true){
//        $areaCityList = $modelObj::select(['id', 'area_name'])
//            ->orderBy('sort_num', 'desc')->orderBy('id', 'desc')
//            ->where([
//                ['company_id', '=', $company_id],
//                ['area_parent_id', '=', $area_parent_id],
//            ])
//            ->get()->toArray();
//        if(!$is_kv) return $areaCityList;
//        return Tool::formatArrKeyVal($areaCityList, 'id', 'area_name');
        if ( isset($kv['key']) && isset($kv['val']) ) {
            if(!in_array($kv['key'], $select)) array_push($select, $kv['key']);
            if(!in_array($kv['val'], $select)) array_push($select, $kv['val']);
        }
 //       if (!empty($select) && is_array($select))  $modelObj = $modelObj::select($select);
        // if (!empty($where) && is_array($where))  $modelObj = $modelObj->where($where);

         $queryParams['select'] = $select;// 缓存用的，代替上面注释的 $modelObj = $modelObj::select($select);
        // #####开始使用缓存数据功能#######开始#################################################################

        // 查询要获取的字段数组 主要用来判断要请求的字段是否包含排除字段 一维数组
        // $select = $select;// $queryParams['select'] ?? [];// static::getCacheFields($modelObj, 1 | 2 | 4)
        // 缓存类型 1 单条记录缓存 2 块级缓存[默认]
        $cacheType = 2;
        // 缓存时对数据的操作  1 转为json 2 序列化 ; 3 不转换  ；如果是对象或数组请用2
        $operateRedis = 2;
        // 会作为缓存键的前缀标识   调用的方法名  一般用这个 __FUNCTION__
        $redisKeyPre = __FUNCTION__;
        // 会作为缓存键的参数数组- 相关的参数, 会md5(json_encode())
        $paramsRedisKey = [__CLASS__, __FUNCTION__, $queryParams];
        // 操作类型 1先读取缓存 ；2 获得数据前，是否再读一次缓存并判断是否有效[有效则直接返回，不从源数据读了]--防击穿，以及雪崩，穿透
        $operateType = 1 | 2;

        $requestData = static::getDataCache($modelObj,
            function(&$isOpenCache) use(&$modelObj, &$queryParams) {// 重新读取数据 注意一定要有返回值[缓存的就是这个值]
                // 可以缓存时，对读取的字段，加入缓存相关的字段 --- 注意： 确定需要缓存才加入要缓存的字段
                if($isOpenCache && isset($queryParams['select']) && !empty($queryParams['select'])){
                    $queryParams['select'] = array_merge($queryParams['select'], static::getCacheFields($modelObj, 1 | 2 | 4));
                }

                // 真正获得数据的代码
                static::resolveSqlParams($modelObj, $queryParams);
                $requestData = $modelObj->get();

                // 返回
                return $requestData;
            }, $redisKeyPre, $paramsRedisKey, $cacheType, $operateRedis, $queryParams, $select, $isOpenCache, $operateType);

        // #####开始使用缓存数据功能#######结束#################################################################
        $areaCityList = is_object($requestData) ? $requestData->toArray() : $requestData;// 使用缓存加的

        // 没有使用缓存前的原代码
//        static::resolveSqlParams($modelObj, $queryParams);
//        $areaCityList = $modelObj->get()->toArray();
        if ( !isset($kv['key']) || !isset($kv['val']) ) return $areaCityList;
        return Tool::formatArrKeyVal($areaCityList, $kv['key'], $kv['val']);
    }

    //###############插入/修改数据时，数据进行验证##########################
    /**
     * 功能：插入/修改数据时，对数据进行验证
     *
     * @param array $judgeType 验证类型 1 普通数据验证--[默认] ; 2 新建数据验证 ；4 修改数据验证
     * @param array $judgeData 待验证数据 一维/二维
     * @param array $mustFields 必填字段
     * @param string $configUbound 读取配置数组下的指这下标，可为空：读整个配置
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return mixed  throws 错误 boolean: true 成功 ; array 具体错误 一维/二维的[根数据的维数一样]
     *  [
     *      'firstErr' => $firstErr,// 第一条错误信息
     *       'errMsg' => $ItemErrMsg,// 所有错误数组 一维数组
     *       'varErrMsg' => $varErrMsg,// 验证变量名[可为空]的，按此下标分组错误信息 每个下标变量下是二维数组
     *  ]
     * @author zouyan(305463219@qq.com)
     *
     */
    public static function judgeDBData(&$modelObj, $judgeType = 1, &$judgeData = [], $mustFields = [], $configUbound = 'fields', $errDo = 1){
        $dbDir = $modelObj::$modelPath;
        $table_name = $modelObj->getTable();
        $result = Tool::judgeInDBData($judgeType, $judgeData, $mustFields, $table_name, $configUbound, $dbDir, 'models', $errDo);
        if(is_string($result)){
            // $error = "没有配置信息！";
            if($errDo == 1) throws($result);
            return $result;
        }
        return $result;
    }

    // 对错误有错进行throws抛出 ,成功，则返回 true;有错，则throws 或返回
    // $reType 有错返回类型 1 throws错误， 2返回字符串
    // $errSlipChar 错误分隔符
    public static function judgeDBDataThrowErr(&$modelObj, $judgeType = 1, &$judgeData = [], $mustFields = [], $reType = 1, $errSlipChar = "<br/>", $configUbound = 'fields'){
        $isMulti = Tool::isMultiArr($judgeData, false);
        $result =static::judgeDBData($modelObj, $judgeType, $judgeData, $mustFields, $configUbound,  1);
        if($result === true) return true;
        if(!$isMulti) $result = [$result];
        $firstErr = [] ;// 第一条错误信息 的数组
        $ItemErrMsg = [] ;// 所有错误数组 一维数组
        $varErrMsg = [] ;  // 验证变量名[可为空]的，按此下标分组错误信息 每个下标变量下是二维数组
        foreach($result as $k => $v){
            $temItemErr = $v['errMsg'] ?? [];
            if(!empty($temItemErr)){
                if(!is_array($temItemErr)) $temItemErr = [$temItemErr];
                foreach($temItemErr as $temErr){
                    $ItemErrMsg[] = $temErr;
                }
                // $ItemErrMsg = array_merge($ItemErrMsg, $temItemErr);
            }
        }
        $ItemErrMsg = array_unique($ItemErrMsg);
        $errMsg = implode($errSlipChar, $ItemErrMsg);
        if(($reType & 1) == 1) throws($errMsg);
        return $errMsg;
    }

    //##########缓存相关的#######开始#################################################################################

    /**
     * 获得数据-从缓存优先 --块缓存
     *
     * @param object $modelObj 当前模型对象
     * @param mixed $fun 没有缓存时，要读取数据 的闭包函数  function(&$isOpenCache){}
     *                                              // $isOpenCache  引用传参 是否开启缓存 true:开启/使用缓存；false：不使用缓存
     * @param string $redisKeyPre 会作为缓存键的前缀标识   调用的方法名  一般用这个 __FUNCTION__ ； array $cacheType 缓存类型 1 单条记录缓存时  主键字段数组 --一维数组 ['字段名1', '字段名2',....]
     * @param array $paramsRedisKey redis 会作为缓存键的参数数组- 相关的参数, 会md5(json_encode()) ； array $cacheType 缓存类型 1 单条记录缓存时 主键字段值数组 --一维数组 ['字段名1值', '字段名2值',....]
     * @param int $cacheType 缓存类型 1 单条记录缓存 2 块级缓存[默认]
     * @param int $operateRedis 缓存时对数据的操作  1 转为json 2 序列化 ; 3 不转换  ；如果是对象或数组请用2
     * @param array $queryParams 查询请求  主要抽取查询条件where whereIn数组  主要用到下标 ；含有 orWhere 不判断
     *                  where whereBetween 二维数组 ；数组每项中 ：第一个参数为字段名  ， 如果是两个值数组直接判断第一个项字段；如果有三个值数组，要判断第二个为=号
     *                      [
     *                          ['city', '1'],
     *                          ['status', '=', '1'],
     *                          ['subscribed', '<>', '1'],
     *                      ]
     *                  'whereIn' 二维数组 [ [字段名=>[多个字段值]],....]
     * @param array $select 查询要获取的字段数组 主要用来判断要请求的字段是否包含排除字段 一维数组
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @param int $operateType  操作类型 1先读取缓存 ；2 获得数据前，是否再读一次缓存并判断是否有效[有效则直接返回，不从源数据读了]--防击穿，以及雪崩，穿透
     * @return array 数据对象
     * @author zouyan(305463219@qq.com)
     */
    public static function getDataCache(&$modelObj, $fun, $redisKeyPre, $paramsRedisKey, $cacheType, $operateRedis = 2, $queryParams = [], $select = [], $isOpenCache = true, $operateType = 1 | 2){
        $modelObjCopy = Tool::copyObject($modelObj);
        // $select = $queryParams['select'] ?? [];
        // $cacheType = 2;// 缓存类型 1 单条记录缓存 2 块级缓存[默认]

        // 是否开启缓存 true:开启/使用缓存；false：不使用缓存
        if($isOpenCache) $isOpenCache = static::getDefaultOpenCache($modelObj, $cacheType, $select);// true;

        if($isOpenCache) Log::info('数据缓存日志 --缓存请求数据-->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . (is_string($redisKeyPre) ? $redisKeyPre : ''), [ $redisKeyPre, $paramsRedisKey, $cacheType, $select, $isOpenCache]);// , $queryParams
        if(($cacheType & 1) == 1){// 1 单条记录缓存
            $redisKey = $modelObj->getCacheKey(5, $redisKeyPre, $paramsRedisKey);
        }else{
            $redisKey = static::getBlockDataRedisKey($modelObj, $redisKeyPre, $paramsRedisKey);
        }
        $requestData = Tool::getRedisCacheData($fun,
            function(&$cacheData, &$isReadOrCache, &$isOpenCache)use(&$modelObjCopy, &$cacheType, &$queryParams, &$select){// 开启缓存且缓存有数据  对读取到的缓存数据进行处理

                // echo '1$isReadOrCache = ' . var_dump($isReadOrCache) . '<br/>';
                if(!$isReadOrCache && $isOpenCache){
                    // 缓存失效，重新读取可能还会缓存
                    $result = $modelObjCopy->judgeCacheData($cacheType, 16, $cacheData, $queryParams, $select, 1);
                    if(!$result) $isReadOrCache = true;// 缓存失效
                }
                // echo '2$isReadOrCache = ' . var_dump($isReadOrCache) . '<br/>';
            },
            function(&$readData, &$isOpenCache)use(&$modelObjCopy, &$cacheType, &$queryParams, &$select){// 开启了缓存时 对读取到的原数据进行处理
                // echo '1$isOpenCache = ' . var_dump($isOpenCache) . '<br/>';
                if($isOpenCache){
                    if(!$modelObjCopy->setDbUpdateTimeCache($cacheType, 8, $readData, $queryParams, $select, 1)) $isOpenCache = false;
                }
                // echo '2$isOpenCache = ' . var_dump($isOpenCache) . '<br/>';
            },
            static::getRedisKeyPre($modelObj),
            $redisKey,
            static::getRedisCacheExpire($modelObj), $operateRedis, $isOpenCache, $operateType,
            static::getRedisOpenCache($modelObj),
            static::getRedisCacheExtendExpire($modelObj)
        );
        if($requestData === false || $requestData === null) throws('获取数据失败，请稍后重试!');

        return $requestData;
    }

    /**
     * 单条记录缓存处理
     * 根据主键缓存或次缓存，获得数据--参数为空，则返回空数组
     *  cacheDb:U:m:{email值}_{email值}  -> {id值}
     * @param object $modelObj 当前模型对象
     * @param array $paramsPrimaryVals 主键或主键相关缓存字段及值 刚好[字段不能多]用上缓存，不然就用不了缓存 [ '字段1' => '字段1的值','字段2' => ['字段2的值1', '字段2的值2']] ;为空，则返回空数组--注意字段是刚好[主键或主字段]，不能有多,顺序无所谓
     * @param array $select 查询要获取的字段数组 一维数组
     * @param array $relations 关系
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @return  mixed 获得的单 条记录对象
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoByCache(&$modelObj, $paramsPrimaryVals = [], $select = [], $relations = [], $isOpenCache = true)
    {
        $keyRedisPre = $modelObj->getCacheSimple();// 前缀

        $funParams = [$paramsPrimaryVals, $select, $relations];// 相关参数
        $dbDataInfo = [];// 最终数据
        if(empty($paramsPrimaryVals)) return $dbDataInfo;// throws('请求参数不能为空！');

        $fieldKeys = array_keys($paramsPrimaryVals);// 查询的字段数组-一维数组

        // 未开通缓存 或 返回的字段包含排除字段
        if(!$isOpenCache || !$modelObj->isDefaultOpenCache(1, $select)){
            Log::info('数据缓存日志 --详情--未开通缓存 或 返回的字段包含排除字段-->'  . date('Y-m-d H:i:s') . $keyRedisPre  . __CLASS__ . '->' . __FUNCTION__, $funParams);
            // 肯定全新表读取数据
            $dbDataInfo = static::getInfoByBlockCache($modelObj, $paramsPrimaryVals, $select, $relations, false);
            return $dbDataInfo;
        }
        // 以下都是可以缓存的情况汇： 开通缓存  不包含排除字段

        // 获得缓存主字段数组
        $cachePrimaryFields = $modelObj->getAllCacheKeyFields(1);

        // 刚好是主键缓存
        // 先判断主键是否就可以获得结果
        if(Tool::isEqualArr($cachePrimaryFields, $fieldKeys, 1) ){
            Log::info('数据缓存日志 --详情--通过主键获得数据-->'  . date('Y-m-d H:i:s') . $keyRedisPre  . __CLASS__ . '->' . __FUNCTION__, $funParams);
            $dbDataInfo = static::setInfoByCache($modelObj, $paramsPrimaryVals, $select, $relations, $isOpenCache);
            return $dbDataInfo;
        }

        // 不用缓存，则直接从数据表获取数据--没有/不是相关缓存，也不是主缓存
        if(!$modelObj->hasPrimaryRelevantKeyFields()){
            Log::info('数据缓存日志 --详情--没有主键相关缓存，直接从数据表获取-->'  . date('Y-m-d H:i:s') . $keyRedisPre  . __CLASS__ . '->' . __FUNCTION__, $funParams);
            // 这里可以用块级缓存--底层已经使用块级缓存
            $dbDataInfo = static::getInfoByBlockCache($modelObj, $paramsPrimaryVals, $select, $relations, $isOpenCache);
            return $dbDataInfo;
        }
        // 1:没有匹配的相关主键 ；2有相关匹配的字段-但没有缓存值/缓存值有问题/主键有变化---需要重新缓存；4 有相关匹配的字段--相关缓存有效；
        $readPrimaryKey = 1;// 是否有读主键缓存 true:有读:false:没有

        $PKeyFields = $modelObj->getCacheKeyFieldsArr(2);
        foreach($PKeyFields as $k => $pkFields){
            if(empty($pkFields)) continue;
            // 选择都包含的
            if(Tool::isEqualArr($pkFields, $fieldKeys, 1)){
                // 有主键值缓存，获取数据
                // 主缓存键的字段值  U:{id值}_{email值}  中的 {id值}_{email值}
                $MkeyVals = $modelObj->getCacheFieldsVal(4, $pkFields, $paramsPrimaryVals, 3);
                // 没有主键值缓存
                if($MkeyVals === false || !is_string($MkeyVals) || strlen($MkeyVals) <= 0){
                    $readPrimaryKey = 2;
                    continue;
                }
                list($primaryFieldsArr, $primaryFieldsValsArr, $temFieldsVals) = array_values($modelObj->analyseFieldsValsText($MkeyVals, true));
                if(empty($primaryFieldsArr) || empty($primaryFieldsValsArr)){
                    $readPrimaryKey = 2;
                    continue;
                }
                // 主键字段有变化
                if(!Tool::isEqualArr($cachePrimaryFields, $primaryFieldsArr, 4)){
                    $readPrimaryKey = 2;
                    continue;
                }
                $dbDataInfo = static::getInfoByCache($modelObj, $temFieldsVals, $select, $relations, $isOpenCache);
                $readPrimaryKey = 4;
                break;
            }
        }
        // 获得缓存成功-直接返回
        if(($readPrimaryKey & 4) == 4) return $dbDataInfo;

        // 1:没有匹配的相关主键 ；
        if(($readPrimaryKey & 1) == 1){
            Log::info('数据缓存日志 --详情--未从缓存读过，需要重新缓存-->'  . date('Y-m-d H:i:s') . $keyRedisPre  . __CLASS__ . '->' . __FUNCTION__, $funParams);
            // 这里可以用块级缓存--底层已经使用
            $dbDataInfo = static::getInfoByBlockCache($modelObj, $paramsPrimaryVals, $select, $relations, $isOpenCache);
            return $dbDataInfo;
        }
        // 下面是2 的情况
        $modelObjCopy = Tool::copyObject($modelObj);
        // 获得主键的值
        $temInfo = static::getInfoByBlockCache($modelObjCopy, $paramsPrimaryVals, $cachePrimaryFields, [], $isOpenCache);
        $dataInfo = is_object($temInfo) ? $temInfo->toArray() : $temInfo;
        $isMulti = Tool::isMultiArr($dataInfo, false);
        // 主键数据为空
        if(!is_array($dataInfo) || empty($dataInfo) || $isMulti){
            // 这里可以用块级缓存--底层已经使用
            $dbDataInfo = static::getInfoByBlockCache($modelObj, $paramsPrimaryVals, $select, $relations, $isOpenCache);
            return $dbDataInfo;
        }
        $primaryKV = Tool::getArrFormatFields($dataInfo, $cachePrimaryFields, true);
        // 重新缓存数据
        $dbDataInfo = static::setInfoByCache($modelObj, $primaryKV, $select, $relations, $isOpenCache);
        return $dbDataInfo;

    }

    /**
     * 单条记录查单条缓存
     * @param object $modelObj 当前模型对象
     * @param array $fieldVals 查询字段及值数组  一维数组 [ '字段1' => '字段1的值','字段2' => ['字段2的值1', '字段2的值2']] ;为空，则返回空数组
     * @param array $select 查询要获取的字段数组 一维数组
     * @param array $relations 关系
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @return  null 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public static function setInfoByCache(&$modelObj, $fieldVals = [], $select = [], $relations = [], $isOpenCache = true){

        // 如果缓存，需要缓存的字段
        $cacheAllFields = $modelObj->getCacheFields();

        $modelObjCopy = Tool::copyObject($modelObj);

        // #####开始使用缓存数据功能#######开始#################################################################
        $queryParams = static::getGueryParams($fieldVals, $select);
        if(empty($queryParams)) return [];
        $fieldKeys = array_keys($fieldVals);// 查询的字段数组-一维数组

        // 查询要获取的字段数组 主要用来判断要请求的字段是否包含排除字段 一维数组
//        $select = $queryParams['select'] ?? [];// static::getCacheFields($modelObj, 1 | 2 | 4)
        // 缓存类型 1 单条记录缓存 2 块级缓存[默认]
        $cacheType = 1;
        // 缓存时对数据的操作  1 转为json 2 序列化 ; 3 不转换  ；如果是对象或数组请用2
        $operateRedis = 2;
        // 会作为缓存键的前缀标识   调用的方法名  一般用这个 __FUNCTION__
        $redisKeyPre = $fieldKeys;// __FUNCTION__;
        // 会作为缓存键的参数数组- 相关的参数, 会md5(json_encode())
        $paramsRedisKey = $fieldVals;// [__CLASS__, __FUNCTION__, $queryParams];
        // 操作类型 1先读取缓存 ；2 获得数据前，是否再读一次缓存并判断是否有效[有效则直接返回，不从源数据读了]--防击穿，以及雪崩，穿透
        $operateType = 1 | 2;

        $dbDataInfo = static::getDataCache($modelObj,
            function(&$isOpenCache) use(&$modelObj, &$queryParams, &$fieldVals, &$select, &$cacheAllFields) {// 重新读取数据 注意一定要有返回值[缓存的就是这个值]
                // 可以缓存时，对读取的字段，加入缓存相关的字段 --- 注意： 确定需要缓存才加入要缓存的字段
                if($isOpenCache && isset($queryParams['select']) && !empty($queryParams['select'])){
                    $queryParams['select'] = array_merge($queryParams['select'], static::getCacheFields($modelObj, 1 | 2 | 4));
                }

                // 真正获得数据的代码
                $dbDataInfo = static::getInfoByFieldVals($modelObj, $fieldVals, $cacheAllFields, false);

                // 返回
                return $dbDataInfo;
            }, $redisKeyPre, $paramsRedisKey, $cacheType, $operateRedis, $queryParams, $select, $isOpenCache, $operateType);

        // #####开始使用缓存数据功能#######结束#################################################################

        //缓存源- 肯定全新表读取数据
//        $dbDataInfo = static::getInfoByFieldVals($modelObj, $fieldVals, $cacheAllFields, false);
        // Log::info('数据缓存日志 --详情--重新从数据表获得数据-->'  . date('Y-m-d H:i:s') . $keyRedisPre  . __CLASS__ . '->' . __FUNCTION__, $funParams);
        // 重新缓存数据
        // $modelObj->cacheInfo($dbDataInfo, $fieldVals, $cacheAllFields);

        // 查询关系参数
        CommonDB::resolveRelations($dbDataInfo, $relations);

        // 返回需要获取的字段
        if(!empty($select))  $dbDataInfo = $modelObjCopy->dataFormatSelect($dbDataInfo , $select);
        return $dbDataInfo;
    }

    /**
     * 单条记录查询块级缓存--查询条件不在主键或主键相关字段中可块级缓存
     * @param object $modelObj 当前模型对象
     * @param array $fieldVals 查询字段及值数组  一维数组 [ '字段1' => '字段1的值','字段2' => ['字段2的值1', '字段2的值2']] ;为空，则返回空数组
     * @param array $select 查询要获取的字段数组 一维数组
     * @param array $relations 关系
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @return  null 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoByBlockCache(&$modelObj, $fieldVals = [], $select = [], $relations = [], $isOpenCache = true){
        // 注：这里用不着再写此块缓存了，因为下面的方法getInfoByFieldVals底层已经有块级缓存了。
        //     是否使用缓存主要就看 $isOpenCache 参数值
        // #####开始使用缓存数据功能#######开始#################################################################

//        $queryParams = static::getGueryParams($fieldVals, $select);
//        if(empty($queryParams)) return [];
//
//        // 查询要获取的字段数组 主要用来判断要请求的字段是否包含排除字段 一维数组
//        $select = $queryParams['select'] ?? [];// static::getCacheFields($modelObj, 1 | 2 | 4)
//        // 缓存类型 1 单条记录缓存 2 块级缓存[默认]
//        $cacheType = 2;
//        // 缓存时对数据的操作  1 转为json 2 序列化 ; 3 不转换  ；如果是对象或数组请用2
//        $operateRedis = 2;
//        // 会作为缓存键的前缀标识   调用的方法名  一般用这个 __FUNCTION__
//        $redisKeyPre = __FUNCTION__;
//        // 会作为缓存键的参数数组- 相关的参数, 会md5(json_encode())
//        $paramsRedisKey = [__CLASS__, __FUNCTION__, $queryParams];
//        // 操作类型 1先读取缓存 ；2 获得数据前，是否再读一次缓存并判断是否有效[有效则直接返回，不从源数据读了]--防击穿，以及雪崩，穿透
//        $operateType = 1 | 2;
//
//        $dbDataInfo = static::getDataCache($modelObj,
//            function(&$isOpenCache) use(&$modelObj, &$queryParams, &$fieldVals, &$select) {// 重新读取数据 注意一定要有返回值[缓存的就是这个值]
//                // 可以缓存时，对读取的字段，加入缓存相关的字段 --- 注意： 确定需要缓存才加入要缓存的字段
//                if($isOpenCache && isset($queryParams['select']) && !empty($queryParams['select'])){
//                    $queryParams['select'] = array_merge($queryParams['select'], static::getCacheFields($modelObj, 1 | 2 | 4));
//                }
//
//                // 真正获得数据的代码
//                $dbDataInfo = static::getInfoByFieldVals($modelObj, $fieldVals, $select, false);
//
//                // 返回
//                return $dbDataInfo;
//            }, $redisKeyPre, $paramsRedisKey, $cacheType, $operateRedis, $queryParams, $select, $isOpenCache, $operateType);
//
//        // #####开始使用缓存数据功能#######结束#################################################################

        $dbDataInfo = static::getInfoByFieldVals($modelObj, $fieldVals, $select, $isOpenCache);
        // 查询关系参数
        CommonDB::resolveRelations($dbDataInfo, $relations);
        return $dbDataInfo;
    }

    /**
     * 生成缓存键前缀
     *
     * @param object $modelObj 当前模型对象
     * @return string // cacheDB:RunBuy:U:
     * @author zouyan(305463219@qq.com)
     */
    public static function getRedisKeyPre(&$modelObj){
        $redisKey = static::exeMethod($modelObj, 'getCacheSimple', []);
        return $redisKey;
    }

    /**
     * 生成缓存键--数据块
     *
     * @param object $modelObj 当前模型对象
     * @param string $fun 方法名，调前的
     * @param array $params 相关的参数, 会md5(json_encode())
     * @return string // cacheDB:RunBuy:U:
     * @author zouyan(305463219@qq.com)
     */
    public static function getBlockDataRedisKey(&$modelObj, $fun, $params){
        if(!is_array($params)) $params = [$params];
        $redisKey = $fun . ':' . md5(json_encode($params));
        return $redisKey;
    }

    /**
     * 获得缓存有效期
     *
     * @param object $modelObj 当前模型对象
     * @return int  缓存有效期 单位秒
     * @author zouyan(305463219@qq.com)
     */
    public static function getRedisCacheExpire(&$modelObj){
        $cacheExpire = static::exeMethod($modelObj, 'getDataCacheExpire', []);
        return $cacheExpire;
    }

    /**
     * 获得单位时间内，访问多少次，开启缓存--目的去掉冷数据 如：1分钟访问2次，则开启缓存
     * @return   array  $openCache // 单位时间内，访问多少次，开启缓存--目的去掉冷数据 如：1分钟访问2次，则开启缓存
     *                  // 值[] 空时，会使用 public.DBDataCache.openCache 配置
     *  [
     *         'expire' => 60 * 3,// 单位时长，单位秒  建议：2-10分钟
     *        'requestNum' => 3,// 访问次数
     *    ];
     * @author zouyan(305463219@qq.com)
     */
    public static function getRedisOpenCache(&$modelObj){
        $openCache = static::exeMethod($modelObj, 'getDataOpenCache', []);
        return $openCache;
    }

    /**
     * 获得缓存自动延期设置 单位时间内访问多少次时，自动延长有效期 10分钟 8次 自动延长有效期
     * @return   array $extendExpire // 缓存自动延期设置 单位时间内访问多少次时，自动延长有效期 10分钟 8次 自动延长有效期 可延长3次
     *                  // 值[] 空时，会使用 public.DBDataCache.extendExpire 配置     *
     * [
     *        'expire' => 60 * 3,// 单位时长，单位秒  建议：2-10分钟
     *       'requestNum' => 8,// 访问次数
     *        'maxExendNum' => 3,// 可延长3次
     *    ]
     * @author zouyan(305463219@qq.com)
     */
    public static function getRedisCacheExtendExpire(&$modelObj){
        $extendExpire = static::exeMethod($modelObj, 'getDataCacheExtendExpire', []);
        return $extendExpire;
    }

    /**
     * 默认是否开启缓存
     *
     * @param object $modelObj 当前模型对象
     * @param int $cacheType 缓存类型 1 单条记录缓存 2 块级缓存[默认]
     * @param array $select 查询要获取的字段数组 一维数组
     * @return  boolean 是否开启缓存 true:开启/使用缓存；false：不使用缓存
     * @author zouyan(305463219@qq.com)
     */
    public static function getDefaultOpenCache(&$modelObj, $cacheType = 2, $select = []){
        $isDefaultOpenCache = static::exeMethod($modelObj, 'isDefaultOpenCache', [$cacheType, $select]);// cacheDB:RunBuy:U:
        return $isDefaultOpenCache;
    }

    /**
     * 获得需要缓存用到的字段-- 一维数据
     *
     * @param object $modelObj 当前模型对象
     * @param int $dataType 需要获得的数据字段 1 主键 ；2：值需要作为缓存主键键的字段；4 值需要参与块级缓存的字段；
     * @return  array 是否开启缓存 true:开启/使用缓存；false：不使用缓存
     * @author zouyan(305463219@qq.com)
     */
    public static function getCacheFields(&$modelObj, $dataType = 1){
        $cacheFields = static::exeMethod($modelObj, 'getAllCacheKeyFields', [$dataType]);// cacheDB:RunBuy:U:
        return $cacheFields;
    }

    /**
     * 获得缓存开通权限
     *
     * @param object $modelObj 当前模型对象
     * @return  int 缓存开通权限值
     * @author zouyan(305463219@qq.com)
     */
    public static function getCachePowerNum(&$modelObj){
        $cachePowerNum = 0;
        $primaryKey = static::getPrimaryKey($modelObj);//  缓存键的主键字段-- 一维数组,也可能是空
        // 没有主键，则不缓存数据
        if(!is_array($primaryKey) || empty($primaryKey)) return $cachePowerNum;

        // 1 缓存详情 2缓存块
        $isCacheBlock = $modelObj->isCacheBlock();//  缓存块
        if($isCacheBlock) $cachePowerNum = ($cachePowerNum | 2);
        $isCacheInfo = $modelObj->isCacheInfo();//  缓存单条记录
        if($isCacheInfo) $cachePowerNum = ($cachePowerNum | 1);
        return $cachePowerNum;
    }

    /**
     * 判断是否修改了缓存字段
     *
     * @param object $modelObj 当前模型对象
     * @param array $dataParams 需要新的数据-- 一维/二维数组--主要用到数组的下标判断是否缓存的键中
     * @return  boolean 是否修改了缓存字段 true:修改缓存字段 ；false:没有修改缓存字段
     * @author zouyan(305463219@qq.com)
     */
    public static function hasModifyCacheField(&$modelObj, $dataParams){
        $hasModifyField = false;
        $cacheFields = static::getCacheFields($modelObj, 1 | 2 | 4);

        $isMulti = Tool::isMultiArr($dataParams, true);

        $dataArr = is_object($dataParams) ? $dataParams->toArray() : $dataParams;

        foreach($dataArr as $info){
            $infoArr = is_object($info) ? $info->toArray() : $info;
            $modifyFields = array_keys($infoArr);
            $intersectFields = array_intersect($cacheFields, $modifyFields);
            if(!empty($intersectFields)){
                $hasModifyField = true;
                break;
            }
        }
        if(!$isMulti) $dataParams = $dataParams[0] ?? [];
        return $hasModifyField;
    }

    // !!!!!!!!!!!!!!!!!更新数据时!!!!更新缓存信息!!!!!从数据表实时读!!!开始!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

    /**
     * 根据查询条件获得数据
     *
     * @param object $modelObj 当前模型对象
     * @param array $recordData 需要更新的记录 -一维或二维数组
     * @param int $operateType 操作类型 1 增[不用返回值]； 2 改 [不用返回值]--一般用这个；4 删除[不用返回值] ；
     *                      更新表记录时，更新表更新时间缓存-  对缓存，不主动创建的原则，执行时没有则创建。这里有缓存键，
     *                      则更新缓存值和有效期。
     * @param boolean $judgeFieldsInCacheFields 是否判断数据中的字段在缓存字段中 true:需要判断 ； false:不用判断[默认]
     * @param array $judgeDataParams 需要判断数据中是否有缓存字段的数据-- 一维/二维数组--主要用到数组的[下标]判断是否缓存的键中 ; $judgeFieldsInCacheFields 为 true时需要此参数
     * @return   mixed true:成功 false:失败 sting 具体错误 ； throws 错误
     * @author zouyan(305463219@qq.com)
     */
    public static function updateTimeByData(&$modelObj, &$recordData, $operateType = 1, $judgeFieldsInCacheFields = false, $judgeDataParams = []){
        if($judgeFieldsInCacheFields ){
            if(!static::hasModifyCacheField($modelObj, $judgeDataParams)){
                return true;
            }
//            $judgeFieldsInCacheFields = false;
        }

//        $hasModifyCache = true;// 是否更新缓存 true:更新缓存 ；false:不更新缓存
//        if($judgeFieldsInCacheFields && !static::hasModifyCacheField($modelObj, $judgeDataParams)) $hasModifyCache = false;
//        if(!$hasModifyCache) return true;

        // 记录已经要更新缓存的对象及数据--方便事务失败回滚时，重新缓存
        array_push(static::$changeDBTables,[
            'tableObj' => Tool::copyObject($modelObj),
            'params' => [$operateType, $recordData, 1]
        ]);

        return $modelObj->operateDbUpdateTimeCache($operateType, $recordData, 1);
    }

    /**
     * 根据查询条件获得数据
     *
     * @param object $modelObj 当前模型对象
     * @param array  $queryParams 查询语句
     * @param int $operateType 操作类型 1 增[不用返回值]； 2 改 [不用返回值]--一般用这个；4 删除[不用返回值] ；
     *                      更新表记录时，更新表更新时间缓存-  对缓存，不主动创建的原则，执行时没有则创建。这里有缓存键，
     *                      则更新缓存值和有效期。
     * @param array $select 需要指定的字段 -一维数组；为空代表所有字段
     * @param boolean $judgeFieldsInCacheFields 是否判断数据中的字段在缓存字段中 true:需要判断 ； false:不用判断[默认]
     * @param array $judgeDataParams 需要判断数据中是否有缓存字段的数据-- 一维/二维数组 ; $judgeFieldsInCacheFields 为 true时需要此参数
     * @param int $doType 执行类型 1 返回数据 --二维数组； 2 执行缓存时间更新[默认]
     * @return   mixed true:成功 false:失败 sting 具体错误 ； throws 错误 ; array 数据二维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function updateTimeByQuery(&$modelObj, $queryParams, $operateType = 1, $select = [], $judgeFieldsInCacheFields = false, $judgeDataParams = [], $doType = 2){
        if($judgeFieldsInCacheFields ){
            if(!static::hasModifyCacheField($modelObj, $judgeDataParams)){
                if( ($doType & 1) == 1) return [];
                return true;
            }
            $judgeFieldsInCacheFields = false;
        }

        if(!is_array($select) || empty($select)) $select = static::getCacheFields($modelObj, 1 | 2 | 4);
        if(!empty($select)){
            if(!isset($queryParams['select'])) {
                $queryParams['select'] = $select;
            }else{
                $queryParams['select'] = array_merge($queryParams['select'], $select);
            }
        }
        $modelObjCopy = Tool::copyObject($modelObj);
        $recordData = static::getList($modelObj, $queryParams, [], false);
        if( ($doType & 1) == 1) return $recordData;
        // return $modelObjCopy->operateDbUpdateTimeCache($operateType, $recordData, 1);
        return static::updateTimeByData($modelObjCopy, $recordData, $operateType, $judgeFieldsInCacheFields, $judgeDataParams);
    }

    /**
     * 根据查询kv数组获得数据
     *
     * @param object $modelObj 当前模型对象
     * @param array $fieldVals 不能为空，为空，则返回空数组； 查询的字段及值 ['字段1' => '字段1的值', '字段2' => ['字段2的值1', '字段2的值2']]
     * @param int $operateType 操作类型 1 增[不用返回值]； 2 改 [不用返回值]--一般用这个；4 删除[不用返回值] ；
     *                      更新表记录时，更新表更新时间缓存-  对缓存，不主动创建的原则，执行时没有则创建。这里有缓存键，
     *                      则更新缓存值和有效期。
     * @param boolean $judgeFieldsInCacheFields 是否判断数据中的字段在缓存字段中 true:需要判断 ； false:不用判断[默认]
     * @param array $judgeDataParams 需要判断数据中是否有缓存字段的数据-- 一维/二维数组 ; $judgeFieldsInCacheFields 为 true时需要此参数
     * @param int $doType 执行类型 1 返回数据 --二维数组； 2 执行缓存时间更新[默认]
     * @return   mixed true:成功 false:失败 sting 具体错误 ； throws 错误; array 数据二维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function updateTimeByKVArr(&$modelObj, $fieldVals = [], $operateType = 1, $judgeFieldsInCacheFields = false, $judgeDataParams = [], $doType = 2){
        if($judgeFieldsInCacheFields ){
            if(!static::hasModifyCacheField($modelObj, $judgeDataParams)){
                if( ($doType & 1) == 1) return [];
                return true;
            }
            $judgeFieldsInCacheFields = false;
        }
        $select = static::getCacheFields($modelObj, 1 | 2 | 4);
        $queryParams = static::getGueryParams($fieldVals, $select);
        return static::updateTimeByQuery($modelObj, $queryParams, $operateType, $select, $judgeFieldsInCacheFields, $judgeDataParams, $doType);
    }

    /**
     * 根据查询kv数组获得数据
     *
     * @param object $modelObj 当前模型对象
     * @param string $id 记录id 主键值字符串  主键字段1的值1 小分隔符 主键字段1的值2 ... 大分隔符  主键字段2的值1 小分隔符 主键字段2的值2 ...
     * @param int $operateType 操作类型 1 增[不用返回值]； 2 改 [不用返回值]--一般用这个；4 删除[不用返回值] ；
     *                      更新表记录时，更新表更新时间缓存-  对缓存，不主动创建的原则，执行时没有则创建。这里有缓存键，
     *                      则更新缓存值和有效期。
     * @param boolean $judgeFieldsInCacheFields 是否判断数据中的字段在缓存字段中 true:需要判断 ； false:不用判断[默认]
     * @param array $judgeDataParams 需要判断数据中是否有缓存字段的数据-- 一维/二维数组 ; $judgeFieldsInCacheFields 为 true时需要此参数
     * @param int $doType 执行类型 1 返回数据 --二维数组； 2 执行缓存时间更新[默认]
     * @return   mixed true:成功 false:失败 sting 具体错误 ； throws 错误; array 数据二维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function updateTimeByPriaryKey(&$modelObj, $id, $operateType = 1, $judgeFieldsInCacheFields = false, $judgeDataParams = [], $doType = 2){
        if($judgeFieldsInCacheFields ){
            if(!static::hasModifyCacheField($modelObj, $judgeDataParams)){
                if( ($doType & 1) == 1) return [];
                return true;
            }
            $judgeFieldsInCacheFields = false;
        }
        $primaryKey = static::getPrimaryKey($modelObj);//  缓存键的主键字段-- 一维数组,也可能是空
        $fieldVals = static::getPrimaryFVArr($modelObj, $id, static::$bigSplit, static::$smallSplit, $primaryKey);// [$primaryKey => $id];
        return static::updateTimeByKVArr($modelObj, $fieldVals, $operateType, $judgeFieldsInCacheFields, $judgeDataParams, $doType);
    }


    // !!!!!!!!!!!!!!!!!更新数据时!!!!更新缓存信息!!!!!从数据表实时读!!!结束!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

    /**
     * 根据缓存指定查询字段条件的数据； 有字段且需要使用缓存时，才会查表拿数据
     *
     * @param object $modelObj 当前模型对象
     * @param array  $primaryKey 主键字段-- 一维数组 ; 为空或不是数组，则重新获取。；也可以指定其它字段
     * @param int  $cachePower 缓存状态号，无主键时，置为0
     * @param array  $queryParams 查询语句
     * @param boolean $isCacheDataByCache 读取缓存数据时,是否从缓存优先 true:有缓存优先使用缓存[默认]；false：不使用缓存,强制从数据表读取
     * @return object 数据二维数组对象
     * @author zouyan(305463219@qq.com)
     */
    public static function getCacheSelectData(&$modelObj, &$primaryKey, &$cachePower, $queryParams, $isCacheDataByCache = true){
        // 获得需要修改的数据主键值
        if(!is_array($primaryKey) || empty($primaryKey)) $primaryKey = static::getPrimaryKey($modelObj);//  缓存键的主键字段-- 一维数组,也可能是空
        // 没有主键，则不缓存数据
        if(!is_array($primaryKey) || empty($primaryKey)) $cachePower = 0;
        if($cachePower <= 0) return [];
        $modelObjCopy = Tool::copyObject($modelObj);
        $queryParams['select'] = $primaryKey;
        // $primaryData = static::getList($modelObjCopy, $queryParams, [], false);
        $primaryData = static::getList($modelObjCopy, $queryParams, [], $isCacheDataByCache);

        // $dataCacheArr = is_object($primaryData) ? $primaryData->toArray() : $primaryData;
        // return $dataCacheArr;
        return $primaryData;
    }

    /**
     * 更新数据并更新缓存，通过查询条件
     *
     * @param object $modelObj 当前模型对象
     * @param mixed $fun 更新数据 的闭包函数  function(){}; 需要返回值，可作为此函数的返回值
     *                      参数 引用 $isReturnFunRes = false;// 是否直接返回闭包函数 返回的结果，不再往下执行 。 true:是；false:不是[默认]
     *                      参数 引用 $cachePower // 0 不用缓存 1 缓存详情 2缓存块
     *                      参数 引用 $dataCacheObj = null;// 二维 要修改的缓存数据对象 --注意：有用缓存时，才会用此查询结果对象---有数据的前提是：有开启缓存$cachePower > 0
     *                      参数 引用 $dataCacheArr = [];//  二维 如果缓存时，要修改数据的值--注意：有用缓存时，才会用此查询结果数据---有数据的前提是：有开启缓存$cachePower > 0
     * @param array  $queryParams 查询语句
     * @param boolean $queryModifyEmptyDoFun 开通使用缓存时，没有查询到数据或数据没有变动时，是否还执行闭包函数 ;
     *                                      true:执行[适合闭包函数不限于查询到的数据处理]；
     *                                      false[默认]:不执行[适合查到的数据就是闭包函数需要操作的数据的情况]
     * @param array $dataParams 需要判断数据中是否有缓存字段的数据-- 一维/二维数组--主要用到数组的下标判断是否缓存的键中 ; $judgeFieldsInCacheFields 为 true时需要此参数
     * @param boolean $updateCacheTimeByOverData 保存数据后，更新缓存时间的方式
     *                              true:直接通过修改值覆盖原数据更新缓存时间(注：此时会用到$dataParams 的值且必须为一维数组) ;
     *                                          --如果确定此时的数据是最新的[与库相同]，可使用此，减少与库的查询。
     *                              false[默认]:通过主键重新获得数据并更新缓存时间--会查库
     * @param boolean $isCacheDataByCache 读取缓存数据时,是否从缓存优先 true:有缓存优先使用缓存[默认]；false：不使用缓存,强制从数据表读取
     * @return  mixed true:没有需要更新的数据 或  闭包函数的返回值 $fun
     * @author zouyan(305463219@qq.com)
     */
    public static function updateAndCacheByQuery(&$modelObj, $fun, $queryParams, $queryModifyEmptyDoFun = false, $dataParams = [], $updateCacheTimeByOverData = false, $isCacheDataByCache = true){
        // 获得是否开通缓存
        $cachePower = static::getCachePowerNum($modelObj);
        $modelObjCopy = null;
        $primaryKeyFields = static::getPrimaryKey($modelObj);//  缓存键的主键字段-- 一维数组,也可能是空
        $cacheFields = static::getCacheFields($modelObj, 1 | 2 | 4);// 所有缓存字段
        $dataCacheArr = [];// 如果缓存时，要修改数据的值
        $dataCacheObj = null;// 要修改的缓存数据对象
        // $dataPrimaryArr = [];// 如果缓存时，要修改数据的主键值
        $isReturnFunRes = false;// 是否直接返回闭包函数 返回的结果，不再往下执行 。 true:是；false:不是[默认]

        // 判断要修改的数据是否与查询数据有不同--真实更新
        $isNeedChange = true;// false:没有不同-不用重新； true:有不同，需要更新

        $isMulti = Tool::isMultiArr($dataParams, false);

        if($cachePower > 0){
            // 是一维数组, 查询返回字段加入要可能要修改字段
            if(!$isMulti){
                $dataKeys = array_keys($dataParams);
                $cacheFields = array_values(array_unique(array_merge($dataKeys, $cacheFields)));
                $isNeedChange = false;
            }
            // 获得需要修改的数据主键值
            $dataCacheObj = static::getCacheSelectData($modelObj, $cacheFields, $cachePower, $queryParams, $isCacheDataByCache);
            $dataCacheArr = is_object($dataCacheObj) ? $dataCacheObj->toArray() : $dataCacheObj;
            // if(empty($dataCacheArr)) $dataPrimaryArr = Tool::getArrFormatFields($dataCacheArr, $primaryKeyFields, true);

            // 判断要修改的数据是否与查询数据有不同--真实更新
            // $isNeedChange = ($cachePower <= 0 || $isMulti ) ? true : false;// false:没有不同-不用重新； true:有不同，需要更新
            if(!$isMulti){
                foreach($dataCacheArr as $infoData){
                    if($isNeedChange) break;
                    $diffUpdateData = array_diff_assoc($dataParams, $infoData);
                    if(!empty($diffUpdateData)){
                        $isNeedChange = true;
                        break;
                    }
                }
            }
        }

        // 确定没有修改数据，直接返回
        if( $cachePower > 0 && (empty($dataCacheArr) || !$isNeedChange)){
            if($queryModifyEmptyDoFun && is_callable($fun)){
                return $fun($isReturnFunRes, $cachePower, $dataCacheObj, $dataCacheArr);
            }
            return true;
        }

        // 使用缓存，复制对象
        if($cachePower > 0) $modelObjCopy = Tool::copyObject($modelObj);

        // 更新数据前，如果有更新缓存字段，则需要先更新缓存时间
        if($cachePower > 0) {
//            $dataCacheArr = is_object($temObj) ? $temObj->toArray() : $temObj;
            static::updateTimeByData($modelObjCopy, $dataCacheArr, 2, true, $dataParams);
//            $modelObjCopyPre = Tool::copyObject($modelObjCopy);
//            static::updateTimeByQuery($modelObjCopyPre, $queryParams, 2, static::getCacheFields($modelObjCopyPre, 1 | 2 | 4), true, $dataParams, 2);
        }

        // 查询条件
//        static::resolveSqlParams($modelObj, $queryParams);
//        $requestData = $modelObj->update($dataParams);
        $requestData = null;
        if(is_callable($fun)){
            $requestData = $fun($isReturnFunRes, $cachePower, $dataCacheObj, $dataCacheArr);
            if($isReturnFunRes) return $requestData;
        }

        // 开通缓存，则更新缓存时间信息
        if($cachePower > 0){
            if($updateCacheTimeByOverData){
                // 直接通过修改值覆盖原数据更新缓存时间
                $dataSaveCacheArr = Tool::arrAppendKeys($dataCacheArr, $dataParams);
                static::updateTimeByData($modelObjCopy, $dataSaveCacheArr, 2, false, []);
            }else{
                // 通过主键重新获得数据并更新缓存时间
                // 汇总主键值
                $PVArr = Tool::collectArrByFields($dataCacheArr, $primaryKeyFields, false, true);
                static::updateTimeByKVArr($modelObjCopy, $PVArr, 2, false, [], 2);
            }
        }
        return $requestData;
    }

    //##########缓存相关的#######结束#################################################################################

    /**
     * 生成主键字段及值的数组   ['字段1' => '字段1的值', '字段2' => ['字段2的值1', '字段2的值2']]
     * 注意：主键会有多字段的情况
     * @param object $modelObj 当前模型对象
     * @param string $fvStr 主键值字符串  主键字段1的值1 小分隔符 主键字段1的值2 ... 大分隔符  主键字段2的值1 小分隔符 主键字段2的值2 ...
     * @param string $bigSplit 主键值大分隔符
     * @param string $smallSplit 主键值小分隔符
     * @param array $primaryKey 缓存键的主键字段-- 一维数组,也可能是空[重新获取]
     * @return   array 主键及值数组 ['字段1' => '字段1的值', '字段2' => ['字段2的值1', '字段2的值2']]
     * @author zouyan(305463219@qq.com)
     */
    public static function getPrimaryFVArr(&$modelObj, $fvStr = '', $bigSplit = '@@!', $smallSplit = ',', $primaryKey = []){
        if(strlen($fvStr) <= 0 ) return [];
        if(!is_array($primaryKey) || empty($primaryKey)) $primaryKey = static::getPrimaryKey($modelObj);//  缓存键的主键字段-- 一维数组,也可能是空
        if(empty($bigSplit)) $bigSplit = static::$bigSplit;
        if(empty($smallSplit)) $smallSplit = static::$smallSplit;

        $fvArr = [];
        $bigArr = explode($bigSplit, $fvStr);
        foreach($bigArr as $k => $v){
            if (strpos($v, $smallSplit) === false) { // 单条
                $fvArr[$k] = $v;
            } else {
                $fvArr[$k] = explode($smallSplit, $v);
            }
        }
        if(count($primaryKey) != count($fvArr)) return [];
        return array_combine($primaryKey, $fvArr);
    }

    /**
     * 根据条件，获得查询
     * @param array $fieldVals 不能为空，为空，则返回空数组； 查询的字段及值 ['字段1' => '字段1的值', '字段2' => ['字段2的值1', '字段2的值2']]
     * @param array $select 需要指定的字段 -一维数组；为空代表所有字段
     * @return  array  $queryParams 查询语句
     * @author zouyan(305463219@qq.com)
     */
    public static function getGueryParams($fieldVals = [], $select = []){
        if(empty($fieldVals)) return [];
        $queryParams = [
            /*
            'where' => [
                // ['my_order_no', $out_trade_no],
            ],
            'select' => [
                'id','title','sort_num','volume'
                ,'operate_staff_id','operate_staff_id_history'
                ,'created_at' ,'updated_at'
            ],
            */
            //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
        ];
        $where = [];
        $whereIn = [];
        foreach($fieldVals as $field => $val){
            // 如果值是一维数组且只有一个值，则自动转换为一个的值---优化in
            if(is_array($val) && count($val) == 1){
                $val = array_values($val);
                $val = $val[0] ?? '';
            }
            if(!is_array($val)){// 不是数组
                $where[] = [$field, $val];
            }else{// 是数组
                $whereIn[$field] = $val;
            }
        }
        if(!empty($where) && !isset($queryParams['where'])) $queryParams['where'] = [];
        if(!empty($where)) $queryParams['where'] = array_merge($queryParams['where'], $where);

        if(!empty($whereIn) && !isset($queryParams['whereIn'])) $queryParams['whereIn'] = [];
        if(!empty($whereIn)) $queryParams['whereIn'] = array_merge($queryParams['whereIn'], $whereIn);

        if(!empty($select)) $queryParams['select'] = $select;
        return $queryParams;
    }

    /**
     * 是否数据库结构有改动
     *
     * @param string $keyRedisPre 数据缓存前缀 引用返回此变量
     * @param string $database_model_dir_name 数据库的数据目录
     * @param string $table_name 数据表名称
     * @return boolean 是否数据表有变动 true:有变动[不可用缓存，重新缓存]；  false:无变动[缓存有效可用]
     * @author zouyan(305463219@qq.com)
     */
    public static function getDBChangeStatus(&$keyRedisPre, $database_model_dir_name, $table_name = ''){
        $version = config('public.DBDataCache.version', 0);
        return static::getDBChangeStatusBase($keyRedisPre, $database_model_dir_name, $table_name, $version, '');
    }

    /**
     * 是否具体的数据库表模型结构有改动
     *
     * @param object $modelObj 当前模型对象
     * @param string $keyRedisPre 数据缓存前缀 引用返回此变量
     * @param string $database_model_dir_name 数据库的数据目录
     * @param string $table_name 数据表名称
     * @return boolean 是否数据表有变动 true:有变动[不可用缓存，重新缓存]；  false:无变动[缓存有效可用]
     * @author zouyan(305463219@qq.com)
     */
    public static function getDBModelChangeStatus(&$modelObj, &$keyRedisPre, $database_model_dir_name, $table_name = ''){

//        $modelName = $database_model_dir_name . '\\' . $table_name;
//        // 验证数据
//        $modelObj = null;
//        static::getObjByModelName($modelName, $modelObj);
        $version = Tool::getAttr($modelObj, 'cacheVersion', 1);// config('public.DBDataCache.version', 0);
        if(strlen($version) == 0) $version = '0';

        return static::getDBChangeStatusBase($keyRedisPre, $database_model_dir_name, $table_name, $version, 'model');
    }

    /**
     * 是否数据库结构有改动
     *
     * @param string $keyRedisPre 数据缓存前缀 引用返回此变量
     * @param string $database_model_dir_name 数据库的数据目录
     * @param string $table_name 数据表名称
     * @param string $configVersion 当前的版本号
     * @param string $typePre 各种类型的前缀，第一种必须唯一  getDBChangeStatus: 的值为空。 表模型：的值为model
     * @return boolean 是否数据表有变动 true:有变动[不可用缓存，重新缓存]；  false:无变动[缓存有效可用]
     * @author zouyan(305463219@qq.com)
     */
    public static function getDBChangeStatusBase(&$keyRedisPre, $database_model_dir_name, $table_name = '', $configVersion = '', $typePre = ''){
//        $dbKey = Tool::getProjectKey(64, ':', ':');
//        $keyRedisPre = $dbKey . 'dbfields:';
//        if(strlen($database_model_dir_name) > 0 ) $keyRedisPre .= $database_model_dir_name . ':';
        if(strlen($keyRedisPre) <= 0) $keyRedisPre = static::getKeyRedisPre($database_model_dir_name);

        $forceReCache = false;// 是否强制重新缓存 true:强制缓存;有变动[不可用缓存，重新缓存] ; false:不强制缓存;无变动[缓存有效可用]
        // 获得版本号
        $versionRedisKey = ((strlen($table_name) > 0) ? $table_name . ':' : '') . 'version';
        if(strlen($typePre) > 0 ) $versionRedisKey .= ':' . $typePre;
        $version = Tool::getRedis($keyRedisPre . $versionRedisKey, 3);
        // $configVersion = config('public.DBDataCache.version', 0);
        if($version === false || ($version != $configVersion)){
            $forceReCache = true;
            Tool::setRedis($keyRedisPre, $versionRedisKey, $configVersion, 0 , 3);
        }
        return $forceReCache;

    }

    /**
     * 获得缓存前缀
     *
     * @param string $database_model_dir_name 数据库的数据目录
     * @return string 缓存前缀  数据库关键字;--如果为空，则默认为 数据库ip + 数据库端口 + 数据库名 : 'dbfields:' [数据库的数据目录：]  注意：最后有：符号
     * @author zouyan(305463219@qq.com)
     */
    public static function getKeyRedisPre($database_model_dir_name){
        $dbKey = Tool::getProjectKey(64, ':', ':');
        $keyRedisPre = $dbKey . 'dbfields:';
        if(strlen($database_model_dir_name) > 0 ) $keyRedisPre .= $database_model_dir_name . ':';
        return $keyRedisPre;
    }

    // *********同步数据表***相关操作******开始****************************************

    /**
     * 获得同步表相关的信息
     * @param $modelObj
     * @return array
     *   ['obj' => '表对象', 'tModel' => '表模型的名称[QualityControl\Staff]', 'tModelDir' => '模型文件目录[QualityControl]'
     * , 'tModelName' => '模型文件名[StaffDoing]', 'tBack' => '加的表后缀[doing]'
     * , 'tPower' => '权限0/1:增、2改  ; 4：删 （1｜ 2：可做业务同步 ；1 ｜ 2 | 4： 操作全同步表【含删除】）']
     */
    public static function getDoingObj($modelObj){
        $return = [];
        // $objName = Tool::getClassBaseName($modelObj,2);
        $objPathName = Tool::getClassBaseName($modelObj, 1);
        if(empty($objPathName)) throws('model class 不存在！');
        $objNameArr = explode("\\", $objPathName);
        $modelFileName = $objNameArr[count($objNameArr) - 1] ?? '';
        if(empty($modelFileName)) throws('model 名称 不存在！');
        $modelDir = $objNameArr[count($objNameArr) - 2] ?? '';
        if(empty($modelDir)) throws('model 目录 不存在！');
        $objName = $modelDir . "\\" . $modelFileName;
            // throws($objName);
        // 获得自有属性
        $ownProperty = Tool::getAttr($modelObj, 'ownProperty', 1);
        // 获得同步表
        $syncTables = Tool::getAttr($modelObj, 'syncTables', 1);
        if(empty($syncTables)) $syncTables = ['doing' => (1 | 2 | 4)];

        // 如果有历史且同步表
//        if(($ownProperty & 32) == 32){
//            $modelAllObj = null;
//            static::getObjByModelName($objName . ucfirst('history'), $modelAllObj);
        //  CommonDB::getHistory($mainObj, $mId, $historyObj, $historyTable, $historySearch, $ignoreFields);
//        }
        if(($ownProperty & 64) == 64){
            foreach($syncTables as $t_back => $t_power){
                $modelDoingObj = null;
                static::getObjByModelName($objName . ucfirst($t_back), $modelDoingObj);
                // static::getObjByModelName($objName, $modelDoingObj);
                array_push($return, ['obj' => $modelDoingObj, 'tModel' => $objName . ucfirst($t_back), 'tModelDir' => $modelDir
                    , 'tModelName' =>  $modelFileName . ucfirst($t_back), 'tBack' => $t_back, 'tPower' => $t_power]);
            }
        }
        return $return;
    }

    /**
     * 对同步的表进行与主表相同的操作
     * @param int $mainId 操作的主表记录id ;可为0：则不进行记录判断
     * @param array $doingArr  getDoingObj($modelObj) 方法返回的同步表的对象相关的信息
     * @param string $fun 需要执行的方法名
     * @param array $params  执行方法需要传递的参数数组 ，可写参数变量
     *                {OBJ} ： 会转换为相关同步表的 对象
     *                {TMODEL} ： 会转换为相关同步表的 表模型的目录及文件名称 QualityControl\StaffDoing
     *                {TMODELDIR} ： 会转换为相关同步表的 表模型的目录 QualityControl
     *                {TMODELNAME} ： 会转换为相关同步表的 表模型的文件名称 StaffDoing
     *                {TBACK} ： 会转换为相关同步表的 加的表后缀 doing
     *                {TPOWER} ： 会转换为相关同步表的 权限 1
     * @param int $power 需要执行的方法名 需要的权限标识  权限0/1:增、2改  ; 4：删
     * @param int $funType 执行方法类型  1:commonDB的静态方法 ；2commonDB的普通方法；4数据模型model的普通方法 ->  ; 8 数据模型model的静态方法 ::
     * @return bool
     */
    public static function doDoingSync($mainId, $doingArr = [], $fun = '', $params = [], $power = 1 | 2 | 4, $funType = 1){
        if(empty($doingArr) || empty($fun)) return true;
        static::doTransactionFun(function() use(&$doingArr, &$fun, &$params, &$power, &$mainId, &$funType) {
            foreach ($doingArr as $v) {
                $temParams = $params;
                foreach ($temParams as &$t_v) {
                    if ($t_v == '{OBJ}') {
                        $t_v = $v['obj'];
                    }
                    if ($t_v == '{TMODEL}') {
                        $t_v = $v['tModel'];
                    }
                    if ($t_v == '{TMODELDIR}') {
                        $t_v = $v['tModelDir'];
                    }
                    if ($t_v == '{TMODELNAME}') {
                        $t_v = $v['tModelName'];
                    }
                    if ($t_v == '{TBACK}') {
                        $t_v = $v['tBack'];
                    }
                    if ($t_v == '{TPOWER}') {
                        $t_v = $v['tPower'];
                    }
                }
                // 有操作权限
                if(($v['tPower'] & $power) > 0){
                    // 有可能记录已经不存在了，怎么处理
                    $modelObjCopy = Tool::copyObject($v['obj']);
                    $infoObj = [];
                    if(!empty($mainId)) $infoObj = static::getInfoById($modelObjCopy, $mainId, [], '', false);
                    // 有相同的权限 且  同步表记录存在
                    if( empty($mainId) || ( !empty($mainId) &&  !empty($infoObj) ) ){
                        if($funType == 1) {
                            static::{$fun}(...$temParams);
                            // static::saveById($v['obj'], $dataParams, $id);
                        }elseif($funType == 2){
                            $this->{$fun}(...$temParams);
                        }elseif($funType == 4){
                            $modelObj = $v['obj'];
                            $modelObj->{$fun}(...$temParams);
                        }elseif($funType == 8){
                            $modelObj = $v['obj'];
                            $modelObj::{$fun}(...$temParams);
                        }
                    }
                }
            }
        });
    }

    // *********同步数据表***相关操作******结束****************************************

    // *********数据表生成主键相关操作******开始****************************************
    /**
     * 设置数据主键的值
     * @param $modelObj
     * @param array $dataParams 主键及要修改的字段值 一维/二维数组 数组；数据中有主键值且不为空，则用数据中的
     * @param string $primaryKey  主键字段---如果为空，则重新通过对象获取
     * @param boolean $forceIncr 如果 数据模型的 主键id的值类型 为  1自增id时 ：是否通过直接读取表中当前的最大主键值来补充数据中的主键；true：是： false:不用处理数据中的主键值
     * @param boolean $isCacheDataByCache 读取缓存数据时,是否从缓存优先 true:有缓存优先使用缓存[默认]；false：不使用缓存,强制从数据表读取
     * @return  array 数组 $dataParams
     */
    public static function setPrimaryKeyVal($modelObj, &$dataParams, $primaryKey = '', $forceIncr = false, $isCacheDataByCache = true){
        // $modelObjArr = static::getDoingObj($modelObj);
        // pr(111);
        // 主键id的值类型：
        //      1自增id[默认]
        //      2计数器，缓存redis，自增，redis没有，则查表中最大值自增
        //      按时间生成 bigint类型
        //          4一秒1  0000个   2【位】+6【位】+ 秒2【位】+自增数5【位】 = 15【位】
        //          8一分钟100 0000个   2【位】+6【位】+自增数6【位】 = 14【位】

        // 获得主键字段---默认为id
        if(empty($primaryKey)){
            $primaryKey = static::exeMethod($modelObj, 'getKeyName', []);
        }
        if(empty($primaryKey)) return $dataParams;// 没有主键字段直接返回
        $primaryFormatKey = str_replace(['_', '-'], ['', ''], $primaryKey);

        // 获得对象类全称 App\Models\QualityControl\CertificateSchedule
        $objPathName = Tool::getClassBaseName($modelObj,1);//  . 'bbb' . $primaryKeyValType;
        $objPathNameMD = md5($objPathName);

        // 获得 主键id的值类型
        $primaryKeyValType = static::getAttr($modelObj, 'primaryKeyValType', 1);
        if($primaryKeyValType == 1){
            if($forceIncr){
                static::fillPrimaryValByDB($modelObj, $dataParams, $primaryKey, $objPathNameMD, $isCacheDataByCache);
            }
            return $dataParams;// 自增直接返回
        }

        // 如果是一维数组,则转为二维数组
        $isMulti = Tool::isMultiArr($dataParams, true);

        $primaryKeyValArr = [];
        if(!in_array($primaryKeyValType, [1])){// ,2 $primaryKeyValType == 256计数器，批量的，自动优先批量生成，在使用的过程中自动补充。--不浪费【没有使用的，自动回收重新历用】
            $dataCount = count($dataParams);
            $getType = 2;
            if($primaryKeyValType == 256) $getType = 1;
            $primaryKeyValArr = static::getMultiSignerArr($modelObj, $getType, $dataCount, 20, 3, $primaryKeyValType, $primaryKey, [], 60
                , $objPathNameMD, $isCacheDataByCache);
        }
        foreach($dataParams as &$info){
            if(isset($info[$primaryKey]) && !empty($info[$primaryKey])) continue;// 主键存在且有值
            if(!in_array($primaryKeyValType, [1]) && (!empty($primaryKeyValArr))){//,2 $primaryKeyValType == 256
                $info[$primaryKey] = array_shift($primaryKeyValArr);
                continue;
            }
            // $primaryKeyVal = static::getPrimaryKeyVal($modelObj, 2, [], $primaryKeyValType, $primaryKey, md5($objPathName), $isCacheDataByCache);
            // pr($primaryKeyVal . '--');
            // $info[$primaryKey] = $primaryKeyVal;
        }
        if(!$isMulti) $dataParams = $dataParams[0] ?? [];
        return $dataParams;
    }

    /**
     * 通过查询数据库中最在的值，设置新数据的主键的值
     * @param $modelObj
     * @param array $dataParams 主键及要修改的字段值 一维/二维数组 数组；数据中有主键值且不为空，则用数据中的
     * @param string $primaryKey  主键字段---如果为空，则重新通过对象获取
     * @param string $objPathNameMD  对象类全称 App\Models\QualityControl\CertificateSchedule 的 md5值 ---如果为空，则重新通过对象获取
     * @param boolean $isCacheDataByCache 读取缓存数据时,是否从缓存优先 true:有缓存优先使用缓存[默认]；false：不使用缓存,强制从数据表读取
     * @return  array 数组 $dataParams
     */
    public static function fillPrimaryValByDB($modelObj, &$dataParams, $primaryKey = '', $objPathNameMD = '', $isCacheDataByCache = true){

        // 获得主键字段---默认为id
        if(empty($primaryKey)){
            $primaryKey = static::exeMethod($modelObj, 'getKeyName', []);
        }
        if(empty($primaryKey)) return $dataParams;// 没有主键字段直接返回
        // 获得对象类全称 App\Models\QualityControl\CertificateSchedule 的md5值

        if(empty($objPathNameMD)){
            $objPathNameMD = md5(Tool::getClassBaseName($modelObj,1));
        }

        // 如果是一维数组,则转为二维数组
        $isMulti = Tool::isMultiArr($dataParams, true);

        // 是否需要加入新的值下标
        $isNeedAddNewPrimaryVal = false;
        foreach($dataParams as &$v){
            if(isset($v[$primaryKey]) && (!empty($v[$primaryKey]))) continue;
            $isNeedAddNewPrimaryVal = true;
            break;
        }
        if($isNeedAddNewPrimaryVal){
            Tool::lockDoSomething('lock:' . Tool::getUniqueKey([Tool::getProjectKey(1, ':', ':')
                    , __CLASS__, __FUNCTION__, $primaryKey, $objPathNameMD]),
                function()  use(&$modelObj, &$dataParams, &$primaryKey, &$objPathNameMD, &$isCacheDataByCache){//

                    $redisKeyPre = 'dbfieldprimaryval' . $primaryKey . ':' . Tool::getProjectKey(1, ':', ':');
                    $redisKey = $objPathNameMD;

                    // 获得最大的id值
                    $usedPrimaryVal = static::getDBMaxPrimaryId($modelObj, $primaryKey, $isCacheDataByCache);

                    // 从缓存中获取最大值
                    $cacheMaxPrimaryVal = RedisString::numIncr($redisKeyPre . $redisKey, 0, $usedPrimaryVal , 1);
                    // 如果缓存中的值更大，则使用缓存中的
                    if($cacheMaxPrimaryVal > $usedPrimaryVal) $usedPrimaryVal = $cacheMaxPrimaryVal;

                    foreach($dataParams as &$v){
                        if(isset($v[$primaryKey]) && (!empty($v[$primaryKey]))) continue;
                        $v[$primaryKey] = ++$usedPrimaryVal;
                    }
                    // 更新缓存
                    Tool::setRedis($redisKeyPre, $redisKey, $usedPrimaryVal, 0 , 3);

                }, function($errDo){
                    // TODO
                    $errMsg = '失败，请稍后重试!';
                    if($errDo == 1) throws($errMsg);
                    return $errMsg;
                }, true, 1, 2000, 2000);
        }

        if(!$isMulti) $dataParams = $dataParams[0] ?? [];
        return $dataParams;
    }

    /**
     * 获得表中记录的最大主键值
     * @param $modelObj
     * @param string $primaryKey  主键字段---如果为空，则重新通过对象获取
     * @param boolean $isCacheDataByCache 读取缓存数据时,是否从缓存优先 true:有缓存优先使用缓存[默认]；false：不使用缓存,强制从数据表读取
     * @return  int 表中记录的最大主键值
     */
    public static function getDBMaxPrimaryId($modelObj, $primaryKey = '', $isCacheDataByCache = true){
        // 获得主键字段---默认为id
        if(empty($primaryKey)){
            $primaryKey = static::exeMethod($modelObj, 'getKeyName', []);
        }
        if(empty($primaryKey)) return 0;// 没有主键字段直接返回

        // 获得最大的id值
        $queryParams = [
            'orderBy' => [$primaryKey => 'desc'],
        ];
        $modelObjCopy = Tool::copyObject($modelObj);
        $info = static::getInfoByQuery($modelObjCopy, 1, $queryParams, [], $isCacheDataByCache);
        $usedPrimaryVal = $info[$primaryKey] ?? 0;
        return $usedPrimaryVal;
    }

    /**
     * 获得对象新的[待加入的]主键的值
     * @param object $modelObj
     * @param int $getType 获取数据的方式 1：缓存池 【可回收使用】；2 实时获取 【默认】；-- $valType:256 会强制用缓存池，$valType:2会强制不用缓存池，其它也不建议用缓存池
     * @param array $fixParams 前中后缀，默认都为空，不用填
     * [
     *  'prefix' => '',// 前缀[1-2位] 可填;可写业务编号等
     *  'midFix' => '',// 日期后中间缀[1-2位] 可填;适合用户编号里的后2位或其它等
     *  'backfix' => '',// 后缀[1-2位] 可填;备用
     * ]
     * @param int $primaryKeyValType  主键id的值类型;如果 <=0,则重新通过对象获取--如果 模型对象没有设置，则必须指定传入，才有值
     * @param string $primaryKey  主键字段---如果为空，则重新通过对象获取
     * @param string $objPathNameMD  对象类全称 App\Models\QualityControl\CertificateSchedule 的 md5值 ---如果为空，则重新通过对象获取
     * @param boolean $isCacheDataByCache 读取缓存数据时,是否从缓存优先 true:有缓存优先使用缓存[默认]；false：不使用缓存,强制从数据表读取
     * @return int 0 没有获取到， > 0 成功获取到
     */
    public static function getPrimaryKeyVal($modelObj, $getType = 2, $fixParams = [], $primaryKeyValType = 0, $primaryKey = '', $objPathNameMD = '', $isCacheDataByCache = true){
        $primaryKeyVal = 0;
        // 获得 主键id的值类型
        if(!is_numeric($primaryKeyValType) || $primaryKeyValType <= 0){
            $primaryKeyValType = static::getAttr($modelObj, 'primaryKeyValType', 1);
            if($primaryKeyValType == 1 || empty($primaryKeyValType)) return $primaryKeyVal;// 自增直接返回-- 如果不是传入的，时空的，自己获取的，则进行判断 是否为 1
        }

        // 获得主键字段---默认为id
        if(empty($primaryKey)){
            $primaryKey = static::exeMethod($modelObj, 'getKeyName', []);
        }
        if(empty($primaryKey)) return $primaryKeyVal;// 没有主键字段直接返回
        $primaryFormatKey = str_replace(['_', '-'], ['', ''], $primaryKey);


        // 获得对象类全称 App\Models\QualityControl\CertificateSchedule 的md5值
        if(empty($objPathNameMD)){
            $objPathNameMD = md5(Tool::getClassBaseName($modelObj,1));
        }

//        switch($primaryKeyValType) {
//            case 2:// 2
//                 $redisKeyPre = 'idvalincr' . $primaryKey . ':' . Tool::getProjectKey(1, ':', ':');
//                 // $redisKey = $objPathNameMD;
//                $primaryKeyVal = Signer::getNewIncrVal($redisKeyPre, $objPathNameMD,function() use(&$modelObj, &$primaryKey, &$isCacheDataByCache){
//                    // 获得最大的id值
//                    $queryParams = [
//                        'orderBy' => [$primaryKey => 'desc'],
//                    ];
//                    $modelObjCopy = Tool::copyObject($modelObj);
//                    $info = static::getInfoByQuery($modelObjCopy, 1, $queryParams, [], $isCacheDataByCache);
//                    $temPrimaryKeyVal = $info[$primaryKey] ?? 0;
//                    // pr($temPrimaryKeyVal);
//                    // $temPrimaryKeyVal++;
//                    return $temPrimaryKeyVal;
//                });
//                break;
//            case 256:// 计数器，批量的，自动优先批量生成，在使用的过程中自动补充。--不浪费【没有使用的，自动回收重新历用】
//                $primaryKeyValArr = static::getMultiSignerArr($modelObj, $getType, 1, 10, 3, 256, $primaryKey, [], 60
//                    , $objPathNameMD, $isCacheDataByCache);
//                $primaryKeyVal = $primaryKeyValArr[0] ?? 0;
//                break;
//            // 按时间生成 bigint类型
//            // 年【2位】+每年中第几分钟【60*24*365=525600 6位】+ 秒【2位】--长度15位
//            case 4:// 4一秒1  0000个   2【位】+6【位】+ 秒2【位】+自增数4【位】 = 16【位】
//                $fixParams = array_merge($fixParams, [
//                    // 'prefix' => '',// 前缀[1-2位] 可填;可写业务编号等
//                    // 'midFix' => '',// 日期后中间缀[1-2位] 可填;适合用户编号里的后2位或其它等
//                    // 'backfix' => '',// 后缀[1-2位] 可填;备用
//                    'expireNums' => [],// redis设置缓存 ，在两个值之间时 - 二维 [[1,20,'缓存的秒数365 * 24 * 60 * 60'], [40,50,'缓存的秒数']]
//                    'needNum' => 1 | 2 | 4 | 8,// 需要拼接的内容 1 年 2日期 4 自定义日期格式 8 自增的序号
//                    'dataFormat' => 's', // needNum 值为 4时的日期格式  'YmdHis'
//                ]);
//                $primaryKeyVal = Tool::makeOrder($objPathNameMD . $primaryFormatKey , $fixParams, 5);
//                break;
//            case 8:// 8一分钟100 0000个   2【位】+6【位】+自增数6【位】 = 16【位】
//                $fixParams = array_merge($fixParams, [
//                    // 'prefix' => '',// 前缀[1-2位] 可填;可写业务编号等
//                    // 'midFix' => '',// 日期后中间缀[1-2位] 可填;适合用户编号里的后2位或其它等
//                    // 'backfix' => '',// 后缀[1-2位] 可填;备用
//                    'expireNums' => [],// redis设置缓存 ，在两个值之间时 - 二维 [[1,20,'缓存的秒数365 * 24 * 60 * 60'], [40,50,'缓存的秒数']]
//                    'needNum' => 1 | 2 | 8,// 需要拼接的内容 1 年 2日期 4 自定义日期格式 8 自增的序号
//                    'dataFormat' => '', // needNum 值为 4时的日期格式  'YmdHis'
//                ]);
//                $primaryKeyVal = Tool::makeOrder($objPathNameMD . $primaryFormatKey , $fixParams, 6);
//                break;
//            case 16:// 一秒1  0000个 年【2位】+ 日期[一年中的第几天 001-365] 3位 +每天中第几秒钟 一天中的第几秒   0-86400【5位】+自增数5【位】 --长度15位
//                $fixParams = array_merge($fixParams, [
//                    // 'prefix' => '',// 前缀[1-2位] 可填;可写业务编号等
//                    // 'midFix' => '',// 日期后中间缀[1-2位] 可填;适合用户编号里的后2位或其它等
//                    // 'backfix' => '',// 后缀[1-2位] 可填;备用
//                    'expireNums' => [],// redis设置缓存 ，在两个值之间时 - 二维 [[1,20,'缓存的秒数365 * 24 * 60 * 60'], [40,50,'缓存的秒数']]
//                    'needNum' => 1 | 16 | 64 | 8,// 需要拼接的内容 1 年 2日期 4 自定义日期格式 8 自增的序号
//                    'dataFormat' => '', // needNum 值为 4时的日期格式  'YmdHis'
//                ]);
//                $primaryKeyVal = Tool::makeOrder($objPathNameMD . $primaryFormatKey , $fixParams, 5);
//                break;
//            case 32:// 一分钟100 0000个 年【2位】+ 日期[一年中的第几天 001-365] 3位 +每天中时分钟 H时i分【4位】 +自增数6【位】 --长度15位
//                $fixParams = array_merge($fixParams, [
//                    // 'prefix' => '',// 前缀[1-2位] 可填;可写业务编号等
//                    // 'midFix' => '',// 日期后中间缀[1-2位] 可填;适合用户编号里的后2位或其它等
//                    // 'backfix' => '',// 后缀[1-2位] 可填;备用
//                    'expireNums' => [],// redis设置缓存 ，在两个值之间时 - 二维 [[1,20,'缓存的秒数365 * 24 * 60 * 60'], [40,50,'缓存的秒数']]
//                    'needNum' => 1 | 4 | 16 | 8,// 需要拼接的内容 1 年 2日期 4 自定义日期格式 8 自增的序号
//                    'dataFormat' => 'Hi', // needNum 值为 4时的日期格式  'YmdHis'
//                ]);
//                $primaryKeyVal = Tool::makeOrder($objPathNameMD . $primaryFormatKey , $fixParams, 6);
//                break;
//            case 64:// 一秒1  0000个 年【2位】+ 日期[月日] 4位 +每天中第几秒钟 一天中的第几秒   0-86400【5位】+自增数5【位】 --长度16位
//                $fixParams = array_merge($fixParams, [
//                    //  'prefix' => '',// 前缀[1-2位] 可填;可写业务编号等
//                    // 'midFix' => '',// 日期后中间缀[1-2位] 可填;适合用户编号里的后2位或其它等
//                    // 'backfix' => '',// 后缀[1-2位] 可填;备用
//                    'expireNums' => [],// redis设置缓存 ，在两个值之间时 - 二维 [[1,20,'缓存的秒数365 * 24 * 60 * 60'], [40,50,'缓存的秒数']]
//                    'needNum' => 1 | 4 | 64 | 8,// 需要拼接的内容 1 年 2日期 4 自定义日期格式 8 自增的序号
//                    'dataFormat' => 'md', // needNum 值为 4时的日期格式  'YmdHis'
//                ]);
//                $primaryKeyVal = Tool::makeOrder($objPathNameMD . $primaryFormatKey , $fixParams, 5);
//                break;
//            case 128:// 一分钟100 0000个 年【2位】+ 日期[月日] 4位 ++每天中时分钟 H时i分【4位】 +自增数6【位】 --长度16位
//                $fixParams = array_merge($fixParams, [
//                    // 'prefix' => '',// 前缀[1-2位] 可填;可写业务编号等
//                    // 'midFix' => '',// 日期后中间缀[1-2位] 可填;适合用户编号里的后2位或其它等
//                    // 'backfix' => '',// 后缀[1-2位] 可填;备用
//                    'expireNums' => [],// redis设置缓存 ，在两个值之间时 - 二维 [[1,20,'缓存的秒数365 * 24 * 60 * 60'], [40,50,'缓存的秒数']]
//                    'needNum' => 1 | 4 | 8,// 需要拼接的内容 1 年 2日期 4 自定义日期格式 8 自增的序号
//                    'dataFormat' => 'mdHi', // needNum 值为 4时的日期格式  'YmdHis'
//                ]);
//                $primaryKeyVal = Tool::makeOrder($objPathNameMD . $primaryFormatKey , $fixParams, 6);
//                break;
//            default:
//                $primaryKeyValArr = static::getMultiSignerArr($modelObj, $getType, 1, 10, 3, $primaryKeyValType, $primaryKey, $fixParams, 60
//                    , $objPathNameMD, $isCacheDataByCache);
//                $primaryKeyVal = $primaryKeyValArr[0] ?? 0;
//                break;
//        }

        $primaryKeyValArr = static::getMultiSignerArr($modelObj, $getType, 1, 10,3, $primaryKeyValType, $primaryKey, $fixParams, 60
            , $objPathNameMD, $isCacheDataByCache);
        $primaryKeyVal = $primaryKeyValArr[0] ?? 0;
        // pr($primaryKeyVal);
        return $primaryKeyVal;
    }

    /**
     * 批量获得 计数器，批量的，自动优先批量生成，在使用的过程中自动补充。--不浪费【没有使用的，自动回收重新历用】
     * @param object $modelObj
     * @param int $getType 获取数据的方式 1：缓存池 【可回收使用】；2 实时获取 【默认】；-- $valType:256 会强制用缓存池，$valType:2会强制不用缓存池，其它也不建议用缓存池
     * @param int $valNums 需要获得值的数量 默认 1 ; --  超过 > $maxValNum 参数的值，则 会自动分批获取并合并
     * @param int $maxValNum  最大的可用记录数量 默认 10-- 缓存中最多可用的数量 ;-- $getType = 2 $getType 时使用
     * @param int $minAvailableNum 记录可用数量最低数量，超过这个数就需要自动填满  默认 3---单次最多获取数量 -- 不要超过 $maxValNum 参数 ;-- $getType = 2 $getType 时使用
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
     * @param string $primaryKey  主键字段---如果为空，则重新通过对象获取
     * @param array $fixParams 前中后缀，默认都为空，不用填
     * [
     *  'prefix' => '',// 前缀[1-2位] 可填;可写业务编号等
     *  'midFix' => '',// 日期后中间缀[1-2位] 可填;适合用户编号里的后2位或其它等
     *  'backfix' => '',// 后缀[1-2位] 可填;备用
     * ]
     * @param int $overTime  占用的超时时间 --单位秒  默认:60秒 ;-- $getType = 2 $getType 时使用
     * @param string $objPathNameMD  对象类全称 App\Models\QualityControl\CertificateSchedule 的 md5值 ---如果为空，则重新通过对象获取
     * @param boolean $isCacheDataByCache 读取缓存数据时,是否从缓存优先 true:有缓存优先使用缓存[默认]；false：不使用缓存,强制从数据表读取
     * @return array 新的主键值数组 [值1,值2,...]
     */
    public static function getMultiSignerArr($modelObj, $getType = 2, $valNums = 1, $maxValNum = 20, $minAvailableNum = 3, $valType = 256, $primaryKey = '', $fixParams = [], $overTime = 60
        , $objPathNameMD = '', $isCacheDataByCache = true){
        $primaryKeyValArr = [];
        // 获得主键字段---默认为id
        if(empty($primaryKey)){
            $primaryKey = static::exeMethod($modelObj, 'getKeyName', []);
        }
        if(empty($primaryKey)) return $primaryKeyValArr;// 没有主键字段直接返回
        $primaryFormatKey = str_replace(['_', '-'], ['', ''], $primaryKey);

        // 获得对象类全称 App\Models\QualityControl\CertificateSchedule 的md5值
        if(empty($objPathNameMD)){
            $objPathNameMD = md5(Tool::getClassBaseName($modelObj,1));
        }

        // BathSigner::getVals
        $redisKeyPre = $primaryFormatKey . ':' . Tool::getProjectKey(1, ':', ':');
        // 缓存池
        if(($getType == 1 && $valType != 2) || $valType == 256){
            $redisKeyPre = 'dbfieldincrbath' . $redisKeyPre;
            $redisKey = $objPathNameMD;
            $primaryKeyValArr = BathSigner::getValsBath($redisKeyPre, $redisKey, $valNums, $maxValNum, $minAvailableNum
                , $valType, $fixParams, $overTime, function() use(&$modelObj, &$primaryKey, &$isCacheDataByCache){
                    // 获得最大的id值
                   return static::getDBMaxPrimaryId($modelObj, $primaryKey, $isCacheDataByCache);
//                    $queryParams = [
//                        'orderBy' => [$primaryKey => 'desc'],
//                    ];
//                    $modelObjCopy = Tool::copyObject($modelObj);
//                    $info = static::getInfoByQuery($modelObjCopy, 1, $queryParams, [], $isCacheDataByCache);
//                    return $info[$primaryKey] ?? 0;
                },function($judgeUsedValArr) use(&$modelObj, &$primaryKey, &$isCacheDataByCache){
                    $reArr = [];// ['值1' => true, '值2' => false,...] ;

                    $modelObjCopy = Tool::copyObject($modelObj);
                    $queryParams = [
                        'whereIn' => [$primaryKey => $judgeUsedValArr],
                    ];
                    $usedListObj = static::getList($modelObjCopy, $queryParams, [], $isCacheDataByCache);

                    $usedList = Tool::objectToArray($usedListObj);
                    // 转为参数代码为下标的数组
                    $usedFormatList = Tool::arrUnderReset($usedList, $primaryKey, 1, '_');
                    foreach($judgeUsedValArr as $usedVal){
                        $temInfo = $usedFormatList[$usedVal] ?? [];
                        $reArr[$usedVal] = (!empty($temInfo)) ? true : false;
                    }
                    return $reArr;
                });
        }else{
            $redisKeyPre = 'dbfieldincr' . $redisKeyPre;
            $redisKey = $objPathNameMD;
            $primaryKeyValArr = Signer::getNewValArr($redisKeyPre, $redisKey, $valType, $valNums, $fixParams
                , function() use(&$modelObj, &$primaryKey, &$isCacheDataByCache){
                    // 获得最大的id值
                    return static::getDBMaxPrimaryId($modelObj, $primaryKey, $isCacheDataByCache);
//                    $queryParams = [
//                        'orderBy' => [$primaryKey => 'desc'],
//                    ];
//                    $modelObjCopy = Tool::copyObject($modelObj);
//                    $info = static::getInfoByQuery($modelObjCopy, 1, $queryParams, [], $isCacheDataByCache);
//                    return $info[$primaryKey] ?? 0;
                });
        }
        return $primaryKeyValArr;
    }
    // *********数据表生成主键相关操作******结束****************************************

}
