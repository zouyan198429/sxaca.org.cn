<?php
namespace App\ModelsVerify;
use App\Services\DB\CommonDB;
use App\Services\Tool;
use Illuminate\Support\Facades\Log;

/**
 * 说明：
 *  fields.字段. 下的 field_name 如果有值，则用当前的，如果没有值（空），则用对应的lang文件的  fields.字段.field_name
 *  fields.字段. 下的valiDateParam.message        如果有值，则用当前的，如果没有值（空），则用对应的lang文件的  fields.字段.message
 *         ......                       ....   这两个message 值 可以包含
 *                                       枚举 {enum}【注意:enum：是lang文件的  fields.字段.enum】；
 *                                            {min}；【valiDateParam数组下的 min】
 *                                            {max}；【valiDateParam数组下的 max】
 *                                            {fieldName} 【这个是真正处理好的名称】；
 *                                      会按这个顺序进行自动替换。
 *  field_name 和 message 都会用 $langModel 多语言公用配置数组 的下标进行遍历替换---也就是可以用这个文件的下标来做标签
 *  这里建议：field_name 和 message 不作设置，全放到lang的文件中去做langModel 标签，这里只是验证设置，具体的文字的还是放lang来设置
 */
class BaseVerify
{

    // public static $dbFileTag = 'models';// 数据表配置文件的标识
    public static $dbDir = '';// 区分多数据库的数据目录
    public static $model_name = '';// 相对于Models的数据模型名称;在子类中定义，使用时用static::,不用self::
    public static $table_name = '';// 数据表名称
    // 需要从父的去掉的字段  -- 一维数组
    // 如 ['version_history_id', 'version_num_history']
    public static $delFields = [];

