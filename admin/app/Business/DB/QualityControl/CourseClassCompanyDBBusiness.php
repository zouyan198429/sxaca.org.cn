<?php
// 培训班企业管理
namespace App\Business\DB\QualityControl;

use App\Services\DB\CommonDB;

/**
 *
 */
class CourseClassCompanyDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\CourseClassCompany';
    public static $table_name = 'course_class_company';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];

    /**
     * 处理分班企业表的及缴费状态
     *
     * @param int  $company_id 企业id
     * @param int $organize_id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param int $id 分班企业表id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 修改的数量   //  array 记录id值，--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function updatePay($company_id, $organize_id = 0, $id = 0, $operate_staff_id = 0, $modifAddOprate = 0, $operate_staff_id_history = 0){
        $modifyNum = 0;
        // 获得订单主表
        $classCompanyInfo = static::getDBFVFormatList(4, 1, ['id' => $id]);
        if(empty($classCompanyInfo)) throws('分班企业[' . $id . ']信息不存在！');
        // if(!in_array($classCompanyInfo['company_status'], [1])) throws('班级[' . $id . ']非正常状态，不能分配学员！');

        // 获得学员信息
        $orderStaffList = CourseOrderStaffDBBusiness::getDBFVFormatList(1, 1, [
            'course_order_id' => $classCompanyInfo['course_order_id'],
            'class_id' => $classCompanyInfo['class_id'],
            'class_company_id' => $id
        ],false);
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
        $new_pay_status = 1;// $classCompanyInfo['pay_status'];
        $new_join_class_status = 1;// $classCompanyInfo['join_class_status'];
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
        if($new_pay_status == $classCompanyInfo['pay_status']) return $modifyNum;//  && $new_join_class_status == $classCompanyInfo['join_class_status']

        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());

        CommonDB::doTransactionFun(function() use(&$company_id, &$organize_id, &$id, &$operate_staff_id, &$modifAddOprate
            , &$modifyNum, &$ownProperty, &$temNeedStaffIdOrHistoryId, &$operate_staff_id_history, &$classCompanyInfo, &$orderStaffList, &$new_pay_status, &$new_join_class_status){


            if($temNeedStaffIdOrHistoryId && $modifAddOprate){
                $temInfo = [];
                static::addOprate($temInfo, $operate_staff_id,$operate_staff_id_history, 1);
            }

            $update = [
                'pay_status' => $new_pay_status,//  缴费状态(1待缴费、2部分缴费、4已缴费、8部分退费、16已退费 )
               // 'join_class_status' => $new_join_class_status,// 分班状态(1待分班、2部分分班、4已分班)
            ];
            static::replaceById($update, $company_id, $id, $operate_staff_id, $modifAddOprate);
            // 记录日志
            CourseLogDBBusiness::saveCourseLog($classCompanyInfo['course_id'], $classCompanyInfo['course_order_id'], $classCompanyInfo['class_id'], $id, 0,
                '更新缴费状态', $operate_staff_id, $operate_staff_id_history);
            $modifyNum = 1;

        });
        return $modifyNum;

    }


    /**
     * 订单完成支付时，对相关数据的处理--单条记录
     *
     * @param int  $company_id 企业id
     * @param int  $class_company_id 报名企业记录id
     * @param int  $orderItemNum 订单报人学员数量
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  null
     * @author zouyan(305463219@qq.com)
     */
    public static function finishPay($company_id, $class_company_id = 0, $orderItemNum = 0, $operate_staff_id = 0, $modifAddOprate = 0){
        if($class_company_id <= 0) return ;
        $classCompanyInfo = static::getInfo($class_company_id);
        if(empty($classCompanyInfo))  throws('报名分班企业记录不存在！');
        // 获得待缴费的人员数量
        $temCourseOrderStaffNum = CourseOrderStaffDBBusiness::getDBFVFormatList(8, 1, ['class_company_id' => $class_company_id, 'pay_status' => [1,2], 'staff_status' => [1,8]]);
        $temSave = [
            // 'pay_status' => 2, //  缴费状态(1待缴费、2部分缴费、4已缴费、8部分退费、16已退费 )
//            'pay_date' => date('Y-m-d'),
//            'pay_time' => date('Y-m-d H:i:s'),
        ];
        $temPayStatus = $classCompanyInfo['pay_status'];
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
        static::saveById($temSave, $class_company_id);
    }
}
