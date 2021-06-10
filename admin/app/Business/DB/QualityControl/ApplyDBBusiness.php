<?php
// 应用管理
namespace App\Business\DB\QualityControl;

use App\Services\DB\CommonDB;
use App\Services\Tool;

/**
 *
 */
class ApplyDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\Apply';
    public static $table_name = 'apply';// 表名称
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

        if(isset($saveData['app_name']) && empty($saveData['app_name'])  ){
            throws('应用名称不能为空！');
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


        // 修改时 需要强制更新员工数量
        $forceCompanyNum =  false;
        $force_company_num = '';
        $companyNumIds = [];// 需要更新的企业id数组
        if(Tool::getInfoUboundVal($saveData, 'force_company_num', $forceCompanyNum, $force_company_num, 1)){
            if(isset($saveData['staff_id']) && is_numeric($saveData['staff_id']) && $saveData['staff_id'] > 0 ){
                array_push($companyNumIds, $saveData['staff_id']);
            }
        }

        // 是否批量操作标识 true:批量操作； false:单个操作 ---因为如果批量操作，有些操作就不能每个操作都执行，也要批量操作---为了运行效率
        // 有此下标就代表批量操作
        $isBatchOperate = false;
        $isBatchOperateVal = '';
        Tool::getInfoUboundVal($saveData, 'isBatchOperate', $isBatchOperate, $isBatchOperateVal, 1);


        $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表
        // 保存前的处理
        static::replaceByIdAPIPre($saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        $modelObj = null;
        //*********************************************************
        $isModify = false;
        CommonDB::doTransactionFun(function() use(&$saveData, &$company_id, &$id, &$operate_staff_id, &$modifAddOprate, &$operate_staff_id_history, &$modelObj, &$isModify
                                                    , &$hasResource, &$resourceIds
                                                    , &$forceCompanyNum, &$force_company_num, &$companyNumIds, &$isBatchOperate, &$isBatchOperateVal){


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
                if($forceCompanyNum){
                    $info_old = static::getInfo($id);
                    $tem_staff_id = $info_old['staff_id'];
                    if($tem_staff_id > 0 && !in_array($tem_staff_id, $companyNumIds)) array_push($companyNumIds, $tem_staff_id);

                }
                $saveBoolen = static::saveById($saveData, $id,$modelObj);
                 $resultDatas = static::getInfo($id);
                // 修改数据，是否当前版本号 + 1
                // 1：有历史表 ***_history;
                // if(($ownProperty & 1) == 1) static::compareHistory($id, 1);
            }
            // 同步修改图片资源关系
            if($hasResource){
//                static::saveResourceSync($id, $resourceIds, $operate_staff_id, $operate_staff_id_history, []);
//                // 更新图片资源表
//                if(!empty($resourceIds)) {
//                    $resourceArr = ['column_type' => 16, 'column_id' => $id];
//                    ResourceDBBusiness::saveByIds($resourceArr, $resourceIds);
//                }
                // ResourceDBBusiness::bathResourceSync(static::thisObj(), 16, [['id' => $id, 'resourceIds' => $resourceIds, 'otherData' => []]], $operate_staff_id, $operate_staff_id_history);
                ResourceDBBusiness::resourceSync(static::thisObj(), 256, $id, $resourceIds, [], $operate_staff_id, $operate_staff_id_history);
            }
            if($isModify && ($ownProperty & 1) == 1){// 1：有历史表 ***_history;
                static::compareHistory($id, 1);
            }
            // 如果是新加，则记录注册记录
            if(!$isModify){
                // 如果是新加，所需要更新企业 应用数量
                // 注意，如果是批量操作，不在这里处理，在批量的业务地方再处理此功能
                if(!$isBatchOperate && is_numeric($resultDatas['staff_id']) && $resultDatas['staff_id'] > 0){
                    StaffDBBusiness::updateApplyNum($resultDatas['staff_id']);
                }
            }else if($forceCompanyNum && !empty($companyNumIds)){// 修改时 需要强制更新企业 应用数量
                StaffDBBusiness::updateApplyNum($companyNumIds);
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
     *       'doOperate' => 1,执行的操作 0 不删除 1 删除源图片[默认]
     *  ]
     * @return  int 记录id值
     * @author zouyan(305463219@qq.com)
     */
    public static function delById($company_id, $id, $operate_staff_id = 0, $modifAddOprate = 0, $extendParams = []){
        $organize_id = $extendParams['organize_id'] ?? 0;// 操作的企业id 可以为0：不指定具体的企业
        $doOperate = $extendParams['doOperate'] ?? 1;

        if(strlen($id) <= 0){
            throws('操作记录标识不能为空！');
        }

//        $info = static::getInfo($id);
//        if(empty($info)) throws('记录不存在');
//        $staff_id = $info['staff_id'];
        $dataListObj = null;
        $organizeIds = [];

        // 获得需要删除的数据
//            $queryParams = [
//                'where' => [
////                ['company_id', $organize_id],
//                ['admin_type', $admin_type],
////                ['teacher_status',1],
//                ],
//                // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
//            ];
        $queryParams = Tool::getParamQuery([], [], []);
        Tool::appendParamQuery($queryParams, $id, 'id', [0, '0', ''], ',', false);
        Tool::appendParamQuery($queryParams, $organize_id, 'staff_id', [0, '0', ''], ',', false);
        $dataListObj = static::getAllList($queryParams, []);
        // $dataListObj = static::getListByIds($id);

        $dataListArr = $dataListObj->toArray();
        if(empty($dataListArr)) throws('没有需要删除的数据');
        // 用户删除要用到的
        $organizeIds = array_values(array_unique(array_column($dataListArr,'staff_id')));
//        DB::beginTransaction();
//        try {
//            DB::commit();
//        } catch ( \Exception $e) {
//            DB::rollBack();
//            throws($e->getMessage());
//            // throws($e->getMessage());
//        }
        CommonDB::doTransactionFun(function() use( &$id, &$organize_id, &$organizeIds, &$doOperate){
            // 删除资源及文件
            ResourceDBBusiness::delResourceByIds(static::thisObj(), $id, 256, $doOperate);

            // 删除主记录
//            $delQueryParams = [
//                'where' => [
//                    ['admin_type', $admin_type],
//                    ['issuper','<>', 1],
//                ],
//            ];
            $delQueryParams = Tool::getParamQuery([], [], []);
            Tool::appendParamQuery($delQueryParams, $id, 'id', [0, '0', ''], ',', false);
            Tool::appendParamQuery($delQueryParams, $organize_id, 'staff_id', [0, '0', ''], ',', false);
            static::del($delQueryParams);
            // static::deleteByIds($id);
            // 删除员工--还需要重新统计企业的员工数
            if(!empty($organizeIds)){
                foreach($organizeIds as $organizeId){
                    // 根据企业id更企业 应用数
                    StaffDBBusiness::updateApplyNum($organizeId);;
                }
            }
        });
        return $id;
    }

    /**
     * 根据id审核通过或不通过单条或多条数据
     *
     * @param int  $company_id 企业id
     * @param int $organize_id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param string/array $id id 数组或字符串
     * @param int $open_status 操作 状态 2审核通过     4审核不通过
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 修改的数量   //  mixed array 记录id值，--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function openStatusById($company_id, $organize_id = 0, $id = 0, $open_status = 2, $operate_staff_id = 0, $modifAddOprate = 0){
        $modifyNum = 0;
        if(!in_array($open_status, [2,4])) throws('参数【open_status】值不是有效值！');
        // 没有需要处理的
        if(!Tool::formatOneArrVals($id)) return $modifyNum;

        $updateData = [
            'open_status' => $open_status
        ];
        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
        $operate_staff_id_history = 0;
//        DB::beginTransaction();
//        try {
//        } catch ( \Exception $e) {
//            DB::rollBack();
////            throws('操作失败；信息[' . $e->getMessage() . ']');
//            throws($e->getMessage());
//        }
        CommonDB::doTransactionFun(function() use(&$company_id, &$organize_id, &$id, &$open_status, &$operate_staff_id, &$modifAddOprate
            , &$modifyNum, &$updateData, &$ownProperty, &$temNeedStaffIdOrHistoryId, &$operate_staff_id_history){
            if($temNeedStaffIdOrHistoryId && $modifAddOprate) static::addOprate($updateData, $operate_staff_id,$operate_staff_id_history, 2);
//            $saveQueryParams = [
//                'where' => [
//                    ['open_status', 1], // 自由点，让他都可以改 ，就注释掉
//                    ['issuper', '<>' , 1],
//                    ['admin_type', $admin_type],
//                ],
////                            'select' => [
////                                'id','title','sort_num','volume'
////                                ,'operate_staff_id','operate_staff_id_history'
////                                ,'created_at' ,'updated_at'
////                            ],
//
//                //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
//            ];
            $saveQueryParams = Tool::getParamQuery(['open_status' => 1],[], []);
            // 加入 id
            Tool::appendParamQuery($saveQueryParams, $id, 'id');
            Tool::appendParamQuery($saveQueryParams, $organize_id, 'staff_id', [0, '0', ''], ',', false);
            $modifyNum = static::save($updateData, $saveQueryParams);
        });
        return $modifyNum;
    }


    /**
     * 根据企业id,获得企业的机构自我声明数
     *
     * @param int  $company_id 企业id
     * @return  mixed  应用数
     * @author zouyan(305463219@qq.com)
     */
    public static function getApplyCount($company_id = 0){
        // 更新班级老师人数
//        $queryParams = [
//            'where' => [
//                ['staff_id', $company_id],
//                ['admin_type', 4],
////                ['open_status', 2],
////                ['account_status',1],
//            ],
//            'count' => 0,
//            // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
//        ];
//        $queryParams = Tool::getParamQuery(['staff_id' => $company_id], [], []);
//        $queryParams['count'] = 0;
//        $count = static::getAllList($queryParams, []);
//        return $count;
        return static::getDBFVFormatList(8, 1, ['staff_id' => $company_id], false);
    }

}
