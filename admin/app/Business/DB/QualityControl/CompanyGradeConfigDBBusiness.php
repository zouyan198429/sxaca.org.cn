<?php
// 企业会员等级配置
namespace App\Business\DB\QualityControl;

use App\Services\DB\CommonDB;
use App\Services\Tool;

/**
 *
 */
class CompanyGradeConfigDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\CompanyGradeConfig';
    public static $table_name = 'company_grade_config';// 表名称
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
        // 如果是新加/修改时
        //    相同时间段内：一个企业，只能有一条记录【审核状态为：待审核、审核通过】
        $isAddOrModify = false;
        if(isset($saveData['company_id']) && isset($saveData['begin_date']) && isset($saveData['end_date'])){
            $isAddOrModify = true;
            $tem_company_id = $saveData['company_id'];
            $tem_begin_date = $saveData['begin_date'];
            $tem_end_date = $saveData['end_date'];
            // 获得企业当前的级别
            $companyInfo = StaffDBBusiness::getInfo($tem_company_id);
            if(empty($companyInfo)) throws('企业信息不存在！');
            $saveData['company_grade_add'] = $companyInfo['company_grade'];
            $saveData['company_grade_final'] = 0;
            $judgeDateArr = [$tem_begin_date, $tem_end_date];// 需要判断的时间
            // 审核不通过状态的记录不用判断
            if(!isset($saveData['open_status']) || (isset($saveData['open_status']) && $saveData['open_status'] != 4)){

                foreach($judgeDateArr as $tem_judge_date){
                    // 先判断开始时间
                    $extParams = [
                        'sqlParams' => [
                            'where' => [
                                ['begin_date', '<' , $tem_judge_date],
                                ['end_date', '>' , $tem_judge_date],
                            ]
                        ]
                    ];
                    if($id > 0) array_push($extParams['sqlParams']['where'], ['id', '<>' ,$id]);
                    $temInfo = static::getDBFVFormatList(4, 1, ['company_id' => $tem_company_id, 'valid_status' => 1,'open_status' => [1,2]], false, [], $extParams);
                    if(!empty($temInfo)) throws('同一时间段内，只能有一条【待审核、审核通过】记录！<br/>您可以修改【待审核】已有的记录或删除已有的记录再新加！');
                }
            }
            // 如果记录已存在---待生效状态的才能操作
            if($id > 0){
                $temInfo = static::getInfo($id);
                if(empty($temInfo)) throws('记录不存在！');
                if($temInfo['valid_status'] != 1) throws('非待生效状态不可进行修改操作！');
            }

        }


        $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表
        // 保存前的处理
        static::replaceByIdAPIPre($saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        $modelObj = null;
        //*********************************************************
        $isModify = false;
        CommonDB::doTransactionFun(function() use(&$saveData, &$company_id, &$id, &$operate_staff_id, &$modifAddOprate, &$operate_staff_id_history, &$modelObj, &$isModify, &$isAddOrModify ){


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
            if($isModify && ($ownProperty & 1) == 1){// 1：有历史表 ***_history;
                static::compareHistory($id, 1);
            }
            if(!$isModify){// 修改时 需要强制更新企业等级配置数量
                // 如果是新加，所需要更新企业等级配置数量
                // 注意，如果是批量操作，不在这里处理，在批量的业务地方再处理此功能
                if( is_numeric($resultDatas['company_id']) && $resultDatas['company_id'] > 0){
                    StaffDBBusiness::updateGradeConfigNum($resultDatas['company_id']);
                }
            }
            if($isAddOrModify){
                // 更新企业的是否有续期
                StaffDBBusiness::updateGradeConfigId($saveData['company_id']);
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
//        Tool::appendParamQuery($queryParams, $organize_id, 'company_id', [0, '0', ''], ',', false);
        $dataListObj = static::getAllList($queryParams, []);
        // $dataListObj = static::getListByIds($id);

        $dataListArr = $dataListObj->toArray();
        if(empty($dataListArr)) throws('没有需要删除的数据');
        // 用户删除要用到的
        $organizeIds = array_values(array_unique(array_column($dataListArr,'company_id')));
        $valid_status = array_values(array_unique(array_column($dataListArr,'valid_status')));
        if(!empty(array_diff($valid_status, [1])))  throws('有非待生效状态记录，不可进行删除操作！');
//        DB::beginTransaction();
//        try {
//            DB::commit();
//        } catch ( \Exception $e) {
//            DB::rollBack();
//            throws($e->getMessage());
//            // throws($e->getMessage());
//        }
        CommonDB::doTransactionFun(function() use( &$id, &$organize_id, &$organizeIds){


            // 删除主记录
//            $delQueryParams = [
//                'where' => [
//                    ['admin_type', $admin_type],
//                    ['issuper','<>', 1],
//                ],
//            ];
            $delQueryParams = Tool::getParamQuery([], [], []);
            Tool::appendParamQuery($delQueryParams, $id, 'id', [0, '0', ''], ',', false);
//            Tool::appendParamQuery($delQueryParams, $organize_id, 'company_id', [0, '0', ''], ',', false);
            static::del($delQueryParams);
            // static::deleteByIds($id);

            // 删除--还需要重新统计企业的简介数
            if(!empty($organizeIds)){
                foreach($organizeIds as $organizeId){
                    // 根据企业id更企业能力范围数
                    StaffDBBusiness::updateGradeConfigNum($organizeId);;
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
            Tool::appendParamQuery($saveQueryParams, $organize_id, 'company_id', [0, '0', ''], ',', false);
            $modifyNum = static::save($updateData, $saveQueryParams);
        });
        return $modifyNum;
    }


    // 对到时间的会员等级进行处理
    public static function autoGradeConfig(){
        $dateTime =  date('Y-m-d H:i:s');
        // 对已生效的进行处理--到期
        $queryParams = Tool::getParamQuery(['valid_status' => 4], ['sqlParams' =>['where' => [['end_date', '<=', $dateTime]]]], []);
        $dataList = static::getAllList($queryParams, [])->toArray();
        if(!empty($dataList)) {
            // $ids = array_values(array_unique(array_column($dataList, 'id')));
            CommonDB::doTransactionFun(function() use(&$dataList, &$dateTime){
                foreach($dataList as $v){
                    // 获得是否有马上开始的
                    $temInfo = static::getDBFVFormatList(4, 1, ['company_id' => $v['company_id'], 'open_status' => 2, 'valid_status' => 1]
                        , false, '', ['sqlParams' => ['where' => [['begin_date', '<=', $dateTime]]]]);

                    static::saveById(['valid_status' => 8, 'final_end_date' => $dateTime], $v['id']);
                    if(!empty($temInfo)){// 继期

                        $new_company_grade = $temInfo['company_grade'];
                        $companyInfo = StaffDBBusiness::getInfo($temInfo['company_id']);
                        $company_grade = $companyInfo['company_grade'] ?? 0;
                        static::saveById(['valid_status' => 4, 'company_grade_final' => $company_grade], $temInfo['id']);
                        // 修改主表
                        StaffDBBusiness::saveById(['company_begin_time' => $temInfo['begin_date'], 'company_end_time' => $temInfo['end_date'],'company_grade' => $new_company_grade], $temInfo['company_id']);
                        // 修改日志
                        CompanyGradeLogDBBusiness::saveGradeLog($temInfo['company_id'], $temInfo['id'], $temInfo['begin_date'], $temInfo['end_date'], $company_grade, $new_company_grade, '会员等级继期生效', 0, 0);
                        // 更新企业的是否有续期
                        StaffDBBusiness::updateGradeConfigId($temInfo['company_id']);
                    }else{// 无继期
                        $new_company_grade = 1;// $v['company_grade'];
                        $companyInfo = StaffDBBusiness::getInfo($v['company_id']);
                        $company_grade = $companyInfo['company_grade'] ?? 0;
                        // 修改主表
                        StaffDBBusiness::saveById(['company_grade' => $new_company_grade], $v['company_id']);
                        // 修改日志
                        CompanyGradeLogDBBusiness::saveGradeLog($v['company_id'], $v['id'], $v['begin_date'], $v['end_date'], $company_grade, $new_company_grade, '会员等级到期', 0, 0);

                    }
                }
            });
        }

        // 对待生效的进行处理
        // 'select' =>['id', 'open_status'],
        $queryParams = Tool::getParamQuery(['valid_status' => 1], ['sqlParams' =>['where' => [['begin_date', '<=', $dateTime]]]], []);
        $dataList = static::getAllList($queryParams, [])->toArray();
        if(!empty($dataList)) {
            // $ids = array_values(array_unique(array_column($dataList, 'id')));
            CommonDB::doTransactionFun(function() use(&$dataList, &$dateTime){
                foreach($dataList as $v){
                    if(in_array($v['open_status'], [1,4])){// 作废处理
                        $saveArr = ['valid_status' => 2];
                        if($v['open_status'] == 1) $saveArr['open_status'] = 4;
                        static::saveById($saveArr, $v['id']);
                    }else{// 开始生效
                        // 对还未过期的进行，完成处理
                        $temInfo = static::getDBFVFormatList(4, 1, ['company_id' => $v['company_id'], 'open_status' => 2, 'valid_status' => 4]
                            , false, '');// , ['sqlParams' => ['where' => [['end_date', '>=', $dateTime], ['begin_date', '<=', $dateTime]]]]
                        if(!empty($temInfo)){
                            static::saveById(['valid_status' => 8, 'final_end_date' => $dateTime], $temInfo['id']);
                        }

                        $new_company_grade = $v['company_grade'];
                        $companyInfo = StaffDBBusiness::getInfo($v['company_id']);
                        $company_grade = $companyInfo['company_grade'] ?? 0;
                        static::saveById(['valid_status' => 4, 'company_grade_final' => $company_grade], $v['id']);
                        // 修改主表
                        StaffDBBusiness::saveById(['company_begin_time' => $v['begin_date'], 'company_end_time' => $v['end_date'],'company_grade' => $new_company_grade], $v['company_id']);
                        // 修改日志
                        CompanyGradeLogDBBusiness::saveGradeLog($v['company_id'], $v['id'], $v['begin_date'], $v['end_date'], $company_grade, $new_company_grade, '新会员等级生效', 0, 0);
                        // 更新企业的是否有续期
                        StaffDBBusiness::updateGradeConfigId($v['company_id']);
                    }

                }
            });
        }
    }

    /**
     * 根据企业id,获得企业的能力范围数
     *
     * @param int  $company_id 企业id
     * @return  mixed 能力范围数
     * @author zouyan(305463219@qq.com)
     */
    public static function getGradeConfigCount($company_id = 0){
        // 更新数
//        $queryParams = [
//            'where' => [
//                ['company_id', $company_id],
//                ['admin_type', 4],
////                ['open_status', 2],
////                ['account_status',1],
//            ],
//            'count' => 0,
//            // 'select' => ['id', 'amount', 'status', 'my_order_no' ]
//        ];
//        $queryParams = Tool::getParamQuery(['company_id' => $company_id], [], []);
//        $queryParams['count'] = 0;
//        $count = static::getAllList($queryParams, []);
//        return $count;
        return static::getDBFVFormatList(8, 1, ['company_id' => $company_id], false);
    }

    /**
     * 根据企业id,获得企业的待生效数
     *
     * @param int  $company_id 企业id
     * @return  mixed 能力范围数
     * @author zouyan(305463219@qq.com)
     */
    public static function getGradeConfigWaitNum($company_id = 0){
        return static::getDBFVFormatList(8, 1, ['company_id' => $company_id, 'open_status' => 2, 'valid_status' => 1], false);
    }
}
