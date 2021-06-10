<?php

namespace App\Business\DB;

use App\Business\BaseBusiness;
use App\Business\DB\QualityControl\ResourceDBBusiness;
use App\Business\DB\QualityControl\StaffDBBusiness;
use App\Business\DB\QualityControl\StaffHistoryDBBusiness;
use App\Services\DB\CommonDB;
use App\Services\Request\CommonRequest;
use App\Services\Response\Data\CommonAPIFromDBBusiness;
use App\Services\Tool;
use Illuminate\Support\Facades\DB;


/**
 *
 */
class BaseDBBusiness extends BaseBusiness
{
    public static $database_model_dir_name = '';// 对应的数据库模型目录名称
    public static $model_name = '';// 相对于Models的数据模型名称;在子类中定义，使用时用static::,不用self::
    public static $table_name = '';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];

    /**
     * 获得对象自己，相当于 $this
     * @param string $model_name 要实例化的，为空，则实例化当前对象 "App\\Business\\DB\\" . $modelName . 'DBBusiness'; 中的  $modelName值
     * @return object
     */
    public static function thisObj($model_name = ''){
        if(empty($model_name)) $model_name = static::$model_name;
        return CommonAPIFromDBBusiness::getBusinessDBObjByModelName($model_name);
    }

    /**
     * 获得模型对象
     *
     * @param array  $dataParams 新加的数据
     * @return object 对象
     * @author zouyan(305463219@qq.com)
     */
    public static function getModelObj(&$modelObj = null){
        CommonDB::getObjByModelName(static::$model_name, $modelObj);
        return $modelObj;
    }

    /**
     * 获得模型对象-- 通过名称
     *
     * @param string  $model_name 模型名称
     * @param array  $dataParams 新加的数据
     * @return object 对象
     * @author zouyan(305463219@qq.com)
     */
    public static function getModelObjByName($model_name, &$modelObj = null){
        CommonDB::getObjByModelName($model_name, $modelObj);
        return $modelObj;
    }

    /**
     * 获得模型的属性
     *
     * @param string  $attrName 属性名称[静态或动态]--注意静态时不要加$ ,与动态属性一样 如 attrTest
     * @param int $isStatic 是否静态属性 0：动态属性 1 静态属性
     * @return mixed 对象
     * @author zouyan(305463219@qq.com)
     */
    public static function getAttr($attrName, $isStatic = 0, &$modelObj = null){
        static::getModelObj($modelObj );
        // return CommonDB::getAttr($modelObj, $attrName, $isStatic);
        return Tool::getAttr($modelObj, $attrName, $isStatic);
    }

    /**
     * 获得模型的自有属性
     *    // 自有属性
     *  // 0：都没有；
     *  // 1：有历史表 ***_history;
     *  // 2：有操作员工id 字段 operate_staff_id
     *  // 4：有操作员工历史id 字段 operate_staff_id_history
     *  // 8：有操作日期字段 created_at timestamp
     *  // 16：有更新日期字段 updated_at  timestamp
     * @return int 自有属性
     * @author zouyan(305463219@qq.com)
     */
    public static function getOwnProperty( &$modelObj = null){
        return static::getAttr('ownProperty', 1,$modelObj);
    }

    /**
     * 获得当前对象自有属性值
     *
     * @return int  当前对象自有属性值
     * @author zouyan(305463219@qq.com)
     *
     */
    public static function getOwnPropertyVal(){
        $modelProObj = null;
        $ownProperty = static::getOwnProperty($modelProObj);
        $modelProObj = null;// 释放
        return $ownProperty;
    }

    /**
     *  使用 list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId())
     *  获得当前对象
     *   $ownProperty  自有属性值
     *   $temNeedStaffIdOrHistoryId 是否需要获取操作员工id和历史id true:需要获取； false:不需要获取
     *
     * @return array [$ownProperty, boolean 返回值 true:需要获取； false:不需要获取]
     * @author zouyan(305463219@qq.com)
     *
     */
    public static function getNeedStaffIdOrHistoryId(){
        // 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        $temNeedStaffIdOrHistoryId = false;
        $ownProperty = static::getOwnPropertyVal();
        // 2：   有操作员工id 字段 operate_staff_id
        // 4：或 有操作员工历史id 字段 operate_staff_id_history
        if( ($ownProperty & 2) == 2 || ($ownProperty & 4) == 4) $temNeedStaffIdOrHistoryId = true;
        return [$ownProperty, $temNeedStaffIdOrHistoryId];
    }

    /**
     * 调用模型方法
     *
     * @param string $methodName 方法名称
     * @param array $params 参数数组 没有参数:[]  或 [第一个参数, 第二个参数,....];
     * @return mixed 返回值
     * @author zouyan(305463219@qq.com)
     *
        // 获得表名称
        $tableName = LrChinaCityBusiness::exeMethod($request, $this, 'getTable', []);
     */
    public static function exeMethod($methodName, $params = [], &$modelObj = null){
        static::getModelObj($modelObj );
        // return CommonDB::exeMethod($modelObj, $methodName, $params);
        return Tool::exeMethod($modelObj, $methodName, $params);
    }

    /**
     * 新加
     *
     * @param array  $dataParams 新加的数据
     * @return object 对象
     * @author zouyan(305463219@qq.com)
     */
    public static function create($dataParams = [], &$modelObj = null)
    {
        // 获得对象
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );
        return CommonDB::create($modelObj, $dataParams);
    }

    /**
     * 查找记录,或创建新记录[没有找到] - $searchConditon +  $updateFields 的字段,
     *
     * @param obj $mainObj 主表对象
     * @param array $searchConditon 查询字段[一维数组]
     * @param array $updateFields 表中还需要保存的记录 [一维数组] -- 新建表时会用
     * @return obj $mainObj 表对象[一维]
     * @author zouyan(305463219@qq.com)
     */
    public static function firstOrCreate(&$mainObj, $searchConditon, $updateFields)
    {
        // 主表
        static::getModelObj($mainObj );

        CommonDB::firstOrCreate($mainObj, $searchConditon, $updateFields );
        return  $mainObj;
    }

    /**
     * 已存在则更新，否则创建新模型--持久化模型，所以无需调用 save()-- $searchConditon +  $updateFields 的字段,
     *
     * @param obj $mainObj 主表对象
     * @param array $searchConditon 查询字段[一维数组]
     * @param array $updateFields 表中还需要保存的记录 [一维数组] -- 新建表时会用
     * @param boolean $isCacheDataByCache 读取缓存数据时,是否从缓存优先 true:有缓存优先使用缓存[默认]；false：不使用缓存,强制从数据表读取
     * @return obj $mainObj 表对象[一维]
     * @author zouyan(305463219@qq.com)
     */
    public static function updateOrCreate(&$mainObj, $searchConditon, $updateFields, $isCacheDataByCache = true)
    {
        // 主表
        static::getModelObj($mainObj );

        CommonDB::updateOrCreate($mainObj, $searchConditon, $updateFields, $isCacheDataByCache);
        return  $mainObj;
    }

    /**
     * 批量新加接口-data只能返回成功true:失败:false
     *
     * @param array $dataParams 一维或二维数组;只返回true:成功;false：失败
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static function addBath($dataParams, &$modelObj = null)
    {
        // 获得对象
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );

        return CommonDB::insertData($modelObj, $dataParams);

    }

    /**
     * 批量新加接口-返回新创建的对象 一维[单条]或二维[多条]
     *
     * @param array $dataParams 一维或二维数组;只返回true:成功;false：失败
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static function addBathObj($dataParams, &$modelObj = null)
    {
        // 获得对象
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );

        return CommonDB::eachAddData($modelObj, $dataParams, 2);

    }


    /**
     * 批量新加接口-data只能返回成功true:失败:false--里面也是一条一条加入的
     *
     * @param array $dataParams 需要新的数据-- 二维数组
     * @param string $primaryKey 默认自增列被命名为 id，如果你想要从其他“序列”获取ID
     * @return array 返回新加的主键值-一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static  function addBathByPrimaryKey($dataParams, $primaryKey = 'id', &$modelObj = null)
    {
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );
        $newIds = CommonDB::insertGetId($modelObj, $dataParams, $primaryKey);
        return $newIds;
    }

    /**
     * 修改接口--按条件修改
     *
     * @param array $dataParams 字段数组/json字符 --一维数组
     * @param array $queryParams 条件数组/json字符
     * @param boolean $isCacheDataByCache 读取缓存数据时,是否从缓存优先 true:有缓存优先使用缓存[默认]；false：不使用缓存,强制从数据表读取
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static  function save($dataParams, $queryParams, &$modelObj = null, $isCacheDataByCache = true)
    {
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );
        return CommonDB::updateQuery($modelObj, $dataParams, $queryParams, $isCacheDataByCache);
    }

    /**
     * 修改接口--通过主键值数组修改
     *
     * @param array $dataParams 字段数组/json字符 --一维数组
     * @param array $ids 主键值数组数组
     * @param array $queryParamsSave 其它条件
     * @param string $primaryName 主键字段名称 ；默认 'id'
     * @param boolean $isCacheDataByCache 读取缓存数据时,是否从缓存优先 true:有缓存优先使用缓存[默认]；false：不使用缓存,强制从数据表读取
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static  function saveByIds($dataParams, $ids = [], $queryParamsSave = [], $primaryName = 'id', &$modelObj = null, $isCacheDataByCache = true)
    {
        // $queryParamsSave = [];
        $queryParamsSave['whereIn'][$primaryName] = $ids;

        return static::save($dataParams, $queryParamsSave, $modelObj, $isCacheDataByCache);
    }

    /**
     * 批量修改设置
     *
     * @param array $dataParams 主键及要修改的字段值 二维数组 数组/json字符
     * @param string $primaryKey 主键字段,默认为id
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static  function saveBathById($dataParams, $primaryKey = 'id')
    {
        return CommonDB::batchSave(static::$model_name, $dataParams, $primaryKey);
    }

    /**
     * 通过id修改接口
     *
     * @param array $dataParams 字段数组/json字符 一维数组
     * @param string $id 主键id
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public  static function saveById($dataParams, $id, &$modelObj = null)
    {
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
         static::getModelObj($modelObj );
         return CommonDB::saveById($modelObj, $dataParams, $id);
    }

    /**
     * 通过列表更新数据【新加 id = 0/ 修改 id > 0】
     * @param array  $fieldRelations 必须 查询用 要执行的表字段与主表的值  ['staff_id' => 'id'// 相系表的字段 =》 在主表中的值: 一维数组或多个，分隔的字符]
     * @param array  $newFieldVals  如果新加时，需要默认加入的字段及值  -- 一维数组  ['字段' => '值'，...]
     * @param array  $newDataList 需要操作的记录  一维或二维数组 , 为 空：清空； ---有主键，则修改， 没有主键字段，则新加【此时会合并 $newFieldVals 的数据】
     * @param int  $isModify 是否先读取所有的  true/1:读取所有的【默认】  false/0: 不读取 -- 确认是只是新加可以用这个
     * @param int $operate_staff_id 操作人id
     * @param int $operate_staff_id_history 操作人历史id
     * @param string  $id_field 主键字段名称 id--默认
     * @param int  $company_id 企业id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @param array  $extendParams 其它扩展参数
     *  $extendParams = [
     *      'del' => [// 删除用
     *          'del_type' => 1,// 删除方式  1：批量删除 static::deleteByIds($dbIds) [默认] 2: 多次单个删除：static::delById($company_id, $tem_id, $operate_staff_id, $modifAddOprate, $extendParams);
     *                          4: 不做删除操作【也就是只是添加或修改】
     *          'extend_params' => [],// 删除的扩展参数 一维数组  del_type = 2时：用 -- 具体可有的参数：请看 delById 方法的扩展参数
     *      ],
     *      'sqlParams' => [// 其它sql条件[拼接/覆盖式],下面是常用的，其它的也可以---查询用
     *          // '如果有值，则替换where' --拼接
     *          'where' => [// -- 可填 如 默认条件 'type_id' => 5  'admin_type' => $user_info['admin_type'],'staff_id' =>  $user_info['id']
     *          ['type_id', 5],
     *          ],
     *          'select' => '如果有值，则替换select',// --覆盖
     *          'orderBy' => '如果有值，则替换orderBy',//--覆盖
     *          'whereIn' => '如果有值，则替换whereIn',// --拼接
     *          'whereNotIn' => '如果有值，则替换whereNotIn',//  --拼接
     *          'whereBetween' => '如果有值，则替换whereBetween',//  --拼接
     *          'whereNotBetween' => '如果有值，则替换whereNotBetween',//  --拼接
     *      ],
     *  ];
     * @return  array 最终的id 值数组 一维
     * @author zouyan(305463219@qq.com)
     */
    public static function updateByDataList($fieldRelations = [], $newFieldVals = [], $newDataList = [], $isModify = 1
        , $operate_staff_id = 0, $operate_staff_id_history = 0
        , $id_field = 'id', $company_id = 0, $modifAddOprate = 0, $extendParams = []){

//        DB::beginTransaction();
//        try {
//            DB::commit();
//        } catch ( \Exception $e) {
//            DB::rollBack();
//            throws($e->getMessage());
//            // throws($e->getMessage());
//        }
        return CommonDB::doTransactionFun(function() use(&$fieldRelations, &$newFieldVals, &$newDataList, &$isModify
            , &$operate_staff_id, &$operate_staff_id_history
            , &$id_field, &$company_id, &$modifAddOprate, &$extendParams){

            $lastIds = [];// 最终的id 值数组
            if(empty($fieldRelations)) return $lastIds;
            Tool::isMultiArr($newDataList, true);

            $dataList = [];
            $dbIds = [];
            if($isModify){// 是修改
                // 获得所有的方法标准
                $queryParams = [
                    'where' => [
//                ['company_id', $organize_id],
                        //    ['ability_join_item_id', $id],
//                ['teacher_status',1],
                    ],
                    // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
                ];

                // 对数据进行拼接处理
                if(isset($extendParams['sqlParams'])){
                    $sqlParams = $extendParams['sqlParams'] ?? [];
                    foreach($sqlParams as $tKey => $tVals){
                        if(isset($queryParams[$tKey]) && in_array($tKey, ['where',  'whereIn', 'whereNotIn', 'whereBetween', 'whereNotBetween'])){// 'select', 'orderBy',
                            $queryParams[$tKey] = array_merge($queryParams[$tKey], $tVals);
                        }else{
                            $queryParams[$tKey] = $tVals;
                        }

                    }
                    unset($extendParams['sqlParams']);
                }
                foreach($fieldRelations as $field => $field_vals){
                    Tool::appendParamQuery($queryParams, $field_vals, $field, ['']);
                    // 加入到新加入中
                    if(!is_array($field_vals) && !isset($newFieldVals[$field])) $newFieldVals[$field] = $field_vals;
                }
                $dataListObj = static::getAllList($queryParams, []);
                $dataList = $dataListObj->toArray();
                if(!empty($dataList)) $dbIds = array_values(array_unique(array_column($dataList, $id_field)));
            }
            //***************************************************************
            if(!empty($newDataList)){
//                $appendArr = [
//                    'operate_staff_id' => $operate_staff_id,
//                    'operate_staff_id_history' => $operate_staff_id_history,
//                ];
                // $ownProperty  自有属性值;
                // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
                list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
                // 新加时
                //                    if(!$isModify){
                //                        $appendArr = array_merge($appendArr, [
                //                            'ability_join_item_id' => $id,
                //                        ]);
                //                    }
                // Tool::arrAppendKeys($ability_join_items, $appendArr);
                foreach($newDataList as $k => $info){
                    $operate_id = $info[$id_field] ?? 0;
                    if(isset($info[$id_field])) unset($info[$id_field]);

                    // 不存在的id
                    if( $operate_id > 0 && !empty($dbIds) && !in_array($operate_id, $dbIds))  $operate_id = 0;

//                    Tool::arrAppendKeys($info, $appendArr);
                    if($operate_id > 0 ){
                        // 加入操作人员信息
                        if($temNeedStaffIdOrHistoryId && $modifAddOprate) static::addOprate($info, $operate_staff_id,$operate_staff_id_history, 1);
                    }else{
                        // 加入操作人员信息
                        if($temNeedStaffIdOrHistoryId) static::addOprate($info, $operate_staff_id,$operate_staff_id_history, 1);
                    }
                    if($operate_id <= 0){
                        if(!empty($newFieldVals)) Tool::arrAppendKeys($info, $newFieldVals);
                    }
                    static::replaceById($info, $company_id, $operate_id, $operate_staff_id, $modifAddOprate);
                    array_push($lastIds, $operate_id);
                    // 移除当前的id
                    if($operate_id){
                        $recordUncode = array_search($operate_id, $dbIds);
                        if($recordUncode !== false) unset($dbIds[$recordUncode]);// 存在，则移除
                    }
                }
            }
            if($isModify && !empty($dbIds)) {// 是修改 且不为空
                $del_type = $extendParams['del']['del_type'] ?? 1;
                $del_extend_params = $extendParams['del']['extend_params'] ?? [];
                // 删除记录
                if($del_type == 2) {
//                    foreach($dbIds as $tem_id){
//                        static::delById($company_id, $tem_id, $operate_staff_id, $modifAddOprate, $del_extend_params);
//                    }
                    // 已经是可以批量删除了
                    static::delById($company_id, $dbIds, $operate_staff_id, $modifAddOprate, $del_extend_params);
                }else if($del_type == 4){// 4: 不做删除操作【也就是只是添加或修改】

                }else{
                    $modelObj = null;
                    static::deleteByIds($dbIds, $modelObj, $id_field);
                }
            }
            //***********************************************
            return $lastIds;
        });
    }

    /**
     * 根据条件删除接口
     *
     * @param int $company_id 公司id
     * @param string $Model_name model名称
     * @param array $queryParams 条件数组/json字符
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static  function del($queryParams, &$modelObj = null)
    {
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );
        return CommonDB::del($modelObj, $queryParams);
    }

    /**
     * 根据id删除接口
     *
     * @param int $ids 删除记录 id,单条记录或 多条[,号分隔]
     * @param object $modelObj 数据模型对象
     * @param string $fieldName 查询的字段名--表中的
     * @param string $valsSeparator 如果是多值字符串，多个值的分隔符;默认逗号 ,
     * @param array $excludeVals 需要除外的参数值--不加入查询条件 [0, '0', ''] --默认；  ['']
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static  function deleteByIds($ids, &$modelObj = null, $fieldName = 'id', $valsSeparator = ',', $excludeVals = [0, '0', ''])
    {
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );
        return CommonDB::delByIds($modelObj, $ids, $fieldName, $valsSeparator, $excludeVals);
    }

    /**
     * 根据单条id删除--可以重写此方法-- 作废，用下面的  delById 方法
     *
     * @param int  $company_id 企业id
     * @param int $id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @param array $extendParams 其它参数--扩展用参数
     * @return  int 记录id值
     * @author zouyan(305463219@qq.com)
     */
