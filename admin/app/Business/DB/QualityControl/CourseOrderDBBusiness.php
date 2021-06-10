<?php
// 报名企业(主表)
namespace App\Business\DB\QualityControl;

use App\Services\DB\CommonDB;
use App\Services\Tool;

/**
 *
 */
class CourseOrderDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\CourseOrder';
    public static $table_name = 'course_order';// 表名称
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
        $logContent = [$saveData, $company_id, $id, $operate_staff_id, $modifAddOprate];
        // 能力验证单次结果--报名时使用
        $course_order_staff = [];// 一维数组
        $has_course_order_staff = false;// 是否有 false:没有 ； true:有
        Tool::getInfoUboundVal($saveData, 'courseOrderStaff', $has_course_order_staff, $course_order_staff, 1);


        $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表
        // 保存前的处理
        static::replaceByIdAPIPre($saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        $modelObj = null;
        //*********************************************************
        $isModify = false;
        CommonDB::doTransactionFun(function() use(&$saveData, &$company_id, &$id, &$operate_staff_id, &$modifAddOprate,
            &$operate_staff_id_history, &$modelObj, &$isModify, &$course_order_staff, &$has_course_order_staff, &$logContent){


            // $ownProperty  自有属性值;
            // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
            list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());

            if(isset($saveData['company_id']) && is_numeric($saveData['company_id']) && $saveData['company_id'] > 0){
                $tem_company_id = $saveData['company_id'];
                $saveData['company_id_history'] = static::getStaffHistoryId($tem_company_id);;
            }
            InvoiceTemplateDBBusiness::appendFieldIdHistory($saveData, 'invoice_template_id', 'invoice_template_id_history');

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
            if($isModify && ($ownProperty & 1) == 1){// 1：有历史表 ***_history;
                static::compareHistory($id, 1);
            }
            // 报名学员
            if($has_course_order_staff){
                $sample_result_ids = CourseOrderStaffDBBusiness::updateByDataList(['course_order_id' => $id], ['course_order_id' => $id]
                    , $course_order_staff, $isModify, $operate_staff_id, $operate_staff_id_history
                    , 'id', $company_id, $modifAddOprate, []);
                // 更新课程报名的人数
                $courseId = $saveData['course_id'] ?? 0;
                if($courseId <= 0) throws('课程id参数有误！');
                $joinNum = count($course_order_staff);

                $queryParams = Tool::getParamQuery(['id' => $courseId], [], []);
                CourseDBBusiness::saveDecIncByQuery('join_num', $joinNum,  'inc', $queryParams, []);
                CourseDBBusiness::saveDecIncByQuery('wait_class_num', $joinNum,  'inc', $queryParams, []);

                // 更新报名次数
                $temCompanyId = $saveData['company_id'] ?? 0;
                if($temCompanyId <= 0) throws('company_id参数有误！');
                $queryParams = Tool::getParamQuery(['staff_id' => $temCompanyId], [], []);
                StaffExtendDBBusiness::saveDecIncByQuery('face_num', 1,  'inc', $queryParams, []);


                // 报名日志
                array_unshift($logContent, '报名:');
                CourseLogDBBusiness::saveCourseLog($saveData['course_id'], $id, 0, 0, 0,
                    $logContent, $operate_staff_id, $operate_staff_id_history);

            }
        });
        // ************************************************
        // 保存成功后的处理
        static::replaceByIdAPISucess($isModify, $modelObj, $saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        return $id;
    }

    /**
     * 处理报名主表的分班状态、及缴费状态
     *
     * @param int  $company_id 企业id
     * @param int $organize_id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param int $id 报名主表id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 修改的数量   //  array 记录id值，--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function updateClassAndPay($company_id, $organize_id = 0, $id = 0, $operate_staff_id = 0, $modifAddOprate = 0, $operate_staff_id_history = 0){
        $modifyNum = 0;
        // 获得订单主表
        $orderInfo = static::getDBFVFormatList(4, 1, ['id' => $id]);
        if(empty($orderInfo)) throws('班级[' . $id . ']信息不存在！');
        // if(!in_array($orderInfo['company_status'], [1])) throws('班级[' . $id . ']非正常状态，不能分配学员！');

        // 获得学员信息
        $orderStaffList = CourseOrderStaffDBBusiness::getDBFVFormatList(1, 1, ['course_order_id' => $id],false);
        // if(empty($orderStaffList)) throws('需要分配班级的学员不能为空！');
        $join_class_status = [];// 分班状态(1待分班、2部分分班、4已分班)
        $pay_status = [];//  缴费状态(1待缴费、2部分缴费、4已缴费、8部分退费、16已退费 )
        foreach($orderStaffList as $info){
            $tem_id = $info['id'];
            $tem_staff_status = $info['staff_status'];// 人员状态1正常4已作废8已结业
            $tem_pay_status = $info['pay_status'];//  缴费状态(1待缴费、2部分缴费、4已缴费、8部分退费、16已退费 )
            $tem_join_class_status = $info['join_class_status'];// 分班状态(1待分班、4已分班)
            // 只看正常的和已结业的
            if(in_array($tem_staff_status, [4])) continue;
            if(!in_array($tem_pay_status, $pay_status)) array_push($pay_status, $tem_pay_status);
            if(!in_array($tem_join_class_status, $join_class_status)) array_push($join_class_status, $tem_join_class_status);
        }
        $new_pay_status = 1;// $orderInfo['pay_status'];
        $new_join_class_status = 1;// $orderInfo['join_class_status'];
        if(in_array(1, $join_class_status) && in_array(4, $join_class_status)){// 部分分班
            $new_join_class_status = 2;
        }else if(!in_array(1, $join_class_status) && in_array(4, $join_class_status)){// 4已分班[所有学员]
            $new_join_class_status = 4;
        }

        if(in_array(1, $pay_status) && in_array(4, $pay_status)){// 2部分缴费
            $new_pay_status = 2;
        }elseif(!in_array(1, $pay_status) && in_array(4, $pay_status) && in_array(16, $pay_status)){// 部分退费
            $new_pay_status = 8;
        }elseif(!in_array(1, $pay_status) && in_array(4, $pay_status) && !in_array(16, $pay_status)){// 已缴费[所有]
            $new_pay_status = 4;
        }
        // 没有变化
        if($new_pay_status == $orderInfo['pay_status'] && $new_join_class_status == $orderInfo['join_class_status']) return $modifyNum;

        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());

        CommonDB::doTransactionFun(function() use(&$company_id, &$organize_id, &$id, &$operate_staff_id, &$modifAddOprate
            , &$modifyNum, &$ownProperty, &$temNeedStaffIdOrHistoryId, &$operate_staff_id_history, &$orderInfo, &$orderStaffList, &$new_pay_status, &$new_join_class_status){


            if($temNeedStaffIdOrHistoryId && $modifAddOprate){
                $temInfo = [];
                static::addOprate($temInfo, $operate_staff_id,$operate_staff_id_history, 1);
            }

            $update = [
                'pay_status' => $new_pay_status,//  缴费状态(1待缴费、2部分缴费、4已缴费、8部分退费、16已退费 )
                'join_class_status' => $new_join_class_status,// 分班状态(1待分班、2部分分班、4已分班)
            ];
            static::replaceById($update, $company_id, $id, $operate_staff_id, $modifAddOprate);
            // 记录日志
            CourseLogDBBusiness::saveCourseLog($orderInfo['course_id'], $id, 0, 0, 0,
                '更新缴费或分班状态', $operate_staff_id, $operate_staff_id_history);
            $modifyNum = 1;

        });
        return $modifyNum;

    }

    /**
     * 订单完成支付时，对相关数据的处理--单条记录
     *
     * @param int  $company_id 企业id
     * @param int  $course_order_id 报名企业记录id
     * @param int  $orderItemNum 订单报人学员数量
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  null
     * @author zouyan(305463219@qq.com)
     */
    public static function finishPay($company_id, $course_order_id = 0, $orderItemNum = 0, $operate_staff_id = 0, $modifAddOprate = 0){

        $courseOrderInfo = static::getInfo($course_order_id);
        if(empty($courseOrderInfo))  throws('报名企业记录不存在！');
        // 获得待缴费的人员数量
        $temCourseOrderStaffNum = CourseOrderStaffDBBusiness::getDBFVFormatList(8, 1, ['course_order_id' => $course_order_id, 'pay_status' => [1,2], 'staff_status' => [1,8]]);
        $temSave = [
            // 'pay_status' => 2, //  缴费状态(1待缴费、2部分缴费、4已缴费、8部分退费、16已退费 )
            'pay_date' => date('Y-m-d'),
            'pay_time' => date('Y-m-d H:i:s'),
        ];
        $temPayStatus = $courseOrderInfo['pay_status'];
        if($temPayStatus == 4) throws('不可重复缴费！');
        if($temPayStatus == 16) throws('已退费！');
        $temNewStatus = 2;
        if($temCourseOrderStaffNum <= $orderItemNum){// 都缴费了
            switch($temPayStatus){
                case 1:// 1待缴费
                case 2:// 2部分缴费
                    $temNewStatus = 4;
                    break;
                case 8:// 8部分退费
                    $temNewStatus = 8;
                    break;
                default:
                    break;
            }
        }else{
            switch($temPayStatus){
                case 1:// 1待缴费
                    $temNewStatus = 2;
                    break;
                case 2:// 2部分缴费
                    $temNewStatus = 2;
                    break;
                case 8:// 8部分退费
                    $temNewStatus = 8;
                    break;
                default:
                    break;
            }
        }
        $temSave['pay_status'] = $temNewStatus;
        static::saveById($temSave, $course_order_id);
    }

}
