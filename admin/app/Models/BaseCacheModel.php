<?php
// 数据缓存层
namespace App\Models;

use App\Services\DB\CommonDB;
use App\Services\Redis\RedisString;
use App\Services\Tool;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class BaseCacheModel extends BaseModel
{

    //****************数据据缓存**相关的***开始********************************************
    /**
     * 用户真实缓存数据 cacheDb:数据库目录:U:{id值}_{email值}  -> {id值}|&|....
     * cacheDb:数据库目录:U:e:{email值}  -> {id值}
     * cacheDb:数据库目录:U:m:{email值}_{email值}  -> {id值}
     * cacheDb:数据库目录:U:a:{email值}  -> {id值}
     *
     *
     *   3表缓存时间
     *   cacheDb:数据库目录:U:{Ttbl} =》 时间{分隔符!!!}微秒的整数16855800
     *
     *   1主键缓存键
     *   cacheDb:数据库目录:U:{Tinfo}:{字段下标-多个_分隔}:{{id值}_{email值}}=》 时间{分隔符!!!}微秒的整数16855800
     *
     *   5单条记录缓存数据
     *  cacheDb:数据库目录:U:{info}:{字段下标-多个_分隔}:{{id值}_{email值}}=》 缓存数据
     *
     *   4其它主键缓存，指向 主键缓存键
     *   cacheDb:数据库目录:U:{TpriVal}:{字段下标-多个_分隔}:{字段值-多个_分隔} =》 {字段下标-多个_分隔}:{{id值}_{email值}}
     *
     *   2多情况缓存
     *   cacheDb:数据库目录:U:{Tblock}:{字段下标-多个_分隔}:{字段值-多个_分隔}=》 时间{分隔符!!!}微秒的整数16855800
     *
     *
     */
    public static $cachePre = 'cacheDB';// 缓存键最前面的关键字  cacheDb:U:{id值}_{email值}  中的 cacheDb
    public static $separatoChar = '__';// 缓存相关的分隔符-主要是键;注意合法性，会作为redis键的一部分
    public static $cacheTimeTableKey = 'Ttbl';// 缓存表更新时间时的缓存关键字
    public static $cacheTimeBlockKey = 'Tblock';// 缓存块更新时间时的缓存关键字
    public static $cacheTimeInfoKey = 'Tinfo';// 缓存表具体详情更新时间时的缓存关键字
    public static $cacheInfoKey = 'info';// 缓存表具体详情数据的缓存关键字
    public static $cachePrimaryValInfoKey = 'TpriVal';// 缓存表其它缓存字段缓存主键值的缓存关键字
    public static $operateRedis = 2;// 操作 1 转为json 2 序列化 ; 3 不转换 ---最好用2 序列化，不然可能会有问题
    public static $cacheExpire = 0;// 60 * 60 * 24 * 10;// 10 天 缓存的时间长度 ; 值<= 0时，会使用 public.DBDataCache.expire 配置

    // 1 缓存详情 2缓存块[确定没有用到关系的块，可以缓存]
    //  public.DBDataCache.cacheType 配置打开，且各模型也打开才会有对应缓存
    public static $cacheType = (1 | 2);// 0
    // 最大缓存数据行数，如果>此值的数据不缓存。; 值<= 0时，会使用 public.DBDataCache.maxCacheRows 配置
    public static $maxCacheRows = 0;

    public static $cacheSimple = '';// 'U';// 表名简写,为空，则使用表名

    public static $cacheVersion = '';// 内容随意改[可0{空默认为0}开始自增]- 如果运行过程中，有直接对表记录进行修改，增加或修改字段名，则修改此值，使表记录的相关缓存过期。
    // $cacheExcludeFields 为空：则缓存所有字段值；排除字段可能是大小很大的字段，不适宜进行缓存
    public static $cacheExcludeFields = [];// 表字段中排除字段; 有值：要小心，如果想获取的字段有在排除字段中的，则不能使用缓存


    public static $cachePrimaryFields = '';// 'id';//格式 '字段a ' 或 一维数组 ['字段b','字段c',....] 为空，则通过 表的主键缓存，再没有就不缓存

    // 可作为单条记录缓存的字段 格式 ['e' => '字段a ', 'm' => ['字段b','字段c',....] 值需要作为缓存键的字段，缓存值为指向 id 字段
    // 多字段的数组为 层级关系，如：从左到右为 第一层[城市站缓存]、第二层[代理站缓存]、第三层[商家站缓存]、第四层[店铺站缓存]...
    public static $cachePrimaryKeyFields = [];// ['e' => 'email', 'm' => 'mobile', 'a' => 'accounts'];

    // 此属性有值；则是多情况（多种平台应该；如按城市分站）缓存，为空：系统/公用类别的缓存
    // 块数据缓存时，需要标记缓存的字段 格式 ['e' => '字段a ', 'm' => ['字段b','字段c',....] 值需要作为缓存键的字段
    // 多字段的数组为 层级关系，如：从左到右为 第一层[城市站缓存]、第二层[代理站缓存]、第三层[商家站缓存]、第四层[店铺站缓存]...
    // 为空，则表级缓存块
    // 有新下标加入或字段变动，所有缓存会自动失效。删除下标：不会影响已有缓存
    public static $cacheBlockFields = [];

    // 单位时间内，访问多少次，开启缓存--目的去掉冷数据 如：1分钟访问2次，则开启缓存
    // 值[] 空时，会使用 public.DBDataCache.openCache 配置
    public static $openCache = [
//        'expire' => 60 * 3,// 单位时长，单位秒  建议：2-10分钟
//        'requestNum' => 3,// 访问次数
    ];
    // 缓存自动延期设置 单位时间内访问多少次时，自动延长有效期 10分钟 8次 自动延长有效期 可延长3次
    // 值[] 空时，会使用 public.DBDataCache.extendExpire 配置
    public static $extendExpire = [
//        'expire' => 60 * 3,// 单位时长，单位秒  建议：2-10分钟
//        'requestNum' => 8,// 访问次数
//        'maxExendNum' => 3,// 可延长3次
    ];


    public  function aaaa(){
        $aaa = ['aaaa', 'bbb'];
        $redisKey =  md5(json_encode($aaa));
        return $redisKey;
        // return date('Y-m-d H:i:s');
        // return $this->getTableAllFields();
        // $info = CommonDB::getInfoById($this, 5, ['id', 'name', 'is_active'], '', false);
        // return $info['is_active_text'];
        // return $this->select(['id', 'name', 'is_active'])->find(5)->toArray();
        // return $this->getTableAllFields();
        // return $this->getDBAppendsFields();
        // return '123';
    }

    //######获得配置相关的############开始#########################################

    /**
     * 获得缓存键时间的rediis配置
     *  使用 list($keyRedisPre, $cacheExpire, $dateTime, $msecint, $dateTimeMsecint) = array_values($this->getCacheTimeConfig());
     * @return   array  ['缓存前缀', '缓存时间', '缓存时间']
     * @author zouyan(305463219@qq.com)
     */
    public function getCacheTimeConfig(){
        $keyRedisPre = $this->getCacheSimple();// 'cacheDb:' . $cacheSimple . ':'; // U:{id值}
        $cacheExpire = $this->getDataCacheExpire() + 0;// 60 * 60 * 24 * 10;
        $dateTime = date('Y-m-d H:i:s');
        $msecint = (Tool::msecTime())['msecint'];
        $dateTimeMsecint = Tool::timeJoinMsec($dateTime, $msecint);
        return [
            'keyRedisPre' => $keyRedisPre,
            'cacheExpire' => $cacheExpire,
            'dateTime' => $dateTime,
            'msecint' => $msecint,
            'dateTimeMsecint' => $dateTimeMsecint
        ];
    }

    /**
     * 解析文字串 {字段下标-多个_分隔}:{字段值-多个_分隔}  fv下标  $isNeedKV 决定
     * @param string $fieldsValsStr 需要解析文字 {字段下标-多个_分隔}:{字段值-多个_分隔}
     * @param boolean $isNeedKV 是否需要返回键值对 true:需要   false:不需要
     * @return array ['fields' => ['字段1', '字段2'] , 'vals' => ['字段1值', 字段2值'], 'fv' => ['字段1' => '字段1值', '字段2' => '字段2值']]
     * @author zouyan(305463219@qq.com)
     */
    public function analyseFieldsValsText($fieldsValsStr = '', $isNeedKV = false){
        $temBigArr = explode(':', $fieldsValsStr);
        $fields = explode(static::$separatoChar, $temBigArr[0] ?? '');
        $vals = explode(static::$separatoChar, $temBigArr[1] ?? '');
        $return = [
            'fields' => $fields,
            'vals' => $vals
        ];
        if($isNeedKV) $return['fv'] = array_combine($fields, $vals);
        return $return;
    }

    /**
     * 获得缓存的键的文字串
     * @param array $fields 字段数组 --一维数组 ['字段名1', '字段名2',....]
     * @param array $fieldVals 字段值数组 --一维数组 ['字段名1值', '字段名2值',....]
     * @return   string 缓存的键的文字串
     * @author zouyan(305463219@qq.com)
     */
    public function getCacheKeyValText($fields = [], $fieldVals = []){
        return implode(static::$separatoChar, $fields) . ':' . implode(static::$separatoChar, $fieldVals);
    }
    /**
     * 获得缓存的键
     * @param int $cacheType 缓存类型 1 单条记录缓存时间； 2 块级缓存[默认]有效] 时间 ；3 表缓存时间 ；4 其它主键缓存，指向 主键缓存键 ；5单条记录缓存数据
     * @param array $fields 字段数组 --一维数组 ['字段名1', '字段名2',....]
     * @param array $fieldVals 字段值数组 --一维数组 ['字段名1值', '字段名2值',....]
     * @return   string 缓存的键
     * @author zouyan(305463219@qq.com)
     */
    public function getCacheKey($cacheType, $fields = [], $fieldVals = []){
        $cacheKey = '';
        $fieldValText = $this->getCacheKeyValText($fields, $fieldVals);
        switch($cacheType) {
            case 1:// 1 单条记录缓存时间；
                //主键缓存键
                //cacheDb:数据库目录:U:{Tinfo}:{字段下标-多个_分隔}:{{id值}_{email值}}=》 时间
                $cacheKey = static::$cacheTimeInfoKey . ':' . $fieldValText;
                break;
            case 5:// 5单条记录缓存数据
                //主键缓存键
                //cacheDb:数据库目录:U:{info}:{字段下标-多个_分隔}:{{id值}_{email值}}=》 缓存数据
                $cacheKey = static::$cacheInfoKey . ':' . $fieldValText;
                break;
            case 2:// 2 块级缓存[默认]有效] 时间 ；
                //多情况缓存
                //cacheDb:数据库目录:U:{Tblock}:{字段下标-多个_分隔}:{字段值-多个_分隔}=》 时间
                $cacheKey = static::$cacheTimeBlockKey . ':' . $fieldValText;
                break;
            case 3:// 3 表缓存时间 ；
                //表缓存时间
                //cacheDb:数据库目录:U:{Ttbl} =》 时间
                $cacheKey = static::$cacheTimeTableKey ;
                break;
            case 4:// 4 其它主键缓存，指向 主键缓存键 ；
                //其它主键缓存，指向 主键缓存键
                //cacheDb:数据库目录:U:{TpriVal}:{字段下标-多个_分隔}:{字段值-多个_分隔} =》 {字段下标-多个_分隔}:{{id值}_{email值}}
                $cacheKey = static::$cachePrimaryValInfoKey . ':' . $fieldValText;
                break;
            default:
                break;
        }
        return $cacheKey;
    }

    /**
     * 设置缓存的值
     * @param mixed $cacheVal 需要缓存的值
     * @param int $cacheType 缓存类型 1 单条记录缓存时间； 2 块级缓存[默认]有效] 时间 ；3 表缓存时间 ；4 其它主键缓存，指向 主键缓存键 ；5单条记录缓存数据
     * @param array $fields 字段数组 --一维数组 ['字段名1', '字段名2',....]
     * @param array $fieldVals 字段值数组 --一维数组  ['字段名1值', '字段名2值',....]
     * @param int 选填 $operate 操作 1 转为json 2 序列化 ; 3 不转换
     * @param array $cacheTimeConfig 可为空：自动获取； 缓存键时间的rediis配置  ['缓存前缀', '缓存时间', '缓存时间']
     *                  使用 list($keyRedisPre, $cacheExpire, $dateTime) = array_values($cacheTimeConfig);
     * @param int 选填 $operateType 操作类型 1 直接设置redis值 2存在，则修改值和有效期,不存在不添加 4不存在，则创建,存在不处理 8 不存在则创建，存在则修改值和有效期
     * @return   mixed 缓存的值 ,失败返回false
     * @author zouyan(305463219@qq.com)
     */
    public function setCacheField($cacheVal, $cacheType = 1, $fields = [], $fieldVals = [], $operate = 3, $cacheTimeConfig = [], $operateType = 1){
        if(!is_array($cacheTimeConfig) || empty($cacheTimeConfig)) $cacheTimeConfig = array_values($this->getCacheTimeConfig());
        list($keyRedisPre, $cacheExpire, $dateTime, $msecint, $dateTimeMsecint) = array_values($cacheTimeConfig);

        $temCacheKey = $this->getCacheKey($cacheType, $fields, $fieldVals);
        // $result = true;
        $resultExist = true;
        $resultNX = true;
        if(($operateType & 2) == 2){//2存在，则修改值和有效期,不存在不添加
            $resultExist = RedisString::existSetnxExpire($keyRedisPre . $temCacheKey , $cacheExpire, $cacheVal, 1 | 2);
        }
        if(($operateType & 1) == 1){// 直接设置redis值
            Tool::setRedis($keyRedisPre, $temCacheKey, $cacheVal, $cacheExpire, $operate);
        }

        if( ($operateType & 4) == 4){//  4不存在，则创建,存在不处理
            $resultNX = RedisString::setnxExpire($keyRedisPre . $temCacheKey, $cacheExpire, $cacheVal);
        }
        if( ($operateType & 8) == 8){// 8 不存在则创建，存在则修改值和有效期
            RedisString::forceSetnxExpire($keyRedisPre . $temCacheKey, $cacheExpire, $cacheVal, 1 | 2);
        }
        return ($resultExist && $resultNX);
    }

    /**
     * 设置缓存的值
     * @param mixed $cacheVal 需要缓存的值
     * @param int $cacheType 缓存类型 1 单条记录缓存时间； 2 块级缓存[默认]有效] 时间 ；3 表缓存时间 ；4 其它主键缓存，指向 主键缓存键 ；5单条记录缓存数据
     * @param array $fields 字段数组 --一维数组 ['字段名1', '字段名2',....]
     * @param array $dataArr 字段值数组 --一维数组 格式为 ['字段名' => '字段值'] ；会包含所有的$fields 字段值
     * @param int 选填 $operate 操作 1 转为json 2 序列化 ; 3 不转换
     * @param array $cacheTimeConfig 可为空：自动获取； 缓存键时间的rediis配置  ['缓存前缀', '缓存时间', '缓存时间']
     *                  使用 list($keyRedisPre, $cacheExpire, $dateTime) = array_values($cacheTimeConfig);
     * @param int 选填 $operateType 操作类型 1 直接设置redis值 2存在，则修改值和有效期,不存在不添加 4不存在，则创建,存在不处理 8 不存在则创建，存在则修改值和有效期
     * @return   mixed 缓存的值 ,失败返回false
     * @author zouyan(305463219@qq.com)
     */
    public function setCacheFieldsVal($cacheVal, $cacheType = 1, $fields = [], $dataArr = [], $operate = 3, $cacheTimeConfig = [], $operateType = 1){
        if(!is_array($cacheTimeConfig) || empty($cacheTimeConfig)) $cacheTimeConfig = array_values($this->getCacheTimeConfig());
        list($keyRedisPre, $cacheExpire, $dateTime, $msecint, $dateTimeMsecint) = array_values($cacheTimeConfig);

        $t_v = Tool::getArrFormatFields($dataArr, $fields, true);

        $temCacheKey = $this->getCacheKey($cacheType, $fields, $t_v);
        // $result = true;
        $resultExist = true;
        $resultNX = true;
        if(($operateType & 2) == 2){//2存在，则修改值和有效期,不存在不添加
            $resultExist = RedisString::existSetnxExpire($keyRedisPre . $temCacheKey , $cacheExpire, $cacheVal, 1 | 2);
        }
        if(($operateType & 1) == 1){// 直接设置redis值
            Tool::setRedis($keyRedisPre, $temCacheKey, $cacheVal, $cacheExpire, $operate);
        }

        if( ($operateType & 4) == 4){//  4不存在，则创建,存在不处理
            $resultNX = RedisString::setnxExpire($keyRedisPre . $temCacheKey, $cacheExpire, $cacheVal);
        }
        if( ($operateType & 8) == 8){// 8 不存在则创建，存在则修改值和有效期
            RedisString::forceSetnxExpire($keyRedisPre . $temCacheKey, $cacheExpire, $cacheVal, 1 | 2);
        }
        return ($resultExist && $resultNX);
    }


    /**
     * 获得缓存的值
     * @param int $cacheType 缓存类型 1 单条记录缓存时间； 2 块级缓存[默认]有效] 时间 ；3 表缓存时间 ；4 其它主键缓存，指向 主键缓存键 ；5单条记录缓存数据
     * @param array $fields 字段数组 --一维数组 ['字段名1', '字段名2',....]
     * @param array $dataArr 字段值数组 --一维数组 格式为 ['字段名' => '字段值'] ；会包含所有的$fields 字段值
     * @param int 选填 $operate 操作 1 转为json 2 序列化 ; 3 不转换
     * @param boolean $ifEmptySet 是否没有缓存时，重新设置此缓存值
     * @param mixed $cacheSetVal 需要缓存的值
     * @param array $cacheTimeConfig 可为空：自动获取； 缓存键时间的rediis配置  ['缓存前缀', '缓存时间', '缓存时间']
     *                  使用 list($keyRedisPre, $cacheExpire, $dateTime) = array_values($cacheTimeConfig);
     * @param int 选填 $operateType 操作类型 1 直接设置redis值 2存在，则修改值和有效期,不存在不添加 4不存在，则创建,存在不处理 8 不存在则创建，存在则修改值和有效期
     * @return   mixed 缓存的值 ,失败返回false
     * @author zouyan(305463219@qq.com)
     */
    public function getCacheFieldsVal($cacheType = 1, $fields = [], $dataArr = [], $operate = 3, $ifEmptySet = false, $cacheSetVal = '', $cacheTimeConfig = [], $operateType = 1){
        $keyRedisPre = $this->getCacheSimple();

        $t_v = Tool::getArrFormatFields($dataArr, $fields, true);

        $temCacheKey = $this->getCacheKey($cacheType, $fields, $t_v);
        $cacheVal = Tool::getRedis($keyRedisPre . $temCacheKey, $operate);
        if($cacheVal === false ){// 没有缓存，重新设置缓存
            if($ifEmptySet) $this->setCacheField($cacheSetVal, $cacheType, $fields, $t_v, $operate, $cacheTimeConfig, $operateType);
        }
        return $cacheVal;
    }
    /**
     * 获得缓存的值
     * @param int $cacheType 缓存类型 1 单条记录缓存时间； 2 块级缓存[默认]有效] 时间 ；3 表缓存时间 ；4 其它主键缓存，指向 主键缓存键 ；5单条记录缓存数据
     * @param array $fields 字段数组 --一维数组 ['字段名1', '字段名2',....]
     * @param array $fieldVals 字段值数组 --一维数组  ['字段名1值', '字段名2值',....]
     * @param int 选填 $operate 操作 1 转为json 2 序列化 ; 3 不转换
     * @param boolean $ifEmptySet 是否没有缓存时，重新设置此缓存值
     * @param mixed $cacheSetVal 需要缓存的值
     * @param array $cacheTimeConfig 可为空：自动获取； 缓存键时间的rediis配置  ['缓存前缀', '缓存时间', '缓存时间']
     *                  使用 list($keyRedisPre, $cacheExpire, $dateTime) = array_values($cacheTimeConfig);
     * @param int 选填 $operateType 操作类型 1 直接设置redis值 2存在，则修改值和有效期,不存在不添加 4不存在，则创建,存在不处理 8 不存在则创建，存在则修改值和有效期
     * @return   mixed 缓存的值 ,失败返回false
     * @author zouyan(305463219@qq.com)
     */
    public function getCacheFieldsValByArr($cacheType = 1, $fields = [], $fieldVals = [], $operate = 3, $ifEmptySet = false, $cacheSetVal = '', $cacheTimeConfig = [], $operateType = 1){
        $keyRedisPre = $this->getCacheSimple();
        $temCacheKey = $this->getCacheKey($cacheType, $fields, $fieldVals);
        $cacheVal = Tool::getRedis($keyRedisPre . $temCacheKey, $operate);
        if($cacheVal === false ){// 没有缓存，重新设置缓存
            if($ifEmptySet) $this->setCacheField($cacheSetVal, $cacheType, $fields, $fieldVals, $operate, $cacheTimeConfig, $operateType);
        }
        return $cacheVal;
    }

    /**
     * 删除缓存
     * @param int $cacheType 缓存类型 1 单条记录缓存时间； 2 块级缓存[默认]有效] 时间 ；3 表缓存时间 ；4 其它主键缓存，指向 主键缓存键 ；5单条记录缓存数据
     * @param array $fields 字段数组 --一维数组 ['字段名1', '字段名2',....]
     * @param array $dataArr 字段值数组 --一维数组 格式为 ['字段名' => '字段值'] ；会包含所有的$fields 字段值
     * @return  boolean  成功删除返回 true, 失败则返回 false
     * @author zouyan(305463219@qq.com)
     */
    public function delCacheFieldsVal($cacheType = 1, $fields = [], $dataArr = []){
        $keyRedisPre = $this->getCacheSimple();

        $t_v = Tool::getArrFormatFields($dataArr, $fields, true);

        $temCacheKey = $this->getCacheKey($cacheType, $fields, $t_v);
        return Tool::delRedis($keyRedisPre . $temCacheKey);
    }

    /**
     * 删除缓存
     * @param int $cacheType 缓存类型 1 单条记录缓存时间； 2 块级缓存[默认]有效] 时间 ；3 表缓存时间 ；4 其它主键缓存，指向 主键缓存键 ；5单条记录缓存数据
     * @param array $fields 字段数组 --一维数组 ['字段名1', '字段名2',....]
     * @param array $fieldVals 字段值数组 --一维数组 ['字段名1值', '字段名2值',....]
     * @return  boolean  成功删除返回 true, 失败则返回 false
     * @author zouyan(305463219@qq.com)
     */
    public function delCacheFields($cacheType = 1, $fields = [], $fieldVals = []){
        $keyRedisPre = $this->getCacheSimple();
        $temCacheKey = $this->getCacheKey($cacheType, $fields, $fieldVals);
        return Tool::delRedis($keyRedisPre . $temCacheKey);
    }

    //######设置更新时间相关的############开始#########################################

    /**
     * 更新表记录时，更新表更新时间缓存
     * @param int 选填 $operateType 操作类型 1 直接设置redis值 2存在，则修改值和有效期,不存在不添加 4不存在，则创建,存在不处理 8 不存在则创建，存在则修改值和有效期
     * @return   mixed true:成功 sting 具体错误 ； throws 错误
     * @author zouyan(305463219@qq.com)
     */
    public function setTableUpdateTimeCache($operateType = 1){
        $cacheTimeConfig = $this->getCacheTimeConfig();
        list($keyRedisPre, $cacheExpire, $dateTime, $msecint, $dateTimeMsecint) = array_values($cacheTimeConfig);
//        $cacheExpire = 0;// 永不过期
//        if(isset($cacheTimeConfig[1])) $cacheTimeConfig[1] = $cacheExpire;
        // 设置更新表的时间
        return $this->setCacheField($dateTimeMsecint, 3, [], [], 3, $cacheTimeConfig, $operateType);
        // return Tool::setRedis($keyRedisPre, $this->getCacheKey(3, [], []), $dateTime, $cacheExpire, 3);
    }

    /**
     * 获得更新表更新时间缓存
     * @param boolean $ifEmptySet 是否没有表时间缓存时，重新设置此缓存为当前时间
     * @param int 选填 $operateType 操作类型 1 直接设置redis值 2存在，则修改值和有效期,不存在不添加 4不存在，则创建,存在不处理 8 不存在则创建，存在则修改值和有效期
     * @return   string  空字符：无缓存记录；具体的时间字符串 'Y-m-d H:i:s'
     * @author zouyan(305463219@qq.com)
     */
    public function getTableUpdateTimeCache($ifEmptySet = false, $operateType = 1){
        $keyRedisPre = $this->getCacheSimple();
        $tableUpdateTime = Tool::getRedis($keyRedisPre . $this->getCacheKey(3, [], []), 3);
        if($tableUpdateTime === false || (!is_string($tableUpdateTime))){
            $tableUpdateTime = '';
            if($ifEmptySet) $this->setTableUpdateTimeCache($operateType);
        }
        return $tableUpdateTime;
    }

    /**
     * 根据缓存数据，判断数据表字段是否有更新
     * @param string $dbFieldsMd5 缓存数据中的缓存时表所有字段的md5值
     * @param array $select 查询时指定的select 需要指定的字段 -一维数组；为空代表所有字段 16 查询--有缓存时，对数据进行判断是否作废--判断表字段是否有变动[增、删、改]用
     * @param array $dbFieds 真实记录中的字段
     * @return   boolean true:有更新 ；false:无更新
     * @author zouyan(305463219@qq.com)
     */
//    public function isTableFieldsChagned($dbFieldsMd5, $select = [], $dbFieds = []){
//        // 获得表所有字段
//        $tableFields = $this->getTableAllFields();
//        if(!empty($select)) $select = array_map("strtolower", $select);// 都转为小写
//        if(!empty($dbFieds)) $dbFieds = array_map("strtolower", $dbFieds);// 都转为小写
//        $tableFieldsMd5 = $this->getTableAllFieldsMd5();// 当前表字段md5值
//        // if($dbFieldsMd5 != $tableFieldsMd5) return true;
//        // 获得所有字段
//        if(empty($select)) {
//            if(empty($dbFieds)){
//                // TODO 所有字段且没有数据
//               // if($dbFieldsMd5 != $tableFieldsMd5) return true;
//            }else{
//                // 表字段有不在数据中的
//                $diffTableFields = array_diff($tableFields, $dbFieds);
//                // 有剩，则说明新加、修改了字段
//                if(!empty($diffTableFields)) return true;
//                // TODO  ，那删除字段了呢?
//                // if($dbFieldsMd5 != $tableFieldsMd5) return true;
//            }
//        }else{// 获得指定字段
//            // 指定的有不存在表字段的--指定字段有不在表中的，说明表字段有变化[增、删、改]
//            $diffTableFields = array_diff($select, $tableFields);
//            if(!empty($diffTableFields)) return true;
//        }
//        return false;
//    }


    /**
     * 获得缓存键的前缀
     * cacheDb:U:
     * @return  string
     * @author zouyan(305463219@qq.com)
     */
    public function getCacheSimple(){
        // 简写,为空，则使用表名
        $cacheSimple = static::$cacheSimple;
        if(empty($cacheSimple))  $cacheSimple = $this->getTable();
        // 如果有缓存版本，则加上版本号
        if(strlen(static::$cacheVersion) > 0)  $cacheSimple .= ':' . static::$cacheVersion;
        $dbKey = Tool::getProjectKey(64, ':', ':');
        return $dbKey . static::$cachePre . ':' . static::$modelPath  . ':' . $cacheSimple . ':';//  'cacheDb:' . $cacheSimple . ':'; // U:{id值};
    }

    /**
     * public.DBDataCache.cacheType 配置打开，且各模型也打开才会有对应缓存
     * 获得缓存开启类型1 缓存详情 2缓存块(指定字段)[确定没有用到关系的块，可以缓存] 4缓存块(表级)
     * @return   int 获得缓存开启类型
     * @author zouyan(305463219@qq.com)
     */
    public function getDataCachType(){
        $DBDataCacheType = static::$cacheType;
        $sysDataCacheType = config('public.DBDataCache.cacheType', 0);
        return ($DBDataCacheType & $sysDataCacheType);
    }

    /**
     * 获得缓存时间
     * @return   int 单位秒
     * @author zouyan(305463219@qq.com)
     */
    public function getMaxCacheRows(){
        $maxCacheRows = static::$maxCacheRows;
        if(!is_numeric($maxCacheRows) || $maxCacheRows <= 0) $maxCacheRows = config('public.DBDataCache.maxCacheRows', 200);
        return $maxCacheRows;
    }

    /**
     * 获得缓存时间
     * @return   int 单位秒
     * @author zouyan(305463219@qq.com)
     */
    public function getDataCacheExpire(){
        $DBDataCacheExpire = static::$cacheExpire;
        if(!is_numeric($DBDataCacheExpire) || $DBDataCacheExpire <= 0) $DBDataCacheExpire = config('public.DBDataCache.expire', 60 * 2);
        return $DBDataCacheExpire;
    }

    /**
     * 获得单位时间内，访问多少次，开启缓存--目的去掉冷数据 如：1分钟访问2次，则开启缓存
     * @return   array $openCache // 单位时间内，访问多少次，开启缓存--目的去掉冷数据 如：1分钟访问2次，则开启缓存
     *                  // 值[] 空时，会使用 public.DBDataCache.openCache 配置
     *  [
     *         'expire' => 60 * 3,// 单位时长，单位秒  建议：2-10分钟
     *        'requestNum' => 3,// 访问次数
     *    ];
     * @author zouyan(305463219@qq.com)
     */
    public function getDataOpenCache(){
        $openCache = static::$openCache;
        if(!is_array($openCache) || empty($openCache)) $openCache = config('public.DBDataCache.openCache', []);
        return $openCache;
    }

    /**
     * 获得缓存自动延期设置 单位时间内访问多少次时，自动延长有效期 10分钟 8次 自动延长有效期
     * @return   array  $extendExpire // 缓存自动延期设置 单位时间内访问多少次时，自动延长有效期 10分钟 8次 自动延长有效期 可延长3次
     *                  // 值[] 空时，会使用 public.DBDataCache.extendExpire 配置     *
     * [
     *        'expire' => 60 * 3,// 单位时长，单位秒  建议：2-10分钟
     *       'requestNum' => 8,// 访问次数
     *        'maxExendNum' => 3,// 可延长3次
     *    ]
     * @author zouyan(305463219@qq.com)
     */
    public function getDataCacheExtendExpire(){
        $extendExpire = static::$extendExpire;
        if(!is_array($extendExpire) || empty($extendExpire)) $extendExpire = config('public.DBDataCache.extendExpire', []);
        return $extendExpire;
    }

    //######获得配置相关的############结束#########################################

    //######获得字段相关的############开始#########################################
    /**
     * 获得表所有字段数组--一维数组
     * @param string $tableName 表名称，为空则自动获取
     * @return  string
     * @author zouyan(305463219@qq.com)
     */
    public function getTableAllFields($tableName = ''){
        if(strlen($tableName) <= 0 ) $tableName = $this->getTable();
        $tableFields = CommonDB::getDbFields($this, $tableName, static::$modelPath, 1);
        $tableFields = array_map("strtolower", $tableFields);// 都转为小写
        return $tableFields;
        // return Schema::getColumnListing($this->getTable());
    }

    /**
     * 获得表所有字段的md5值
     * cacheDb:U:
     * @return  string
     * @author zouyan(305463219@qq.com)
     */
    public function getTableAllFieldsMd5(){
        $tableFieldsMd5 = CommonDB::getDbFieldsMd5($this, $this->getTable(), static::$modelPath, 1);
        return $tableFieldsMd5;
    }

    /**
     * 根据缓存数据，判断数据表字段是否有更新
     * @param string $dbFieldsMd5 缓存数据中的缓存时表所有字段的md5值
     * @return   boolean true:有更新 ；false:无更新
     * @author zouyan(305463219@qq.com)
     */
    public function isDBFieldsChagned($dbFieldsMd5){
        // 当前表字段md5值
        $tableFieldsMd5 = $this->getTableAllFieldsMd5();
        if(strlen($tableFieldsMd5) <= 0) return true;
        if(strlen($tableFieldsMd5) > 0 && $dbFieldsMd5 != $tableFieldsMd5) return true;
        return false;
    }

    /**
     * 获得数据表的扩展字段-- 一维数组,也可能是空，不代表是要缓存的字段[只有前缀相同才是需要的]
     *
     * @return  array 一维数组,也可能是空 ['is_active_text']
     * @author zouyan(305463219@qq.com)
     */
    public  function getDBAppendsFields(){
        return $this->getTableAttr('appends', 0);
    }

    /**
     * 获得需要缓存键的主键字段-- 一维数组,也可能是空
     *
     * @return  array 一维数组,也可能是空
     * @author zouyan(305463219@qq.com)
     */
    public function getCachePrimaryFields(){
        // 处理主键
        $cachePrimaryFields = static::$cachePrimaryFields;
        // 为空，则通过 表的主键缓存
        if( empty($cachePrimaryFields)){
            $cachePrimaryFields = $this->getKeyName();
        }
        if(is_string($cachePrimaryFields) && strlen($cachePrimaryFields) > 0){
            $cachePrimaryFields = [$cachePrimaryFields];
        }
        if(!is_array($cachePrimaryFields)) $cachePrimaryFields = [];
        return $cachePrimaryFields;
    }

    /**
     * 获得需要缓存键的字段-- 一维数据,注意也可能为空
     *
     * @param int $fieldType 要获得的字段类型 2 ：值需要作为缓存主键键的字段；4 值需要参与块级缓存的字段；
     * @return  array 一维数据,注意也可能为空 ['字段名1', '字段名2',....]
     * @author zouyan(305463219@qq.com)
     */
    public function getCacheKeyFields($fieldType = 2){
        $fields = [];
        $cacheKeyFields = [];
        if($fieldType == 2){
            $cacheKeyFields = static::$cachePrimaryKeyFields;
        }elseif($fieldType == 4){
            $cacheKeyFields = static::$cacheBlockFields;
        }
        if(empty($cacheKeyFields)) return $fields;
        foreach($cacheKeyFields as $v){
            if(is_string($v)){
                $fields[] = $v;
            }elseif(is_array($v)){
                $fields = array_values(array_merge($fields, $v));
            }
        }
        return array_values(array_unique($fields));
    }

    /**
     * 获得需要缓存键的字段-- 一维数据,注意也可能为空,如果有多个字段的，_分隔
     *
     * @param int $fieldType 要获得的字段类型 2 ：值需要作为缓存主键键的字段；4 值需要参与块级缓存的字段；
     * @return  array 一维数据,注意也可能为空 ['字段名1', '字段名2{分隔符}字段2',....]
     * @author zouyan(305463219@qq.com)
     */
    public function getFieldsTextArr($fieldType = 2){
        $fields = [];
        $cacheKeyFields = [];
        if($fieldType == 2){
            $cacheKeyFields = static::$cachePrimaryKeyFields;
        }elseif($fieldType == 4){
            $cacheKeyFields = static::$cacheBlockFields;
        }
        if(empty($cacheKeyFields)) return $fields;
        foreach($cacheKeyFields as $v){
            if(is_string($v)){
                $fields[] = $v;
            }elseif(is_array($v)){
                $fields[] = implode(static::$separatoChar, $v);
            }
        }
        return array_values($fields);
    }

    /**
     * 获得需要缓存键的字段数组-- 二维数据,注意也可能为空 ['标识' => ['字段',...],...] 这样的格式
     *
     * @param int $fieldType 要获得的字段类型 2 ：值需要作为缓存主键键的字段；4 值需要参与块级缓存的字段；
     * @return  array 二维数据,注意也可能为空 [['字段名1'], ['字段名1','字段名2'],....]
     * @author zouyan(305463219@qq.com)
     */
    public function getCacheKeyFieldsArr($fieldType = 2){
        $fields = [];
        $cacheKeyFields = [];
        if($fieldType == 2){
            $cacheKeyFields = static::$cachePrimaryKeyFields;
        }elseif($fieldType == 4){
            $cacheKeyFields = static::$cacheBlockFields;
        }
        if(empty($cacheKeyFields)) return $fields;
        foreach($cacheKeyFields as $k => $v){
            if(is_string($v)){
                $fields[$k] = [$v];
            }elseif(is_array($v)){
                $fields[$k] = $v;
            }
        }
        return $fields;
    }

    /**
     * 获得需要缓存键的字段-- 一维数据
     *
     * @param int $dataType 需要获得的数据字段 1 主键 ；2：值需要作为缓存主键键的字段；4 值需要参与块级缓存的字段；
     * @return  array 空数组，代表不缓存，有字段值[一维]：代表需要缓存的字段  ['字段名1', '字段名2',....]
     * @author zouyan(305463219@qq.com)
     */
    public function getAllCacheKeyFields($dataType = 0){
        $fields = [];
        // 1 主键
        if(($dataType & 1) == 1) {
            $cachePrimaryFields = $this->getCachePrimaryFields();
            if (!empty($cachePrimaryFields)) {
                $fields = array_values(array_merge($fields, $cachePrimaryFields));
            }
        }

        // 2 值需要作为缓存键的字段
        if(($dataType & 2) == 2) {
            $cacheOtherFields = $this->getCacheKeyFields(2);
            if (!empty($cacheOtherFields)) {
                $fields = array_values(array_merge($fields, $cacheOtherFields));
            }
        }

        // 4 值需要参与块级缓存的字段；
        if(($dataType & 4) == 4) {
            $cacheBlockFields = $this->getCacheKeyFields(4);
            if (!empty($cacheBlockFields)) {
                $fields = array_values(array_merge($fields, $cacheBlockFields));
            }
        }
        return array_values(array_unique($fields));
    }

    //######获得字段相关的############结束#########################################

    //##########对表数据操作##增##改##删除####开始##############
    /**
     * ---入口
     * 更新表记录时，更新表更新时间缓存-  对缓存，不主动创建的原则，执行时没有则创建。这里有缓存键，则更新缓存值和有效期。
     *      操作类型 1 增[不用返回值]； 2 改 [不用返回值]--一般用这个；4 删除[不用返回值] ；
     * @param int $operateType
     * @param array $recordData 需要更新的记录 -一维或二维数组
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return   mixed true:成功 false:失败 sting 具体错误 ； throws 错误
     * @author zouyan(305463219@qq.com)
     */
    public function operateDbUpdateTimeCache($operateType = 1, &$recordData = [], $errDo = 1){
        $isCacheBlock = $this->isCacheBlock();//  缓存块
        $isCacheInfo = $this->isCacheInfo();//  缓存单条记录
        // 没有开启任何缓存，直接返回
        if(!$isCacheBlock && !$isCacheInfo) return true;
        // 对表时间缓存
        $this->setTableUpdateTimeCache(8);// 8 不存在则创建，存在则修改值和有效期
        // 增加时 没有开块缓存
        if( ($operateType & 1) == 1 && !$isCacheBlock) return true;
        // 增加时  且 表不是多情况（多种平台应该；如按城市分站）缓存 且主键不是多字段
        if( ($operateType & 1) == 1 && !$this->isMultiQueryTable() && !$this->isMultiPrimaryFields()) return true; //
        // 对数据的记录进行处理
        $result = $this->operateDataUpdateTimeCache($operateType, $recordData, $errDo);
        return $result;
    }

    /**
     * 更新表具体记录记录时，更新表具体记录更新时间缓存
     *      操作类型 1 增[不用返回值]； 2 改 [不用返回值]；4 删除[不用返回值] ；
     * @param int $operateType
     * @param array $recordData 需要更新的记录 -一维或二维数组 需要有所有要缓存时间的字段值[主键字段、块缓存字段]
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return   mixed true:缓存可用；false:缓存不可用[终止往下执行] sting 具体错误 ； throws 错误
     * @author zouyan(305463219@qq.com)
     */
    public function operateDataUpdateTimeCache($operateType = 1, &$recordData = [], $errDo = 1){
        $isCacheBlock = $this->isCacheBlock();//  缓存块
        $isCacheInfo = $this->isCacheInfo();//  缓存单条记录
        // 没有开启任何缓存，直接返回
        if(!$isCacheBlock && !$isCacheInfo) return true;

        $dataArr = is_object($recordData) ? $recordData->toArray() : $recordData;

        // 没有数据，则直接返回
        if(empty($dataArr)) return true;

        $isMulti = Tool::isMultiArr($recordData, true);

        $cacheTimeConfig = array_values($this->getCacheTimeConfig());

        // 设置更新表记录的时间
        $result = true;
        $blockSingleFields = [];// 块单个字段时间缓存记录 ['字段1_字段1值1', '字段1_字段1值2', '字段2_字段2值1', '字段2_字段2值2',...]
        foreach($recordData as $k => $info){
            // 更新主键时间及详情其它缓存指向缓存
            $this->setPrimaryFieldsTime($operateType, $info, $cacheTimeConfig,$blockSingleFields);
            // 更新块缓存键时间-- 开启块缓存 且 有块的相关字段
            if($this->isCacheBlock() && ( $this->isMultiQueryTable()  ) ){// || $this->isMultiPrimaryFields()
                $this->setBlockFieldsTime($operateType, $info, $cacheTimeConfig,$blockSingleFields);
            }
            $recordData[$k] = $info;
        }
        if(!$isMulti) $recordData = $recordData[0] ?? [];
        return $result;
    }

    /**
     * 更新主键时间及详情其它缓存指向缓存
     *      操作类型 1 增[不用返回值]； 2 改 [不用返回值]；4 删除[不用返回值] ；
     * @param int $operateType
     * @param array $info 需要更新的记录 -一维数组 需要有所有要缓存时间的字段值[主键字段、块缓存字段]
     * @param array $cacheTimeConfig 可为空：自动获取； 缓存键时间的rediis配置  ['缓存前缀', '缓存时间', '缓存时间']
     *                  使用 list($keyRedisPre, $cacheExpire, $dateTime) = array_values($cacheTimeConfig);
     * @param array $blockSingleFields  块单个字段时间缓存记录 // ['字段1_字段1值1', '字段1_字段1值2', '字段2_字段2值1', '字段2_字段2值2',...]
     * @return   mixed true:成功 sting 具体错误 ； throws 错误
     * @author zouyan(305463219@qq.com)
     */
    public function setPrimaryFieldsTime($operateType = 1, &$info = [], $cacheTimeConfig = [], &$blockSingleFields = []){
        // 没有主键
        if(!$this->hasPrimaryFields()) return false;
        // 获得缓存主字段数组
        $cachePrimaryFields = $this->getAllCacheKeyFields( 1);

        if(!is_array($cacheTimeConfig) || empty($cacheTimeConfig)) $cacheTimeConfig = array_values($this->getCacheTimeConfig());
        list($keyRedisPre, $cacheExpire, $dateTime, $msecint, $dateTimeMsecint) = array_values($cacheTimeConfig);

        $infoArr = is_object($info) ? $info->toArray() : $info;
        if(empty($infoArr)) return true;
        $temInfoKeys = array_keys($infoArr);

        // 判断主键是否在数据中
        $diffFields = array_diff($cachePrimaryFields, $temInfoKeys);
        if(!empty($diffFields)) return false;
        // 4 删除
        if(($operateType & 4) == 4){
            $this->delCacheFieldsVal(1, $cachePrimaryFields, $infoArr);
            //增、改
        }elseif(($operateType & (1 | 2 )) > 0){
            $this->setCacheFieldsVal($dateTimeMsecint, 1, $cachePrimaryFields, $infoArr, 3, $cacheTimeConfig, 2);
        }
        // 如果主键是多个字段 ,则单个字段还要缓存时间到block
        $isCacheBlock = $this->isCacheBlock();
        if($isCacheBlock && count($cachePrimaryFields) > 1){
            foreach($cachePrimaryFields as $temField){
                $temFieldVal = $infoArr[$temField] ?? '';
                if(empty($temField) || strlen($temFieldVal) <= 0) continue;
                $temFieldKey = $temField . '_' . $temFieldVal;
                if(in_array($temFieldKey, $blockSingleFields)) continue;
                $blockSingleFields[] = $temFieldKey;
                $this->setCacheField($dateTimeMsecint, 2, [$temField], [$temFieldVal], 3, $cacheTimeConfig, 2);
            }
        }

        // 单条缓存的相关指向缓存
        $result = true;
        // 缓存单条记录--单条记录
        if( $this->isCacheInfo() && $this->hasPrimaryRelevantKeyFields() ) {
            $t_v = Tool::getArrFormatFields($infoArr, $cachePrimaryFields, true);
            $result = $this->setPrimaryFieldsVal($operateType, $info, $this->getCacheKeyValText($cachePrimaryFields, $t_v), $cacheTimeConfig);
        }
        return $result;
    }

    /**
     * 更新主键时间及详情其它缓存指向缓存
     *      操作类型 1 增[不用返回值]； 2 改 [不用返回值]；4 删除[不用返回值] ；
     * @param int $operateType
     * @param array $info 需要更新的记录 -一维数组 需要有所有要缓存时间的字段值[主键字段、块缓存字段]
     * @param string $primaryFieldsVal 记录主键的值 {字段下标-多个_分隔}:{{id值}_{email值}}
     * @param array $cacheTimeConfig 可为空：自动获取； 缓存键时间的rediis配置  ['缓存前缀', '缓存时间', '缓存时间']
     *                  使用 list($keyRedisPre, $cacheExpire, $dateTime) = array_values($cacheTimeConfig);
     * @return   mixed true:成功 sting 具体错误 ； throws 错误
     * @author zouyan(305463219@qq.com)
     */
    public function setPrimaryFieldsVal($operateType = 1, &$info = [], $primaryFieldsVal = '', $cacheTimeConfig = []){
        if(!$this->isCacheInfo() || !$this->hasPrimaryRelevantKeyFields()) return true;

        // if(!is_array($cacheTimeConfig) || empty($cacheTimeConfig)) $cacheTimeConfig = array_values($this->getCacheTimeConfig());
        // list($keyRedisPre, $cacheExpire, $dateTime, $msecint, $dateTimeMsecint) = array_values($cacheTimeConfig);

        $infoArr = is_object($info) ? $info->toArray() : $info;

        if(empty($infoArr)) return true;
        $temInfoKeys = array_keys($infoArr);

        // 单条缓存的相关指向缓存

        // 主键相关的
        $PKeyFields = $this->getCacheKeyFieldsArr(2);
        if(!is_array($PKeyFields) || empty($PKeyFields)) return true;

        foreach($PKeyFields as $k => $v){
            $diffFields = array_diff($v, $temInfoKeys);
            // 有不是表字段的缓存键，则不缓存--跳过
            if(!empty($diffFields)) continue;// throws('缓存键字段[' . implode('_', $diffFields) . ']不存在!');re . ':' . implode(static::$separatoChar, $t_v);

            // 4 删除
            if(($operateType & 4) == 4){
                $this->delCacheFieldsVal(4, $v, $infoArr);
                //增、改
            }elseif(($operateType & (1 | 2 )) > 0){
                $this->setCacheFieldsVal($primaryFieldsVal, 4, $v, $infoArr, 3, $cacheTimeConfig, 2);
            }
        }
        return true;
    }

    /**
     * 更新块缓存键时间
     *      操作类型 1 增[不用返回值]； 2 改 [不用返回值]；4 删除[不用返回值] ；
     * @param int $operateType
     * @param array $info 需要更新的记录 -一维数组 需要有所有要缓存时间的字段值[主键字段、块缓存字段]
     * @param array $cacheTimeConfig 可为空：自动获取； 缓存键时间的rediis配置  ['缓存前缀', '缓存时间', '缓存时间']
     *                  使用 list($keyRedisPre, $cacheExpire, $dateTime) = array_values($cacheTimeConfig);
     * @param array $blockSingleFields  块单个字段时间缓存记录 // ['字段1_字段1值1', '字段1_字段1值2', '字段2_字段2值1', '字段2_字段2值2',...]
     * @return   mixed true:成功 sting 具体错误 ； throws 错误
     * @author zouyan(305463219@qq.com)
     */
    public function setBlockFieldsTime($operateType = 1, &$info = [], $cacheTimeConfig = [], &$blockSingleFields = []){
        if(!$this->isCacheBlock() || (!$this->isMultiQueryTable())) return true;//  && !$this->isMultiPrimaryFields()

        if(!is_array($cacheTimeConfig) || empty($cacheTimeConfig)) $cacheTimeConfig = array_values($this->getCacheTimeConfig());
        list($keyRedisPre, $cacheExpire, $dateTime, $msecint, $dateTimeMsecint) = array_values($cacheTimeConfig);

        $infoArr = is_object($info) ? $info->toArray() : $info;
        // $shop_id = $infoArr['shop_id'] ?? 0;
        // if($shop_id == 255) pr($infoArr);

        if(empty($infoArr)) return true;
        $temInfoKeys = array_keys($infoArr);
        // 块字段
        $blockFields = $this->getCacheKeyFieldsArr(4);
        if(!is_array($blockFields) || empty($blockFields)) return true;
        foreach($blockFields as $t_k => $t_f){
            $diffFieldsTV = array_diff($t_f, $temInfoKeys);
            if(!empty($diffFieldsTV)) continue;
            // 4 删除
//            if(($operateType & 4) == 4){
//                // $this->delCacheFieldsVal(2, $t_f, $infoArr);
//
//                //增、改
//            }elseif(($operateType & (1 | 2 )) > 0){
//                // 只有一个字段，且缓存过--跳过
//                if(count($t_f) <= 1 && empty(array_diff($t_f, $blockSingleFields))) continue;
//                $this->setCacheFieldsVal($dateTimeMsecint, 2, $t_f, $infoArr, 3, $cacheTimeConfig);
//            }
            // 如果是两个以上字段,则单个字段还要缓存时间
//            if(count($t_f) <= 1) continue;
            foreach($t_f as $temField){
                $temFieldVal = $infoArr[$temField] ?? '';
                if(empty($temField) || strlen($temFieldVal) <= 0) continue;
                $temFieldKey = $temField . '_' . $temFieldVal;
                if(in_array($temFieldKey, $blockSingleFields)) continue;
                $blockSingleFields[] = $temFieldKey;
                $this->setCacheField($dateTimeMsecint, 2, [$temField], [$temFieldVal], 3, $cacheTimeConfig, 2);
            }
        }
        return true;
    }

    //##########对表数据操作##增##改##删除####结束##############


    //##########有缓存数据##判断缓存数据是否失效####开始##############

    /**
     * 解析缓存数据，对数据进行有效效验
     * 注意：增、改、删除与  $cacheType 缓存类型 没有关系，也和
     * @param int $cacheType 缓存类型 1 单条记录缓存 2 块级缓存[默认]
     *      操作类型 16 查询--有缓存时，对数据进行判断是否作废 [需要返回值：缓存是否有效]
     * @param int $operateType
     * @param array $recordData 需要更新的记录 -一维或二维数组；成效：数据自动改为真实数据
     * @param array $queryParams 查询请求  主要抽取查询条件where whereIn数组  主要用到下标 ；含有 orWhere 不判断
     *                  where whereBetween 二维数组 ；数组每项中 ：第一个参数为字段名  ， 如果是两个值数组直接判断第一个项字段；如果有三个值数组，要判断第二个为=号
     *                      [
     *                          ['city', '1'],
     *                          ['status', '=', '1'],
     *                          ['subscribed', '<>', '1'],
     *                      ]
     *                  'whereIn' 二维数组 [ [字段名=>[多个字段值]],....]
     *
     * @param array $select 需要指定的字段 主要用来判断要请求的字段是否包含排除字段-一维数组；为空代表所有字段 16 查询--有缓存时，对数据进行判断是否作废--判断表字段是否有变动[增、删、改]用
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return   mixed true:成功[缓存有效] false:失败[缓存失效] sting 具体错误 ； throws 错误
     * @author zouyan(305463219@qq.com)
     */
    public function judgeCacheData($cacheType = 2, $operateType = 16, &$recordData = [], $queryParams = [], $select = [], $errDo = 1){
        // 未开通缓存 或 返回的字段包含排除字段
        if(!$this->isDefaultOpenCache($cacheType, $select))  return false;
        // if(!isset($recordData['time']) || !isset($recordData['md5'])  || !isset($recordData['data']) ) return false;

        $cacheTime = $recordData['time'] ?? '';//  缓存时的时间--必有
        $msecint = $recordData['msecint'] ?? '';//  微秒的整数--必有
        // $cacheBlockArr = $recordData['block'] ?? [];//  如果是块缓存数据，则是缓存数据的块字段数组 二维数组 --块级缓存 可能用，数据为空时，就是空
        $dataMd5 = $recordData['md5'] ?? '';// 缓存时表字段的md5值 16 查询--有缓存时，对数据进行判断是否作废--必有
        $cacheData = $recordData['data'] ?? [];// 缓存的数据--必有，可能为空
        // 判断表结构是否有变化--有变化
        if($this->isDBFieldsChagned($dataMd5)) return false;

        // 判断缓存是否过期---缓存有效
        $dataIsValid = $this->isValidTime($cacheType, $operateType, $cacheTime, $msecint, $cacheData, $queryParams);// , $cacheBlockArr
        // 数据改为真实数据
        $recordData = $cacheData;
        return $dataIsValid;
    }

    /**
     * 判断缓存是否有效 时间是否过期（比最新的记录更新时间早）
     * @param int $cacheType 缓存类型 1 单条记录缓存 2 块级缓存[默认]
     *      操作类型 16 查询--有缓存时，对数据进行判断是否作废 [需要返回值：缓存是否有效]
     * @param int $operateType
     * @param string $cacheTime 缓存记录的时间字符串
     * @param int $msecint 微秒的整数
     * @param array $cacheData 需要判断的缓存的记录 -一维或二维数组
     * @param array $queryParams 查询请求 主要抽取查询条件where whereIn数组 主要用到下标 ；含有 orWhere 不判断
     *                  where whereBetween 二维数组 ；数组每项中 ：第一个参数为字段名  ， 如果是两个值数组直接判断第一个项字段；如果有三个值数组，要判断第二个为=号
     *                      [
     *                          ['city', '1'],
     *                          ['status', '=', '1'],
     *                          ['subscribed', '<>', '1'],
     *                      ]
     *                  'whereIn' 二维数组 [ [字段名=>[多个字段值]],....]
     *
     * @  param array $cacheBlockArr 如果是块缓存数据，则是缓存数据的块字段数组 二维数组 ； $operateType 为 16 时 有此值  ；  $operateType 为 8 时；会对数据加上此属性。
     * @return  boolean true:缓存有效；false：缓存过期
     * @author zouyan(305463219@qq.com)
     */
    public function isValidTime($cacheType = 2, $operateType = 16, $cacheTime = '', $msecint = 0, &$cacheData = [], $queryParams = []){// , $cacheBlockArr = []

        // 缓存没有时间信息，则作废
        if(empty($cacheTime)) return false;
        $dateTime = date('Y-m-d H:i:s');

        // 表最新的更新时间
        $tableUpdateTime = $this->getTableUpdateTimeCache(false, 0);
        // 没有表的更新时间
        if(empty($tableUpdateTime)) return false;

        list($tableUpdateTime, $cacheMsecint) = array_values(Tool::getTimeMsec($tableUpdateTime));

        // $cacheTimeFormat = judgeDate($cacheTime,"Y-m-d H:i:s");
        // 不是有效日期
        // if($cacheTimeFormat === false) return false;
        // 两个时间都不能为空，且都不能大于当前时间
        $reslut = Tool::judgeBeginEndDate($tableUpdateTime, $cacheTime,  1 + 2 + 4 + 32 , 2, $dateTime, '时间');

        // 出错 有效日期失效
        if($reslut !== true) return false;

        // 如果时间相等，则判断微秒的情况[缓存记录的微秒 <= 参考点微秒，则过期]
        if($tableUpdateTime == $cacheTime && $msecint <= $cacheMsecint) return false;

        // 时间及微秒都相等，才是真的相等
        $judge_type = 256;
        if($tableUpdateTime == $cacheTime && $msecint == $cacheMsecint) $judge_type = $judge_type | 512;

        // 表缓存时间不能 >= 数据缓存时间 （ <）---表的缓存时间如果小于缓存数据的时间，说明：表一直未更新过，所以肯定有效--直接返回。
        $reslut = Tool::judgeBeginEndDate($tableUpdateTime, $cacheTime, $judge_type, 2, $dateTime, '时间');

        // 缓存时间大于表更新时间，则缓存肯定有效
        if($reslut === true) return true;

        // 下面的都是 表的缓存时间 >= 缓存数据的时间 的情况

        $dataArr = is_object($cacheData) ? $cacheData->toArray() : $cacheData;

        // 判断逻辑
        //  查询字段判断
        //          没有查询字段(说明是系统级查询):表更新时间>=缓存时间，就直接失效
        //          单条记录：开启单条缓存---块记录也优先判断主键
        //                  判断查询字段是否都在主键字段中[全匹配]--是单条查询
        //                  判断查询字段是否有过期--单条查询是否失效判断
        //          块记录：开启块缓存
        //                  判断查询字段是否有字段 在主键字段(字段>1的前提) 和 块缓存数字中
        //                                                                  没有：说明是系统级查询:表更新时间>=缓存时间，就直接失效
        //                                                                  有记录：判断字段时间缓存是否失效---
        // 没有数据[空数据] 或非数组数据
        //          有效缓存
        // 有数据的单条或列表记录
        //           判断每一条记录的主键缓存
        //           有块缓存：判断每一条记录的块缓存
        //

        // 对查询条件字段进行判断
        $isCacheBlock = $this->isCacheBlock();//  缓存块
        $isCacheInfo = $this->isCacheInfo();//  缓存单条记录

        // 获得查询字段及字段值 ['字段1' => ['字段1值1', '字段1值2', ...] , ...]
        $whereFields = $this->getQueryFieldsVals($queryParams);
        //  没有查询字段(说明是系统级查询):表更新时间>=缓存时间，就直接失效
        if(empty($whereFields)) return false;
        $whereFieldsArr = array_keys($whereFields);

        // 已经判断过的
        $judgedFieldArr = [];// ['{1操作编号}字段名:字段值','{1操作编号}字段名{分隔符}字段名:字段值{分隔符}字段值',...]
        // 获得缓存主字段数组
        $primaryFields = $this->getAllCacheKeyFields( 1);
        if(!$this->hasPrimaryFields()) return false;
        // 是详情判断 -- 都需要优先判断主键
        // if(($cacheType & 1) == 1){
        // if(!$isCacheInfo) return  false;
        // 主键不在查询条件里
        if(($cacheType & 1) == 1 && !empty(array_diff($primaryFields, $whereFieldsArr))) return false;
        $hasPrimaryRight = false;// 是否有主键验证并通过 true:有 false:没有
        // 主键在查询条件中
        if(empty(array_diff($primaryFields, $whereFieldsArr))){
            // 查询条件中主键的值
            $t_fields = [];
            foreach($primaryFields as $t_field){
                $t_field_valArr =  $whereFields[$t_field] ?? [];
                if(empty($t_field_valArr)) return false;
                $t_fields[$t_field] = $t_field_valArr;
            }
            // echo '---456---';
            // 笛卡尔积
            $primaryValArr = Tool::descartes($t_fields);// Descartes($t_fields);// [ [字段值]或['字段1值','字段2值'],....]
            foreach($primaryValArr as $temPrimaryValArr){
                $temJudgedStr = '1' . $this->getCacheKeyValText($primaryFields, $temPrimaryValArr);
                if(in_array($temJudgedStr, $judgedFieldArr)) continue;
                $judgedFieldArr[] = $temJudgedStr;

                $cachePrimaryTime = $this->getCacheFieldsValByArr(1, $primaryFields, $temPrimaryValArr, 3);

                // 没有主键值缓存--下一条数据
                if($cachePrimaryTime === false || !is_string($cachePrimaryTime) || strlen($cachePrimaryTime) <= 0) return false;

                list($cachePrimaryTime, $cacheMsecint) = array_values(Tool::getTimeMsec($cachePrimaryTime));

                // 时间及微秒都相等，才是真的相等
                $judge_type = 1 + 2 + 4 + 32 + 256;
                if($cachePrimaryTime == $cacheTime && $msecint == $cacheMsecint) $judge_type = $judge_type | 512;


                // 对时间进行判断
                // 两个时间都不能为空，且都不能大于当前时间 且 单条块缓存时间，不能>= 块数据缓存时间
                $temResult = Tool::judgeBeginEndDate($cachePrimaryTime, $cacheTime, $judge_type, 2, $dateTime, '时间');

                // 出错 有效日期失效
                if($temResult !== true) return false;

                // 如果时间相等，则判断微秒的情况[缓存记录的微秒 <= 参考点微秒，则过期]
                 if($cachePrimaryTime == $cacheTime && $msecint <= $cacheMsecint) return false;

                $hasPrimaryRight = true;
            }
        }
        // }
        // 块缓存判断
        if(($cacheType & 2) == 2){
            if(!$isCacheBlock) return  false;
            $blockFields = $this->getCacheKeyFieldsArr(4);// ['标识' => ['字段',...],...]
            // 如果主键是多字段，需要加入进来
            if(count($primaryFields) > 1){
                $blockFields[] = $primaryFields;
            }
            if(empty($blockFields) && !$hasPrimaryRight) return false;

            $blockWhereArr = [];// ['block' => ['字段',.块缓存字段..], 'whereField' => '字段：当前的块缓存中在查询条件中的字段（从后往前查第一个）']
            foreach($blockFields as $temBlockFields){
                // 反转数组值
                $temBlockRverseFields = array_reverse($temBlockFields);
                foreach($temBlockRverseFields as $temField){
                    // 字段不在查询字段中
                    if(!in_array($temField, $whereFieldsArr)) continue;
                    $blockWhereArr[] = [
                        // 'block' => $temBlockFields,
                        'whereField' => $temField,
                    ];
                    break;// 只取到一个就可以
                }
            }
            // 查询字段，没有在块字段中的
            if(empty($blockWhereArr) && !$hasPrimaryRight) return false;
            // 进行判断
            foreach($blockWhereArr as $itemBlockWhere){
                $temBlockFields = $itemBlockWhere['whereField'];// 字段名
                $temFieldValArr = $whereFields[$temBlockFields] ?? [];// 字段值数组 -一维
                if(empty($temBlockFields) || empty($temFieldValArr)) continue;
                foreach($temFieldValArr as $temFv){
                    $temJudgedStr = '2' . $this->getCacheKeyValText([$temBlockFields], [$temFv]);
                    if(in_array($temJudgedStr, $judgedFieldArr)) continue;
                    $judgedFieldArr[] = $temJudgedStr;

                    $temCacheBlockTime = $this->getCacheFieldsValByArr(2, [$temBlockFields] , [$temFv], 3);
                    // 没有主键值缓存--下一条数据
                    if($temCacheBlockTime === false || !is_string($temCacheBlockTime) || strlen($temCacheBlockTime) <= 0) return false;

                    list($temCacheBlockTime, $cacheMsecint) = array_values(Tool::getTimeMsec($temCacheBlockTime));

                    // 时间及微秒都相等，才是真的相等
                    $judge_type = 1 + 2 + 4 + 32 + 256;
                    if($temCacheBlockTime == $cacheTime && $msecint == $cacheMsecint) $judge_type = $judge_type | 512;
                    // 两个时间都不能为空，且都不能大于当前时间 且 单条块缓存时间，不能>= 块数据缓存时间
                    $temResult = Tool::judgeBeginEndDate($temCacheBlockTime, $cacheTime, $judge_type, 2, $dateTime, '时间');
                    // 出错 有效日期失效
                    if($temResult !== true) return false;

                    // 如果时间相等，则判断微秒的情况[缓存记录的微秒 <= 参考点微秒，则过期]
                    if($temCacheBlockTime == $cacheTime && $msecint <= $cacheMsecint) return false;
                }
            }
        }

        // 如果数据为空，表的缓存时间 >= 缓存数据的时间 --表数据有变动    ----重新缓存
        // 或不是数组
        if(empty($dataArr) || !is_array($dataArr) ) return true;
        // 下面的都是数组

        $isMulti = Tool::isMultiArr($dataArr, true);
        // 超过最大缓存行数--有可能不是数组，不判断
        if($isMulti && count($dataArr) > $this->getMaxCacheRows()) return false;


        // 块级缓存 （表的缓存时间 >= 缓存数据的时间 ）
        // ---普通块缓存 -- （表的缓存时间 >= 缓存数据的时间 ）缓存失效, 因为不知道表更新了，是否会影响当前的查询结果
        //        a 没有开启 或 b 开启了但没有块缓存字段的 或 c 开启了且有缓存块字段，但查询字段没有在块缓存字段中匹配的
        // ---指定字段块缓存 -- （表的缓存时间 >= 缓存数据的时间 ）
        //        当前的缓存块有匹配的块缓存字段
        //            是否失效：判断块缓存字段的最新缓存时间 及 主键的缓存时间。

//        if( ($cacheType & 2) == 2 ){
//            // 没有开启块缓存 或 没有块缓存字段 或 查询字段不在块缓存字段中匹配
//            if(!$this->isCacheBlock() || !$this->isMultiQueryTable() || !$this->isValidBlockQuery($queryParams)) return false;
//            // 缓存的数据没有缓存 块缓存字段相关的
//            if(empty($cacheBlockArr)) return false;
//            $blockFieldsTextArr = $this->getFieldsTextArr(4);
//            $cacheBlockFieldsTextArr = array_keys($cacheBlockArr);
//            // 如果两个字段不同，--块字段有变化
//            if( !Tool::isEqualArr($blockFieldsTextArr, $cacheBlockFieldsTextArr, 1)) return false;
//            // 判断每一个
//            foreach($cacheBlockArr as $t_block_key => $block_time_Arr){
//                if(!is_array($block_time_Arr)) return false;
//                foreach($block_time_Arr as $blockFieldsValsStr){
//                    if(empty($blockFieldsValsStr)) return false;
//                    list($temFieldsArr, $temFieldsValsArr) = array_values($this->analyseFieldsValsText($t_block_key . ':' . $blockFieldsValsStr, false));
//                    $temCacheBlockTime = $this->getCacheFieldsValByArr(2, $temFieldsArr , $temFieldsValsArr, 3);
//                    // 没有主键值缓存--下一条数据
//                    if($temCacheBlockTime === false || !is_string($temCacheBlockTime) || strlen($temCacheBlockTime) <= 0) return false;
//
//                    list($temCacheBlockTime, $cacheMsecint) = array_values(Tool::getTimeMsec($temCacheBlockTime));
//                    // 时间及微秒都相等，才是真的相等
//                    $judge_type = 1 + 2 + 4 + 32 + 256;
//                    if($temCacheBlockTime == $cacheTime && $msecint == $cacheMsecint) $judge_type = $judge_type | 512;
//                    // 两个时间都不能为空，且都不能大于当前时间 且 单条块缓存时间，不能>= 块数据缓存时间
//                    $temResult = Tool::judgeBeginEndDate($temCacheBlockTime, $cacheTime, $judge_type, 2, $dateTime, '时间');
//                    // 出错 有效日期失效
//                    if($temResult !== true) return false;
//                    // 如果时间相等，则判断微秒的情况[缓存记录的微秒 <= 参考点微秒，则过期]
//                    if($temCacheBlockTime == $cacheTime && $msecint <= $cacheMsecint) return false;
//                }
//            }
//        }

        // 设置更新表记录的时间
        // 获得缓存主字段数组
        // $cachePrimaryFields = $this->getAllCacheKeyFields( 1);
        // if(!$this->hasPrimaryFields()) return false;

        // 对每天数据进行主键缓存时间进行判断
        // $isMulti = Tool::isMultiArr($cacheData, true);

        // 对每条数据进行主键缓存时间判断
        $result = true;

        foreach($dataArr as $k => $info){
            $infoArr = is_object($info) ? $info->toArray() : $info ;
            $temInfoKeys = array_keys($infoArr);
            if(!empty(array_diff($primaryFields, $temInfoKeys))){
                $result = false;
                break;
            }

            $t_v = Tool::getArrFormatFields($infoArr, $primaryFields, true);

            $temJudgedStr = '1' . $this->getCacheKeyValText($primaryFields, $t_v);
            if(in_array($temJudgedStr, $judgedFieldArr)) continue;
            $judgedFieldArr[] = $temJudgedStr;

            $cachePrimaryTime = $this->getCacheFieldsVal(1, $primaryFields, $infoArr, 3);
            // 没有主键值缓存--下一条数据
            if($cachePrimaryTime === false || !is_string($cachePrimaryTime) || strlen($cachePrimaryTime) <= 0){
                $result = false;
                break;
            }

            list($cachePrimaryTime, $cacheMsecint) = array_values(Tool::getTimeMsec($cachePrimaryTime));
            // 时间及微秒都相等，才是真的相等
            $judge_type = 1 + 2 + 4 + 32 + 256;
            if($cachePrimaryTime == $cacheTime && $msecint == $cacheMsecint) $judge_type = $judge_type | 512;
            // 对时间进行判断
            // 两个时间都不能为空，且都不能大于当前时间 且 单条块缓存时间，不能>= 块数据缓存时间
            $temResult = Tool::judgeBeginEndDate($cachePrimaryTime, $cacheTime, $judge_type, 2, $dateTime, '时间');
            // 出错 有效日期失效
            if($temResult !== true){
                $result = false;
                break;
            }

            // 如果时间相等，则判断微秒的情况[缓存记录的微秒 <= 参考点微秒，则过期]
            if($cachePrimaryTime == $cacheTime && $msecint <= $cacheMsecint){
                $result = false;
                break;
            }

        }
        if(!$isMulti) $dataArr = $dataArr[0] ?? [];
        return $result;
    }

    /**
     * 判断缓存是否有效的块字段查询条件
     * @param array $queryParams 查询请求 主要用到下标 ；含有 orWhere 不判断
     *                  where whereBetween 二维数组 ；数组每项中 ：第一个参数为字段名  ， 如果是两个值数组直接判断第一个项字段；如果有三个值数组，要判断第二个为=号
     *                      [
     *                          ['city', '1'],
     *                          ['status', '=', '1'],
     *                          ['subscribed', '<>', '1'],
     *                      ]
     *                  'whereIn' 二维数组 [ [字段名=>[多个字段值]],....]
     * @return  boolean true:是块查询条件；false：不是块查询条件
     * @author zouyan(305463219@qq.com)
     */
    public function isValidBlockQuery($queryParams = []){
        if(empty($queryParams)) return false;
        // if(isset($queryParams['orWhere']) && !empty(isset($queryParams['orWhere']))) return false;

        $blockFields = $this->getCacheKeyFieldsArr(4);
        if(!is_array($blockFields) || empty($blockFields)) return false;
        // if(isset($queryParams['whereNotBetween']) && !empty(isset($queryParams['whereNotBetween']))) return false;
        $queryWhereFields = $this->getQueryFields($queryParams);
        if(!is_array($queryWhereFields) ||  empty($queryWhereFields)) return false;
        foreach($blockFields as $fieldsArr){
            if(!is_array($fieldsArr) || empty($fieldsArr)) continue;
            if(empty(array_diff($fieldsArr, $queryWhereFields))) return true;
        }
        return false;
    }

    /**
     * 获得查询字段及值数组
     * @param array $queryParams 查询请求 主要用到下标 ；含有 orWhere 不判断
     *                  where whereBetween 二维数组 ；数组每项中 ：第一个参数为字段名  ， 如果是两个值数组直接判断第一个项字段；如果有三个值数组，要判断第二个为=号
     *                      [
     *                          ['city', '1'],
     *                          ['status', '=', '1'],
     *                          ['subscribed', '<>', '1'],
     *                      ]
     *                  'whereIn' 二维数组 [ [字段名=>[多个字段值]],....]
     * @return  array  查询字段及值数组 ['字段1' => ['字段1值1', '字段1值2', ...] , ...]
     * @author zouyan(305463219@qq.com)
     */
    public function getQueryFields($queryParams = []){
        return array_keys($this->getQueryFieldsVals($queryParams));
    }

    /**
     * 获得查询字段及值数组
     * @param array $queryParams 查询请求 主要用到下标 ；含有 orWhere 不判断
     *                  where whereBetween 二维数组 ；数组每项中 ：第一个参数为字段名  ， 如果是两个值数组直接判断第一个项字段；如果有三个值数组，要判断第二个为=号
     *                      [
     *                          ['city', '1'],
     *                          ['status', '=', '1'],
     *                          ['subscribed', '<>', '1'],
     *                      ]
     *                  'whereIn' 二维数组 [ [字段名=>[多个字段值]],....]
     * @return  array  查询字段及值数组 ['字段1' => ['字段1值1', '字段1值2', ...] , ...]
     * @author zouyan(305463219@qq.com)
     */
    public function getQueryFieldsVals($queryParams = []){
        $queryWhereFields = [];// 格式 ['字段1' => ['字段1值1', '字段1值2', ...] , ...]
        if(empty($queryParams)) return $queryWhereFields;
        if(isset($queryParams['orWhere']) && !empty(isset($queryParams['orWhere']))) return $queryWhereFields;

        // 和where查询相同结构的下标
        $whereFields = ['where', 'whereBetween'];// , 'whereDate', 'whereMonth', 'whereDay', 'whereYear', 'whereTime'
        foreach($whereFields as $field){
            if(isset($queryParams[$field]) && !empty(isset($queryParams[$field]))){
                $fieldQuery = $queryParams[$field];
                if(!is_array($fieldQuery)) continue;
                foreach($fieldQuery as $itemWhere){
                    if(!is_array($itemWhere)) continue;
                    if(!in_array(count($itemWhere), [2, 3])) continue;
                    $tField = $itemWhere[0] ?? '';
                    if(empty($tField)) continue;
                    $temFieldVal = '';// 字段值
                    if(count($itemWhere) == 3){
                        $operateStr = $itemWhere[1] ?? '';
                        if($operateStr != '=') continue;
                        $temFieldVal = $itemWhere[2] ?? '';
                    }elseif(count($itemWhere) == 2){
                        $temFieldVal = $itemWhere[1] ?? '';
                    }
                    if(empty($temFieldVal)) continue;
                    if(!is_array($temFieldVal)) $temFieldVal =  [$temFieldVal];
                    if(!isset($queryWhereFields[$tField])){
                        $queryWhereFields[$tField] = $temFieldVal;
                    }else{
                        $queryWhereFields[$tField] = array_unique(array_merge($queryWhereFields[$tField], $temFieldVal));
                    }
                }
            }
        }

        // 和whereIn查询相同结构的下标
        $whereInFields = ['whereIn'];
        foreach($whereInFields as $field){
            if(isset($queryParams[$field]) && !empty(isset($queryParams[$field]))){
                $fieldInQuery = $queryParams[$field];
                if(!is_array($fieldInQuery)) continue;
                foreach($fieldInQuery as $tField => $itemWhereVals){
                    if(!is_array($itemWhereVals) || empty($itemWhereVals)) continue;
                    if(!isset($queryWhereFields[$tField])){
                        $queryWhereFields[$tField] = $itemWhereVals;
                    }else{
                        $queryWhereFields[$tField] = array_unique(array_merge($queryWhereFields[$tField], $itemWhereVals));
                    }
                }
            }
        }
        return $queryWhereFields;
    }

    //##########有缓存数据##判断缓存数据是否失效####结束##############
    //##########重新缓存数据时##对数据进行处理##开始##############


    //##########重新缓存数据时##对数据进行处理####结束##############
    /**
     * 更新表记录时，更新表更新时间缓存
     * 注意：增、改、删除与  $cacheType 缓存类型 没有关系，也和
     * @param int $cacheType 缓存类型 1 单条记录缓存 2 块级缓存[默认]
     * @param int $operateType 操作类型 8 查询--缓存/重新缓存时[不用返回值]；
     * @param array/int $recordData 需要更新的记录 -一维或二维数组
     * @param array $queryParams 查询请求 主要抽取查询条件where whereIn数组  主要用到下标 ；含有 orWhere 不判断
     *                  where whereBetween 二维数组 ；数组每项中 ：第一个参数为字段名  ， 如果是两个值数组直接判断第一个项字段；如果有三个值数组，要判断第二个为=号
     *                      [
     *                          ['city', '1'],
     *                          ['status', '=', '1'],
     *                          ['subscribed', '<>', '1'],
     *                      ]
     *                  'whereIn' 二维数组 [ [字段名=>[多个字段值]],....]
     *
     * @param array $select 需要指定的字段 主要用来判断要请求的字段是否包含排除字段 -一维数组；为空代表所有字段 16
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @return   mixed false:不用缓存数据  true:$recordData 处理后的数据[缓存结构的数据] sting 具体错误 ； throws 错误
     * @author zouyan(305463219@qq.com)
     */
    public function setDbUpdateTimeCache($cacheType = 2, $operateType = 8, &$recordData = [], $queryParams = [], $select = [], $errDo = 1){
        // 未开通缓存 或 返回的字段包含排除字段
        if(!$this->isDefaultOpenCache($cacheType, $select))  return false;
        $isCacheBlock = $this->isCacheBlock();
        $isCacheInfo = $this->isCacheInfo();
        // 都不用缓存---直接返回
        if(!$isCacheBlock && !$isCacheInfo) return false;

        $dateTime = date('Y-m-d H:i:s');
        $msecint = (Tool::msecTime())['msecint'];
        $dateTimeMsecint = Tool::timeJoinMsec($dateTime, $msecint);

        $dataArr = is_object($recordData) ? $recordData->toArray() : $recordData;

        $isMulti = false;
        if(is_array($dataArr) ) $isMulti = Tool::isMultiArr($dataArr, true);
        // 超过最大缓存行数 --有可能不是数组，不判断
        if( $isMulti && is_array($dataArr) && count($dataArr) > $this->getMaxCacheRows()) return false;

        // 对每天数据进行主键缓存时间进行判断
        // $isMulti = Tool::isMultiArr($dataArr, true);
        // 所有缓存字段
        $cacheKeyFields = $this->getAllCacheKeyFields( 1 | 2 | 4 );
        if(!is_array($cacheKeyFields) || empty($cacheKeyFields)) return false;

        // 获得缓存主字段数组
        $primaryFields = $this->getAllCacheKeyFields( 1);
        if(!$this->hasPrimaryFields()) return false;

        // 块字段
        $blockFields = $this->getCacheKeyFieldsArr(4);
        if(($cacheType & 2) == 2 && $isCacheBlock && !is_array($blockFields)) return false;//  || empty($blockFields)

        // 主键相关的
        $PKeyFields = $this->getCacheKeyFieldsArr(2);
        if(($cacheType & 1) == 1 && $isCacheInfo && !is_array($PKeyFields)) return false;//  || empty($PKeyFields)

        // 已经判断过的
        $judgedFieldArr = [];// ['{1操作编号}字段名:字段值','{1操作编号}字段名{分隔符}字段名:字段值{分隔符}字段值',...]

        // 获得查询字段及字段值 ['字段1' => ['字段1值1', '字段1值2', ...] , ...]
        $whereFields = $this->getQueryFieldsVals($queryParams);
        //  没有查询字段(说明是系统级查询):表更新时间>=缓存时间，就直接失效
        // if(empty($whereFields)) return false;
        $whereFieldsArr = array_keys($whereFields);
        $cacheTimeConfig = array_values($this->getCacheTimeConfig());

        if(!empty($whereFields)){
            // 主键在查询条件中
            if(empty(array_diff($primaryFields, $whereFieldsArr))){
                // 查询条件中主键的值
                $t_fields = [];
                foreach($primaryFields as $t_field){
                    $t_field_valArr = $whereFields[$t_field] ?? [];
                    if(empty($t_field_valArr)) return false;
                    $t_fields[$t_field] = $t_field_valArr;
                }
                // 笛卡尔积
                $primaryValArr = Tool::descartes($t_fields);// Descartes($t_fields);// [ [字段值]或['字段1值','字段2值'],....]
                foreach($primaryValArr as $temPrimaryValArr){
                    $temJudgedStr = '1' . $this->getCacheKeyValText($primaryFields, $temPrimaryValArr);
                    if(in_array($temJudgedStr, $judgedFieldArr)) continue;
                    $judgedFieldArr[] = $temJudgedStr;
                    $this->getCacheFieldsValByArr(1, $primaryFields, $temPrimaryValArr, 3, true, $dateTimeMsecint, $cacheTimeConfig, 4);
                }
            }
            // 块缓存判断
            if(($cacheType & 2) == 2 && $isCacheBlock){
                // 如果主键是多字段，需要加入进来
                if(count($primaryFields) > 1){
                    $blockFields[] = $primaryFields;
                }
                // if(empty($blockFields)) return false;

                $blockWhereArr = [];// ['block' => ['字段',.块缓存字段..], 'whereField' => '字段：当前的块缓存中在查询条件中的字段（从后往前查第一个）']
                foreach($blockFields as $temBlockFields){
                    // 反转数组值
                    $temBlockRverseFields = array_reverse($temBlockFields);
                    foreach($temBlockRverseFields as $temField){
                        // 字段不在查询字段中
                        if(!in_array($temField, $whereFieldsArr)) continue;
                        $blockWhereArr[] = [
                            // 'block' => $temBlockFields,
                            'whereField' => $temField,
                        ];
                        break;// 只取到一个就可以
                    }
                }
                // 查询字段，没有在块字段中的
                // if(empty($blockWhereArr)) return false;
                // 进行判断
                foreach($blockWhereArr as $itemBlockWhere){
                    $temBlockFields = $itemBlockWhere['whereField'];// 字段名
                    $temFieldValArr = $whereFields[$temBlockFields] ?? [];// 字段值数组 -一维
                    if(empty($temBlockFields) || empty($temFieldValArr)) continue;
                    foreach($temFieldValArr as $temFv){
                        $temJudgedStr = '2' . $this->getCacheKeyValText([$temBlockFields], [$temFv]);
                        if(in_array($temJudgedStr, $judgedFieldArr)) continue;
                        $judgedFieldArr[] = $temJudgedStr;
                        $this->getCacheFieldsValByArr(2, [$temBlockFields], [$temFv], 3, true, $dateTimeMsecint, $cacheTimeConfig, 4);
                    }
                }
            }
        }


//        $block = [];// 块字段缓存 [ '字段1' => ['字段1值1', '字段1值2',...],  '字段1{分隔符}字段2' => ['字段1值1{分隔符}字段2值1', '字段1值1{分隔符}字段2值1',...]]
//        $blockSingleFields = [];// 块单个字段缓存 ['字段1', '字段2',...]
//        $primaryValArr = [];// 主键缓存字段 ['字段1', '字段1{分隔符}字段2']
//        $PKey = [];// 主键指向缓存字段 与变量 $block 变量 结构相同
        // 为空或不是数组
        if(empty($dataArr) || !is_array($dataArr)){


        }else if(!empty($dataArr) && is_array($dataArr)){

            foreach($dataArr as $info){
                $infoArr = is_object($info) ? $info->toArray() : $info;
                if(empty($infoArr)) return false;
                $infoKey = array_keys($infoArr);
                // 缓存相关的键不存数据下标中
                if(!empty(array_diff($cacheKeyFields, $infoKey))) return false;

//                // 块字段缓存没有，则重新缓存
//                if($isCacheBlock && $this->isMultiQueryTable()){
//                    foreach($blockFields as $blockFieldsArr){
//                        $blockValArr = Tool::getArrFormatFields($infoArr, $blockFieldsArr, true);// 获得 ['字段名' => '字段值',...]
//                        $blockUbound = implode(static::$separatoChar, $blockValArr);// 值文字
//                        $blockFieldText = implode(static::$separatoChar, $blockFieldsArr);// 字段文字
//                        if(!isset($block[$blockFieldText])){
//                            $block[$blockFieldText] = [$blockUbound];
//                        }else{
//                            if(in_array($blockUbound, $block[$blockFieldText])) continue;// 字段值已存在
//                            $block[$blockFieldText][] = $blockUbound;
//                        }
//                        $this->getCacheFieldsValByArr(2, $blockFieldsArr, $blockValArr, 3, true, $dateTimeMsecint);
//
//                        // 如果是两个以上字段,则单个字段还要缓存时间
//                        if(count($blockFieldsArr) <= 1){// 只有一个字段,记录下,上面已经缓存
//                            foreach($blockFieldsArr as $temField){
//                                if(!in_array($temField, $blockSingleFields)) $blockSingleFields[] = $temField;
//                            }
//                            continue;
//                        }
//                        foreach($blockFieldsArr as $temField){
//                            $temFieldVal = $infoArr[$temField] ?? '';
//                            if(empty($temField) || strlen($temFieldVal) <= 0) continue;
//                            if(in_array($temField, $blockSingleFields)) continue;
//                            $blockSingleFields[] = $temField;
//                            $this->getCacheFieldsValByArr(2, [$temField], [$temFieldVal], 3, true, $dateTimeMsecint);
//                        }
//                    }
//                }

                // 主键缓存没有，则重新缓存
                $p_v = Tool::getArrFormatFields($infoArr, $primaryFields, true);

                $temJudgedStr = '1' . $this->getCacheKeyValText($primaryFields, $p_v);
                if(!in_array($temJudgedStr, $judgedFieldArr)){
                    $judgedFieldArr[] = $temJudgedStr;
                    $this->getCacheFieldsValByArr(1, $primaryFields, $p_v, 3, true, $dateTimeMsecint, $cacheTimeConfig, 4);
                }

//                $primaryUbound = implode(static::$separatoChar, $primaryFields);// 字段文字
//                $primaryValText = implode(static::$separatoChar, $p_v);// 值文字
//                if(!in_array($primaryValText, $primaryValArr)){
//                    $primaryValArr[] = $primaryValText;
//                    $this->getCacheFieldsValByArr(1, $primaryFields, $p_v, 3, true, $dateTimeMsecint);
//                }
                /**
                 *
                 * 如果主键是多个字段 ,则单个字段还要缓存时间到block
                 *if($isCacheBlock && count($primaryFields) > 1){
                 *   foreach($primaryFields as $temField){
                 *       $temFieldVal = $infoArr[$temField] ?? '';
                 *       $temJudgedStr = '2' . $this->getCacheKeyValText([$temField], [$temFieldVal]);
                 *      if(in_array($temJudgedStr, $judgedFieldArr)) continue;
                 *      $judgedFieldArr[] = $temJudgedStr;
                 *      $this->getCacheFieldsValByArr(2, [$temField], [$temFieldVal], 3, true, $dateTimeMsecint);
                 *
                 *
                 *        // if(empty($temField) || strlen($temFieldVal) <= 0) continue;
                 *        // if(in_array($temField, $blockSingleFields)) continue;
                 *        // $blockSingleFields[] = $temField;
                 *        //  $this->getCacheFieldsValByArr(2, [$temField], [$temFieldVal], 3, true, $dateTimeMsecint);
                 *   }
                 *}
                 *
                 */
                // 主键相关的指向
                if(($cacheType & 1) == 1 && $isCacheInfo && $this->hasPrimaryRelevantKeyFields()){
                    foreach($PKeyFields as $PKeyFieldsArr){
                        $PKeyValArr = Tool::getArrFormatFields($infoArr, $PKeyFieldsArr, true);
                        $temJudgedStr = '4' . $this->getCacheKeyValText($PKeyFieldsArr, $PKeyValArr);
                        if(in_array($temJudgedStr, $judgedFieldArr)) continue;
                        $judgedFieldArr[] = $temJudgedStr;
                        $this->getCacheFieldsValByArr(4, $PKeyFieldsArr, $PKeyValArr, 3, true, $this->getCacheKeyValText($primaryFields, $p_v), $cacheTimeConfig, 4);

//                        $PKeyUbound = implode(static::$separatoChar, $PKeyValArr);// 值文字
//                        $PKeyFieldText = implode(static::$separatoChar, $PKeyFieldsArr);// 字段文字
//
//                        if(!isset($PKey[$PKeyFieldText])){
//                            $PKey[$PKeyFieldText] = [$PKeyUbound];
//                        }else{
//                            if(in_array($PKeyUbound, $PKey[$PKeyFieldText])) continue;// 字段值已存在
//                            $PKey[$PKeyFieldText][] = $PKeyUbound;
//                        }
//                        $this->getCacheFieldsValByArr(4, $PKeyFieldsArr, $PKeyValArr, 3, true, $this->getCacheKeyValText($primaryFields, $p_v));
                    }
                }
            }
            // if(!$isMulti) $dataArr = $dataArr[0] ?? [];
        }
        if(is_array($dataArr) && !$isMulti) $dataArr = $dataArr[0] ?? [];
        // 表最新的更新时间--不存在缓存值时，缓存一下
        $this->getTableUpdateTimeCache(true, 4);
        // 更改$recordData的值
        $recordData = [
            'time' => date('Y-m-d H:i:s'),// 缓存时间
            'msecint' => (Tool::msecTime())['msecint'],// 微秒的整数形式
            'md5' => $this->getTableAllFieldsMd5(),// 当前表字段md5值
            // 'block' => $block,// 如果是块级缓存，块级字段的值数组
            'data' => $recordData,// 缓存数据
        ];
        return true;
    }
    //######设置更新时间相关的############结束#########################################


    /**
     * 根据查询字段，及$cacheExcludeFields = [];// 表字段中排除字段; 判断默认是否开启缓存
     * @param int $cacheType 缓存类型 1 单条记录缓存 2 块级缓存[默认]
     * @param array $select 查询要获取的字段数组 一维数组
     * @return  boolean 是否开启缓存 true:开启/使用缓存；false：不使用缓存
     * @author zouyan(305463219@qq.com)
     */
    public function isDefaultOpenCache($cacheType = 2, $select = []){
        // 对缓存排除字段进行判断；排除字段可能是大小很大的字段，不适宜进行缓存
        $cacheExcludeFields = static::$cacheExcludeFields;
        if(empty($select) && !empty($cacheExcludeFields)) return false;// 获得所有数据，但是有排除字段---不缓存
        // 如果有交集，则不缓存。
        if(!empty($select) && !empty($cacheExcludeFields)){
            $intersectFields = array_intersect($cacheExcludeFields, $select);
            if(!empty($intersectFields)) return false;
        }
        // 单条记录缓存
        if($cacheType == 1){
            return $this->isCacheInfo();
        }elseif($cacheType == 2){// 块级缓存
            return $this->isCacheBlock();
        }
        return true;
    }

    /**
     * 表是否多情况（多种平台应该；如按城市分站）缓存
     *
     * @return  boolean true:多情况（多种平台应该；如按城市分站）缓存；false：系统/公用类别的缓存
     * @author zouyan(305463219@qq.com)
     */
    public function isMultiQueryTable(){
        if(!is_array(static::$cacheBlockFields) || empty(static::$cacheBlockFields)){
            return false;
        }
        return true;
    }


    /**
     * 表是否有主键相关指向缓存
     *
     * @return  boolean true:多情况（多种平台应该；如按城市分站）缓存；false：系统/公用类别的缓存
     * @author zouyan(305463219@qq.com)
     */
    public function hasPrimaryRelevantKeyFields(){
        if(!is_array(static::$cachePrimaryKeyFields) || empty(static::$cachePrimaryKeyFields)){
            return false;
        }
        return true;
    }

    /**
     * 字段是否是有效字段--有字段时
     *
     * @param int $dataType 需要获得的数据字段 1 主键 ；2：值需要作为缓存主键键的字段；4 值需要参与块级缓存的字段；
     * @return  boolean true:有效；false：无效
     * @author zouyan(305463219@qq.com)
     */
    public function isValidFields($dataType = 2){
        $cacheFields = $this->getAllCacheKeyFields( $dataType );
        // 这里必须返回true;而不是false;因为可以为空
        if(!is_array($cacheFields) || empty($cacheFields)) return true;

        // 获得表所有字段
        $tableFields = $this->getTableAllFields();
        // 指定的主有不存在表字段的
        $diffTableFields = array_diff($cacheFields, $tableFields);
        if(!empty($diffTableFields)) return false;
        return true;
    }

    /**
     * 表是否有主键，记录唯一键
     *
     * @return  boolean true:有主键；false：无主键
     * @author zouyan(305463219@qq.com)
     */
    public function hasPrimaryFields(){
        // 1 缓存主键
        $primaryFields = $this->getAllCacheKeyFields( 1);
        // 没有指定主键
        if(empty($primaryFields)) return false;
        // 获得表所有字段
        $tableFields = $this->getTableAllFields();
        // 指定的主有不存在表字段的
        $diffTableFields = array_diff($primaryFields, $tableFields);
        if(!empty($diffTableFields)) return false;
        return true;
    }

    /**
     * 主键是否多字段
     *
     * @return  boolean true:多个字段；false：1个字段
     * @author zouyan(305463219@qq.com)
     */
    public function isMultiPrimaryFields(){
        // 1 缓存主键
        $primaryFields = $this->getAllCacheKeyFields( 1);
        if(count($primaryFields) >= 2) return true;
        return false;
    }

    /**
     * 是否缓存单条记录 :1指定不缓存或2没有主缓存/主键或3主缓存/主键有不是表中字段的
     *
     * @return  boolean true:可缓存；false：不可缓存
     * @author zouyan(305463219@qq.com)
     */
    public function isCacheInfo()
    {
        if(($this->getDataCachType() & 1) != 1)   return false;
        $result = $this->hasPrimaryFields();
        if($result){
            return $this->isValidFields(2);
        }
        return $result;
    }

    /**
     * 是否缓存块记录 :1指定不缓存或2没有指定字段3字段不是表中字段的
     *
     * @return  boolean true:可缓存；false：不可缓存
     * @author zouyan(305463219@qq.com)
     */
    public function isCacheBlock()
    {
        if(($this->getDataCachType() & 2) != 2)   return false;
        $result = $this->hasPrimaryFields();
        if($result){
            return $this->isValidFields(4);
        }
        return $result;
    }

    /**
     * 获得需要缓存的字段-- 一维数据--注意未包含扩展字段$appends
     *
     * @return  array 空数组，代表所有字段，有字段值[一维]：代表需要缓存的字段
     * @author zouyan(305463219@qq.com)
     */
    public function getCacheFields()
    {
        $needCacheFields = [];
        // 排除字段和指定字段为空，则缓存所有字段
        if(empty(static::$cacheExcludeFields)) return $needCacheFields;

        // 获得表所有字段
        $tableFields = $this->getTableAllFields();

        // 去掉排除的
        $needCacheFields = array_values(array_diff($tableFields, static::$cacheExcludeFields));

        // 加入指定的字段及和要作为缓存键的字段
        $cacheKeyFields = $this->getAllCacheKeyFields( 1 | 2 | 4 );
        $needCacheFields = array_values(array_merge($needCacheFields, $cacheKeyFields));
        return $needCacheFields;
    }

    /**
     * 对数据，获得指定字段的格式化---内部之所以用排除法，是因为 $dataArr可能会包含其它非表中的字段的下标，且此下标也不在$select中，要保留
     * @param array $dataArr 一维/二维数据
     * @param array $select 需要指定的字段 -一维数组；为空代表所有字段
     * @return  array 指定字段后的数据
     * @author zouyan(305463219@qq.com)
     */
    public function dataFormatSelect($dataArr , $select = []){
        if(empty($select)) return $dataArr;
        // 获得表所有字段
        $tableFields = $this->getTableAllFields();
        $excludeFields = array_diff($tableFields, $select);
        return  Tool::getArrFormatExcludeFields($dataArr, $excludeFields);
    }

    /**
     * 缓存单条记录数据--空记录也会缓存
     *
     * @param array/object $dbDataInfo 要缓存的数据 --一维数组
     * @param array $fieldVals 不能为空，为空，则返回空数组； 查询的字段及值 ['字段1' => '字段1的值', '字段2' => '字段2的值']
     * @param array $select 需要指定的字段 -一维数组；为空代表所有字段
     * @return  null 无返回值
     * @author zouyan(305463219@qq.com)
     */
//    public function cacheInfo($dbDataInfo = [], $fieldVals = [], $select = []){
//        $keyRedisPre = $this->getCacheSimple();
//        $funParams = [$fieldVals, $select];
//
//        // 未开通缓存 或 返回的字段包含排除字段
//        if(!$this->isDefaultOpenCache(1, $select))  return false;
//
//        $dataInfo = is_object($dbDataInfo) ? $dbDataInfo->toArray() : $dbDataInfo;
//        $fieldKeys = array_keys($dataInfo);
//
//        $queryParams = CommonDB::getGueryParams($fieldVals, $select);
//        // 缓存前对数据进行处理
//        if(!$this->setDbUpdateTimeCache(1, 8, $dbDataInfo, $queryParams, $select, 1)) return false;
//
//        // 获得缓存主字段数组
//        $cachePrimaryFields = $this->getAllCacheKeyFields( 1);
//
//        if(!empty(array_diff($cachePrimaryFields, $fieldKeys))) return false;
//
//        // 只保留主键字段的值
//        $primaryVals = Tool::getArrFormatFields($dataInfo, $cachePrimaryFields, true);
//
//        $temCacheKey = $this->getCacheKey(5, $cachePrimaryFields, $primaryVals);
//
//        // 更新新缓存
//        Log::info('数据缓存日志 --详情--缓存数据-->'  . date('Y-m-d H:i:s') . $keyRedisPre . __CLASS__ . '->' . __FUNCTION__, $funParams);
//        Tool::setRedis($this->getCacheSimple(), $temCacheKey, $dbDataInfo, $this->getDataCacheExpire(), static::$operateRedis);
//        return true;
//
//    }

    /**
     * 根据次缓存，获得数据--参数为空，则返回空数组
     *  cacheDb:U:m:{email值}_{email值}  -> {id值}
     * @param array $paramsPrimaryVals 刚好[字段不能多]用上缓存，不然就用不了缓存 [ '字段1' => '字段1的值','字段2' => '字段2的值'] ;为空，则返回空数组--注意字段是刚好[主键或主字段]，不能有多,顺序无所谓
     * @param array $select 查询要获取的字段数组 一维数组
     * @param array $relations 关系
     * @return  null 无返回值
     * @author zouyan(305463219@qq.com)
     */
//    public function getInfoByCache($paramsPrimaryVals = [], $select = [], $relations = [], $isOpenCache = true, $openCacheRequest = [], $extendExpire = [])
//    {
//        $keyRedisPre = $this->getCacheSimple();// 前缀
//        $funParams = [$paramsPrimaryVals, $select, $relations];// 相关参数
//        $dbDataInfo = [];// 最终数据
//        // $isOpenCache = true;// 是否开启缓存 true:开启/使用缓存；false：不使用缓存
//        $isReadOrCache = true;// 是否需要重新读取并缓存 true:重新读取并缓存；false:不用重新读取[有缓存数据]
//
//        if(empty($paramsPrimaryVals)) $dbDataInfo;// throws('请求参数不能为空！');
//
//        $fieldKeys = array_keys($paramsPrimaryVals);// 查询的字段数组-一维数组
//
//        // $isCacheInfo = $this->isCacheInfo();
//        // 未开通缓存 或 返回的字段包含排除字段
//        if(!$isOpenCache || !$this->isDefaultOpenCache(1, $select)){
//            Log::info('数据缓存日志 --详情--未开通缓存 或 返回的字段包含排除字段-->'  . date('Y-m-d H:i:s') . $keyRedisPre  . __CLASS__ . '->' . __FUNCTION__, $funParams);
//            // 肯定全新表读取数据
//            $dbDataInfo = CommonDB::getInfoByFieldVals($this,$paramsPrimaryVals, $select, false);
//            // 查询关系参数
//            CommonDB::resolveRelations($dbDataInfo, $relations);
//            return $dbDataInfo;
//        }
//        // 以下都是可以缓存的情况汇： 开通缓存  不包含排除字段
//
//        // 获得缓存主字段数组
//        $cachePrimaryFields = $this->getAllCacheKeyFields( 1);
//        // 如果缓存，需要缓存的字段
//        $cacheAllFields = $this->getCacheFields();
//
//        // 刚好是主键缓存
//        // 先判断主键是否就可以获得结果
//        if(Tool::isEqualArr($cachePrimaryFields, $fieldKeys, 1) ){
//            Log::info('数据缓存日志 --详情--通过主键获得数据-->'  . date('Y-m-d H:i:s') . $keyRedisPre  . __CLASS__ . '->' . __FUNCTION__, $funParams);
//            $isNeedReCache = false;// 是否需要重新获得数据并缓存
//            $dbDataInfo = $this->getCacheFieldsVal(5, $cachePrimaryFields, $paramsPrimaryVals, static::$operateRedis);
//            // 没有主键值缓存--下一条数据
//            if($dbDataInfo === false){//  || !is_array($dbDataInfo)
//                Log::info('数据缓存日志 --详情--缓存失效-->'  . date('Y-m-d H:i:s') . $keyRedisPre  . __CLASS__ . '->' . __FUNCTION__, $funParams);
//                $isNeedReCache = true;
//            }else{
//                $queryParams = CommonDB::getGueryParams($paramsPrimaryVals, $select);
//                // 判断缓存数据是还有效
//                if(!$this->judgeCacheData(1, 16, $dbDataInfo, $queryParams, $select, 1)){
//                    $isNeedReCache = true;
//                    Log::info('数据缓存日志 --详情--缓存失效-->'  . date('Y-m-d H:i:s') . $keyRedisPre  . __CLASS__ . '->' . __FUNCTION__, $funParams);
//                }
//            }
//            if($isNeedReCache){
//                //缓存源- 肯定全新表读取数据
//                $dbDataInfo = CommonDB::getInfoByFieldVals($this,$paramsPrimaryVals, $cacheAllFields, false);
//                Log::info('数据缓存日志 --详情--重新从数据表获得数据-->'  . date('Y-m-d H:i:s') . $keyRedisPre  . __CLASS__ . '->' . __FUNCTION__, $funParams);
//                // 重新缓存数据
//                $this->cacheInfo($dbDataInfo, $paramsPrimaryVals, $cacheAllFields);
//            }else{
//                Log::info('数据缓存日志 --详情--通过主键缓存获得数据--缓存有效-->'  . date('Y-m-d H:i:s') . $keyRedisPre  . __CLASS__ . '->' . __FUNCTION__, $funParams);
//            }
//            // 查询关系参数
//            CommonDB::resolveRelations($dbDataInfo, $relations);
//            // 返回需要获取的字段
//            if(!empty($select))  $dbDataInfo = $this->dataFormatSelect($dbDataInfo , $select);
//            return $dbDataInfo;
//        }
//
//        // 不用缓存，则直接从数据表获取数据--没有/不是相关缓存，也不是主缓存
//        if(!$this->hasPrimaryRelevantKeyFields()){
//            Log::info('数据缓存日志 --详情--没有主键相关缓存，直接从数据表获取-->'  . date('Y-m-d H:i:s') . $keyRedisPre  . __CLASS__ . '->' . __FUNCTION__, $funParams);
//            // 这里可以用块级缓存--底层已经使用块级缓存
//            $dbDataInfo = CommonDB::getInfoByFieldVals($this, $paramsPrimaryVals, $select, $isOpenCache);
//            // 查询关系参数
//            CommonDB::resolveRelations($dbDataInfo, $relations);
//            return $dbDataInfo;
//        }
//        // 1:没有匹配的相关主键 ；2有相关匹配的字段-但没有缓存值/缓存值有问题/主键有变化---需要重新缓存；4 有相关匹配的字段--相关缓存有效
//        $readPrimaryKey = 1;// 是否有读主键缓存 true:有读:false:没有
//
//        // 获得缓存主字段数组
//        // $cachePrimaryFields = $this->getAllCacheKeyFields( 1);
//
//        $PKeyFields = $this->getCacheKeyFieldsArr(2);
//        foreach($PKeyFields as $k => $pkFields){
//            if(empty($pkFields)) continue;
//            // 选择都包含的
//            if(Tool::isEqualArr($pkFields, $fieldKeys, 1)){
//                // 有主键值缓存，获取数据
//                // 主缓存键的字段值  U:{id值}_{email值}  中的 {id值}_{email值}
//                $MkeyVals = $this->getCacheFieldsVal(4, $pkFields, $paramsPrimaryVals, 3);
//                // 没有主键值缓存--下一条数据
//                if($MkeyVals === false || !is_string($MkeyVals) || strlen($MkeyVals) <= 0){
//                    $readPrimaryKey = 2;
//                    continue;
//                }
//                list($primaryFieldsArr, $primaryFieldsValsArr, $temFieldsVals) = array_values($this->analyseFieldsValsText($MkeyVals, true));
//                if(empty($primaryFieldsArr) || empty($primaryFieldsValsArr)){
//                    $readPrimaryKey = 2;
//                    continue;
//                }
//                // 主键字段有变化
//               if(!Tool::isEqualArr($cachePrimaryFields, $primaryFieldsArr, 4)){
//                   $readPrimaryKey = 2;
//                   continue;
//               }
//                $dbDataInfo = $this->getInfoByCache($temFieldsVals, $select, $relations);
//                $readPrimaryKey = 4;
//                break;
//            }
//        }
//        // 1:没有匹配的相关主键 ；
//        if(($readPrimaryKey & 1) == 1){
//            Log::info('数据缓存日志 --详情--未从缓存读过，需要重新缓存-->'  . date('Y-m-d H:i:s') . $keyRedisPre  . __CLASS__ . '->' . __FUNCTION__, $funParams);
//            // 这里可以用块级缓存--底层已经使用块级缓存
//            $dbDataInfo = CommonDB::getInfoByFieldVals($this, $paramsPrimaryVals, $select, $isOpenCache);
//            // 查询关系参数
//            CommonDB::resolveRelations($dbDataInfo, $relations);
//        }elseif(($readPrimaryKey & 2) == 2){// 2有相关匹配的字段-但没有缓存值/缓存值有问题/主键有变化---需要重新缓存；
//            Log::info('数据缓存日志 --详情--未从缓存读过，需要重新缓存-->'  . date('Y-m-d H:i:s') . $keyRedisPre  . __CLASS__ . '->' . __FUNCTION__, $funParams);
//            // 重新读取数据 缓存源- 肯定全新表读取数据
//            $dbDataInfo = CommonDB::getInfoByFieldVals($this, $paramsPrimaryVals, $cacheAllFields, false);
//            // TODO 主缓存不存在 重新缓存
//            // TODO 主缓存存在，仅更新相关缓存
//            $this->cacheInfo($dbDataInfo, $paramsPrimaryVals, $cacheAllFields);
//            // 查询关系参数
//            CommonDB::resolveRelations($dbDataInfo, $relations);
//            // 返回需要获取的字段
//            if(!empty($select))  $dbDataInfo = $this->dataFormatSelect($dbDataInfo , $select);
//        }
//        // 4 有相关匹配的字段--相关缓存有效
//        return $dbDataInfo;
//    }
}