//    public static function delById($company_id, $id, $operate_staff_id = 0, $modifAddOprate = 0, $extendParams = []){
//
//        if(strlen($id) <= 0){
//            throws('操作记录标识不能为空！');
//        }
////        $info = static::getInfo($id);
////        if(empty($info)) throws('记录不存在');
//        // if($info['status'] != 1) throws('当前记录非【待开始】状态，不可删除！');
////        DB::beginTransaction();
////        try {
////            // 删除主记录
////            static::deleteByIds($id);
////            // 其它操作
////            DB::commit();
////        } catch ( \Exception $e) {
////            DB::rollBack();
////            throws($e->getMessage());
////            // throws($e->getMessage());
////        }
//        CommonDB::doTransactionFun(function() use(&$id){
//            // 删除主记录
//            static::deleteByIds($id);
//        });
//        return $id;
//    }

    /**
     * 根据id删除--可批量删除-可以重写此方法
     * @param int  $company_id 企业id
     * @param string $id id 多个用，号分隔  或 一维数组
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @param array $extendParams 其它参数--扩展用参数
     * [
     *      'primary_field' => 'id',// 主键字段 默认：id
     *      'sqlParams' => [// 其它sql条件[拼接/覆盖式],下面是常用的，其它的也可以---查询用
     *          // '如果有值，则替换where' --拼接
     *          'where' => [// -- 可填 如 默认条件 'type_id' => 5  'admin_type' => $user_info['admin_type'],'staff_id' =>  $user_info['id']
     *          ['type_id', 5],
     *          ],
     *          'select' => '如果有值，则替换select',// --覆盖
     *          'orderBy' => '如果有值，则替换orderBy',//--覆盖
     *          'whereIn' => '如果有值，则替换whereIn',// --拼接
     *          'whereNotIn' => '如果有值，则替换whereNotIn',//  --拼接
     *          'whereBetween' => '如果有值，则替换whereBetween',//  --拼接
     *          'whereNotBetween' => '如果有值，则替换whereNotBetween',//  --拼接
     *      ],
     * ]
     * @return  int 记录id值
     * @author zouyan(305463219@qq.com)
     */
    public static function delById($company_id, $id, $operate_staff_id = 0, $modifAddOprate = 0, $extendParams = []){

        if(is_string($id) && strlen($id) <= 0){
            throws('操作记录标识不能为空！');
        }
        if(empty($id)){
            throws('操作记录标识不能为空！');
        }

//        $info = static::getInfo($id);
//        if(empty($info)) throws('记录不存在');
//        $staff_id = $info['staff_id'];
//        $dataListObj = null;
        $dataListArr = [];
        $abilityIds = [];
        $primary_field = $extendParams['primary_field'] ?? 'id';
        // 获得需要删除的数据
        $queryParams = [
            'where' => [
//                ['company_id', $organize_id],
                //  ['admin_type', $admin_type],
//                ['teacher_status',1],
            ],
            // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
        ];
        // 对数据进行拼接处理
        if(isset($extendParams['sqlParams'])){
            $sqlParams = $extendParams['sqlParams'] ?? [];
            foreach($sqlParams as $tKey => $tVals){
                if(isset($queryParams[$tKey]) && in_array($tKey, ['where',  'whereIn', 'whereNotIn', 'whereBetween', 'whereNotBetween'])){// 'select', 'orderBy',
                    $queryParams[$tKey] = array_merge($queryParams[$tKey], $tVals);
                }else{
                    $queryParams[$tKey] = $tVals;
                }

            }
            unset($extendParams['sqlParams']);
        }
        Tool::appendParamQuery($queryParams, $id, $primary_field, [0, '0', ''], ',', false);

//        $dataListObj = static::getAllList($queryParams, []);
//        // $dataListObj = static::getListByIds($id);
//
//        $dataListArr = $dataListObj->toArray();
//        if(empty($dataListArr)) throws('没有需要删除的数据');
//        // 用户删除要用到的
//        $abilityIds = array_values(array_unique(array_column($dataListArr,'ability_id')));

//        DB::beginTransaction();
//        try {
//            DB::commit();
//        } catch ( \Exception $e) {
//            DB::rollBack();
//            throws($e->getMessage());
//            // throws($e->getMessage());
//        }
        CommonDB::doTransactionFun(function() use(&$queryParams){

            // 删除主记录
            static::del($queryParams);
            // static::deleteByIds($id);
            // 如果是删除，则减少报名数量
//            foreach($abilityIds as $ability_id){
//                if($ability_id > 0){
//                    $queryParams = [
//                        'where' => [
//                            ['id', $ability_id],
//                        ],
//                        // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
//                    ];
//                    AbilitysDBBusiness::saveDecIncByQuery('join_num', 1,  'dec', $queryParams, []);
//                }
//            }
        });
        return $id;
    }

    /**
     * 根据字段=》值数组；获得数据[格式化后的数据]
     *
     * @param int $operate_type 操作为型  1 所有[默认] all 二维【数组】 2 ：指定返回数量 limit_num 1：一维【对象】 ，>1 二维【数组】 4：只返回一条 one_num 一维【对象】， 8：获得数量值
     * @param int $page_size 2时返回的数量
     * @param array $fieldValParams
     *  $fieldValParams = [
     *      'ability_join_id' => [// 格式一
     *          'vals' => "字段值[可以是字符'多个逗号分隔'或一维数组] ",// -- 此种格式，必须指定此下标【值下标】
     *          'excludeVals' => "过滤掉的值 默认[''];// [0, '0', '']",
     *          'valsSeparator' => ',' 如果是多值字符串，多个值的分隔符;默认逗号 ,
     *          'hasInIsMerge=>  false 如果In条件有值时  true:合并；false:用新值--覆盖 --默认
     *      ],// 格式二
     *      'id' =>  "字段值[可以是字符'多个逗号分隔'或一维数组]"
     *   ];
     * @param boolean $fieldEmptyQuery 如果参数字段值都为空时，是否还查询数据 true:查询 ；false:不查
     * @param mixed $relations 关系
     * @param array $extParams 其它扩展参数，
     *    $extParams = [
     *       'page' = 1,// 当前页号--可无此下标--仅在$operate_type=2时使用
     *       // 'useQueryParams' => '是否用来拼接查询条件，true:用[默认];false：不用'
     *        'sqlParams' => [// 其它sql条件[拼接/覆盖式],下面是常用的，其它的也可以
     *            // '如果有值，则替换where' --拼接
     *           'where' => [// -- 可填 如 默认条件 'type_id' => 5  'admin_type' => $user_info['admin_type'],'staff_id' =>  $user_info['id']
     *               ['type_id', 5],
     *           ],
     *           'select' => '如果有值，则替换select'--覆盖
     *           'orderBy' => '如果有值，则替换orderBy'--覆盖
     *           'whereIn' => '如果有值，则替换whereIn' --拼接
     *           'whereNotIn' => '如果有值，则替换whereNotIn' --拼接
     *           'whereBetween' => '如果有值，则替换whereBetween' --拼接
     *           'whereNotBetween' => '如果有值，则替换whereNotBetween' --拼接
     *       ],
     *       'handleKeyArr'=> [],// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     *       'relationFormatConfigs'=> [],// 相关表数组格式化配置 具体格式请看  formatRelationList方法的参数
     *       'listHandleKeyArr'=> [],// 一维数组，对列表二维数数据【批量】需要处理的标记--与 handleKeyArr 功能相同， 重写 handleRelationDataFormat 方法实现
     *       'infoHandleKeyArr'=> [],// 一维数组，对详情一维数数据【单个】需要处理的标记 -- 重写 infoRelationFormatExtend 方法  temDataList[表间关系才用上的，这里不用] 可传空值
     *       'formatDataUbound' => [// 格式化数据[取指下下标、排除指定下标、修改下标名称]具体参数使用说明，请参阅 Tool::formatArrUbound 方法  --为空数组代表不格式化
     *           'needNotIn' => true, // keys在数组中不存在的，false:不要，true：空值 -- 用true的时候多
     *           'includeUboundArr' => [],// 要获取的下标数组 [优先]--一维数组，可为空[ '新下标名' => '原下标名' ]  Tool::arrEqualKeyVal(['shop_id', 'shop_name', 'linkman', 'mobile'])
     *           'exceptUboundArr' => [], // 要排除的下标数组 --一维数组，可为空[ '原下标名' ,....]
     *       ]
     *   ];
     * @return  null 列表数据 一维或二维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function getDBFVFormatList($operate_type = 1, $page_size = 1, $fieldValParams = [], $fieldEmptyQuery = false, $relations = '', $extParams = [])
    {
        $dataArr = [];
        // $isEmpeyVals = true;//  查询字段值是否都为空; true:都为空，false:有值
        $hasValidVal = false;// 是否有拼接值 true: 有；false:没有
        // 获得信息
        $queryParams = [
            'where' => [
                // ['type_id', 5],
                //                //['mobile', $keyword],
            ],
            //            'select' => [
            //                'id','company_id','position_name','sort_num'
            //                //,'operate_staff_id','operate_staff_id_history'
            //                ,'created_at'
            //            ],
            // 'orderBy' => static::$orderBy,// ['sort_num'=>'desc', 'id'=>'desc'],//
        ];
        foreach($fieldValParams as $field => $valConfig){
            $excludeVals = [''];// [0, '0', ''];
            $fieldVals = [];
            // 是数组
            if(is_array($valConfig) && isset($valConfig['vals'])){
                if(isset($valConfig['excludeVals']) && is_array($valConfig['excludeVals'])){
                    $excludeVals = $valConfig['excludeVals'];
                }
                $fieldVals = $valConfig['vals'] ;
            }else{
                $fieldVals = $valConfig;
            }
            $valsSeparator = $valConfig['valsSeparator'] ?? ',';
            $hasInIsMerge = $valConfig['hasInIsMerge'] ?? false;
//            if(!empty($excludeVals))  Tool::formatOneArrVals($fieldVals, $excludeVals, $valsSeparator);
//            if( ( (is_string($fieldVals) || is_numeric($fieldVals)) && strlen($fieldVals) > 0) || (is_array($fieldVals) && !empty($fieldVals)) ) $isEmpeyVals = false;
            if(Tool::appendParamQuery($queryParams, $fieldVals, $field, $excludeVals, $valsSeparator, $hasInIsMerge)){
                $hasValidVal = true;
            }
        }
        if(!isset($extParams['useQueryParams'])) $extParams['useQueryParams'] = false;
//        $extParams = [
//            'handleKeyArr' => ['ability', 'joinItemsStandards', 'projectStandards'],//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//        ];
        // 对数据进行拼接处理
        if(isset($extParams['sqlParams'])){
            $sqlParams = $extParams['sqlParams'] ?? [];
            foreach($sqlParams as $tKey => $tVals){
                if(isset($queryParams[$tKey]) && in_array($tKey, ['where',  'whereIn', 'whereNotIn', 'whereBetween', 'whereNotBetween'])){// 'select', 'orderBy',
                    $queryParams[$tKey] = array_merge($queryParams[$tKey], $tVals);
                }else{
                    $queryParams[$tKey] = $tVals;
                }

            }
            unset($extParams['sqlParams']);
        }

        // 查询字段有值  或  查询字段无值  但是  指定 强制查询时
        // if(!$isEmpeyVals || ($isEmpeyVals && $fieldEmptyQuery)){
        if($hasValidVal || (!$hasValidVal && $fieldEmptyQuery)){
            switch ($operate_type)
            {
                case 1:
                    // $dataArr = static::getList(1, $queryParams, $relations, $extParams, $notLog)['result']['data_list'] ?? [];
                    $dataArr = static::getAllList($queryParams, $relations)->toArray();
                    break;
                case 8:// 获得数量值
                    $queryParams['count'] = 0;
                    $dataArr = static::getAllList($queryParams, []);
                    break;
                case 2:
                    $page = $extParams['page'] ?? 1;
                    if(!is_numeric($page) || $page <= 0) $page = 1;
                    $company_id = 0;// $controller->company_id;
                    // $dataArr = static::getLimitDataQuery($company_id, $page_size, $queryParams, $relations, $extParams, $notLog);
                    $dataArr = static::getDataLimit($page, $page_size, 1, $queryParams , $relations)['dataList'] ?? [];
                    // 对象转数组
                    if($page_size > 1) $dataArr = (is_object($dataArr)) ? $dataArr->toArray() :  $dataArr;// 二维数组
                    if($page_size == 1) $dataArr = $dataArr[0] ?? [];// 一维对象
                    break;
                case 4:
                    $company_id = 0;// $controller->company_id;
                    // $dataArr = static::getInfoDataByQuery($company_id, $queryParams, $relations, $extParams, $notLog);
                    $dataArr = static::getInfoByQuery(1, $queryParams, $relations);// 一维对象
                    break;
                default:
//                    $str = md5($str);
                    break;
            }
        }
        if($operate_type == 8 && !is_numeric($dataArr)) $dataArr = 0;
        return $dataArr;
    }

    /**
     * 获得model所有记录-- 查询/所有记录分批获取[推荐],也可以获得总数量
     * 注意如果想要数组，记得 ->toArray()
     *
     * @param json/array $queryParams 查询条件  有count下标则是查询数量--是否是查询总数

    //        $queryParams = [
    //            'where' => [
    //                  ['id', '&' , '16=16'],
    //                ['company_id', $company_id],
    //                //['mobile', $keyword],
    //                //['admin_type',self::$admin_type],
    //            ],
    //            'whereIn' => [
    //                'id' => $cityPids,
    //            ],
    ////            'select' => [
    ////                'id','company_id','type_name','sort_num'
    ////                //,'operate_staff_id','operate_staff_id_history'
    ////                ,'created_at'
    ////            ],
    //            // 'orderBy' => ['id'=>'desc'],
     //               'limit' => 10,//  $pagesize 第页显示数量-- 一般不用
     //               'offset' => 0,//  $offset 偏移量-- 一般不用
      //              'count' => 0,//  有count下标则是查询数量--是否是查询总数
    //        ];
     * @param json/array $relations 要查询的与其它表的关系
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @return object 数据对象
     * @author zouyan(305463219@qq.com)
     */
    public static function getAllList($queryParams, $relations, &$modelObj = null, $isOpenCache = true){
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );
        $obj = CommonDB::getAllModelDatas($modelObj, $queryParams, $relations, $isOpenCache);