    /**
     * 获得验证规则-- 对外
     *
     * @param string $configUbound 读取配置数组下的指这下标，可为空：读整个配置
     * @param string $dbFileTag 数据表配置文件的标识
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public static function getVerifyRule($configUbound = "fields", $dbFileTag = 'models'){
        $fieldsConfig = static::getVerifyRuleArr($dbFileTag);
        if(!is_array($fieldsConfig)) $fieldsConfig = [];
        if(is_string($configUbound) && strlen($configUbound) > 0) $fieldsConfig = $fieldsConfig[$configUbound] ?? [];
        return $fieldsConfig;
    }
    /**
     * 获得验证规则
     *
     * @param string $dbFileTag 数据表配置文件的标识
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public static function getVerifyRuleArr($dbFileTag = 'models')
    {
//        $dbFileTag = static::$dbFileTag;// 'models';
//        $dbDir = static::$dbDir;// 'RunBuy';
//        $table_name = static::$table_name;// 'brands';
//        $keyModel = $dbFileTag;
//        $key = $dbFileTag . $dbDir . $table_name; // 'modelsRunBuyfailed_jobs.fields'
        // $keyRedisPre = 'lang:' . static::$dbFileTag  . ':' . static::$dbDir  ;
        $keyRedisPre = Tool::getProjectKey(1, ':', ':') . 'lang:' . $dbFileTag  . ':' . static::$dbDir  ;
        $keyRedis = ':' . static::$table_name;
        $operateRedis = 1;
        $tableConfig = [];
        // 获得缓存
        $needCache = config('public.langDBFieldCacheOpen');
        $cacheExpire = config('public.langDBFieldCacheExpire');

        // 判断数据库结构是否有改动
        $keyRedisPreNot = '';
        // $forceReCache 是否强制重新缓存 true:强制缓存;有变动[不可用缓存，重新缓存] ; false:不强制缓存;无变动[缓存有效可用]
        $forceReCache = CommonDB::getDBChangeStatus($keyRedisPreNot, static::$dbDir, static::$table_name);

        // 判断是否具体的数据库表模型结构有改动
        if(!$forceReCache){
            $modelObj = null;
            CommonDB::getObjByModelName(static::$model_name, $modelObj);
            $forceReCache = CommonDB::getDBModelChangeStatus($modelObj,$keyRedisPreNot, static::$dbDir, static::$table_name);
        }

        if($needCache && !$forceReCache){
            $tableConfig = Tool::getRedis($keyRedisPre . $keyRedis, $operateRedis);
            Log::info('多语言单个数据表配置日志 --从缓存中获取到-->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . __FUNCTION__, [$tableConfig]);
            if(is_array($tableConfig) && !empty($tableConfig)) return $tableConfig;
        }
        $langConfig = static::getLangConfig($dbFileTag, (1 | 2 | 8));
        $langModel = $langConfig['models'] ?? [];// __($keyModel);
        $langModelDB = $langConfig['modelsDB'] ?? [];//当前数据库的
        $langTable = $langConfig['local'] ?? [];// __($key);
        $tableConfig = static::getTableConfig($dbFileTag, $langModel, $langModelDB, $langTable);
        // 增加或去掉字段
        static::formatTableConfig($tableConfig, $langModel, $langModelDB, $langTable);
        // 缓存
        if($needCache){
            Log::info('多语言单个数据表配置日志 --缓存数据-->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . __FUNCTION__, [$tableConfig]);
            Tool::setRedis($keyRedisPre, $keyRedis, $tableConfig, $cacheExpire , $operateRedis);
        }

        return $tableConfig;
    }

    /**
     * 获得多语言的model 或 对应数据库的
     *
     * @param string $dbFileTag 数据库多语言文件的标识
     * @param int $langType 数据库多语言文件的类型 1 总标识model的 8 当前数据库的 -- 只能一个一个的用
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public static function getModelsOrDbLang($dbFileTag = 'models', $langType = 8){
        $needCache = config('public.langDBFieldCacheOpen');
        $cacheExpire = config('public.langDBFieldCacheExpire');

        // 判断数据库结构是否有改动
        $keyRedisPreNot = '';
        // $forceReCache 是否强制重新缓存 true:强制缓存;有变动[不可用缓存，重新缓存] ; false:不强制缓存;无变动[缓存有效可用]
        $forceReCache = CommonDB::getDBChangeStatus($keyRedisPreNot, static::$dbDir, static::$table_name);

        // 判断是否具体的数据库表模型结构有改动
        if(!$forceReCache){
            $modelObj = null;
            CommonDB::getObjByModelName(static::$model_name, $modelObj);
            $forceReCache = CommonDB::getDBModelChangeStatus($modelObj, $keyRedisPreNot, static::$dbDir, static::$table_name);
        }

        if(($langType & 8) == 8){
            $keyRedisPre = Tool::getProjectKey(1 | 2 | 4, ':', ':') . 'lang:' . $dbFileTag  ;
            $keyRedis = ':' . static::$dbDir ;
            $operateRedis = 1;

            // 获得缓存
            if($needCache && !$forceReCache){
                $langModelDB = Tool::getRedis($keyRedisPre . $keyRedis, $operateRedis);
                Log::info('多语言单个数据库配置日志 --从缓存中获取到-->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . __FUNCTION__, [$langModelDB]);
                if(is_array($langModelDB) && !empty($langModelDB)) return $langModelDB;
            }

            $langConfig = static::getLangConfig($dbFileTag, 8);// (1 | 8)
            // $langModel = $langConfig['models'] ?? [];// __($keyModel);
            $langModelDB = $langConfig['modelsDB'] ?? [];//当前数据库的
            // $langTable = $langConfig['local'] ?? [];// __($key);

            // 缓存
            if($needCache){
                Log::info('多语言单个数据库配置日志 --缓存数据-->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . __FUNCTION__, [$langModelDB]);
                Tool::setRedis($keyRedisPre, $keyRedis, $langModelDB, $cacheExpire , $operateRedis);
            }

            return $langModelDB;
        }else{
            $keyRedisPre = Tool::getProjectKey(1 | 2 | 4, ':', ':') . 'lang'  ;
            $keyRedis = ':' . $dbFileTag ;
            $operateRedis = 1;

            // 获得缓存
            if($needCache && !$forceReCache){
                $langModel = Tool::getRedis($keyRedisPre . $keyRedis, $operateRedis);
                Log::info('多语言单个数据库配置日志 --从缓存中获取到-->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . __FUNCTION__, [$langModel]);
                if(is_array($langModel) && !empty($langModel)) return $langModel;
            }

            $langConfig = static::getLangConfig($dbFileTag, 1);// (1 | 8)
            $langModel = $langConfig['models'] ?? [];// __($keyModel);
            // $langModelDB = $langConfig['modelsDB'] ?? [];//当前数据库的
            // $langTable = $langConfig['local'] ?? [];// __($key);

            // 缓存
            if($needCache){
                Log::info('多语言单个数据库配置日志 --缓存数据-->'  . date('Y-m-d H:i:s') . __CLASS__ . '->' . __FUNCTION__, [$langModel]);
                Tool::setRedis($keyRedisPre, $keyRedis, $langModel, $cacheExpire , $operateRedis);
            }
            return $langModel;
        }
    }

    /**
     * 获得多语言配置内容-- 指定表优先
     *
     * @param int $langType 语言配置类型 1 总的model 2 当前表的 4指定表的;8 当前数据库的
     * @param string $table_name 指定表--优先
     * @return array ['models' => '总的model', 'modelsDB' => '当前数据库的', 'local' => ' 2 当前表的 或 4指定表的']
     * @author zouyan(305463219@qq.com)
     */
    public static function getLangConfig($dbFileTag = 'models', $langType = 0, $table_name = ''){
        // $dbFileTag = static::$dbFileTag;// 'models';
        $dbDir = static::$dbDir;// 'RunBuy';
        if(empty($table_name)){
            if(($langType & 2) == 2) $table_name = static::$table_name;// 'brands';
        }
        $keyModel = $dbFileTag;
        $keyModelDB = $dbFileTag . $dbDir;//modelsRunBuy
        $key = $dbFileTag . $dbDir . $table_name; // 'modelsRunBuyfailed_jobs.fields'
        // $langModel = __($keyModel);
        // $langTable = __($key);
        $langConfig = [];
        if(($langType & 1) == 1){
            $models = __($keyModel);
            if(!is_array($models)) $models = [];
            $langConfig['models'] = $models;
        }
        if(($langType & 8) == 8){
            $modelsDB = __($keyModelDB);
            if(!is_array($modelsDB)) $modelsDB = [];
            $langConfig['modelsDB'] = $modelsDB;
        }
        if(($langType & 2) == 2 || ($langType & 4) == 4){
            $local = __($key);
            if(!is_array($local)) $local = [];
            $langConfig['local'] = $local;
        }
        return $langConfig;
    }



