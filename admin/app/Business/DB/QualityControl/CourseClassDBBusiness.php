<?php
// 培训班管理
namespace App\Business\DB\QualityControl;

use App\Services\DB\CommonDB;
use App\Services\Tool;

/**
 *
 */
class CourseClassDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\CourseClass';
    public static $table_name = 'course_class';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];

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

//        $info = static::getInfo($id);
//        if(empty($info)) throws('记录不存在');
//        $staff_id = $info['staff_id'];
        $dataListObj = null;

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
        // Tool::appendParamQuery($queryParams, $organize_id, 'company_id', [0, '0', ''], ',', false);
        $dataListObj = static::getAllList($queryParams, []);
        // $dataListObj = static::getListByIds($id);

        $dataListArr = $dataListObj->toArray();
        if(empty($dataListArr)) throws('没有需要删除的数据');
        // 用户删除要用到的
        $join_nums = array_values(array_unique(array_column($dataListArr,'join_num')));
        foreach($join_nums as $join_num){
            if($join_num > 0)   throws('班级已有分配学员，不可进行删除操作！');
        }
        foreach($dataListArr as $tInfo) {
            $class_name = $tInfo['class_name'];
            $class_status = $tInfo['class_status'];
            $join_num = $tInfo['join_num'];
            if(!in_array($class_status, [1])) throws('班级【' . $class_name . '】,非待开班状态，不可进行此操作');
        }

        CommonDB::doTransactionFun(function() use( &$id, &$organize_id, &$dataListArr){
            // 删除资源及文件
           // ResourceDBBusiness::delResourceByIds(static::thisObj(), $id, 32);

            // 删除主记录
//            $delQueryParams = [
//                'where' => [
//                    ['admin_type', $admin_type],
//                    ['issuper','<>', 1],
//                ],
//            ];
            $delQueryParams = Tool::getParamQuery([], [], []);
            Tool::appendParamQuery($delQueryParams, $id, 'id', [0, '0', ''], ',', false);
            // Tool::appendParamQuery($delQueryParams, $organize_id, 'company_id', [0, '0', ''], ',', false);
            static::del($delQueryParams);
            // static::deleteByIds($id);

        });
        return $id;
    }


    /**
     * 根据id开班 、 结业、作废单条或多条数据
     *
     * @param int  $company_id 企业id
     * @param int $organize_id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param string/array $id id 数组或字符串
     * @param int $operate_type 操作类型 1开班 、 2结业、4作废
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 修改的数量   //  array 记录id值，--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function operateStatusById($company_id, $organize_id = 0, $id = 0, $operate_type = 1, $operate_staff_id = 0, $modifAddOprate = 0){
        $modifyNum = 0;
        if(!in_array($operate_type, [1, 2, 4])) throws('参数【operate_type】值不是有效值！');
        // 没有需要处理的
        if(!Tool::formatOneArrVals($id)) return $modifyNum;

        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
        $operate_staff_id_history = 0;
        // 获得需要操作的数据
        $fieldValParams = ['id' => $id];
        // if(is_numeric($organize_id) && $organize_id > 0) $fieldValParams['company_id'] = $organize_id;
        $dataList = static::getDBFVFormatList(1, 1, $fieldValParams, false);
        if(empty($dataList))  return $modifyNum;// 没有要操作的记录，便不进行操作了

        $updateData = [];
        foreach($dataList as $tInfo){
            $class_name = $tInfo['class_name'];
            $class_status = $tInfo['class_status'];
            $join_num = $tInfo['join_num'];
            switch($operate_type) {
                case 1://  1开班
                    if(!in_array($class_status, [1])) throws('班级【' . $class_name . '】,非待开班状态，不可进行此操作');
                    break;
                case 2:// 2结业
                    if($join_num <=0) throws('班级【' . $class_name . '】,还没有学员，不可进行此操作');
                    if(!in_array($class_status, [2])) throws('班级【' . $class_name . '】,非开班中状态，不可进行此操作');
                    break;
                case 4:// 4作废
                    if(!in_array($class_status, [1, 2])) throws('班级【' . $class_name . '】,非待开班或开班中状态，不可进行此操作');
                    if(in_array($class_status, [2]) && $join_num > 0) throws('班级【' . $class_name . '】有学员' . $join_num . ' 人，不可进行此操作');
                    break;
                default:
                    break;
            }
        }

        CommonDB::doTransactionFun(function() use(&$company_id, &$organize_id, &$id, &$operate_type, &$operate_staff_id, &$modifAddOprate
            , &$modifyNum, &$ownProperty, &$temNeedStaffIdOrHistoryId, &$operate_staff_id_history, &$fieldValParams, &$updateData, &$dataList){
            if($temNeedStaffIdOrHistoryId && $modifAddOprate) static::addOprate($updateData, $operate_staff_id,$operate_staff_id_history, 2);

            $date = date('Y-m-d');
            $dateTime = date('Y-m-d H:i:s');

            $orderNoArr = [];
            switch($operate_type) {
                case 1://  1开班
                    $updateData['class_status'] = 2;
                    foreach($dataList as $tInfo){
                        $class_id = $tInfo['id'];
                        $course_id = $tInfo['course_id'];
                        // 修改培训班企业管理 状态为开班
                        CourseClassCompanyDBBusiness::save([
                            'class_status' => 2,
                        ],Tool::getParamQuery(['course_id' => $course_id, 'class_id' => $class_id, 'class_status' => [1]], [], []));
                        // 记录日志
                        CourseLogDBBusiness::saveCourseLog($course_id, 0, $class_id, 0, 0,
                            '班级开班', $operate_staff_id, $operate_staff_id_history);

                    }
                    break;
                case 2:// 2结业
                    $updateData['class_status'] = 8;
                    foreach($dataList as $tInfo){
                        $class_id = $tInfo['id'];
                        $course_id = $tInfo['course_id'];
                        $class_join_num = $tInfo['join_num'];
                        $staffQueryKV = ['course_id' => $course_id, 'class_id' => $class_id, 'staff_status' => 1];
                        $staffList = CourseOrderStaffDBBusiness::getDBFVFormatList(1, 1, $staffQueryKV, false);

                        $orderNoArr = Tool::getArrFields($staffList, 'order_no');
                        // 写日志
                        foreach($staffList as $staffInfo){
                            // 对应的已确认订单，改为完成服务状态
                            // $order_no = $staffInfo['order_no'];

                            // 记录日志
                            CourseLogDBBusiness::saveCourseLog($course_id, $staffInfo['course_order_id'], $class_id, $staffInfo['class_company_id'], $staffInfo['staff_id'],
                                '结业' , $operate_staff_id, $operate_staff_id_history);
                        }
                        // 修改班级的人员状态为已结业
                        CourseOrderStaffDBBusiness::save([
                            'staff_status' => 8,
                            'finish_date' => $date,
                            'finish_time' => $dateTime,
                        ],Tool::getParamQuery($staffQueryKV, [], []));

                        // 已结业人数 +
                        $queryParams = Tool::getParamQuery(['id' => $course_id], [], []);
                        CourseDBBusiness::saveDecIncByQuery('finish_num', $class_join_num,  'inc', $queryParams, []);
                        // 已分班人数 -
                        $queryParams = Tool::getParamQuery(['id' => $course_id], [], []);
                        CourseDBBusiness::saveDecIncByQuery('joined_class_num', $class_join_num,  'dec', $queryParams, []);
                        // 对企业报名主表进行结业处理
                        $classCompayList = CourseClassCompanyDBBusiness::getDBFVFormatList(1, 1, ['course_id' => $course_id, 'class_id' => $class_id, 'class_status' => [1,2]], false);
                        $courseOrderIds = Tool::getArrFields($classCompayList, 'course_order_id');
                        foreach($classCompayList as $classCompanyInfo){
                            $class_company_id = $classCompanyInfo['id'];
                            $class_company_join_num = $classCompanyInfo['join_num'];
                            $class_company_course_order_id = $classCompanyInfo['course_order_id'];
                            if($class_company_join_num > 0){
                                // 已结业人数 +
                                $queryParams = Tool::getParamQuery(['id' => $class_company_course_order_id], [], []);
                                CourseOrderDBBusiness::saveDecIncByQuery('finish_num', $class_company_join_num,  'inc', $queryParams, []);
                                // 已分班人数 -
                                $queryParams = Tool::getParamQuery(['id' => $class_company_course_order_id], [], []);
                                CourseOrderDBBusiness::saveDecIncByQuery('joined_class_num', $class_company_join_num,  'dec', $queryParams, []);
                            }
                            // 记录日志
                            CourseLogDBBusiness::saveCourseLog($course_id, $class_company_course_order_id, $class_id, $class_company_id, 0,
                                '班级结业' , $operate_staff_id, $operate_staff_id_history);

                        }
                        if(!empty($courseOrderIds)){
                            CourseOrderDBBusiness::save([
                                'company_status' => 8,
                                'finish_date' => $date,
                                'finish_time' => $dateTime,
                            ],Tool::getParamQuery(['id' => $courseOrderIds, 'company_status' => [1]], ['sqlParams' =>['where' => [['joined_class_num', '<=', 0]]]], []));
                        }
                        // 修改培训班企业管理 状态为已结业
                        CourseClassCompanyDBBusiness::save([
                            'class_status' => 8,
                        ],Tool::getParamQuery(['course_id' => $course_id, 'class_id' => $class_id, 'class_status' => [1,2]], [], []));

                    }
                    break;
                case 4:// 4作废
                    $updateData['class_status'] = 4;
                    foreach($dataList as $tInfo){
                        $class_id = $tInfo['id'];
                        $course_id = $tInfo['course_id'];
                        // 修改培训班企业管理 状态为作废
                        CourseClassCompanyDBBusiness::save([
                            'class_status' => 4,
                        ],Tool::getParamQuery(['course_id' => $course_id, 'class_id' => $class_id, 'class_status' => [1,2]], [], []));
                        // 记录日志
                        CourseLogDBBusiness::saveCourseLog($course_id, 0, $class_id, 0, 0,
                            '班级作废', $operate_staff_id, $operate_staff_id_history);

                    }
                    break;
                default:
                    break;
            }

            $saveQueryParams = Tool::getParamQuery($fieldValParams, [], []);
            $modifyNum = static::save($updateData, $saveQueryParams);

            if($operate_type == 2){// 结业，需要对确认订单进行完成服务操作
                $orderIsFinisArr = OrdersDBBusiness::judgeOrderIsFinish($orderNoArr);
                foreach($orderIsFinisArr as $temOrderNo => $orderJudgeInfo){
                    $isFinish = $orderJudgeInfo['is_finish'] ?? true;
                    $errStr = $orderJudgeInfo['err_str'] ?? '';
                    if($isFinish){
                        if(!empty($temOrderNo)){
                            OrdersDBBusiness::save([
                                'order_status' => 8,
                                'check_time' => $dateTime,
                            ],Tool::getParamQuery(['order_type' => 1, 'order_status' => 4, 'order_no' => $temOrderNo], [], []));
                        }
                    }
                }
            }

        });
        return $modifyNum;
    }

}
