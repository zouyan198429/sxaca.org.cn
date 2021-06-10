<?php
// 报名学员
namespace App\Business\DB\QualityControl;

use App\Services\alipaySdk\AlipayPayAPI;
use App\Services\DB\CommonDB;
use App\Services\pay\weixin\easyWechatPay;
use App\Services\Tool;

/**
 *
 */
class CourseOrderStaffDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\CourseOrderStaff';
    public static $table_name = 'course_order_staff';// 表名称
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
        $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表
        // 保存前的处理
        static::replaceByIdAPIPre($saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        $modelObj = null;
        //*********************************************************
        $isModify = false;
        CommonDB::doTransactionFun(function() use(&$saveData, &$company_id, &$id, &$operate_staff_id, &$modifAddOprate,
            &$operate_staff_id_history, &$modelObj, &$isModify){


            // $ownProperty  自有属性值;
            // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
            list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());

            if(isset($saveData['company_id']) && is_numeric($saveData['company_id']) && $saveData['company_id'] > 0){
                $tem_company_id = $saveData['company_id'];
                $saveData['company_id_history'] = static::getStaffHistoryId($tem_company_id);;
            }
            if(isset($saveData['staff_id']) && is_numeric($saveData['staff_id']) && $saveData['staff_id'] > 0){
                $tem_staff_id = $saveData['staff_id'];
                $saveData['staff_id_history'] = static::getStaffHistoryId($tem_staff_id);;
            }
            InvoiceTemplateDBBusiness::appendFieldIdHistory($saveData, 'invoice_template_id', 'invoice_template_id_history');

            InvoiceProjectTemplateDBBusiness::appendFieldIdHistory($saveData, 'invoice_project_template_id', 'invoice_project_template_id_history');

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

        });
        // ************************************************
        // 保存成功后的处理
        static::replaceByIdAPISucess($isModify, $modelObj, $saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        return $id;
    }


    /**
     * 根据id作废或取消作废单条或多条数据
     *
     * @param int  $company_id 企业id
     * @param int $organize_id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param string/array $id id 数组或字符串
     * @param int $staff_status 操作 状态 1正常--取消作废操作； 4已作废--作废操作
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 修改的数量   //  array 记录id值，--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function staffStatusById($company_id, $organize_id = 0, $id = 0, $staff_status = 1, $operate_staff_id = 0, $modifAddOprate = 0){
        $modifyNum = 0;
        if(!in_array($staff_status, [1, 4])) throws('参数【staff_status】值不是有效值！');
        // 没有需要处理的
        if(!Tool::formatOneArrVals($id)) return $modifyNum;

        $updateData = [
            'staff_status' => $staff_status,
            'cancel_date' => date('Y-m-d'),
            'cancel_time' => date('Y-m-d H:i:s'),
        ];
        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
        $operate_staff_id_history = 0;

//            $saveQueryParams = [
//                'where' => [
//                   //  ['staff_status', 1],
//                ],
////                            'select' => [
////                                'id','title','sort_num','volume'
////                                ,'operate_staff_id','operate_staff_id_history'
////                                ,'created_at' ,'updated_at'
////                            ],
//
//                //   'orderBy' => [ 'id'=>'desc'],//'sort_num'=>'desc',
//            ];
        $saveQueryParams = [];
        $oldStaffStatus = 1;// -- 可以作废状态
        // 取消作废操作
        if($staff_status == 1) $oldStaffStatus = 4; // --可以取消作废状态
        Tool::appendParamQuery($saveQueryParams, $oldStaffStatus, 'staff_status');
        // 加入 id
        Tool::appendParamQuery($saveQueryParams, $id, 'id');
        Tool::appendParamQuery($saveQueryParams, $organize_id, 'company_id', [0, '0', ''], ',', false);
        // pr($saveQueryParams);
        // 查询到要操作的记录
        $dataList = static::getAllList($saveQueryParams, [])->toArray();
        if(empty($dataList))  return $modifyNum;// 没有要操作的记录，便不进行操作了
        CommonDB::doTransactionFun(function() use(&$company_id, &$organize_id, &$id, &$staff_status, &$operate_staff_id, &$modifAddOprate
            , &$modifyNum, &$updateData, &$ownProperty, &$temNeedStaffIdOrHistoryId, &$operate_staff_id_history, &$saveQueryParams, &$dataList){
            if($temNeedStaffIdOrHistoryId && $modifAddOprate) static::addOprate($updateData, $operate_staff_id,$operate_staff_id_history, 2);
            $courseOrderIds = [];// 作
            foreach($dataList as $info){
                $courseId = $info['course_id'];
                  $courseOrderId = $info['course_order_id'];
                if($courseOrderId > 0 && !in_array($courseOrderId, $courseOrderIds)) array_push($courseOrderIds, $courseOrderId);
                $logContent = [];
                if($staff_status == 1) {// 取消作废
                    // 课程表
                    $queryParams = Tool::getParamQuery(['id' => $courseId], [], []);
                    CourseDBBusiness::saveDecIncByQuery('wait_class_num', 1,  'inc', $queryParams, []);
                    CourseDBBusiness::saveDecIncByQuery('cancel_num', 1,  'dec', $queryParams, []);
                    // 报名主表
                    $queryParams = Tool::getParamQuery(['id' => $courseOrderId], [], []);
                    CourseOrderDBBusiness::saveDecIncByQuery('cancel_num', 1,  'dec', $queryParams, []);
                    array_push($logContent , '取消作废');
                }else{// 作废
                    // 课程表
                    $queryParams = Tool::getParamQuery(['id' => $courseId], [], []);
                    CourseDBBusiness::saveDecIncByQuery('wait_class_num', 1,  'dec', $queryParams, []);
                    CourseDBBusiness::saveDecIncByQuery('cancel_num', 1,  'inc', $queryParams, []);
                    // 报名主表
                    $queryParams = Tool::getParamQuery(['id' => $courseOrderId], [], []);
                    CourseOrderDBBusiness::saveDecIncByQuery('cancel_num', 1,  'inc', $queryParams, []);
                    array_push($logContent , '作废');

                }
                // 记录日志
                // array_unshift($logContent, '报名:');
                CourseLogDBBusiness::saveCourseLog($courseId, $info['course_order_id'], $info['class_id'], $info['class_company_id'], $info['id'],
                    $logContent, $operate_staff_id, $operate_staff_id_history);
            }
            // 如果企业报名的所有人员都作废了，则修改企业报名状态及记录时间
            if(!empty($courseOrderIds)){
                $OrderList = CourseOrderDBBusiness::getDBFVFormatList(1, 1, ['id' => $courseOrderIds], false);
                foreach($OrderList as $info){
                    $temId = $info['id'];
                    $tem_course_id = $info['course_id'];
                    $join_num = $info['join_num'];
                    $cancel_num = $info['cancel_num'];
                    $company_status = $info['company_status'];
                    $joined_class_num = $info['joined_class_num'];
                    $pay_status = $info['pay_status'];
                    $tem_company_status = $info['company_status'];
                    if($staff_status == 1) {// 取消作废
                        if($tem_company_status == 4){// 作废 -> 正常
                            CourseOrderDBBusiness::saveById(['company_status' => 1], $temId);
                        }elseif($tem_company_status == 8){// 已结业 ->正常
                            throws('已结业,不可进行【取消作废】操作');
                        }
                    }else {// 作废
                        if($join_num <= $cancel_num){// 正常->作废
                            CourseOrderDBBusiness::saveById(['company_status' => 4, 'cancel_date' => date('Y-m-d'), 'cancel_time' => date('Y-m-d H:i:s')], $temId);
                        }elseif ( $join_num <= ($cancel_num + $joined_class_num)){// 已经有分班，如果班级都结束，则进入已结业 -- 不能结业，只能再班级点结业时才结业
                            $classCompanyList = CourseClassCompanyDBBusiness::getDBFVFormatList(1, 1, ['course_id' => $tem_course_id, 'course_order_id' => $temId, 'class_status' => [1,2]], false);
                            if(empty($classCompanyList)){
                                CourseOrderDBBusiness::saveById(['company_status' => 8, 'finish_date' => date('Y-m-d'), 'finish_time' => date('Y-m-d H:i:s')], $temId);
                            }
                        }
                    }
                }
            }

            $modifyNum = static::save($updateData, $saveQueryParams);
//            DB::commit();
        });
        return $modifyNum;
    }

    /**
     * 学员分配班级
     *
     * @param int  $company_id 企业id
     * @param int $organize_id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param string/array $ids 数组或字符串 学员id
     * @param int $class_id 分配到班级的id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 修改的数量   //  array 记录id值，--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function joinClassById($company_id, $organize_id = 0, $ids = 0, $class_id = 0, $operate_staff_id = 0, $modifAddOprate = 0){
        $modifyNum = 0;
        // 对学员id参数进行转换
        Tool::formatOneArrVals($ids);
        if(empty($ids)) throws('需要分配班级的学员不能为空！');

        // 获得班级信息
        $classInfo = CourseClassDBBusiness::getDBFVFormatList(4, 1, ['id' => $class_id]);
        if(empty($classInfo)) throws('班级信息不存在！');
        if(!in_array($classInfo['class_status'], [1, 2])) throws('班级非待开班或开班中状态，不能分配学员！');

        $course_id = $classInfo['course_id'];// 课程id

        // 获得学员信息
        $orderStaffList = static::getDBFVFormatList(1, 1, ['id' => $ids, 'course_id' => $course_id],false);
        if(empty($orderStaffList)) throws('需要分配班级的学员不能为空！');
        foreach($orderStaffList as $info){
            $tem_id = $info['id'];
            $tem_staff_status = $info['staff_status'];
            $tem_pay_status = $info['pay_status'];
            $tem_join_class_status = $info['join_class_status'];
            if($tem_join_class_status != 1 || $tem_staff_status != 1){
                throws('记录【' . $tem_id . '】非待分班且正常状态的人员，不可以进行此操作');
            }
        }
        // 按主表id格式化数据
        $formatOrderStaffList = Tool::arrUnderReset($orderStaffList, 'course_order_id', 2, '_');

        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
        $operate_staff_id_history = 0;
        CommonDB::doTransactionFun(function() use(&$company_id, &$organize_id, &$ids, &$class_id, &$operate_staff_id, &$modifAddOprate
            , &$modifyNum, &$ownProperty, &$temNeedStaffIdOrHistoryId, &$operate_staff_id_history, &$classInfo, &$orderStaffList, &$formatOrderStaffList, &$course_id){

            if($temNeedStaffIdOrHistoryId && $modifAddOprate){
                $temInfo = [];
                static::addOprate($temInfo, $operate_staff_id,$operate_staff_id_history, 1);
            }

            // 开始分配班级
            foreach($formatOrderStaffList as $courseOrderId => $vList){
                // 获得订单主表
                $orderInfo = CourseOrderDBBusiness::getDBFVFormatList(4, 1, ['id' => $courseOrderId]);
                if(empty($orderInfo)) throws('班级[' . $courseOrderId . ']信息不存在！');
                if(!in_array($orderInfo['company_status'], [1])) throws('班级[' . $courseOrderId . ']非正常状态，不能分配学员！');
                // 培训班企业信息处理
                $classCompanyQuery = ['course_id' => $course_id, 'class_id' => $class_id, 'course_order_id' => $courseOrderId];
                $classCompanyInfo = CourseClassCompanyDBBusiness::getDBFVFormatList(4, 1, $classCompanyQuery, false, [], [
                    'sqlParams' => ['whereIn' =>['class_status' =>[1,2]]]
                ]);
                $classCompanyId = $classCompanyInfo['id'] ?? 0;

                $tem_join_num = $classCompanyInfo['join_num'] ?? 0;
                $tem_class_status = $classCompanyInfo['class_status'] ?? 0;
                $addClassCompany = [
                    // 'course_id' => $course_id,
                    // 'course_order_id' => $courseOrderId,
//                    'admin_type' => $orderInfo['admin_type'],
//                    'company_id' => $orderInfo['company_id'],
//                    'company_id_history' => $orderInfo['company_id_history'],
                    // 'class_id' => $class_id,
                    'join_num' => $tem_join_num + count($vList),
                    // 'pay_status' => '',//  缴费状态(1待缴费、2部分缴费、4已缴费、8部分退费、16已退费 )
                    // 'class_status' => 2,// 班级状态1待开班2开班中4已作废8已结业
                ];
                if($classCompanyId <= 0) $addClassCompany = array_merge($addClassCompany, $classCompanyQuery, [
                    'class_status' => 2,
                    'admin_type' => $orderInfo['admin_type'],
                    'company_id' => $orderInfo['company_id'] ,
                    'company_id_history' => $orderInfo['company_id_history'],
                ]);
                // 保存或更新企业培训班
                CourseClassCompanyDBBusiness::replaceById($addClassCompany, $company_id, $classCompanyId, $operate_staff_id, $modifAddOprate);
                // 记录日志
                CourseLogDBBusiness::saveCourseLog($course_id, $courseOrderId, $class_id, $classCompanyId, 0,
                    '企业培训班信息：' . json_encode($addClassCompany), $operate_staff_id, $operate_staff_id_history);

                // 更新每一条学员记录
                foreach($vList as &$k_info){
                    $kUpdate = [
                        'class_id' => $class_id,
                        'class_company_id' => $classCompanyId,
                        'join_class_status' => 4,
                        'join_class_date' => date('Y-m-d'),
                        'join_class_time' => date('Y-m-d H:i:s'),
                    ];
                    static::replaceById($kUpdate, $company_id, $k_info['id'], $operate_staff_id, $modifAddOprate);
                    // 记录日志
                    CourseLogDBBusiness::saveCourseLog($course_id, $courseOrderId, $class_id, $classCompanyId, $k_info['id'],
                        '分班', $operate_staff_id, $operate_staff_id_history);
                }

                // 增加报名企业主表人数
                $queryParams = Tool::getParamQuery(['id' => $courseOrderId], [], []);
                CourseOrderDBBusiness::saveDecIncByQuery('joined_class_num', count($vList),  'inc', $queryParams, []);
                // 记录日志
                CourseLogDBBusiness::saveCourseLog($course_id, 0, $class_id, 0, 0,
                    '增加报名企业主表分班人数+' . count($vList), $operate_staff_id, $operate_staff_id_history);

                // 处理报名主表的分班状态、及缴费状态
                CourseOrderDBBusiness::updateClassAndPay($company_id, $organize_id, $courseOrderId, $operate_staff_id, $modifAddOprate, $operate_staff_id_history);
                // 处理分班企业表的缴费状态
                CourseClassCompanyDBBusiness::updatePay($company_id, $organize_id, $classCompanyId, $operate_staff_id, $modifAddOprate, $operate_staff_id_history);
            }
            // 修改班级状态为开班中
            if($classInfo['class_status'] == 1){
                CourseClassDBBusiness::saveById(['class_status' => 2], $class_id);

                // 修改培训班企业管理 状态为开班
                CourseClassCompanyDBBusiness::save([
                    'class_status' => 2,
                ],Tool::getParamQuery(['course_id' => $course_id, 'class_id' => $class_id, 'class_status' => [1]], [], []));

                // 记录日志
                CourseLogDBBusiness::saveCourseLog($course_id, 0, $class_id, 0, 0,
                    '班级状态变更：待开班->开班中', $operate_staff_id, $operate_staff_id_history);
            }
            // 增加班级人数
            $queryParams = Tool::getParamQuery(['id' => $class_id], [], []);
            CourseClassDBBusiness::saveDecIncByQuery('join_num', count($orderStaffList),  'inc', $queryParams, []);
            // 课程报名池的人数 -
            $queryParams = Tool::getParamQuery(['id' => $course_id], [], []);
            CourseDBBusiness::saveDecIncByQuery('wait_class_num', count($orderStaffList),  'dec', $queryParams, []);
            // 课程已分班的人数 +
            $queryParams = Tool::getParamQuery(['id' => $course_id], [], []);
            CourseDBBusiness::saveDecIncByQuery('joined_class_num', count($orderStaffList),  'inc', $queryParams, []);
            // 记录日志
            CourseLogDBBusiness::saveCourseLog($course_id, 0, $class_id, 0, 0,
                '班级人数+' . count($orderStaffList), $operate_staff_id, $operate_staff_id_history);

        });
        return $modifyNum;
    }



    /**
     * 取消学员分配班级
     *
     * @param int  $company_id 企业id
     * @param int $organize_id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param string/array $ids 数组或字符串 学员id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 修改的数量   //  array 记录id值，--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function cancelClassById($company_id, $organize_id = 0, $ids = 0, $operate_staff_id = 0, $modifAddOprate = 0){
        $modifyNum = 0;
        // 对学员id参数进行转换
        Tool::formatOneArrVals($ids);
        if(empty($ids)) throws('需要取消分配班级的学员不能为空！');

        // 获得学员信息
        $orderStaffList = static::getDBFVFormatList(1, 1, ['id' => $ids],false);
        if(empty($orderStaffList)) throws('需要取消分配班级的学员不能为空！');
        foreach($orderStaffList as $info){
            $tem_id = $info['id'];
            $tem_staff_status = $info['staff_status'];
            $tem_pay_status = $info['pay_status'];
            $tem_join_class_status = $info['join_class_status'];
            if($tem_join_class_status != 4 || $tem_staff_status != 1){
                throws('记录【' . $tem_id . '】非已分班且正常状态的人员，不可以进行此操作');
            }
        }

        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
        $operate_staff_id_history = 0;
        CommonDB::doTransactionFun(function() use(&$company_id, &$organize_id, &$ids, &$operate_staff_id, &$modifAddOprate
            , &$modifyNum, &$ownProperty, &$temNeedStaffIdOrHistoryId, &$operate_staff_id_history, &$orderStaffList){

            if($temNeedStaffIdOrHistoryId && $modifAddOprate){
                $temInfo = [];
                static::addOprate($temInfo, $operate_staff_id,$operate_staff_id_history, 1);
            }
            // 按课程id进行不同的处理
            $formatCourseOrderStaffList = Tool::arrUnderReset($orderStaffList, 'course_id', 2, '_');
            foreach($formatCourseOrderStaffList as $course_id => $courseStaffList){// 按课程
                $modifyNum += count($courseStaffList);
                // 再接班级进行处理
                $formatClassStaffList = Tool::arrUnderReset($courseStaffList, 'class_id', 2, '_');
                foreach($formatClassStaffList as $class_id => $classStaffList){
                    static::cancelClassByClassId($company_id, $organize_id, $classStaffList, $class_id, $operate_staff_id, $modifAddOprate);
                }

            }

        });
        // $modifyNum = count($orderStaffList);
        return $modifyNum;
    }

    /**
     * 取消学员分配班级--根据班级id
     *
     * @param int  $company_id 企业id
     * @param int $organize_id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param array $orderStaffList  一个班级的 学员二维数组
     * @param int $class_id 分配到班级的id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 修改的数量   //  array 记录id值，--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function cancelClassByClassId($company_id, $organize_id = 0, $orderStaffList = [], $class_id = 0, $operate_staff_id = 0, $modifAddOprate = 0){
        $modifyNum = 0;

        // 获得班级信息
        $classInfo = CourseClassDBBusiness::getDBFVFormatList(4, 1, ['id' => $class_id]);
        if(empty($classInfo)) throws('班级信息不存在！');
        if(!in_array($classInfo['class_status'], [1, 2])) throws('班级非待开班或开班中状态，不能取消分配学员！');

        $course_id = $classInfo['course_id'];// 课程id

        // 获得学员信息
        if(empty($orderStaffList)) throws('需要分配班级的学员不能为空！');
        foreach($orderStaffList as $info){
            $tem_id = $info['id'];
            $tem_staff_status = $info['staff_status'];
            $tem_pay_status = $info['pay_status'];
            $tem_join_class_status = $info['join_class_status'];
            if($tem_join_class_status != 4 || $tem_staff_status != 1){
                throws('记录【' . $tem_id . '】非已分班且正常状态的人员，不可以进行此操作');
            }
        }
        // 按主表id格式化数据
        $formatOrderStaffList = Tool::arrUnderReset($orderStaffList, 'course_order_id', 2, '_');

        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
        $operate_staff_id_history = 0;
        CommonDB::doTransactionFun(function() use(&$company_id, &$organize_id, &$class_id, &$operate_staff_id, &$modifAddOprate
            , &$modifyNum, &$ownProperty, &$temNeedStaffIdOrHistoryId, &$operate_staff_id_history, &$classInfo, &$orderStaffList, &$formatOrderStaffList, &$course_id){

            if($temNeedStaffIdOrHistoryId && $modifAddOprate){
                $temInfo = [];
                static::addOprate($temInfo, $operate_staff_id,$operate_staff_id_history, 1);
            }

            // 开始取消分配班级
            foreach($formatOrderStaffList as $courseOrderId => $vList){
                // 获得订单主表
                $orderInfo = CourseOrderDBBusiness::getDBFVFormatList(4, 1, ['id' => $courseOrderId]);
                if(empty($orderInfo)) throws('班级[' . $courseOrderId . ']信息不存在！');
                if(!in_array($orderInfo['company_status'], [1])) throws('班级[' . $courseOrderId . ']非正常状态，不能取消分配学员！');
                // 培训班企业信息处理
                $classCompanyQuery = ['course_id' => $course_id, 'class_id' => $class_id, 'course_order_id' => $courseOrderId];
                $classCompanyInfo = CourseClassCompanyDBBusiness::getDBFVFormatList(4, 1, $classCompanyQuery, false, [], [
                    'sqlParams' => ['whereIn' =>['class_status' =>[1,2]]]
                ]);
                if(empty($classCompanyInfo)) throws('班级企业信息不存在！');

                $classCompanyId = $classCompanyInfo['id'] ?? 0;
                // 企业班级人数 减
                $queryParams = Tool::getParamQuery(['id' => $classCompanyId], [], []);
                CourseClassCompanyDBBusiness::saveDecIncByQuery('join_num', count($vList),  'dec', $queryParams, []);

                // 记录日志
                CourseLogDBBusiness::saveCourseLog($course_id, $courseOrderId, $class_id, $classCompanyId, 0,
                    '企业培训班信息【企业班级人数减少】-' . count($vList), $operate_staff_id, $operate_staff_id_history);

                // 更新每一条学员记录
                foreach($vList as &$k_info){
                    $kUpdate = [
                        'class_id' => 0,
                        'class_company_id' => 0,
                        'join_class_status' => 1,
//                        'join_class_date' => date('Y-m-d'),
//                        'join_class_time' => date('Y-m-d H:i:s'),
                    ];
                    static::replaceById($kUpdate, $company_id, $k_info['id'], $operate_staff_id, $modifAddOprate);
                    // 记录日志
                    CourseLogDBBusiness::saveCourseLog($course_id, $courseOrderId, $class_id, $classCompanyId, $k_info['id'],
                        '取消分班', $operate_staff_id, $operate_staff_id_history);
                }

                // 减少报名企业主表人数
                $queryParams = Tool::getParamQuery(['id' => $courseOrderId], [], []);
                CourseOrderDBBusiness::saveDecIncByQuery('joined_class_num', count($vList),  'dec', $queryParams, []);
                // 记录日志
                CourseLogDBBusiness::saveCourseLog($course_id, 0, $class_id, 0, 0,
                    '减少报名企业主表分班人数+' . count($vList), $operate_staff_id, $operate_staff_id_history);

                // 处理报名主表的分班状态、及缴费状态
                CourseOrderDBBusiness::updateClassAndPay($company_id, $organize_id, $courseOrderId, $operate_staff_id, $modifAddOprate, $operate_staff_id_history);
                // 处理分班企业表的缴费状态
                CourseClassCompanyDBBusiness::updatePay($company_id, $organize_id, $classCompanyId, $operate_staff_id, $modifAddOprate, $operate_staff_id_history);
            }
            // 减少班级人数
            $queryParams = Tool::getParamQuery(['id' => $class_id], [], []);
            CourseClassDBBusiness::saveDecIncByQuery('join_num', count($orderStaffList),  'dec', $queryParams, []);
            // 课程报名池的人数 +
            $queryParams = Tool::getParamQuery(['id' => $course_id], [], []);
            CourseDBBusiness::saveDecIncByQuery('wait_class_num', count($orderStaffList),  'inc', $queryParams, []);
            // 课程已分班的人数 -
            $queryParams = Tool::getParamQuery(['id' => $course_id], [], []);
            CourseDBBusiness::saveDecIncByQuery('joined_class_num', count($orderStaffList),  'dec', $queryParams, []);
            // 记录日志
            CourseLogDBBusiness::saveCourseLog($course_id, 0, $class_id, 0, 0,
                '班级人数减少-' . count($orderStaffList), $operate_staff_id, $operate_staff_id_history);
            // 修改班级状态为开班中
            if($classInfo['class_status'] == 2){
                // 重新获得班级信息
                $classInfo = CourseClassDBBusiness::getDBFVFormatList(4, 1, ['id' => $class_id]);
                if($classInfo['join_num'] <= 0){
                    CourseClassDBBusiness::saveById(['class_status' => 1], $class_id);
                    // 修改培训班企业管理 状态为待开班
                    CourseClassCompanyDBBusiness::save([
                        'class_status' => 1,
                    ],Tool::getParamQuery(['course_id' => $course_id, 'class_id' => $class_id, 'class_status' => [2]], [], []));

                    // 记录日志
                    CourseLogDBBusiness::saveCourseLog($course_id, 0, $class_id, 0, 0,
                        '班级状态变更：开班中->待开班', $operate_staff_id, $operate_staff_id_history);
                }
            }

        });
        return $modifyNum;
    }

    /**
     * 取消学员分配班级--根据班级id
     *
     * @param int  $company_id 企业id
     * @param int $organize_id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param string/array $ids 数组或字符串 学员id -- 对应课程id 的学员id
     * @param int $course_id 课程的id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 修改的数量   //  array 记录id值，--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function cancelClassByIds($company_id, $organize_id = 0, $ids = 0, $course_id = 0, $operate_staff_id = 0, $modifAddOprate = 0){
        $modifyNum = 0;
        // 对学员id参数进行转换
        Tool::formatOneArrVals($ids);
        if(empty($ids)) throws('需要取消分配班级的学员不能为空！');

        // 获得班级信息
        $courseInfo = CourseDBBusiness::getDBFVFormatList(4, 1, ['id' => $course_id]);
        if(empty($courseInfo)) throws('课程信息不存在！');
        if(!in_array($courseInfo['class_status'], [1])) throws('课程非上线状态，不能取消分配学员！');

        // 获得学员信息
        $orderStaffList = static::getDBFVFormatList(1, 1, ['id' => $ids, 'course_id' => $course_id],false);
        if(empty($orderStaffList)) throws('需要分配班级的学员不能为空！');
        foreach($orderStaffList as $info){
            $tem_id = $info['id'];
            $tem_staff_status = $info['staff_status'];
            $tem_pay_status = $info['pay_status'];
            $tem_join_class_status = $info['join_class_status'];
            if($tem_join_class_status != 1 || $tem_staff_status != 1){
                throws('记录【' . $tem_id . '】非待分班且正常状态的人员，不可以进行此操作');
            }
        }
        // 按主表id格式化数据
        $formatClassStaffList = Tool::arrUnderReset($orderStaffList, 'class_id', 2, '_');

        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
        $operate_staff_id_history = 0;
        CommonDB::doTransactionFun(function() use(&$company_id, &$organize_id, &$ids, &$operate_staff_id, &$modifAddOprate
            , &$modifyNum, &$ownProperty, &$temNeedStaffIdOrHistoryId, &$operate_staff_id_history, &$courseInfo, &$orderStaffList, &$formatClassStaffList, &$course_id){

            if($temNeedStaffIdOrHistoryId && $modifAddOprate){
                $temInfo = [];
                static::addOprate($temInfo, $operate_staff_id,$operate_staff_id_history, 1);
            }
            foreach ($formatClassStaffList as $tem_class_id => $classStaffList){
                $modifyNum += count($classStaffList);
                static::cancelClassByClassId($company_id, $organize_id, $classStaffList, $tem_class_id, $operate_staff_id, $modifAddOprate);
            }

        });
        return $modifyNum;
    }

    /**
     * 生成订单
     *
     * @param int  $company_id 企业id 或用户id--无所属企业
     * @param int $organize_id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param string/array $ids 数组或字符串 学员id -- 对应课程id 的学员id
     * @param int $pay_config_id 收款帐号配置id
     * @param int $pay_method 收款方式编号
     * @param array $otherParams 其它参数
     *
     *   $otherParams = [
     *      'total_price_discount' => '0.02',// 商品下单时优惠金额
     *      'payment_amount' => 0,// 总支付金额
     *      'change_amount' => 0,// 找零金额
     *       'remarks' => '',// 订单备注
     *       'auth_code' => '',// 扫码枪扫的付款码
     *  ];
     * @param int $operate_type 操作类型1用户操作2平台操作
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  array
     *  [
     *    'order_no' =>$order_no ,
     *    'pay_config_id' => $pay_config_id,
     *    'pay_method' => $pay_method,
     *    'params' => $return_params
     *    [
     *        'code_url' => '微信网页生成付款码的源地址'
     *        'pay_order_no' => '我方的支付单号'
     *     ]
     *   ]
     * @author zouyan(305463219@qq.com)
     */
    public static function createOrder($company_id, $organize_id = 0, $ids = 0, $pay_config_id = 0, $pay_method = 0, $otherParams = [], $operate_type = 2, $operate_staff_id = 0, $modifAddOprate = 0){

        // 判断收款方式
        $orderPayMethodInfo = OrderPayMethodDBBusiness::getDBFVFormatList(4, 1, ['pay_method' => $pay_method]);
        if(empty($orderPayMethodInfo)) throws('收款方式不存在');
        if($orderPayMethodInfo['status_online'] != 1) throws('收款方式未开启');
        // 判断收款配置
        $orderPayConfigInfo = OrderPayConfigDBBusiness::getDBFVFormatList(4, 1, ['id' => $pay_config_id]);
        if(empty($orderPayConfigInfo)) throws('收款账号不存在');
        if($orderPayConfigInfo['open_status'] != 1) throws('收款账号未开启');
        if(($orderPayConfigInfo['pay_method'] & $pay_method) != $pay_method) throws('收款账号未开启支付方式【' . $orderPayMethodInfo['pay_name'] . '】');

        // 如果是支付宝二维码或支付宝扫码支付，则需要先获得授权信息
        $alipayAuthTokenInfo = [];
        if(in_array($pay_method, [4,64])){
            $alipayAuthTokenInfo = AlipayAuthTokenDBBusiness::getDBFVFormatList(4, 1, ['pay_config_id' => $pay_config_id, 'operate_status' => 2]);
            if(empty($alipayAuthTokenInfo)) throws('此收款帐号支付宝未授权，请先授权才能使用！');
        }

        // 判断人员信息
        $courseStaffList = CourseOrderStaffDBBusiness::getDBFVFormatList(1, 1, ['id' => $ids]);
        if(empty($courseStaffList))  throws('缴费人员信息不能为空！');
        $invoiceTemplateIds = Tool::getArrFields($courseStaffList, 'invoice_template_id');
        if(count($invoiceTemplateIds) > 1) throws('只能选择相同的【发票开票模板】，才能进行多条记录操作！');
        $invoice_template_id = $invoiceTemplateIds[0] ?? 0;

        // 获得报名企业信息
        $courseOrderIds = Tool::getArrFields($courseStaffList, 'course_order_id');
        $courseOrderList = CourseOrderDBBusiness::getDBFVFormatList(1, 1, ['id' => $courseOrderIds]);
        if(empty($courseOrderList))  throws('企业报名信息不能为空！');

        // 获得订单号
        $orderNoArr = Tool::getArrFields($courseStaffList, 'order_no');
        // 去掉空
        Tool::formatOneArrVals($orderNoArr);
        $orderFormatList = [];// 订单信息-- 订单号为下标的数组
        if(!empty($orderNoArr)){
            $orderList = OrdersDBBusiness::getDBFVFormatList(1, 1, ['order_no' => $orderNoArr]);
            $orderFormatList = Tool::arrUnderReset($orderList, 'order_no', 1, '_');
        }

        $total_price = 0;
        foreach($courseStaffList as $courseStaffInfo){
            $tem_course_staff_id = $courseStaffInfo['id'] ?? 0;
            $tem_order_no = $courseStaffInfo['order_no'] ?? '';
            $tem_pay_status = $courseStaffInfo['pay_status'] ?? 0;
            if(!in_array($tem_pay_status, [1]))  throws('记录[' . $tem_course_staff_id . ']非待缴费状态，不可进行缴费操作！');
            if(strlen($tem_order_no) > 0 ){
                $orderInfo = $orderFormatList[$tem_order_no] ?? [];
                if(empty($orderInfo)) throws('订单[' . $tem_order_no . ']不存在！');
                $orderStatus = $orderInfo['order_status'] ?? 0;// 状态1待支付2待确认4已确认8订单完成【服务完成】16取消[系统取消]32取消[用户取消]
                $orderHasRefund = $orderInfo['has_refund'] ?? 0;// 是否退费0未退费1已退费2待退费
                if($orderHasRefund == 2) throws('订单[' . $tem_order_no . ']退费中，不可进行缴费操作！');
                if(in_array($orderStatus, [2,4,8]) && $orderHasRefund == 1) throws('订单[' . $tem_order_no . ']已缴费，不可进行缴费操作！');
            }
            $price = $courseStaffInfo['price'] ?? 0;
            // $total_price += $price;
            $total_price = bcadd($total_price, $price, 0);
        }
        // 价格转为整型
        Tool::bathPriceCutFloatInt($otherParams, ['payment_amount', 'change_amount'], [], 1);
        $total_price_discount = $otherParams['total_price_discount'] ?? 0;
        $total_price_discount = Tool::formatFloadPriceToIntPrice($total_price_discount);// 商品下单时优惠金额
        $total_price_goods = bcsub($total_price, $total_price_discount, 0);// $total_price - $total_price_discount;
        $payment_amount = $otherParams['payment_amount'];// 总支付金额
        $change_amount = $otherParams['change_amount'];// 找零金额
        $auth_code = $otherParams['auth_code'];// 扫码枪扫的付款码

        if(!is_numeric($payment_amount) || !is_numeric($change_amount)) throws('参数：总支付金额或 找零金额 格式有误！');
        if(bcsub($payment_amount, $change_amount, 0) < $total_price_goods) throws('实收金额不能小于应付金额【' . Tool::formatIntPriceToFloadPrice($total_price_goods) . '】');
        $createOrder = [
            'company_id' => $company_id,
            'order_type' => 1,// 订单类型1面授培训2会员年费
            'pay_config_id' => $pay_config_id,// 收款帐号配置id
            'pay_method' => $pay_method,// 支付方式(1现金、2微信支付、4支付宝)
            'remarks' => $otherParams['remarks'] ?? '',// 订单备注
            'total_amount' => count($courseStaffList),// 商品数量-实际/实时
            'total_price' => $total_price,// 商品总价-实际/实时
            'total_price_discount' => $total_price_discount,// 商品下单时优惠金额
            'total_price_goods' => $total_price_goods,// $total_price - $total_price_discount,// 商品应付金额--平台按量结算值(商品总价-实际/实时 total_price －　商品下单时优惠金额　total_price_discount)
            'payment_amount' => $payment_amount,// 总支付金额
            'change_amount' => $change_amount,// 找零金额
            'invoice_template_id' => $invoice_template_id,// 发票开票模板id
        ];
        $order_no = '';
        $return_params = [];// 返回的附加参数
        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
        $operate_staff_id_history = 0;
        CommonDB::doTransactionFun(function() use(&$company_id, &$organize_id, &$ids, &$operate_staff_id, &$modifAddOprate
            , &$ownProperty, &$temNeedStaffIdOrHistoryId, &$operate_staff_id_history, &$order_no, &$createOrder
            , &$pay_config_id, &$pay_method, &$return_params, &$orderPayConfigInfo, &$auth_code, &$operate_type, &$invoice_template_id
            , &$courseOrderList, &$courseOrderIds, &$courseStaffList, &$alipayAuthTokenInfo){
            $invoice_template_id_history = InvoiceTemplateDBBusiness::getIdHistory($invoice_template_id);
            $createOrder['invoice_template_id_history'] = $invoice_template_id_history;
            // 生成订单
            OrdersDBBusiness::createOrder($company_id, $createOrder, $order_no, $operate_staff_id, $modifAddOprate);
            // 修改订单号
            $saveData = [
                'order_no' => $order_no,
                'invoice_template_id_history' => $invoice_template_id_history,
            ];
            static::saveByIds($saveData, $ids);

            info('生成支付订单日志:',[$order_no, $ids]);
            // 修改订单发票商品项目模板id历史
            $courseFormatOrderStaff = Tool::arrUnderReset($courseStaffList, 'invoice_project_template_id', 2, '_');
            foreach($courseFormatOrderStaff as  $invoice_project_template_id => $temOrderStaff){
                $invoice_project_template_id_history = InvoiceProjectTemplateDBBusiness::getIdHistory($invoice_project_template_id);
                $temCourseOrderIds = Tool::getArrFields($temOrderStaff, 'id');
                static::saveByIds([
                    'invoice_project_template_id_history' => $invoice_project_template_id_history
                ], $temCourseOrderIds);
            }

            // 修改企业报名信息
            $saveOrderData = [
                'invoice_template_id_history' => $invoice_template_id_history,
            ];
            CourseOrderDBBusiness::saveByIds($saveOrderData, $courseOrderIds);

            $payKey = $orderPayConfigInfo['pay_key'];// 'banner';
            $createPayOrder = [
                'company_id' => $company_id,
                'operate_type' => $operate_type,// 操作类型1用户操作2平台操作
                'pay_no' => '',// 支付单号(第三方)
                'pay_price' => $createOrder['total_price_goods'],// 支付费用
                'remarks' => '',// 备注
            ];
            switch($pay_method){
                case 2:// 微信收款码【线上--网页生成】
                    $resultPay = OrdersDBBusiness::createOrderPay($company_id, $createPayOrder, $order_no, $operate_staff_id, $modifAddOprate);
                    $app = app('wechat.payment.' . $payKey);
                    $params = [
                        'body' => '面授课--微信收款码支付费用',
                        'out_trade_no' => $resultPay['pay_order_no'],
                        'total_fee' => $createOrder['total_price_goods'],// ceil($createOrder['total_price_goods'] * 100),
                        // 'spbill_create_ip' => '123.12.12.123', // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
                        // 'notify_url' => 'https://pay.weixin.qq.com/wxpay/pay.action', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
                        'trade_type' => 'NATIVE', // 请对应换成你的支付方式对应的值类型
                        // 'openid' => $openid, // 'oUpF8uMuAJO_M2pxb1Q9zNjWeS6o',
                        'product_id' => $payKey . '_' . $order_no, // $message['product_id'] 则为生成二维码时的产品 ID

                    ];
                    try{
                        $result = easyWechatPay::miniProgramunifyExtend($app, $params, 8,function ($resultWX) use(&$return_params, &$resultPay){

                            // 从上一步得到的 $result['code_url'] 得到二维码内容：将 $result['code_url'] 生成二维码图片向用户展示即可扫码，生成工具上面自己找一下即可。 SDK 不内置
                            $return_params['code_url'] = $resultWX['code_url'];
                            $return_params['pay_order_no'] = $resultPay['pay_order_no'];
                            return $resultWX;
                        });
                    } catch ( \Exception $e) {
                        throws('失败；信息[' . $e->getMessage() . ']');
                    }
                    break;
                case 16:// 微信收付款码【线上--扫码枪】
                    $resultPay = OrdersDBBusiness::createOrderPay($company_id, $createPayOrder, $order_no, $operate_staff_id, $modifAddOprate);
                    $app = app('wechat.payment.' . $payKey);
                    $params = [
                        'body' => '面授课--微信收付款码支付费用',
                        'out_trade_no' => $resultPay['pay_order_no'],
                        'total_fee' => $createOrder['total_price_goods'],// ceil($createOrder['total_price_goods'] * 100),
                        // 'spbill_create_ip' => '123.12.12.123', // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
                        // 'notify_url' => 'https://pay.weixin.qq.com/wxpay/pay.action', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
                        // 'trade_type' => 'MICROPAY', // 请对应换成你的支付方式对应的值类型
                        // 'openid' => $openid, // 'oUpF8uMuAJO_M2pxb1Q9zNjWeS6o',
                        'auth_code' => $auth_code, // $message['product_id'] 则为生成二维码时的产品 ID

                    ];
                    try{
                        Tool::phpInitSet();// 可长时间执行
                        $return_params['pay_order_no'] = $resultPay['pay_order_no'];
                        $result = easyWechatPay::microPay($app, $params, function ($resultWX) use(&$return_params,
                            &$order_no, &$resultPay, &$payKey){

                            $return_params['openid'] = $resultWX['openid'] ?? '';
                            // 无论支付成功，都让前端去轮询结果
                            // 付款成功
//                            $orderPayInfo = OrderPayDBBusiness::getDBFVFormatList(4, 1, ['pay_order_no' => $resultPay['pay_order_no']]);

//                            try {
//                                // 先查一下接口，再改状态这样放心
//                                OrderPayDBBusiness::payWXJudgeThirdQuery($order_no, $resultPay['pay_order_no'], $payKey, $orderPayInfo);
//                            } catch ( \Exception $e) {
//                                $errStr = $e->getMessage();
//                                $errCode = $e->getCode();
//                                if(in_array($errCode, [11])){// 已付款成功
////                                    $returnStr = $errStr;
////                                    return $returnStr;
//                                }else{// 没有付款成功
//                                    //                    throws('操作失败；信息[' . $e->getMessage() . ']');
//                                    throws($errStr, $errCode);
//                                }
//                            }
//                            return $resultWX;
                        });
                    } catch ( \Exception $e) {
                        $errStr = $e->getMessage();
                        $errCode = $e->getCode();
                        $errUpperCode = strtoupper($errCode);
                        // SYSTEMERROR	接口返回错误	请立即调用被扫订单结果查询API，查询当前订单状态，并根据订单的状态决定下一步的操作。
                        // BANKERROR	银行系统异常	请立即调用被扫订单结果查询API，查询当前订单的不同状态，决定下一步的操作。
                        // USERPAYING	用户支付中，需要输入密码	等待5秒，然后调用被扫订单结果查询API，查询当前订单的不同状态，决定下一步的操作。
                        $errArr = ['SYSTEMERROR', 'BANKERROR' , 'USERPAYING'];
                        $isThrowErr = true;
                        foreach($errArr as $errKey){
                            if(strpos($errUpperCode, $errKey) !== false){// 包含
                                $isThrowErr = false;
                                break;
                            }
                        }
                        if($isThrowErr) throws('失败；信息[' . $errStr . ']', $errCode);
                    }
                    break;
                case 4:// 支付宝收款码【线上--网页生成】
                    $resultPay = OrdersDBBusiness::createOrderPay($company_id, $createPayOrder, $order_no, $operate_staff_id, $modifAddOprate);
                    $apiParams = [
                        'out_trade_no' => $resultPay['pay_order_no'],// '20150320010101001',//String	必选	64 商户订单号,64个字符以内、只能包含字母、数字、下划线；需保证在商户端不重复
                        'total_amount' => bcdiv($createOrder['total_price_goods'], '100', 2),// Price	必选	11 订单总金额，单位为人民币（元），取值范围为 0.01~100000000.00，精确到小数点后两位。
                        // 'discountable_amount' => '8.88',// Price	可选	11 可打折金额. 参与优惠计算的金额，单位为人民币（元），取值范围为 0.01~100000000.00，精确到小数点后两位。
                        'subject' => '面授课--支付宝收款码支付费用',// String	必选	256	商品的标题/交易标题/订单标题/订单关键字等。 注意：不可使用特殊字符，如 /，=，& 等。
                        // 'product_code' => 'FACE_TO_FACE_PAYMENT',// String	可选	64 销售产品码。 如果签约的是当面付快捷版，则传 OFFLINE_PAYMENT；其它支付宝当面付产品传 FACE_TO_FACE_PAYMENT；不传默认使用 FACE_TO_FACE_PAYMENT。
                         'operator_id' => $operate_staff_id,// 'yx_001',// String	可选	28	商户操作员编号
                         'store_id' => $operate_staff_id,// 'NJ_001',// String	可选	32	商户门店编号
                         'terminal_id' => $operate_staff_id,// 'NJ_T_001',// String	可选	32	商户机具终端编号
                        'timeout_express' => '2m',// String	可选	6 该笔订单允许的最晚付款时间，逾期将关闭交易。取值范围：1m～15d。m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）。该参数数值不接受小数点， 如 1.5h，可转换为 90m。
                        // 'merchant_order_no' => '20161008001',// String	可选	32	商户原始订单号，最大长度限制32位
                        'passback_params' => urlencode($resultPay['pay_order_no']),//  'merchantBizType%3d3C%26merchantBizNo%3d2016010101111',// String	可选	512 公用回传参数，如果请求时传递了该参数，则返回给商户时会回传该参数。支付宝只会在同步返回（包括跳转回商户网站）和异步通知时将该参数原样返回。本参数必须进行UrlEncode之后才可以发送给支付宝。
                        'qr_code_timeout_express' => '90m',// String	可选	6该笔订单允许的最晚付款时间，逾期将关闭交易，从生成二维码开始计时，默认有效期2h。 取值范围：1m～15d。m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）。该参数数值不接受小数点， 如 1.5h，可转换为 90m。当面付场景最大有效期为2h，该场景下本参数设置超过2h，订单将在2h时关闭。
                    ];
                    $alipayConfig = config('public.alipayConfig.APIConfig');
                    $app_auth_token = $alipayAuthTokenInfo['app_auth_token'];
                    $aplipayPID = config('public.alipayConfig.app.trmwebApp.pid');// $alipayAuthTokenInfo['user_id'];
                    try{
                        // 支付预生成订单
                        $notifyUrl = config('public.alipayConfig.notifyUrl');// url('api/pay/alipay/alipayNotify');// String	否	256	支付宝服务器主动通知商户服务器里指定的页面http/https路径。	http://api.test.alipay.net/atinterface/receive_notify.htm
                        $result = AlipayPayAPI::tradePrecreate($alipayConfig, $apiParams, $notifyUrl, $app_auth_token, $aplipayPID);
                        $return_params['code_url'] = $result['qr_code'];
                        $return_params['pay_order_no'] = $resultPay['pay_order_no'];
                    } catch ( \Exception $e) {
                        throws('失败；信息[' . $e->getMessage() . ']');
                    }
                    break;
                case 64:// 支付宝收付款码【线上--扫码枪】
                    $resultPay = OrdersDBBusiness::createOrderPay($company_id, $createPayOrder, $order_no, $operate_staff_id, $modifAddOprate);

                    $apiParams = [
                        'out_trade_no' => $resultPay['pay_order_no'],// '20150320010101001',//String	必选	64 商户订单号,64个字符以内、只能包含字母、数字、下划线；需保证在商户端不重复
                        'scene' => 'bar_code',// * String	必选	32	支付场景。 条码支付，取值：bar_code； 声波支付，取值：wave_code
                        'auth_code' => $auth_code,// '28763443825664394',// *  String	必选	64	支付授权码。25~30开头的长度为16~24位的数字，实际字符串长度以开发者获取的付款码长度为准
                        // 'product_code' => 'FACE_TO_FACE_PAYMENT',// String	可选	64 销售产品码，商家和支付宝签约的产品码。 当面付场景下， 如果签约的是当面付快捷版，则传 OFFLINE_PAYMENT; 其它支付宝当面付产品传 FACE_TO_FACE_PAYMENT；不传则默认使用FACE_TO_FACE_PAYMENT。
                        'subject' => '面授课--支付宝收款码支付费用',// String	必选	256	商品的标题/交易标题/订单标题/订单关键字等。 注意：不可使用特殊字符，如 /，=，& 等。
                        'total_amount' => bcdiv($createOrder['total_price_goods'], '100', 2),// Price	必选	11 订单总金额，单位为人民币（元），取值范围为 0.01~100000000.00，精确到小数点后两位。
                        // 'discountable_amount' => '8.88',// Price	可选	11 可打折金额. 参与优惠计算的金额，单位为人民币（元），取值范围为 0.01~100000000.00，精确到小数点后两位。

                        'operator_id' => $operate_staff_id,// 'yx_001',// String	可选	28	商户操作员编号
                        'store_id' => $operate_staff_id,// 'NJ_001',// String	可选	32	商户门店编号
                        'terminal_id' => $operate_staff_id,// 'NJ_T_001',// String	可选	32	商户机具终端编号
                        'timeout_express' => '2m',// String	可选	6 该笔订单允许的最晚付款时间，逾期将关闭交易。取值范围：1m～15d。m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）。该参数数值不接受小数点， 如 1.5h，可转换为 90m。

                    ];
                    $alipayConfig = config('public.alipayConfig.APIConfig');
                    $app_auth_token = $alipayAuthTokenInfo['app_auth_token'];
                    $aplipayPID = config('public.alipayConfig.app.trmwebApp.pid');// $alipayAuthTokenInfo['user_id'];


                    try{
                        Tool::phpInitSet();// 可长时间执行
                        $return_params['pay_order_no'] = $resultPay['pay_order_no'];
                        // 支付宝统一收单交易支付接口)
                        $notifyUrl = config('public.alipayConfig.notifyUrl');// url('api/pay/alipay/alipayNotify');// String	否	256	支付宝服务器主动通知商户服务器里指定的页面http/https路径。	http://api.test.alipay.net/atinterface/receive_notify.htm

                        try {
                            $resultObj = AlipayPayAPI::tradePay($alipayConfig, $apiParams, $notifyUrl, $app_auth_token, $aplipayPID);
                        } catch ( \Exception $e) {
                            $errStr = $e->getMessage();
                            $errCode = $e->getCode();//  支付成功（10000）， 支付失败（40004）， 等待用户付款（10003）和 未知异常（20000）。
                            if(!in_array($errCode, [10000, 40004, 10003, 20000])){
                                throws($errStr, $errCode);
                            }
                        }
                        // $return_params['openid'] = $resultObj->buyer_logon_id;
                        // 无论支付成功，都让前端去轮询结果

//                        // 付款成功
//                        $orderPayInfo = OrderPayDBBusiness::getDBFVFormatList(4, 1, ['pay_order_no' => $resultPay['pay_order_no']]);
//
//                        try {
//                            // 先查一下接口，再改状态这样放心
//                            OrderPayDBBusiness::payAlipayJudgeThirdQuery($order_no, $resultPay['pay_order_no'], $alipayConfig, $alipayAuthTokenInfo, $orderPayInfo);
//                        } catch ( \Exception $e) {
//                            $errStr = $e->getMessage();
//                            $errCode = $e->getCode();
//                            if(in_array($errCode, [11])){// 已付款成功
////                                    $returnStr = $errStr;
////                                    return $returnStr;
//                            }else{// 没有付款成功
//                                //                    throws('操作失败；信息[' . $e->getMessage() . ']');
//                                throws($errStr, $errCode);
//                            }
//                        }
                    } catch ( \Exception $e) {

                        throws('失败；信息[' . $e->getMessage() . ']');
                    }
                    break;
                default:// 现金等直接确认收款完成的  订单完成支付 1、现金；8、微信收款码【个人-燕】；32、支付宝收款码【个人-燕】
                    OrdersDBBusiness::finishPay($company_id, $order_no, '', $operate_staff_id, $modifAddOprate);
                    break;
            }
        });
        return ['order_no' =>$order_no , 'pay_config_id' => $pay_config_id, 'pay_method' => $pay_method, 'params' => $return_params];

    }

    // *****************支付相关的*********开始*********************************************************

    /**
     * 订单完成支付时，对相关数据的处理
     *
     * @param int  $company_id 企业id
     * @param string  $order_no 生成的订单号
     * @param array  $orderInfo 订单详情
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  null
     * @author zouyan(305463219@qq.com)
     */
    public static function finishPay($company_id, $order_no = '', &$orderInfo, $operate_staff_id = 0, $modifAddOprate = 0){
        // 获得当前订单的所有学员信息
        $staffList = static::getDBFVFormatList(1, 1, ['order_no' => $order_no, 'pay_status' => [1,2]]);

        $ids = Tool::getArrFields($staffList, 'id');
        $total_amount = count($staffList);
        // if($total_amount <= 0) return ;
        if($orderInfo['total_amount'] != $total_amount) throws('订单商品数量与学员数量有误！');
        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
        $operate_staff_id_history = 0;
        CommonDB::doTransactionFun(function() use(&$company_id, &$order_no, &$orderInfo, &$operate_staff_id, &$modifAddOprate
            , &$ownProperty, &$temNeedStaffIdOrHistoryId, &$operate_staff_id_history, &$ids, &$staffList){

            // 对报名企业(主表)  缴费状态进行处理
            $courseOrderFormatList = Tool::arrUnderReset($staffList, 'course_order_id', 2, '_');
            foreach($courseOrderFormatList as $course_order_id => $temStaffList){
                CourseOrderDBBusiness::finishPay($company_id, $course_order_id, count($temStaffList), $operate_staff_id, $modifAddOprate);
            }

            // 对报名企业(主表)班级  缴费状态进行处理
            $classCompanyFormatList = Tool::arrUnderReset($staffList, 'class_company_id', 2, '_');
            foreach($classCompanyFormatList as $class_company_id => $temStaffList){
                CourseClassCompanyDBBusiness::finishPay($company_id, $class_company_id, count($temStaffList), $operate_staff_id, $modifAddOprate);
            }

            // 修改缴费状态
            $saveData = [
                'pay_status' => 4, //  缴费状态(1待缴费、2部分缴费、4已缴费、8部分退费、16已退费 )
                'pay_date' => date('Y-m-d'),
                'pay_time' => date('Y-m-d H:i:s'),
            ];
            static::saveByIds($saveData, $ids);
        });


    }
    // *****************支付相关的*********结束*********************************************************

}
