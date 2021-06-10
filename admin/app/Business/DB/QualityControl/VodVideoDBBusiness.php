<?php
// 点播课程视频目录
namespace App\Business\DB\QualityControl;

use App\Services\DB\CommonDB;
use App\Services\Tool;

/**
 *
 */
class VodVideoDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\VodVideo';
    public static $table_name = 'vod_video';// 表名称
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

        if(isset($saveData['video_name']) && empty($saveData['video_name'])  ){
            throws('目录/视频名称不能为空！');
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

        // 是否有图片资源--视频
        $hasResourceVideo = false;
        $resourceIdsVideo = [];
        if(Tool::getInfoUboundVal($saveData, 'resourceIdsVideo', $hasResourceVideo, $resourceIdsVideo, 1)){
            // $saveData['resource_id'] = $resourceIds[0] ?? 0;// 第一个图片资源的id
        }
        // $resource_ids = $saveData['resource_ids'] ?? '';// 图片资源id串(逗号分隔-未尾逗号结束)
        // if(isset($saveData['resource_ids']))  unset($saveData['resource_ids']);
        // if(isset($saveData['resource_id']))  unset($saveData['resource_id']);

        // 是否有图片资源--附件资料
        $hasResourceCourseware = false;
        $resourceIdsCourseware = [];
        if(Tool::getInfoUboundVal($saveData, 'resourceIdsCourseware', $hasResourceCourseware, $resourceIdsCourseware, 1)){
            // $saveData['resource_id'] = $resourceIds[0] ?? 0;// 第一个图片资源的id
        }
        // $resource_ids = $saveData['resource_ids'] ?? '';// 图片资源id串(逗号分隔-未尾逗号结束)
        // if(isset($saveData['resource_ids']))  unset($saveData['resource_ids']);
        // if(isset($saveData['resource_id']))  unset($saveData['resource_id']);

        // 如果有父记录，则获得父记录
        $hasParentVideoId = false;
        $parentVideoId = 0;
        $pInfo = [];
        $infoOld = [];
        if($id > 0) $infoOld = static::getInfo($id);
        if(Tool::getInfoUboundVal($saveData, 'parent_video_id', $hasParentVideoId, $parentVideoId, 0)){
            // 如果没有修改所属id
            $infoParentVideoId = $infoOld['parent_video_id'] ?? '';
            if($infoParentVideoId === $parentVideoId){
                $hasParentVideoId = false;
            }else{
                $temParentId = $saveData['parent_video_id'] ?? 0;
                if($temParentId > 0){
                    $pInfo = static::getInfo($temParentId);
                    if(empty($pInfo)) throws('章节记录不能为空！');
                    if($pInfo['is_video'] != 1) throws('章节记录类型不正确！');
                }
            }
        }

        $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表
        // 保存前的处理
        static::replaceByIdAPIPre($saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        $modelObj = null;
        //*********************************************************
        $isModify = false;
        CommonDB::doTransactionFun(function() use(&$saveData, &$company_id, &$id, &$operate_staff_id, &$modifAddOprate, &$operate_staff_id_history
            , &$modelObj, &$isModify, &$hasResource, &$resourceIds, &$hasParentVideoId, &$parentVideoId , &$pInfo, &$infoOld, &$hasResourceVideo, &$resourceIdsVideo
            , &$hasResourceCourseware, &$resourceIdsCourseware){


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
                ResourceDBBusiness::resourceSync(static::thisObj(), 16384, $id, $resourceIds, [], $operate_staff_id, $operate_staff_id_history);
            }
            // 同步修改图片资源关系--视频
            if($hasResourceVideo){
//                static::saveResourceSync($id, $resourceIds, $operate_staff_id, $operate_staff_id_history, []);
//                // 更新图片资源表
//                if(!empty($resourceIds)) {
//                    $resourceArr = ['column_type' => 8, 'column_id' => $id];
//                    ResourceDBBusiness::saveByIds($resourceArr, $resourceIds);
//                }
                // ResourceDBBusiness::bathResourceSync(static::thisObj(), 8, [['id' => $id, 'resourceIds' => $resourceIds, 'otherData' => []]], $operate_staff_id, $operate_staff_id_history);
                ResourceDBBusiness::resourceSync(static::thisObj(), 32768, $id, $resourceIdsVideo, [], $operate_staff_id, $operate_staff_id_history);
            }
            // 同步修改图片资源关系--附件资料
            if($hasResourceCourseware){
//                static::saveResourceSync($id, $resourceIds, $operate_staff_id, $operate_staff_id_history, []);
//                // 更新图片资源表
//                if(!empty($resourceIds)) {
//                    $resourceArr = ['column_type' => 8, 'column_id' => $id];
//                    ResourceDBBusiness::saveByIds($resourceArr, $resourceIds);
//                }
                // ResourceDBBusiness::bathResourceSync(static::thisObj(), 8, [['id' => $id, 'resourceIds' => $resourceIds, 'otherData' => []]], $operate_staff_id, $operate_staff_id_history);
                ResourceDBBusiness::resourceSync(static::thisObj(), 65536, $id, $resourceIdsCourseware, [], $operate_staff_id, $operate_staff_id_history);
            }
            //
            if($hasParentVideoId){
                $ids = $pInfo['ids'] ?? '';
                if(empty($ids)) $ids = ',';
                $ids .= $id . ',';
                $idsArr = explode(',', $ids);
                $saveParams = [
                    'ids' => $ids,
                    'level_no' => count($idsArr) - 2,
                ];
                static::saveById($saveParams, $id);
                // 如果是修改，则如果有子类，则还需要修改子类信息
                if($isModify){
                    $idsOld = $infoOld['ids'] ?? '';
                    $childList = static::getDBFVFormatList(1, 1, [], true, [], [
                        'sqlParams' => [
                            'where' => [
                                ['ids', 'like', '' . $idsOld . '%']
                            ],
                            // 'orderBy' =>['sort_num' => 'desc', 'id' => 'desc']
                        ]
                    ]);
                    foreach($childList as $temInfo){
                        $temIds = $temInfo['ids'];
                        $temIds = str_replace($idsOld,$ids, $temIds);
                        $temIdsArr = explode(',', $temIds);
                        static::saveById(['ids' => $temIds, 'level_no' => count($temIdsArr) - 2], $temInfo['id']);
                    }
                }
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
        // 判断是不是可以删除--有子节点就不能删除
        $videoList = static::getDBFVFormatList(1, 1, ['parent_video_id' => $id],false);
        if(!empty($videoList)){
            // $vodsFormatList = Tool::arrUnderReset($videoList, 'vod_type_id', 2, '_');
            $vodParentIds = Tool::getArrFields($videoList, 'parent_video_id');
            throws('记录【' . implode(',', $vodParentIds) . '】还有子章节或课件，不可删除！<br/>您可以删除子章节或课件后再操作！');
        }

        return CommonDB::doTransactionFun(function() use(&$company_id, &$id, &$operate_staff_id, &$modifAddOprate, &$extendParams){
            // 删除资源及文件
            ResourceDBBusiness::delResourceByIds(static::thisObj(), $id, 16384);
            // 删除资源及文件--视频
            ResourceDBBusiness::delResourceByIds(static::thisObj(), $id, 32768);
            // 删除资源及文件--附件资料
            ResourceDBBusiness::delResourceByIds(static::thisObj(), $id, 65536);
            // 删除记录
            static::deleteByIds($id);
            return $id;
        });
    }
}
