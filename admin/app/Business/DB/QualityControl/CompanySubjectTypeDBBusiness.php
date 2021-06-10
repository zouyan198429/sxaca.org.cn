<?php
// 试题分类[一级分类]
namespace App\Business\DB\QualityControl;

use App\Services\DB\CommonDB;
use App\Services\Tool;

/**
 *
 */
class CompanySubjectTypeDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\CompanySubjectType';
    public static $table_name = 'company_subject_type';// 表名称
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

        // 分类编号--唯一
        if( isset($saveData['type_no']) && !isset($saveData['company_id'])){
            throws('公司或企业id不能为空！');
        }
        if( isset($saveData['type_no']) && !Tool::isBitNum($saveData['type_no'], 63)){
            throws('分类编号不是用效格式【1、2、4...】！');
        }
        if( isset($saveData['type_no']) && static::judgeFieldExist($company_id, $id ,"type_no", $saveData['type_no'], [['company_id', $saveData['company_id']]],1)){
            throws('分类编号已存在！');
        }
        // 判断是否已经使用---修改记录时【分类编号有变化】
        if(isset($saveData['type_no']) && $id > 0 ){
            $info = static::getInfo($id);
            if(empty($info)) throws('记录不存在！');
            if($info['type_no'] != $saveData['type_no'] && static::judgeTypeNoUsed($saveData['type_no'], $saveData['company_id'])){
                throws('分类编号已使用，不可变更！');
            }
        }

        // 修改时 需要强制更新数量
        $forceCompanyNum =  false;
        $force_company_num = '';
        $companyNumIds = [];// 需要更新的企业id数组
        if(Tool::getInfoUboundVal($saveData, 'force_company_num', $forceCompanyNum, $force_company_num, 1)){
            if(isset($saveData['company_id']) && is_numeric($saveData['company_id']) && $saveData['company_id'] > 0 ){
                array_push($companyNumIds, $saveData['company_id']);
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
                    $tem_company_id = $info_old['company_id'];
                    if($tem_company_id > 0 && !in_array($tem_company_id, $companyNumIds)) array_push($companyNumIds, $tem_company_id);

                }
                $saveBoolen = static::saveById($saveData, $id,$modelObj);
                // $resultDatas = static::getInfo($id);
                // 修改数据，是否当前版本号 + 1
                // 1：有历史表 ***_history;
                // if(($ownProperty & 1) == 1) static::compareHistory($id, 1);
            }
            if($isModify && ($ownProperty & 1) == 1){// 1：有历史表 ***_history;
                static::compareHistory($id, 1);
            }
            // 如果是新加，则记录注册记录
            if(!$isModify){
                // 如果是新加，所需要更新企业能力范围数量
                // 注意，如果是批量操作，不在这里处理，在批量的业务地方再处理此功能
                // if(!$isBatchOperate && is_numeric($resultDatas['company_id']) && $resultDatas['company_id'] > 0){
                //    StaffDBBusiness::updateInvoiceAddrNum($resultDatas['company_id']);
                // }
                if(!$isBatchOperate && isset($saveData['company_id']) && is_numeric($saveData['company_id']) && $saveData['company_id'] > 0){
                    // StaffDBBusiness::updateSubjectTypeNum($saveData['company_id']);

                    StaffDBBusiness::updateExtendNum($saveData['company_id'], 'subject_type');
                }
            }else if($forceCompanyNum && !empty($companyNumIds)){// 修改时 需要强制更新数量
                // StaffDBBusiness::updateSubjectTypeNum($companyNumIds);

                StaffDBBusiness::updateExtendNum($companyNumIds, 'subject_type');
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
     *  [
     *       'organize_id' => 3,操作的企业id 可以为0：不指定具体的企业
     *       'doOperate' => 1,执行的操作 0 不删除 1 删除源图片[默认]
     *  ]
     * @return  int 记录id值
     * @author zouyan(305463219@qq.com)
     */
    public static function delById($company_id, $id, $operate_staff_id = 0, $modifAddOprate = 0, $extendParams = []){
        $organize_id = $extendParams['organize_id'] ?? 0;// 操作的企业id 可以为0：不指定具体的企业

        if(strlen($id) <= 0){
            throws('操作记录标识不能为空！');
        }
        $dataList = static::getDBFVFormatList(1, 1, ['id' => $id], false);
        if(empty($dataList)) throws('操作记录不存在！');

        foreach($dataList as $info){
            $t_company_id = $info['company_id'] ?? 0;// 企业 id
            if($organize_id > 0 && $organize_id != $t_company_id) throws('没有操作记录【' . $info['id'] . '】的权限');
            if(static::judgeTypeNoUsed($info['type_no'], $t_company_id)){
                throws('记录【' . $info['id'] . '】的分类编号【' . $info['type_no'] . '】已使用，不可进行删除操作！');
            }
        }

        $organizeIds = Tool::getArrFields($dataList, 'company_id');

        return CommonDB::doTransactionFun(function() use(&$company_id, &$id, &$operate_staff_id, &$modifAddOprate, &$extendParams, &$organizeIds){

            // 删除记录
            static::deleteByIds($id);
            // 删除员工--还需要重新统计企业的员工数
            if(!empty($organizeIds)){
                foreach($organizeIds as $organizeId){
                    // 根据企业id更此记录数
                    // StaffDBBusiness::updateSubjectTypeNum($organizeId);;
                    StaffDBBusiness::updateExtendNum($organizeId, 'subject_type');
                }
            }
            return $id;
        });
    }

    /**
     * 判断分类编号是否已经使用
     *
     * @param int $type_no 分类编号值
     * @param int  $company_id 企业id
     * @return  boolean 已经使用 true; 未使用false
     * @author zouyan(305463219@qq.com)
     */
    public static function judgeTypeNoUsed($type_no, $company_id = 0){

        // 判断在试题中是否使用
        $subjectInfo = CompanySubjectDBBusiness::getDBFVFormatList(4, 1, ['company_id' => $company_id], false, [], [
            'sqlParams' => [
                'where' => [
                    ['type_no', '&' , $type_no . ' > 0'],
                ]
            ]
        ]);
        if(!empty($subjectInfo)) return true;
        // 判断在试卷中是否使用
        $paperInfo = CompanyPaperDBBusiness::getDBFVFormatList(4, 1, ['company_id' => $company_id], false, [], [
            'sqlParams' => [
                'where' => [
                    ['type_no', '&' , $type_no . ' > 0'],
                ]
            ]
        ]);
        if(!empty($paperInfo)) return true;
        // 判断在考次中是否使用
        $examInfo = CompanyExamDBBusiness::getDBFVFormatList(4, 1, ['company_id' => $company_id], false, [], [
            'sqlParams' => [
                'where' => [
                    ['type_no', '&' , $type_no . ' > 0'],
                ]
            ]
        ]);
        if(!empty($examInfo)) return true;
        // 判断在考次人员中是否使用
        $examStaffInfo = CompanyExamStaffDBBusiness::getDBFVFormatList(4, 1, ['company_id' => $company_id], false, [], [
            'sqlParams' => [
                'where' => [
                    ['type_no', '&' , $type_no . ' > 0'],
                ]
            ]
        ]);
        if(!empty($examStaffInfo)) return true;
        return false;

    }

}