//        if($reType == 2 && is_object($obj)){
//            return $obj->toArray();
//        }
        return $obj;
    }


    /**
     * 获得model记录-根据条件
     *
     * @param json/array $queryParams 查询条件  有count下标则是查询数量--是否是查询总数

    //        $queryParams = [
    //            'where' => [
    //                  ['id', '&' , '16=16'],
    //                ['company_id', $company_id],
    //                //['mobile', $keyword],
    //                //['admin_type',self::$admin_type],
    //            ],
    //            'whereIn' => [
    //                'id' => $cityPids,
    //            ],
    ////            'select' => [
    ////                'id','company_id','type_name','sort_num'
    ////                //,'operate_staff_id','operate_staff_id_history'
    ////                ,'created_at'
    ////            ],
    //            // 'orderBy' => ['id'=>'desc'],
    //               'limit' => 10,//  $pagesize 第页显示数量-- 一般不用
    //               'offset' => 0,//  $offset 偏移量-- 一般不用
    //              'count' => 0,//  有count下标则是查询数量--是否是查询总数
    //        ];
     * @param json/array $relations 要查询的与其它表的关系
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @return object 数据对象
     * @author zouyan(305463219@qq.com)
     */
    public static function getList($queryParams, $relations, &$modelObj = null, $isOpenCache = true){
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);

        static::getModelObj($modelObj );
        return CommonDB::getList($modelObj, $queryParams, $relations, $isOpenCache);
    }

    /**
     * 获得列表数据--根据ids
     *
     * @param string / array $ids  查询的id ,多个用逗号分隔, 或数组【一维】
     * @param json/array $queryParams 查询条件  有count下标则是查询数量--是否是查询总数--可以放其它条件---一般为空
    //        $queryParams = [
    //            'where' => [
    //                  ['id', '&' , '16=16'],
    //                ['company_id', $company_id],
    //                //['mobile', $keyword],
    //                //['admin_type',self::$admin_type],
    //            ],
    //            'whereIn' => [
    //                'id' => $cityPids,
    //            ],
    ////            'select' => [
    ////                'id','company_id','type_name','sort_num'
    ////                //,'operate_staff_id','operate_staff_id_history'
    ////                ,'created_at'
    ////            ],
    //            // 'orderBy' => ['id'=>'desc'],
    //               'limit' => 10,//  $pagesize 第页显示数量-- 一般不用
    //               'offset' => 0,//  $offset 偏移量-- 一般不用
    //              'count' => 0,//  有count下标则是查询数量--是否是查询总数
    //        ];
     * @param json/array $relations 要查询的与其它表的关系
     * @param string $primaryName 主键字段名称 ；默认 'id'
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @return mixed object 数据对象
     * @author zouyan(305463219@qq.com)
     */
    public static function getListByIds($ids, $queryParams = [], $relations = [], $primaryName = 'id', &$modelObj = null, $isOpenCache = true){
        if(empty($ids)) return [];
        if(is_array($ids))  $ids = implode(',', $ids);

        if (!empty($ids)) {
            if (strpos($ids, ',') === false) { // 单条
                if(!isset($queryParams['where'])) $queryParams['where'] = [];
                array_push($queryParams['where'], [$primaryName, $ids]);
            } else {
                $idArr = array_values(array_unique(explode(',', $ids)));// 去重，重按数字下标
                $queryParams['whereIn'][$primaryName] = Tool::arrClsEmpty($idArr);
            }
        }
        return static::getList($queryParams, $relations, $modelObj , $isOpenCache);
    }

    /**
     * 获得指定条件的多条数据-- 分页+总数量
     *
     * @param int 选填 $pagesize 每页显示的数量 [默认10]
     * @param int 选填 $total 总记录数,优化方案：传<=0传重新获取总数[默认0];=-5:只统计条件记录数量，不返回数据
     * @param string 选填 $queryParams 条件数组/json字符
     * @param string 选填 $relations 关系数组/json字符
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @return array 数据
        $listData = [
            'pageSize' => $pagesize,
            'page' => $page,
            'total' => $total,
            'totalPage' => ceil($total/$pagesize),
            'dataList' => $requestData,
        ];
     * @author zouyan(305463219@qq.com)
     */
    public static function getDataLimit($page = 1, $pagesize = 10, $total = 0, $queryParams = [], $relations = [], &$modelObj = null, $isOpenCache = true){
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );
        // $page = 1;
        // $pagesize = 10;
        // $total = 10;