    /**
     * 单个表的特殊处理--单个数据表配置数组
     * // 必填时的情况：输入框：**不能为空。 下拉框、单选框、复选框： 请选择***
     * @param array $tableConfig 单个数据表配置数组
     * @param array $langModel 多语言公用配置数组
     * @param array $langModelDB 多语言单个数据库配置数组
     * @param array $langTable 多语言单个数据表配置数组
     * @return array  单个数据表配置数组
     * @author zouyan(305463219@qq.com)
     */
    public static function formatTableConfig(&$tableConfig = [], $langModel = [], $langModelDB = [], $langTable = []){
        // 把$langTable 多语言单个数据表配置数组 中，除了fields下标合并进来。
        $langTableTem = $langTable;
        if(isset($langTableTem['fields'])) unset($langTableTem['fields']);

        foreach($tableConfig as $k => $v){
            if(in_array($k, ['fields'])) continue;
            if(!isset($langTableTem[$k])) continue;
            if(is_array($v) && is_array($langTableTem[$k])){
                $tableConfig[$k] = array_merge($tableConfig[$k], $langTableTem[$k]);
            }else{
                $tableConfig[$k] = $langTableTem[$k];
            }
            unset($langTableTem[$k]);
        }

        if(!empty($langTableTem)) $tableConfig = array_merge($tableConfig, $langTableTem);

        // 其它下标的关键词替换
        foreach($tableConfig as $k => $v){
            // if(in_array($k, ['table_name', 'fields'])) continue;
            // 替换 $langModel 多语言公用配置数组
            Tool::arrReplaceKV($tableConfig[$k], $langModel, '{', '}');
            // 替换 $langModelDB 多语言公用配置数组
            Tool::arrReplaceKV($tableConfig[$k], $langModelDB, '{', '}');
        }

        // 需要从父的去掉的字段  -- 一维数组
        $delFields = static::$delFields;// [];

        static::formatTableConfigSelf($tableConfig, $langModel, $langModelDB, $langTable);

        $fields = $tableConfig['fields'] ?? [];
        foreach($delFields as $field){
            if(isset($fields[$field])) unset($fields[$field]);
        }


        // 字段处理
        foreach($fields as $field => $fieldConfig){
            // 如果名称为空，则用lang配置的名称
            $field_name = $fieldConfig['field_name'] ?? '';
            if(is_string($field_name) && strlen($field_name) <= 0){
                $field_name = $langTable['fields'][$field]['field_name'] ?? '';
            }
            // 替换 $langModel 多语言公用配置数组
            Tool::strReplaceKV($field_name, $langModel, '{', '}');
            // 替换 $langModelDB 多语言公用配置数组
            Tool::strReplaceKV($field_name, $langModelDB, '{', '}');
            $fields[$field]['field_name'] = $field_name;

            // 如果 message为空，则用lang对应的配置
            $tem_valiDateParam_twoarr = $fieldConfig['valiDateParam'] ?? [];
            $isMulti = Tool::isMultiArr($tem_valiDateParam_twoarr, true);
            $messageArr = $langTable['fields'][$field]['message'] ?? '';
            // 转为数组
            if(is_string($messageArr)) $messageArr = [$messageArr];

            foreach($tem_valiDateParam_twoarr as $tem_k => $tem_valiDateParam){

                $message = $tem_valiDateParam['message'] ?? '';
                if(is_string($message) && strlen($message) <= 0){
                    $message = $messageArr[$tem_k] ?? '';// $langTable['fields'][$field]['message'] ?? '';
                }

                // 替换操作

                // 枚举 {enum}
                $enum =  $langTable['fields'][$field]['enum'] ?? '';
                $message = str_replace('{enum}', $enum, $message);

                // {min}
//                if(isset($fieldConfig['valiDateParam']['min'])){
//                    $min = $fieldConfig['valiDateParam']['min'];
//                    $message = str_replace('{min}', $min, $message);
//                }
                if(isset($tem_valiDateParam['min'])){
                    $min = $tem_valiDateParam['min'];
                    $message = str_replace('{min}', $min, $message);
                }
                // {max}
//                if(isset($fieldConfig['valiDateParam']['max'])){
//                    $max = $fieldConfig['valiDateParam']['max'];
//                    $message = str_replace('{max}', $max, $message);
//                }
                if(isset($tem_valiDateParam['max'])){
                    $max = $tem_valiDateParam['max'];
                    $message = str_replace('{max}', $max, $message);
                }
                // {fieldName}
                $message = str_replace('{fieldName}', $field_name, $message);

                // 替换 $langModel 多语言公用配置数组
                Tool::strReplaceKV($message, $langModel, '{', '}');
                // 替换 $langModelDB 多语言公用配置数组
                Tool::strReplaceKV($message, $langModelDB, '{', '}');

                // $fields[$field]['valiDateParam']['message'] = $message;
                $tem_valiDateParam_twoarr[$tem_k]['message'] = $message;
            }
            if($isMulti){
                $fields[$field]['valiDateParam'] = $tem_valiDateParam_twoarr;
            }else{
                $fields[$field]['valiDateParam'] = $tem_valiDateParam_twoarr[0] ?? [];
            }
        }

        $tableConfig['fields'] = $fields;


        return $tableConfig;
    }

