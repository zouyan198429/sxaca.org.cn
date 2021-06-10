<?php
// 通知公告管理
namespace App\Business\DB\QualityControl;

use App\Services\DB\CommonDB;
use App\Services\Tool;

/**
 *
 */
class PlatformNoticesDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\PlatformNotices';
    public static $table_name = 'platform_notices';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];


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

        if(isset($saveData['resource_name']) && empty($saveData['resource_name'])  ){
            throws('资源名称不能为空！');
        }

        // 是否有图片资源
        $hasResource = false;
        $resourceIds = [];
        if(Tool::getInfoUboundVal($saveData, 'resourceIds', $hasResource, $resourceIds, 1)){
            // $saveData['resource_id'] = $resourceIds[0] ?? 0;// 第一个图片资源的id
        }
        // $resource_ids = $saveData['resource_ids'] ?? '';// 图片资源id串(逗号分隔-未尾逗号结束)
        // if(isset($saveData['resource_ids']))  unset($saveData['resource_ids']);
        // if(isset($saveData['resource_id']))  unset($saveData['resource_id']);


        // 是否批量操作标识 true:批量操作； false:单个操作 ---因为如果批量操作，有些操作就不能每个操作都执行，也要批量操作---为了运行效率
        // 有此下标就代表批量操作
        $isBatchOperate = false;
        $isBatchOperateVal = '';
        Tool::getInfoUboundVal($saveData, 'isBatchOperate', $isBatchOperate, $isBatchOperateVal, 1);

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
        CommonDB::doTransactionFun(function() use(&$saveData, &$company_id, &$id, &$operate_staff_id, &$modifAddOprate, &$operate_staff_id_history, &$modelObj, &$isModify
            , &$hasResource, &$resourceIds
            , &$isBatchOperate, &$isBatchOperateVal ){


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
                $resultDatas = static::create($saveData,$modelObj);
                $id = $resultDatas['id'] ?? 0;
            }else{// 修改
                $saveBoolen = static::saveById($saveData, $id,$modelObj);
                $resultDatas = static::getInfo($id);
                // $resultDatas = static::getInfo($id);
                // 修改数据，是否当前版本号 + 1
                // 1：有历史表 ***_history;
                // if(($ownProperty & 1) == 1) static::compareHistory($id, 1);
            }
            // 同步修改图片资源关系
            if($hasResource){
//                static::saveResourceSync($id, $resourceIds, $operate_staff_id, $operate_staff_id_history, []);
//                // 更新图片资源表
//                if(!empty($resourceIds)) {
//                    $resourceArr = ['column_type' => 64, 'column_id' => $id];
//                    ResourceDBBusiness::saveByIds($resourceArr, $resourceIds);
//                }
                // ResourceDBBusiness::bathResourceSync(static::thisObj(), 64, [['id' => $id, 'resourceIds' => $resourceIds, 'otherData' => []]], $operate_staff_id, $operate_staff_id_history);
                ResourceDBBusiness::resourceSync(static::thisObj(), 64, $id, $resourceIds, [], $operate_staff_id, $operate_staff_id_history);
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
     * 根据id删除--可批量删除
     * 删除员工--还需要重新统计企业的员工数
     * 企业删除 ---有员工的企业不能删除，需要先删除/解绑员工
     * @param int  $company_id 企业id
     * @param string $id id 多个用，号分隔
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @param array $extendParams 其它参数--扩展用参数
     *  [
     *       'organize_id' => 3,操作的企业id 可以为0：不指定具体的企业
     *  ]
     * @return  int 记录id值
     * @author zouyan(305463219@qq.com)
     */
    public static function delById($company_id, $id, $operate_staff_id = 0, $modifAddOprate = 0, $extendParams = []){
        $organize_id = $extendParams['organize_id'] ?? 0;// 操作的企业id 可以为0：不指定具体的企业

        if(strlen($id) <= 0){
            throws('操作记录标识不能为空！');
        }

        CommonDB::doTransactionFun(function() use( &$id, &$organize_id){
            // 删除资源及文件
            ResourceDBBusiness::delResourceByIds(static::thisObj(), $id, 64);

            // 删除主记录
            $delQueryParams = Tool::getParamQuery([], [], []);
            Tool::appendParamQuery($delQueryParams, $id, 'id', [0, '0', ''], ',', false);
            // Tool::appendParamQuery($delQueryParams, $organize_id, 'company_id', [0, '0', ''], ',', false);
            static::del($delQueryParams);
            // static::deleteByIds($id);
        });
        return $id;
    }
}
