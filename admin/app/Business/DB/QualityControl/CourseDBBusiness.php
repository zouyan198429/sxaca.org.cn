<?php
// 课程管理
namespace App\Business\DB\QualityControl;

use App\Services\DB\CommonDB;
use App\Services\Response\Data\CommonAPIFromDBBusiness;
use App\Services\Tool;

/**
 *
 */
class CourseDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\Course';
    public static $table_name = 'course';// 表名称
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

        if(isset($saveData['course_name']) && empty($saveData['course_name'])  ){
            throws('课程名称不能为空！');
        }

        if(isset($saveData['price_member']) && !is_numeric($saveData['price_member'])  ){
            throws('收费标准(会员)格式不正确！');
        }

        if(isset($saveData['price_general']) && !is_numeric($saveData['price_general'])  ){
            throws('收费标准(非会员)格式不正确！');
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

        // 是否有详细内容
        $hasSourseContent = false;
        $SourseContent = '';
        Tool::getInfoUboundVal($saveData, 'course_content', $hasSourseContent, $SourseContent, 1);

        $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表
        // 保存前的处理
        static::replaceByIdAPIPre($saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        $modelObj = null;
        //*********************************************************
        $isModify = false;
        CommonDB::doTransactionFun(function() use(&$saveData, &$company_id, &$id, &$operate_staff_id, &$modifAddOprate, &$operate_staff_id_history
            , &$modelObj, &$isModify, &$hasResource, &$resourceIds, &$hasSourseContent, &$SourseContent ){


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
//                    $resourceArr = ['column_type' => 8, 'column_id' => $id];
//                    ResourceDBBusiness::saveByIds($resourceArr, $resourceIds);
//                }
                // ResourceDBBusiness::bathResourceSync(static::thisObj(), 8, [['id' => $id, 'resourceIds' => $resourceIds, 'otherData' => []]], $operate_staff_id, $operate_staff_id_history);
                ResourceDBBusiness::resourceSync(static::thisObj(), 8, $id, $resourceIds, [], $operate_staff_id, $operate_staff_id_history);
            }
            if($isModify && ($ownProperty & 1) == 1){// 1：有历史表 ***_history;
                static::compareHistory($id, 1);
            }

            // 如果是新加，则记录注册记录
            if($hasSourseContent) {
                $contentObj = null;
                CourseContentDBBusiness::updateOrCreate($contentObj, ['course_id' => $id], ['course_content' => $SourseContent] );
            }
        });
        // ************************************************
        // 保存成功后的处理
        static::replaceByIdAPISucess($isModify, $modelObj, $saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        return $id;
    }


    /**
     * 根据id删除
     *
     * @param int  $company_id 企业id
     * @param int $id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @param array $extendParams 其它参数--扩展用参数
     * @return  int 记录id值
     * @author zouyan(305463219@qq.com)
     */
    public static function delById($company_id, $id, $operate_staff_id = 0, $modifAddOprate = 0, $extendParams = []){

        if(strlen($id) <= 0){
            throws('操作记录标识不能为空！');
        }
        // 判断是不是可以删除
        $queryParams = Tool::getParamQuery(['id' => $id], ['sqlParams' =>['select' =>['id', 'join_num' ]]], []);
        $dataList = Tool::objectToArray(static::getList($queryParams,[]));
        $joinNums = Tool::getArrFields($dataList, 'join_num');
        foreach($joinNums as $join_num){
            if(!is_numeric($join_num) || $join_num > 0) throws('已有报名，不可删除！');
        }


        return CommonDB::doTransactionFun(function() use(&$company_id, &$id, &$operate_staff_id, &$modifAddOprate, &$extendParams){
            // 删除内容
            $contentObj = null;
            CourseContentDBBusiness::deleteByIds($id, $contentObj,  'course_id');
            // 删除资源及文件
            ResourceDBBusiness::delResourceByIds(static::thisObj(), $id, 8);
            // 删除记录
            static::deleteByIds($id);
            return $id;
        });
    }


}
