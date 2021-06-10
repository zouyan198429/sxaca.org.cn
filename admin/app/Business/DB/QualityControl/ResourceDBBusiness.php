<?php
// 资源
namespace App\Business\DB\QualityControl;

use App\Services\DB\CommonDB;
use App\Services\Tool;
use Illuminate\Support\Facades\DB;
/**
 *
 */
class ResourceDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\Resource';
    public static $table_name = 'resource';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = ['resource_id'];

    // 获得记录历史id
    public static function getIdHistory($mainId = 0, &$mainDBObj = null, &$historyDBObj = null){
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        return static::getHistoryId($mainDBObj, $mainId, ResourceHistoryDBBusiness::$model_name
            , ResourceHistoryDBBusiness::$table_name, $historyDBObj, ['resource_id' => $mainId], static::$ignoreFields);
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
            'resource_id' => $id,
        ];
        // $mainDBObj = null ;
        // $historyDBObj = null ;
        return static::compareHistoryOrUpdateVersion($mainDBObj, $id, ResourceHistoryDBBusiness::$model_name
            , ResourceHistoryDBBusiness::$table_name, $historyDBObj, $historySearch, static::$ignoreFields, $forceIncVersion);
    }

    /**
     * 根据id新加或修改单条数据-id 为0 新加，  > 0 ：修改对应的记录，返回记录id值
     *
     * @param array $saveData 要保存或修改的数组
     * @param int  $company_id 企业id
     * @param int $id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 记录id值
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceById($saveData, $company_id, &$id, $operate_staff_id = 0, $modifAddOprate = 0){


//        DB::beginTransaction();
//        try {
//            DB::commit();
//        } catch ( \Exception $e) {
//            DB::rollBack();
////            throws('操作失败；信息[' . $e->getMessage() . ']');
//             throws($e->getMessage());
//        }
        return CommonDB::doTransactionFun(function() use(&$saveData, &$company_id, &$id, &$operate_staff_id, &$modifAddOprate){

//        if(isset($saveData['resource_name']) && empty($saveData['resource_name'])  ){
//            throws('资源名称不能为空！');
//        }

            // 根据类型自定义id,获得类型自定义历史id
            ResourceTypeSelfDBBusiness::appendFieldIdHistory($saveData, 'type_self_id', 'type_self_id_history');


            $isModify = false;
            $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表

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
            // 新加或修改
            if($id <= 0){// 新加
                $resultDatas = static::create($saveData);
                $id = $resultDatas['id'] ?? 0;
            }else{// 修改
                $modelObj = null;
                $saveBoolen = static::saveById($saveData, $id,$modelObj);
                // $resultDatas = static::getInfo($id);
            }
            // 修改时，更新版本号
            if($isModify && ($ownProperty & 1) == 1){// 1：有历史表 ***_history;
                static::compareHistory($id, 1);
            }
            return $id;
        });
    }

    /**
     * 根据主表记录id，删除图片资源记录
     * @param object $mainObj 主表对象
     * @param  int/sting/array $id  单条记录或 多条[,号分隔] 或一维数组
     * @param  int $columnType 主表的类型标识
     * @param  int $doOperate 执行的操作 1 删除源图片[默认]
     * @return  array $resourceIds 删除的资源表的id 数组
     * @author zouyan(305463219@qq.com)
     */
    public static function delResourceByIds($mainObj, $id, $columnType, $doOperate = 1){
        $resourceIds = [];
        // 没有需要处理的
        if(!Tool::formatOneArrVals($id, [0, '0', ''])) return $resourceIds;
        if(!Tool::formatOneArrVals($columnType, [0, '0', ''])) return $resourceIds;

        $delFilesArr = [];// 删除成功，要执行的删除文件

        CommonDB::doTransactionFun(function() use(&$resourceIds, &$mainObj, &$id, &$columnType, &$delFilesArr, &$doOperate){

            foreach($id as $itemId){
                // 删除图片资源关系
                $mainObj::delResourceDetach($itemId, []);

                // 获得图片记录
//            $queryParams = [
//                'where' => [
//                    ['column_type', '=' , 3],
//                    ['column_id', '=' , $itemId],
//                    // ['main_name', 'like', '' . $main_name . '%'],
//                    // ['id', '&' , '16=16'],
//                ],
//                'select' => [
//                    'resource_url'
//                ]
//            ];
                $queryParams = Tool::getParamQuery(['column_type' => $columnType, 'column_id' => $itemId], ['sqlParams' =>['select' =>['id', 'resource_url' ]]], []);
                $resourceList = Tool::objectToArray(static::getList($queryParams,[]));
                $temResourceIds = Tool::getArrFields($resourceList, 'id');
                $resourceIds = array_merge($resourceIds, $temResourceIds);
                // 删除图片文件
                // Tool::resourceDelFile($resourceList);
                $delFilesArr = array_merge($delFilesArr, $resourceList);

                // 删除图片表
                // static::del($queryParams);
            }
            if(!empty($resourceIds)) static::deleteByIds($resourceIds);
            // 删除图片文件
            if(($doOperate & 1) == 1) Tool::resourceDelFile($delFilesArr);
            return $resourceIds;
        });
        return $resourceIds;
    }

    /**
     * 同步修改图片资源关系
     * @param object $mainObj  主表对象  例如 new CourseDBBusiness()
     * @param  int $columnType 主表的类型标识
     * @param int $id 主表记录id
     * @param array $resourceIds  关联的门资源id， 可以为空数组：代表清除
     * @param array $otherData 一维数组 关联表的其它字段值 -- 一般为空
     * @param int $operate_staff_id  操作人id
     * @param int $operate_staff_id_history 操作人历史id
     */
    public static function resourceSync($mainObj, $columnType, $id, $resourceIds, $otherData = [], $operate_staff_id = 0, $operate_staff_id_history = 0){

        CommonDB::doTransactionFun(function() use(&$mainObj, &$columnType, &$id, &$resourceIds, &$otherData, &$operate_staff_id, &$operate_staff_id_history){

            $saveData = [];
            // $mainObj::setOperateStaffIdHistory($saveData, $operate_staff_id, $operate_staff_id_history, 2);
            $mainObj::addOprate($saveData, $operate_staff_id, $operate_staff_id_history, 2);

            $mainObj::saveResourceSync($id, $resourceIds, $operate_staff_id, $operate_staff_id_history, $otherData);
            // 更新图片资源表
            if(!empty($resourceIds)) {
                $resourceArr = ['column_type' => $columnType, 'column_id' => $id];
                static::saveByIds($resourceArr, $resourceIds);
            }
        });
    }

    /**
     * 批量同步修改图片资源关系
     * @param object $mainObj  主表对象 例如 new CourseDBBusiness()
     * @param  int $columnType 主表的类型标识
     * @param array $mainKeyArr 二维数组  需要批量操作的数据
     *  [
     *     [
     *          'id' => 1,-- 主表id
     *          'resourceIds' => [23,1], -- 相关的资源id
     *          'otherData' => [],-- 此下标可选  资源中间表的其它字段值 ， 一般为空数组
     *     ]
     *  ]
     * @param int $operate_staff_id 操作人id
     * @param int $operate_staff_id_history 操作人历史id
     */
    public static function bathResourceSync($mainObj, $columnType, $mainKeyArr, $operate_staff_id = 0, $operate_staff_id_history = 0)
    {
        CommonDB::doTransactionFun(function() use(&$mainObj, &$columnType, &$mainKeyArr, &$operate_staff_id, &$operate_staff_id_history){
            $saveData = [];
            // $mainObj::setOperateStaffIdHistory($saveData, $operate_staff_id, $operate_staff_id_history, 2);
            $mainObj::addOprate($saveData, $operate_staff_id, $operate_staff_id_history, 2);
            foreach($mainKeyArr  as $info){
                $id = $info['id'];
                $resourceIds = $info['resourceIds'];
                $otherData = $info['otherData'] ?? [];
                static::resourceSync($mainObj, $columnType, $id, $resourceIds, $otherData, $operate_staff_id, $operate_staff_id_history);
            }
        });
    }
}