//        $queryParams = [
//            'where' => [
//                  ['id', '&' , '16=16'],
//                ['company_id', $company_id],
//                //['mobile', $keyword],
//                //['admin_type',self::$admin_type],
//            ],
//            'whereIn' => [
//                'id' => $cityPids,
//            ],
////            'select' => [
////                'id','company_id','type_name','sort_num'
////                //,'operate_staff_id','operate_staff_id_history'
////                ,'created_at'
////            ],
//            // 'orderBy' => ['id'=>'desc'],
//        ];

        /*
        if ($group_id > 0) {
            array_push($queryParams['where'], ['group_id', $group_id]);
        }

        if (!empty($keyword)) {
            array_push($queryParams['where'], ['real_name', 'like', '%' . $keyword . '%']);
        }
        $ids = CommonRequest::get($request, 'ids');// 多个用逗号分隔,
        if (!empty($ids)) {
            if (strpos($ids, ',') === false) { // 单条
                array_push($queryParams['where'], ['id', $ids]);
            } else {
                $queryParams['whereIn']['id'] = explode(',', $ids);
            }
        }
        */
        // $relations = ''; $requestData =
        return CommonDB::getModelListDatas($modelObj, $page, $pagesize, $total, $queryParams, $relations, $isOpenCache);

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
    public static function getKeyVals($kv = [], $select =[], $queryParams = [], &$modelObj = null, $isOpenCache = true){
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );
//        $areaCityList = $modelObj::select(['id', 'area_name'])
//            ->orderBy('sort_num', 'desc')->orderBy('id', 'desc')
//            ->where([
//                ['company_id', '=', $company_id],
//                ['area_parent_id', '=', $area_parent_id],
//            ])
//            ->get()->toArray();
//        if(!$is_kv) return $areaCityList;
//        return Tool::formatArrKeyVal($areaCityList, 'id', 'area_name');
        return CommonDB::getKeyVals($modelObj, $kv, $select, $queryParams, $isOpenCache);
    }

    //################可以用到单条缓存############开始##################################################
    /**
     * 根据id获得详情--主键缓存
     *
     * @param int $id
     * @param array $select 需要获得的字段数组   ['id', 'area_name'] ; 参数 $kv 不为空，则此参数可为空数组
     * @param string $relations 关系数组/json字符
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @return  mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfo($id, $select = [], $relations = [], &$modelObj = null, $isOpenCache = true)
    {
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );

        $requestData = CommonDB::getInfoById($modelObj, $id, $select, $relations, $isOpenCache);

        return  $requestData;
    }

    /**
     * 单条记录缓存处理
     * 根据主键缓存或次缓存，获得数据--参数为空，则返回空数组
     *  cacheDb:U:m:{email值}_{email值}  -> {id值}
     * @param array $paramsPrimaryVals 主键或主键相关缓存字段及值 刚好[字段不能多]用上缓存，不然就用不了缓存 [ '字段1' => '字段1的值','字段2' => '字段2的值'] ;为空，则返回空数组--注意字段是刚好[主键或主字段]，不能有多,顺序无所谓
     *                                  字段多了[超过缓存字段]会自动转为块级缓存
     * @param array $select 查询要获取的字段数组 一维数组
     * @param array $relations 关系
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @return  mixed 获得的单 条记录对象
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoByPrimaryCache($paramsPrimaryVals = [], $select = [], $relations = [], &$modelObj = null, $isOpenCache = true)
    {
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );
        return CommonDB::getInfoByPrimaryFields($modelObj, $paramsPrimaryVals, $select, $relations, $isOpenCache);
    }
    //################可以用到单条缓存############结束##################################################

    /**
     * 根据条件获得详情 获得单条记录数据 1:返回一维数组,>1 返回二维数组
     *
     * @param string $pagesize 要获得的数据 1:
     * @param string $queryParams 条件数组/json字符
     * @param string $relations 关系数组/json字符
     * @param boolean $isOpenCache 是否开启缓存 true:开启/使用缓存[默认]；false：不使用缓存,强制从数据表读取-- 有时在缓存前需要判断，所如有不用的情况，在此传入
     * @return  mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoByQuery($pagesize = 1, $queryParams = [], $relations = [], &$modelObj = null, $isOpenCache = true)
    {
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );

        $requestData = CommonDB::getInfoByQuery($modelObj, $pagesize, $queryParams, $relations, $isOpenCache);

        return  $requestData;
    }

    /**
     * 根据条件，查询单条记录--从数据库
     * @param array $fieldVals 不能为空，为空，则返回空数组； 查询的字段及值 ['字段1' => '字段1的值', '字段2' => '字段2的值']
     * @param array $select 需要指定的字段 -一维数组；为空代表所有字段
     * @return  null 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoByFieldVals($fieldVals = [], $select = []){
        if(empty($fieldVals)) return [];
        $queryParams = [
            'where' => [
                // ['my_order_no', $out_trade_no],
            ],
            /*
            'select' => [
                'id','title','sort_num','volume'
                ,'operate_staff_id','operate_staff_id_history'
                ,'created_at' ,'updated_at'
            ],
            */
            //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
        ];
        $where = [];
        foreach($fieldVals as $field => $val){
            $where[] = [$field, $val];
        }
        $queryParams['where'] = array_merge($queryParams['where'], $where);
        if(!empty($select)) $queryParams['select'] = $select;
        // 查询记录
        $dbDataInfo = static::getInfoByQuery(1, $queryParams, []);
        return $dbDataInfo;
    }

    /**
     * 通过查询数据库中最在的值，设置新数据的主键的值
     * @param $modelObj
     * @param array $dataParams 主键及要修改的字段值 一维/二维数组 数组；数据中有主键值且不为空，则用数据中的
     * @param string $primaryKey  主键字段---如果为空，则重新通过对象获取
     * @param string $objPathNameMD  对象类全称 App\Models\QualityControl\CertificateSchedule 的 md5值 ---如果为空，则重新通过对象获取
     * @param boolean $isCacheDataByCache 读取缓存数据时,是否从缓存优先 true:有缓存优先使用缓存[默认]；false：不使用缓存,强制从数据表读取
     * @return  array 数组 $dataParams
     * @author zouyan(305463219@qq.com)
     */
    public  static function fillPrimaryValDataByPrimaryMaxVal(&$dataParams, $primaryKey = '', $objPathNameMD = '', $isCacheDataByCache = true, &$modelObj = null)
    {
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );
        return CommonDB::fillPrimaryValByDB($modelObj,$dataParams, $primaryKey, $objPathNameMD, $isCacheDataByCache);
    }

    /**
     * 获得表中记录的最大主键值
     * @param $modelObj
     * @param string $primaryKey  主键字段---如果为空，则重新通过对象获取
     * @param boolean $isCacheDataByCache 读取缓存数据时,是否从缓存优先 true:有缓存优先使用缓存[默认]；false：不使用缓存,强制从数据表读取
     * @return  int 表中记录的最大主键值
     * @author zouyan(305463219@qq.com)
     */
    public  static function getDBMaxPrimaryVal($primaryKey = '', $isCacheDataByCache = true, &$modelObj = null)
    {
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );
        return CommonDB::getDBMaxPrimaryId($modelObj, $primaryKey, $isCacheDataByCache);
    }

    /**
     * 设置数据主键的值
     * @param $modelObj
     * @param array $dataParams 主键及要修改的字段值 一维/二维数组 数组；数据中有主键值且不为空，则用数据中的
     * @param string $primaryKey  主键字段---如果为空，则重新通过对象获取
     * @param boolean $forceIncr 如果 数据模型的 主键id的值类型 为  1自增id时 ：是否通过直接读取表中当前的最大主键值来补充数据中的主键；true：是： false:不用处理数据中的主键值
     * @param boolean $isCacheDataByCache 读取缓存数据时,是否从缓存优先 true:有缓存优先使用缓存[默认]；false：不使用缓存,强制从数据表读取
     * @return  array 数组 $dataParams
     * @author zouyan(305463219@qq.com)
     */
    public  static function setDataPrimaryKeyVal(&$dataParams, $primaryKey = '', $forceIncr = false, $isCacheDataByCache = true, &$modelObj = null)
    {
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );
        return CommonDB::setPrimaryKeyVal($modelObj, $dataParams, $primaryKey, $forceIncr, $isCacheDataByCache);
    }

    /**
     * 获得对象新的[待加入的]主键的值
     * @param int $getType 获取数据的方式 1：缓存池 【可回收使用】；2 实时获取 【默认】；-- $valType:256 会强制用缓存池，$valType:2会强制不用缓存池，其它也不建议用缓存池
     * @param array $fixParams 前中后缀，默认都为空，不用填
     * [
     *  'prefix' => '',// 前缀[1-2位] 可填;可写业务编号等
     *  'midFix' => '',// 日期后中间缀[1-2位] 可填;适合用户编号里的后2位或其它等
     *  'backfix' => '',// 后缀[1-2位] 可填;备用
     * ]
     * @param int $primaryKeyValType 必填 ；主键id的值类型;如果 <=0,则重新通过对象获取--如果 模型对象没有设置，则必须指定传入，才有值
     * @param string $primaryKey  主键字段---如果为空，则重新通过对象获取
     * @param string $objPathNameMD  对象类全称 App\Models\QualityControl\CertificateSchedule 的 md5值 ---如果为空，则重新通过对象获取
     * @param boolean $isCacheDataByCache 读取缓存数据时,是否从缓存优先 true:有缓存优先使用缓存[默认]；false：不使用缓存,强制从数据表读取
     * @param $modelObj
     * @return int 0 没有获取到， > 0 成功获取到
     * @author zouyan(305463219@qq.com)
     */
    public  static function getNextPrimaryKeyVal($getType = 2, $fixParams = [], $primaryKeyValType = 0, $primaryKey = '', $objPathNameMD = '', $isCacheDataByCache = true, &$modelObj = null)
    {
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );
        return CommonDB::getPrimaryKeyVal($modelObj, $getType, $fixParams, $primaryKeyValType, $primaryKey, $objPathNameMD, $isCacheDataByCache);
    }

    /**
     * 批量获得 计数器，批量的，自动优先批量生成，在使用的过程中自动补充。--不浪费【没有使用的，自动回收重新历用】
     * @param int $getType 获取数据的方式 1：缓存池 【可回收使用】；2 实时获取 【默认】；-- $valType:256 会强制用缓存池，$valType:2会强制不用缓存池，其它也不建议用缓存池
     * @param int $valNums 需要获得值的数量 默认 1 ; --  超过 > $maxValNum 参数的值，则 会自动分批获取并合并
     * @param int $maxValNum  最大的可用记录数量 默认 10-- 缓存中最多可用的数量 ; ;-- $getType = 2 $getType 时使用
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
     * @param $modelObj
     * @return array 新的主键值数组 [值1,值2,...]
     * @author zouyan(305463219@qq.com)
     */
    public  static function getDBMultiSignerArr($getType = 2, $valNums = 1, $maxValNum = 20, $minAvailableNum = 3, $valType = 256, $primaryKey = '', $fixParams = [], $overTime = 60
        , $objPathNameMD = '', $isCacheDataByCache = true, &$modelObj = null)
    {
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );
        return CommonDB::getMultiSignerArr($modelObj, $getType, $valNums, $maxValNum, $minAvailableNum, $valType, $primaryKey, $fixParams, $overTime
            , $objPathNameMD, $isCacheDataByCache);
    }

    /**
     * 自增自减接口,通过条件-data操作的行数
     *
     * @param string incDecField 增减字段
     * @param string incDecVal 增减值
     * @param string incDecType 增减类型 inc 增 ;dec 减[默认]
     * @param string $queryParams 条件数组/json字符
     * @param string modifFields 修改的其它字段 -没有，则传空数组json
     * @param boolean $isCacheDataByCache 读取缓存数据时,是否从缓存优先 true:有缓存优先使用缓存[默认]；false：不使用缓存,强制从数据表读取
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static  function saveDecIncByQuery($incDecField, $incDecVal = 1, $incDecType = 'inc', $queryParams = [], $modifFields = [], &$modelObj = null, $isCacheDataByCache = true)
    {
//        $modelObj = null;
//        Common::getObjByModelName(static::$model_name, $modelObj);
        static::getModelObj($modelObj );
        return CommonDB::saveDecInc($modelObj, $incDecField, $incDecVal, $incDecType, $queryParams, $modifFields, $isCacheDataByCache);
    }

    /**
     * 自增自减接口,通过数组[二维]-data操作的行数数组
     *
     * @param int $company_id 公司id
     * @param string $dataParams 条件数组/json字符
        $dataParams = [
            [
                'Model_name' => 'model名称',
                'primaryVal' => '主键字段值',
                'incDecType' => '增减类型 inc 增 ;dec 减[默认]',
                'incDecField' => '增减字段',
                'incDecVal' => '增减值',
                'modifFields' => '修改的其它字段 -没有，则传空数组',
            ],
        ];
    如:
        [
            [
                'Model_name' => 'CompanyProSecurityLabel',
                'primaryVal' => '7',
                'incDecType' => 'inc',
                'incDecField' => 'validate_num',
                'incDecVal' => '2',
                'modifFields' => [],
            ],
            [
                'Model_name' => 'CompanyProSecurityLabel',
                'primaryVal' => '9',
                'incDecType' => 'inc',
                'incDecField' => 'validate_num',
                'incDecVal' => '1',
                'modifFields' => [
                    'status' => 1,
                ],
            ],
        ];
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static  function saveDecIncByArr($dataParams)
    {
        return CommonDB::saveDecIncBatchByPrimaryKey($dataParams );
    }

    /**
     * 同步修改关系接口
     *
     * @param string $Model_name model名称
     * @param int $id
     * @param string/array $synces 同步关系数组/json字符  格式 [ '关系方法名' =>[关系id,...],...可多个....]
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static function sync($id, $synces, &$modelObj = null)
    {
        static::getModelObj($modelObj );
        return CommonDB::sync($modelObj, $id, $synces);
    }

    /**
     * 移除关系接口
     *
     * @param string $Model_name model名称
     * @param int $id
     * @param string/array $detaches 移除关系数组/json字符 空：移除所有，id数组：移除指定的
     * @return mixed Response
     * @author zouyan(305463219@qq.com)
     */
    public static function detach($id, $detaches, &$modelObj = null)
    {
        static::getModelObj($modelObj );
        return CommonDB::detach($modelObj, $id, $detaches);;
    }

    /**
     * 获得表的主键字段
     *
     * @return array  缓存键的主键字段-- 一维数组,也可能是空
     * @author zouyan(305463219@qq.com)
     */
    public static function getPrimaryKey(&$mainObj = null)
    {
        // 主表
        static::getModelObj($mainObj );
        $primaryKey = CommonDB::getPrimaryKey($mainObj);
        return $primaryKey;
    }

    /**
     * 根据表对象名称获得表的主键字段
     *
     * @param string $objName 表对象名称
     * @return array 缓存键的主键字段-- 一维数组,也可能是空
     * @author zouyan(305463219@qq.com)
     */
    public static function getPrimaryKeyByObjName($objName, &$obj)
    {
        static::getModelObjByName($objName, $obj);

        $primaryKey = CommonDB::getPrimaryKey($obj);
        return $primaryKey;
    }


    /**
     * 根据主表id，获得对应的历史表id
     *
     * @param Request $request
     * @param mixed $mId 主表对象主键值
     * @param string $historyObjName 历史表对象名称
     * @param string $historyTable 历史表名字
     * @param array $historySearch 历史表查询字段[一维数组][一定要包含主表id的] +  版本号(不用传，自动会加上) 格式 ['字段1'=>'字段1的值','字段2'=>'字段2的值' ... ]
         [
            'company_id' => $company_id,
            'subject_id' => $main_id,
        ]
     * @param array $ignoreFields 忽略都有的字段中，忽略主表中的记录 [一维数组] 格式 ['字段1','字段2' ... ]
     * @return  int 历史表id
     * @author zouyan(305463219@qq.com)
     */
    public static function getHistoryId(&$mainObj, $mId, $historyObjName, $historyTable, &$historyObj, $historySearch = [], $ignoreFields = [] )
    {
        // 主表
        static::getModelObj($mainObj );

        // 历史表
        static::getModelObjByName($historyObjName, $historyObj);

        CommonDB::getHistory($mainObj, $mId, $historyObj, $historyTable, $historySearch, $ignoreFields);

        return  $historyObj->id;
    }

    /**
     * 对数据加上对应的，历史id字段值：如果历史字段id值已存在，则不重新获得历史字段id值
     *
     * @param array $saveData 一维或二维数组
     * @param string $fieldIdName 原字段id字段名称
     * @param string $fieldIdHistoryNmae 字段历史id字段名称
     * @return  mixed 返回   二维数组：['原字段id值' => '历史id值'] --- 新获取的，原有的历史id不加入;        一维数组 ：历史id值
     * @author zouyan(305463219@qq.com)
     */
    public static function appendFieldIdHistory(&$saveData, $fieldIdName, $fieldIdHistoryNmae){
        // 如果是一维数组,则转为二维数组
        $isMulti = Tool::isMultiArr($saveData, true);

        $fieldIdHistoryArr = [];
        foreach($saveData as $k => &$info){
            $tem_id = $info[$fieldIdName] ?? 0;
            // 有原id值，且无有效的原id历史值
            if(Tool::judgeArrVal($info, [$fieldIdName => 1 | 2 | 4]) && !Tool::judgeArrVal($info, [$fieldIdHistoryNmae => 1 | 2 | 4])){
                if(!isset($fieldIdHistoryArr[$tem_id])){
                    $fieldIdHistory = static::getIdHistory($tem_id);
                    $fieldIdHistoryArr[$tem_id] = $fieldIdHistory;
                }else{
                    $fieldIdHistory = $fieldIdHistoryArr[$tem_id];
                }
                $info[$fieldIdHistoryNmae] = $fieldIdHistory;
            }else{
                // 有原id值，且有效的原id历史值
//                if(Tool::judgeArrVal($info, [$fieldIdName => 1 | 2 | 4]) && Tool::judgeArrVal($info, [$fieldIdHistoryNmae => 1 | 2 | 4])){
//                    if(!isset($fieldIdHistoryArr[$tem_id])){
//                        $fieldIdHistory = $info[$fieldIdHistoryNmae];
//                        $fieldIdHistoryArr[$tem_id] = $fieldIdHistory;
//                    }
//                }
            }
        }

        if(!$isMulti){// 一维数组
            $saveData = $saveData[0] ?? [];
            return $saveData[$fieldIdHistoryNmae] ?? 0;
        }else{// 二维数组
            return $fieldIdHistoryArr;
        }
    }

    /**
     * 对比主表和历史表是否相同，相同：不更新版本号，不同：版本号+1
     *
     * @param obj $mainObj 主表对象
     * @param mixed $mId 主表对象主键值
     * @param obj $historyObj 历史表对象
     * @param string $historyTable 历史表名字
     * @param array $historySearch 历史表查询字段[一维数组][一定要包含主表id的] +  版本号(不用传，自动会加上)格式 ['字段1'=>'字段1的值','字段2'=>'字段2的值' ... ]
         [
            'company_id' => $company_id,
            'staff_id' => $main_id,
        ];
     * @param array $ignoreFields 忽略都有的字段中，忽略主表中的记录 [一维数组] - 必须会有 [历史表中对应主表的id字段] 格式 ['字段1','字段2' ... ]
     * @param int $forceIncVersion 如果需要主表版本号+1,是否更新主表 1 更新 ;0 不更新
     * @return array 不同字段的内容 数组 [ '字段名' => ['原表中的值','历史表中的值']]; 空数组：不用版本号+1;非空数组：版本号+1
     * @author zouyan(305463219@qq.com)
     */
    public static function compareHistoryOrUpdateVersion(&$mainObj, $mId, $historyObjName, $historyTable, &$historyObj, $historySearch = [], $ignoreFields = [], $forceIncVersion = 0)
    {
        // 主表
        static::getModelObj($mainObj );

        // 历史表
        static::getModelObjByName( $historyObjName, $historyObj);


        $diffDataArr = CommonDB::compareHistoryOrUpdateVersion($mainObj, $mId,
            $historyObj, $historyTable, $historySearch, $ignoreFields,
            $forceIncVersion);

        return  $diffDataArr;
    }

    //************************基类扩展出来的公用方法*******************************************************************************
    /**
     * 判断后机号是否已经存在 true:已存在;false：不存在
     *
     * @param int $company_id id
     * @param int $id id
     * @param string $fieldName 需要判断的字段名 mobile  admin_username  work_num
     * @param string $fieldVal 当前要判断的值
     * @param array $otherWhere 其它查询条件 --二维数组
        [
        //  ['company_id', $company_id],
        [$fieldName,$fieldVal],
        // ['admin_type',self::$admin_type],
        ]
     * @param int $reType 返回类型 1:布尔型 2:当前存在的记录 [没有，则为空数组[]]
     * @return  mixed boolean/array 单条数据 - -维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function judgeFieldExist($company_id, $id, $fieldName, $fieldVal, $otherWhere = [], $reType = 1){
        // $company_id = $controller->company_id;
        $queryParams = [
            'where' => [
               // ['id', 100],
                //  ['company_id', $company_id],
                [$fieldName,$fieldVal],
                // ['admin_type',self::$admin_type],
            ],
            // 'limit' => 1
        ];
        if(is_array($otherWhere) && !empty($otherWhere))  $queryParams['where'] = array_merge($queryParams['where'], $otherWhere);
        if( is_numeric($id) && $id >0){
            array_push($queryParams['where'],['id', '<>' ,$id]);
        }

        $infoData = static::getInfoByQuery(1, $queryParams, []);
        // if(is_object($infoData))  $infoData = $infoData->toArray();
        if(empty($infoData)){//  || count($infoData)<=0
            if(($reType & 2) ==2) return [];
            return false;
        }
        if(($reType & 2) ==2) return $infoData;
        return true;
    }

    /**
     * 获得操作人员历史id
     * @param array $saveData 需要操作的数组 [一维或二维数组]
     * @param int $operate_staff_id 操作人id
     * @return  mixed 单条数据 - -维数组 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     * @author zouyan(305463219@qq.com)
     */
    public static function getStaffHistoryId($operate_staff_id = 0){
        $staffDBObj = null ;
        $staffHistoryDBObj = null ;
        $operate_staff_id_history = StaffDBBusiness::getHistoryId($staffDBObj, $operate_staff_id, StaffHistoryDBBusiness::$model_name
            , StaffHistoryDBBusiness::$table_name, $staffHistoryDBObj, ['staff_id' => $operate_staff_id], StaffDBBusiness::$ignoreFields);
        return $operate_staff_id_history;
    }

    /**
     * 数据加入操作人员历史id
     * @param array $saveData 需要操作的数组 [一维或二维数组]
     * @param int $operate_staff_id 操作人id
     * @param int $operate_staff_id_history 操作人历史id
     * @param int $operate_type 操作类型 主要用1，2-一般不用，一般会在使用之前判断是不是应该用此来获取
     *                              1 [默认]必须要获得[下面代码也要用] $operate_staff_id_history 操作人历史id;--肯定要获取到
     *                              2 当前对象有这个字段就获取或只有调用的地方会用到 $operate_staff_id_history 操作人历史id;--不一定要获取
     * @return  mixed 单条数据 - -维数组 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     * @author zouyan(305463219@qq.com)
     */
    public static function addOprate(&$saveData, $operate_staff_id = 0, &$operate_staff_id_history = 0, $operate_type = 1){
        if(!is_numeric($operate_staff_id) || $operate_staff_id <= 0) return $saveData;

        // 获得当前对象自有属性值
        $ownProperty = static::getOwnPropertyVal();

        // 判断是否需要获取 $operate_staff_id_history 操作人历史id
        $needStaffIdHistory = true;

        // 2 [默认]当前对象有这个字段就获取或只有调用的地方会用到
        // 且 没有有操作员工历史id 字段 operate_staff_id_history
        if( ($operate_type & 2) == 2 && ($ownProperty & 4) != 4) $needStaffIdHistory = false;

        // 进行优化 ：如果传入值　<= 0 且 源数据是一维数组  且有 历史下标值 且值 > 0 , 则直接可以使用此值，不用再去查询了
        if($operate_staff_id_history <= 0 && isset($saveData['operate_staff_id_history']) && is_numeric($saveData['operate_staff_id_history']) && $saveData['operate_staff_id_history'] > 0) $operate_staff_id_history = $saveData['operate_staff_id_history'];

        if ($needStaffIdHistory && $operate_staff_id_history <= 0) $operate_staff_id_history = static::getStaffHistoryId($operate_staff_id);

        // 加入操作人员信息
        $oprateArr = [
            // 'operate_staff_id' => $operate_staff_id,// $controller->operate_staff_id,
            // 'operate_staff_id_history' => $operate_staff_id_history,// $controller->operate_staff_id_history,
        ];
        // 2：有操作员工id 字段 operate_staff_id
        if( ($ownProperty & 2) == 2) $oprateArr['operate_staff_id'] = $operate_staff_id;
        // 4：有操作员工历史id 字段 operate_staff_id_history
        if( ($ownProperty & 4) == 4) $oprateArr['operate_staff_id_history'] = $operate_staff_id_history;

        // 为空值，直接返回
        if(empty($oprateArr)) return $saveData;

        $isMultiArr = false; // true:二维;false:一维
        foreach($saveData as $k => $v){
            if(is_array($v)){
                $isMultiArr = true;
            }
            break;
        }
        if($isMultiArr){ //二维

            foreach($saveData as $k => $v){
                $v = array_merge($v, $oprateArr);
                $saveData[$k] = $v;
            }
        }else{// 一维
            $saveData = array_merge($saveData, $oprateArr);
        }
        return $saveData;
    }

    /**
     * 数据加入操作人员历史id
     * @param array $saveData 需要操作的数组 [一维或二维数组]
     * @param int $operate_staff_id 操作人id
     * @param int $operate_staff_id_history 操作人历史id
     * @param int $operate_type 操作类型 主要用1，2-一般不用，一般会在使用之前判断是不是应该用此来获取
     *                              1 [默认]必须要获得[下面代码也要用] $operate_staff_id_history 操作人历史id;--肯定要获取到
     *                              2 当前对象有这个字段就获取或只有调用的地方会用到 $operate_staff_id_history 操作人历史id;--不一定要获取
     * @return  mixed 单条数据 - -维数组 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     * @author zouyan(305463219@qq.com)
     */