    /**
     * 单个表的特殊处理--单个数据表配置数组 ---如果还有特别的需求，可以重写此方法
     *
     * @param array $tableConfig 单个数据表配置数组
     * @param array $langModel 多语言公用配置数组
     * @param array $langModelDB 多语言单个数据库配置数组
     * @param array $langTable 多语言单个数据表配置数组
     * @return array  单个数据表配置数组
     * @author zouyan(305463219@qq.com)
     */
    public static function formatTableConfigSelf(&$tableConfig = [], $langModel = [], $langModelDB = [], $langTable = []){

        // 需要添加的其它段段信息
        $fields = $tableConfig['fields'] ?? [];
        $otherFields = static::addFields($langModel, $langModelDB, $langTable);

        if(!empty($otherFields)){

            $fields = array_merge($fields, $otherFields);
        }
        $tableConfig['fields'] = $fields;

        // 其它操作....

        return $tableConfig;
    }
//#################子类##可能需要重写的方法######################################
    /**
     * 获得验证规则---字类重写此方法
     *
     * @param string $dbFileTag 数据表配置文件的标识
     * @param array $langModel 多语言公用配置数组
     * @param array $langModelDB 多语言单个数据库配置数组
     * @param array $langTable 多语言单个数据表配置数组
     * @return array  单个数据表配置数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getTableConfig($dbFileTag = 'models', $langModel = [], $langModelDB = [], $langTable = []){
        $tableConfig = [];
        // 如果用父类的，则在此指定父类
        // $tableConfig = brands::getVerifyRuleArr($dbFileTag);
        return $tableConfig;
    }

    /**
     * 单个数据表配置数组中，需要新加的字段配置---重写此方法
     *
     * @param array $langModel 多语言公用配置数组
     * @param array $langModelDB 多语言单个数据库配置数组
     * @param array $langTable 多语言单个数据表配置数组
     * @return array  单个数据表配置数组中需要新加的字段配置-- field下标的值--数组
     * @author zouyan(305463219@qq.com)
     */
    public static function addFields($langModel = [], $langModelDB = [], $langTable = []){
        $addFields = [

        ];
        return $addFields;

    }
}