//    public static function setOperateStaffIdHistory(&$saveData = [], $operate_staff_id = 0, &$operate_staff_id_history = 0, $operate_type = 1){
//
//        // if($operate_staff_id_history <= 0){
//
//        CommonDB::doTransactionFun(function() use( &$saveData, &$operate_staff_id, &$operate_staff_id_history, &$operate_type){
//
//            // $ownProperty  自有属性值;
//            // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
//            list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
//            // 加入操作人员信息
//            // $temData = [];
//            if($temNeedStaffIdOrHistoryId) static::addOprate($saveData, $operate_staff_id,$operate_staff_id_history, $operate_type);
//        });
//        // }
//    }

    // 判断权限-----开始
    // 判断权限 ,返回当前记录[可再进行其它判断], 有其它主字段的，可以重新此方法
    /**
     * 判断权限-- 注意用具体的**DBBusiness来调
     *
     * @param int $id id ,多个用,号分隔 为0或''时，可以用条件参数$relations来查询
     * @param array $judgeArr 需要判断的下标[字段名]及值 一维数组
     * @param int $companyId 企业id
     * @param array $otherWhere 其它查询条件 --二维数组
     * @param json/array $relations 要查询的与其它表的关系
        [
            //  ['company_id', $company_id],
            [$fieldName,$fieldVal],
            // ['admin_type',self::$admin_type],
        ]
     * @return array 一维数组[单条] 二维数组[多条]
     * @author zouyan(305463219@qq.com)
     */
    public static function judgePower($id = 0, $judgeArr = [] , $company_id = '', $otherWhere = [], $relations = ''){
        // $this->InitParams($request);
//        if(empty($model_name)){
//            $model_name = $this->model_name;
//        }
        $dataList = [];
        $isSingle = true;// 是否单条记录 true:是;false：否
        if (strpos($id, ',') === false) { // 单条
            // 获得当前记录
            // $dataList[] =  static::getinfoApi($model_name, '', $relations, $company_id , $id, $notLog);
            if($id != '' &&  $id!= 0){
                $dataList[] =  static::getInfo($id, [], $relations);
            }else{
                $queryParams =  [
                    'where' => [
                        //['company_id', $company_id],
                        //['mobile', $keyword],
                    ],
//                    'select' => [
//                        'id','company_id','type_name','sort_num'
//                    ],
                    // 'orderBy' => ['id'=>'desc'],
                ];
                if(is_array($otherWhere) && !empty($otherWhere))  $queryParams['where'] = array_merge($queryParams['where'], $otherWhere);
                $dataList[] = static::getInfoByQuery(1, $queryParams, $relations);
            }

        }else{
            $isSingle = false;
            $queryParams =  [
                'where' => [
                    //['company_id', $company_id],
                    //['mobile', $keyword],
                ],
//            'select' => [
//                'id','company_id','type_name','sort_num'
//            ],
                // 'orderBy' => ['id'=>'desc'],
            ];
//            if($company_id != ''){
//                array_push($queryParams['where'],['company_id', $company_id]);
//            }
            $ids = explode(',',$id);
            foreach($ids as $k => $tem_id){
                if($id == '' &&  $id == 0) unset($ids[$k]);
            }
            if(!empty($ids))  $queryParams['whereIn']['id'] = $ids;

            if(is_array($otherWhere) && !empty($otherWhere))  $queryParams['where'] = array_merge($queryParams['where'], $otherWhere);
            // $dataList = static::ajaxGetAllList($model_name, [], $company_id,$queryParams ,$relations, $notLog );
            $dataList = static::getAllList($queryParams, $relations);
        }
        foreach($dataList as $infoData){
            static::judgePowerByObj($infoData, $judgeArr);
        }
        return $isSingle ? $dataList[0] : $dataList;
    }

    public static function judgePowerByObj($infoData, $judgeArr = [] ){
        if(empty($infoData)){
            // throws('记录不存!', $this->source);
            throws('记录不存!');
        }
        foreach($judgeArr as $field => $val){
            if(!isset($infoData[$field])){
                // throws('字段[' . $field . ']不存在!', $this->source);
                throws('字段[' . $field . ']不存在!');
            }
            if( $infoData[$field] != $val ){
                // throws('没有操作此记录权限!信息字段[' . $field . ']', $this->source);
                throws('没有操作此记录权限!信息字段[' . $field . ']');
            }
        }
    }

    // 判断权限-----结束


    /**
     * 根据id获得详情及history id信息; 有历史功能的主表使用，注意在具体的类中需要定义 getIdHistory 方法，才能正常使用
     *
     * @param int  $company_id 企业id
     * @param int $id id
     * @param int $operate_staff_id 操作人id
     * @param string $relations 关系数组/json字符
     * @return  array 单条数据 - -维数组 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，
     * @author zouyan(305463219@qq.com)
     */
    public static function getInfoHistoryId($company_id, &$id, $operate_staff_id = 0, $relations = [])
    {
        if(!is_array($relations))  $relations = [];
        $info = [];
        if(!empty($relations)){
            $info = static::getInfo($id, [], $relations);
        }

        $mainDBObj = null ;
        $historyDBObj = null ;
        $historyId = static::getIdHistory($id, $mainDBObj, $historyDBObj);
        if(empty($info)) $info = $mainDBObj;
        $info['history_id'] = $historyId ;
        $info['now_state'] = 0;// 最新的试题 0没有变化 ;1 已经删除  2 试卷不同
        return $info;
    }

    /**
     * 保存图片资源关系
     *
     * @param int  $company_id 企业id
     * @param int $id 主表记录id
     * @param array $resourceIds 关系表id数组
     * @param int $operate_staff_id 操作人id
     * @param int $operate_staff_id_history 操作人id历史 默认 0
     * @param array $otherData 其它参数数组 - 一维
     * @return null
     * @author zouyan(305463219@qq.com)
     */
    public static function saveResourceSync($id, $resourceIds = [], $operate_staff_id = 0, $operate_staff_id_history = 0, $otherData = []){
        // 加入company_id字段
        $syncResourceArr = [];
        $temArr =  [
            // 'company_id' => $company_id,
//                    'operate_staff_id' => $operate_staff_id,
//                    'operate_staff_id_history' => $operate_staff_id_history,
        ];
        $temArr = array_merge($temArr, $otherData);// 其它参数合并进来
         // 加入操作人员信息
        static::addOprate($temArr, $operate_staff_id,$operate_staff_id_history, 1);
        foreach($resourceIds as $resourceId){
            if(empty($resourceId)) continue;
            $syncResourceArr[$resourceId] = $temArr;
            // 资源id 历史 resource_id_history
            $syncResourceArr[$resourceId]['resource_id_history'] = ResourceDBBusiness::getIdHistory($resourceId);
        }
        // 为空，则是移除关系
        if(empty($syncResourceArr)){// 解除关系
            $syncParams =[
                'siteResources' => $syncResourceArr,//标签
            ];
            return static::detach($id, $syncParams);
        }else{// 绑定关系
            $syncParams =[
                'siteResources' => $syncResourceArr,//标签
            ];
            return static::sync($id, $syncParams);
        }
    }

    /**
     * 删除图片资源关系
     *
     * @param int  $company_id 企业id
     * @param int $id 主表记录id
     * @param array $resourceIds 关系表id数组  空：移除所有，id数组：移除指定的
     * @return null
     * @author zouyan(305463219@qq.com)
     */
    public static function delResourceDetach($id, $resourceIds = []){
        $syncParams =[
            'siteResources' => $resourceIds,//标签
        ];
        return static::detach($id, $syncParams);
    }

    // ~~~~~~~~~~~子类[如有需要]需要重写的方法~~~~~~~~开始~~~~~~~~~~~~~~~~~~~

    // 获得记录历史id
    public static function getIdHistory($mainId = 0, &$mainDBObj = null, &$historyDBObj = null){
//        return static::getHistoryId($mainDBObj, $mainId, CityHistoryDBBusiness::$model_name
//            , CityHistoryDBBusiness::$table_name, $historyDBObj, ['city_table_id' => $mainId], ['city_table_id']);
        return 0;
    }

    /**
     * 对比主表和历史表是否相同，相同：不更新版本号，不同：版本号+1
     *
     * @param mixed $mId 主表对象主键值
     * @param int $forceIncVersion 如果需要主表版本号+1,是否更新主表 1 更新 ;0 不更新
     * @return array 不同字段的内容 数组 [ '字段名' => ['原表中的值','历史表中的值']]; 空数组：不用版本号+1;非空数组：版本号+1
     * @author zouyan(305463219@qq.com)
     */
    public static function compareHistory($id = 0, $forceIncVersion = 0, &$mainDBObj = null, &$historyDBObj = null){
        // 判断版本号是否要+1
        $historySearch = [
            //  'company_id' => $company_id,
            'city_table_id' => $id,
        ];
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        // return static::compareHistoryOrUpdateVersion($mainDBObj, $id, CityHistoryDBBusiness::$model_name
        //    , CityHistoryDBBusiness::$table_name, $historyDBObj, $historySearch, ['city_table_id'], $forceIncVersion);
        return [];
    }

    /**
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param array $saveData 要保存或修改的数组
     * @param int  $company_id 企业id
     * @param int $id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 记录id值，
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceById($saveData, $company_id, &$id, $operate_staff_id = 0, $modifAddOprate = 0){

//        DB::beginTransaction();
//        try {
//            DB::commit();
//        } catch ( \Exception $e) {
//            DB::rollBack();
//            throws($e->getMessage());
//            // throws($e->getMessage());
//        }
        //        if(isset($saveData['real_name']) && empty($saveData['real_name'])  ){
//            throws('联系人不能为空！');
//        }
//
//        if(isset($saveData['mobile']) && empty($saveData['mobile'])  ){
//            throws('手机不能为空！');
//        }
        $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表
        // 保存前的处理
        static::replaceByIdAPIPre($saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        $modelObj = null;
        //*********************************************************
        $isModify = false;
        CommonDB::doTransactionFun(function() use(&$saveData, &$company_id, &$id, &$operate_staff_id, &$modifAddOprate, &$operate_staff_id_history, &$modelObj, &$isModify ){


            // $ownProperty  自有属性值;
            // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
            list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
            if($id > 0){
                $isModify = true;
                // 判断权限
                //            $judgeData = [
                //                'company_id' => $company_id,
                //            ];
                //            $relations = '';
                //            static::judgePower($id, $judgeData , $company_id , [], $relations);
                if($temNeedStaffIdOrHistoryId && $modifAddOprate) static::addOprate($saveData, $operate_staff_id,$operate_staff_id_history, 1);

            }else {// 新加;要加入的特别字段
                //            $addNewData = [
                //                'company_id' => $company_id,
                //            ];
                //            $saveData = array_merge($saveData, $addNewData);
                // 加入操作人员信息
                if($temNeedStaffIdOrHistoryId) static::addOprate($saveData, $operate_staff_id,$operate_staff_id_history, 1);
            }
            $resultDatas = [];
            // 新加或修改
            if($id <= 0){// 新加
                $resultDatas = static::create($saveData,$modelObj);
                $id = $resultDatas['id'] ?? 0;
            }else{// 修改
                $saveBoolen = static::saveById($saveData, $id,$modelObj);
                // $resultDatas = static::getInfo($id);
                // 修改数据，是否当前版本号 + 1
                // 1：有历史表 ***_history;
                // if(($ownProperty & 1) == 1) static::compareHistory($id, 1);
            }
            if($isModify && ($ownProperty & 1) == 1){// 1：有历史表 ***_history;
                static::compareHistory($id, 1);
            }
        });
        // ************************************************
        // 保存成功后的处理
        static::replaceByIdAPISucess($isModify, $modelObj, $saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        return $id;
    }

    /**
     * replaceById 方法操作前可执行的一些操作,数据判重等----具体的表，如有需要，请重写此方法
     *
     * @param array $saveData 请求的原数据
     * @param int  $company_id 企业id
     * @param int $id id 成功后返回的id
     * @param int $operate_staff_id 操作人id
     * @param int $operate_staff_id_history 操作人id历史
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  null 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceByIdAPIPre(&$saveData = [], $company_id = 0, $id = 0, $operate_staff_id = 0, &$operate_staff_id_history = 0, $modifAddOprate = 0){

    }

    /**
     * replaceById 方法操作成功后可执行的一些操作----具体的表，如有需要，请重写此方法
     *
     * @param boolean $isModify true:修改操作； false:新加操作
     * @param object $modelObj 主数据对象
     * @param array $saveData 请求的原数据
     * @param int  $company_id 企业id
     * @param int $id id 成功后返回的id
     * @param int $operate_staff_id 操作人id
     * @param int $operate_staff_id_history 操作人id历史
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  null 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceByIdAPISucess($isModify = false, $modelObj = null, $saveData = [], $company_id = 0, $id = 0, $operate_staff_id = 0, $operate_staff_id_history = 0, $modifAddOprate = 0){

    }

    /**
     * 更新相关功能的数量--通用的
     *
     * @param array $configInfo 一维数组，操作配置
     *  $configInfo =  [// 试题
     *      'num_db_business_name' => 'App\Business\DB\QualityControl\StaffExtendDBBusiness',// 保存数量的对象名称
     *      'num_company_field_name' => 'staff_id',// 保存数量的对象中的主记录字段；默认为 'staff_id'
     *      'extend_field' => 'subject_type_num', // 扩展中记录数量的字段
     *
     *      'db_business_name' => 'App\Business\DB\QualityControl\CompanySubjectTypeDBBusiness',// 统计数量的 DBBusiness对象的名称
     *      'static_fun' => '',// 扫行统计的方法, 为空时：不指定，默认为  getCompanyRecordCount($company_id, $company_id_field, $other_condition)
     *      'company_id_field' => 'company_id',// 企业id字段 默认为 'company_id'
     *      'other_condition' => [] // $fieldValParams 其它条件 键值 数组;  默认为 []
     *  ];
     * @param int  / array $company_ids 企业id  多个时为一维数组或逗号分隔的字符串
     * @return  mixed 员工人数
     * @author zouyan(305463219@qq.com)
     */
    public static function updateEextndNum($configInfo, $company_ids){

        // 没有需要处理的
        if(!Tool::formatOneArrVals($company_ids)) return true;

        $num_db_business_name = $configInfo['num_db_business_name'] ?? 'App\Business\DB\QualityControl\StaffExtendDBBusiness';
        if(empty($num_db_business_name)) $num_db_business_name = 'App\Business\DB\QualityControl\StaffExtendDBBusiness';

        $num_company_field_name = $configInfo['num_company_field_name'] ?? 'staff_id';
        if(empty($num_company_field_name)) $num_company_field_name = 'staff_id';

        $db_business_name = $configInfo['db_business_name'];

        $extend_field = $configInfo['extend_field'];

        $company_id_field = $configInfo['company_id_field'] ?? 'company_id';
        if(empty($company_id_field)) $company_id_field = 'company_id';

        $static_fun = $configInfo['static_fun'] ?? 'getCompanyRecordCount';
        if(empty($static_fun)) $static_fun = 'getCompanyRecordCount';

        $other_condition = $configInfo['other_condition'] ?? [];
        if(empty($other_condition) || !is_array($other_condition)) $other_condition = [];
        // 更新企业的员工人数
//        DB::beginTransaction();
//        try {
//            DB::commit();
//        } catch ( \Exception $e) {
//            DB::rollBack();
//            throws($e->getMessage());
//            // throws($e->getMessage());
//        }
        CommonDB::doTransactionFun(function() use(&$company_ids, &$num_db_business_name, &$num_company_field_name, &$db_business_name, &$extend_field, &$static_fun, &$company_id_field, &$other_condition){


            foreach($company_ids as $company_id){
                // $count = CompanySubjectTypeDBBusiness::getCompanyRecordCount($company_id);
                $count = $db_business_name::{$static_fun}($company_id, $company_id_field, $other_condition);
                $updateFields = [
                    // 'subject_type_num' => $count,
                    $extend_field => $count,
                ];
                $searchConditon = [
                    // 'admin_type' => 2,
                    // 'staff_id' => $company_id,
                    $num_company_field_name => $company_id,
                ];
                $mainObj = null;
                // StaffExtendDBBusiness::updateOrCreate($mainObj, $searchConditon, $updateFields );
                $num_db_business_name::updateOrCreate($mainObj, $searchConditon, $updateFields );
            }
        });
        return true;
    }

    /**
     * 根据企业id,获得企业的此栏目数
     *
     * @param int  $company_id 企业id
     * @param int  $company_id_field 企业id字段 默认为 'company_id'
     * @param array $fieldValParams 其它条件 数组
     * @return  mixed 能力范围数
     * @author zouyan(305463219@qq.com)
     */
    public static function getCompanyRecordCount($company_id = 0, $company_id_field = 'company_id', $fieldValParams = []){
        if(strlen($company_id) > 0) $fieldValParams = array_merge($fieldValParams, [$company_id_field => $company_id]);
        return static::getDBFVFormatList(8, 1, $fieldValParams, false);
    }

    // ~~~~~~~~~~~子类[如有需要]需要重写的方法~~~~~~~~结束~~~~~~~~~~~~~~~~~~~

    /**
     * 生成单号
     *
     * @param int $company_id 企业id
     * @param int $user_id 当前用户
     * @param int  $orderType 要保存或修改的数组 1 11 14 18订单号 2 退款订单 3 支付跑腿费【支付费用】  4 追加跑腿费 5 冲值  6 提现 7 压金或保证金
     * @return  int
     * @author zouyan(305463219@qq.com)
     */
    public static function createSn($company_id , $user_id, $orderType = 1){
        // $company_id = $controller->company_id;
        // $user_id = $controller->user_id ?? '';
        $namespace = '';
        $prefix = $orderType;
        $midFix = '';
        $backfix = '';
        $length = 6;
        $expireNums = [];
        $needNum = 0;
        $dataFormat = '';
        switch ($orderType)
        {
            case 1:// 订单--不用
            case 11:// 面授培训
            case 12:// 会员年费
            case 14://
            case 18:
                $userIdBack = str_pad(substr($user_id, -2), 2, '0', STR_PAD_LEFT);
                $midFix = $userIdBack;
                $namespace = 'order' . $userIdBack;
                $length = 4;
                $needNum = 1 + 2 + 8;
//                $expireNums = [
//                  [1000,1100,365 * 24 * 60 * 60]  // 缓存的秒数365 * 24 * 60 * 60
//                ];
                break;
            case 3:// 3 支付跑腿费 -- 不用
            case 31:// 面授培训
            case 32:// 会员年费
            case 34:
            case 38:
                $userIdBack = str_pad(substr($user_id, -2), 2, '0', STR_PAD_LEFT);
                $midFix = $userIdBack;
                $namespace = 'orderPay' . $userIdBack;
                $length = 4;
                $needNum = 1 + 2 + 8;
//                $expireNums = [
//                  [1000,1100,365 * 24 * 60 * 60]  // 缓存的秒数365 * 24 * 60 * 60
//                ];
                break;
            case 51: // 电子发票
                $userIdBack = str_pad(substr($user_id, -2), 2, '0', STR_PAD_LEFT);
                $midFix = $userIdBack;
                $namespace = 'invoice' . $userIdBack;
                $length = 4;
                $needNum = 1 + 2 + 8;
//                $expireNums = [
//                  [1000,1100,365 * 24 * 60 * 60]  // 缓存的秒数365 * 24 * 60 * 60
//                ];
                break;
            case 2:// 2 退款订单
            case 4:// 4 追加跑腿费
            case 5:// 5 冲值
            case 6:// 6 提现
            case 7:// 7 压金或保证金
                $userIdBack = str_pad(substr($user_id, -2), 2, '0', STR_PAD_LEFT);
                $midFix = $userIdBack;
                $namespace = 'orderRefund' . $userIdBack;
                $length = 2;// 总共一秒一万
                $needNum = 4 + 8;
                $dataFormat = 'ymdHis';

//                $expireNums = [
//                  [1000,1100,365 * 24 * 60 * 60]  // 缓存的秒数365 * 24 * 60 * 60
//                ];
                break;
            default:
        }
        $fixParams = [
            'prefix' => $prefix,// 前缀[1-2位] 可填;可写业务编号等
            'midFix' => $midFix,// 日期后中间缀[1-2位] 可填;适合用户编号里的后2位或其它等
            'backfix' => $backfix,// 后缀[1-2位] 可填;备用
            'expireNums' => $expireNums,// redis设置缓存 ，在两个值之间时 - 二维 [[1,20,'缓存的秒数365 * 24 * 60 * 60'], [40,50,'缓存的秒数']]
            'needNum' => $needNum,// 需要拼接的内容 1 年 2日期 4 自定义日期格式 8 自增的序号
            'dataFormat' => $dataFormat, // needNum 值为 4时的日期格式  'YmdHis'
        ];
        return Tool::makeOrder($namespace , $fixParams, $length);
    }
}
